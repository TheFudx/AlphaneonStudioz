<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\User;
use App\Models\UserOtp;
use Mail;
use Illuminate\Http\Request;
use Validator;
use Hash;   
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    use SendsPasswordResetEmails;
      public function sendOtpOnMail(Request $request){
        
        $data = $request->all();
        $rules = [
            'email' => 'required|email'
        ];
        $validator = Validator::make($data, $rules);
        if($validator->fails()){
            return response()->json(['status'=>false,"message"=> $validator->errors()->first()]);
        }
        $user = User::where('email',$data['email'])->first();
       
        $otp = rand(1000,9999);
        if($user){
            // if($user->type != 5){
            //     return response()->json([
            //     'status'=>404,
            //     'message'=> 'Please try with your gmail account'
            // ]);
            // }
            // Mail::send('otp', ['otp' => $otp,'user'=>$user], function ($message) use ($request) {
            //     $message->to($request->input('email'))->subject('Your OTP');
            // });
            $mail = new PHPMailer(true);
            try {
                    //Server settings                    
                    $mail->isSMTP();
                    $mail->Host       = 'mail.alphastudioz.in'; // Set the SMTP server to send through
                    $mail->SMTPAuth   =  true;
                    $mail->Username   = 'info@alphastudioz.in';
                    $mail->Password   = 'Info#alpha@123';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    $mail->SMTPOptions = [
                            'ssl' => [
                                'verify_peer'       => false,
                                'verify_peer_name'  => false,
                                'allow_self_signed' => true,
                            ],
                        ];
                    //Recipients
                    $mail->setFrom('info@alphastudioz.in', config('app.project_name'));
                    $mail->addAddress($user->email != null ? $user->email : $user->username, config('app.project_full_name'));
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'OTP for Forgot Password';
                    $mail->Body    = "
                    <html>
                    <body style='padding:5px'>
                       </br> 
                            <h5>Your email id is {$user->email} please enter given OTP to reset your password </h5>
                        </br>
                              <b>  OTP - {$otp} </b>
                        </br>
                    </body>
                    </html>";
                    $mail->send();
                    $validate_user_otp =  UserOtp::where('user_id',$user->id)->first();
                    if($validate_user_otp){
                        $validate_user_otp->otp = $otp;
                        $validate_user_otp->update();
                        $user_otp_id = $validate_user_otp->id;
                    }else{
                        $user_otp = UserOtp::create([
                            'user_id'=>$user->id,
                            'otp'=>$otp,
                        ]);
                        $user_otp_id = $user_otp->id;
                    }
                    $res =  ['user_id'=>$user->id,'otp'=>$otp,'user_otp_id'=>$user_otp_id];
                    return response()->json(['status'=> true,'message'=> 'OTP send successfully on email','data'=>$res ]);
            } catch (Exception $e) {
                    return response()->json([
                        'status'=>false,
                        'message'=> 'Message could not be sent. Mailer Error:'.$mail->ErrorInfo
                    ]);
            }
           
        }else{
            return response()->json([ 'status'=>false,'message'=> 'This email address is not registered with us. Please check your email or log in using OTP.']);
        }
    }
    public function verifyOtp(Request $request){
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'otp' => 'required',
            'user_otp_id'=>'required'
        ];
        $validator = Validator::make($data, $rules);
        if($validator->fails()){
            return response()->json(['status'=>false,"message"=> $validator->errors()->first()]);
        }
        $user = User::find($data['user_id']);
        $user_otp = UserOtp::where('id',$data['user_otp_id'])->where('user_id',$data['user_id'])->first();
    
     
        if($user_otp){     
            if($user_otp->otp === $data['otp']){
               
                return response()->json([
                    'status'=>true,
                    'message'=> 'Otp Verified Successfully',
                    'data'=>[
                        'userId'=>$user->id,
                    ]
                ]);
                 $user_otp->delete();   
                
            }else{
                return response()->json(['status'=>false,'message'=> 'Please enter valid otp']);
            }
        }else{
           return response()->json(['status'=>false,'message'=> 'User not found']);
        }
        
    }
    public function updatePassword(Request $request){
        
        $data = $request->all();
        $rules = [
            'user_id' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one number
                'regex:/[@$!%*#?&]/', // must contain at least one special character
            ],
            'password_confirmation' => 'required|same:password',
        ];
        $messages = [
            'password.required'              => 'Please enter a new password.',
            'password.min'                 => 'Password must be at least 8 characters long.',
            'password.regex'               => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
            'password_confirmation.required' => 'Please confirm your new password.',
            'password_confirmation.same'     => 'Confirm password does not match.',
        ];
        $validator = Validator::make($data, $rules, $messages);
        if($validator->fails()){
            return response()->json(['status'=>false,"message"=> $validator->errors()->first()]);
        }
        $user = User::where('id',$data['user_id'])->first();
        if(!$user){
            return response()->json(['status'=>false, 'message'=>'User not found']);
        }

        // Update the user's password
        $user->password = Hash::make($data['password']);
        $user->save();
        return response()->json(['status'=>true,"message"=> "Password reset successfully.", "url"=> url('/profile/view')]);
    }
}
