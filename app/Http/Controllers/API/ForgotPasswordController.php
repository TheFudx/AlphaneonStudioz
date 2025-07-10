<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserOtp;
use Mail;
use Validator;
use Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class ForgotPasswordController extends Controller
{
    //
    public function sendOtpOnMail(Request $request){
        
        $data = $request->all();
        $rules = [
            'email' => 'required|email'
        ];
        $validator = Validator::make($data, $rules);
        if($validator->fails()){
            return response()->json(["status"=>"error","message"=> $validator->errors()->first()]);
        }
        $user = User::where('email',$data['email'])->first();
       
        $otp = rand(1000,9999);
        if($user){
            if($user->type != 5){
                return response()->json([
                'status'=>"error",
                'message'=> 'Please try with your gmail account'
            ]);
            }
           
                // Mail::send('otp', ['otp' => $otp,'user'=>$user], function ($message) use ($request) {
                //     $message->to($request->input('email'))->subject('Your OTP');
                // });
                // Send email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    
                    $mail->isSMTP();
                    $mail->Host       = 'mail.alphastudioz.in'; // Set the SMTP server to send through
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'info@alphastudioz.in';
                    $mail->Password   = 'Info#alpha@123';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    //Recipients
                    $mail->setFrom('info@alphastudioz.in', 'Alphastudioz IT Media Pvt Ltd ');
                    $mail->addAddress('info@alphastudioz.in', 'Alpha Stdudioz');
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
                    return response()->json([
                        'status'=> "success",
                        'message'=> 'OTP send successfully on email',
                        'data'=>[
                            'user_id'=>$user->id,
                            'otp'=>$otp,
                        ]
                    ]);
                } catch (Exception $e) {
                    return response()->json([
                        'status'=>"error",
                        'message'=> 'Message could not be sent. Mailer Error: {$mail->ErrorInfo}"'
                    ]);
                }
           
        }else{
            return response()->json([
                'status'=>"error",
                'message'=> 'User not found'
            ]);
        }
    }
    public function verifyOtp(Request $request){
        $data = $request->all();
        $rules = [
            'user_id' => 'required|numeric',
            'otp' => 'required',
        ];
        $validator = Validator::make($data, $rules);
        if($validator->fails()){
            return response()->json(["status"=>"error","message"=> $validator->errors()->first()]);
        }
        $user = User::find($data['user_id']);
        $user_otp = UserOtp::where('user_id',$data['user_id'])->where('otp',$data['otp'])->first();
    
        // var_dump($user);die();
        if($user_otp){     
            if($user_otp->otp === $data['otp']){
    
                $user_otp->delete();
    
                return response()->json([
                    'status'=> "success",
                    'message'=> 'Otp Verified Successfully',
                    'data'=>[
                        'userId'=>$user->id,
                    ]
                ]);
    
            }else{
                return response()->json([
                    'status'=>"error",
                    'message'=> 'Please enter valid otp'
                ]);
            }
        }else{
            return response()->json([
                    'status'=>"error",
                    'message'=> 'User not found'
                ]);
        }
        
    }
    public function updatePassword(Request $request){
        
        $data = $request->all();
        $rules = [
            'user_id'=>'required|numeric',
            // 'current_password' => 'required',
            'password' => 'required|min:8',
        ];
        $validator = Validator::make($data, $rules);
        if($validator->fails()){
            return response()->json(["status"=>"error","message"=> $validator->errors()->first()]);
        }
        $user = User::where('id',$data['user_id'])->first();
        
        // if ($data['current_password'] && !Hash::check($data['current_password'], $user->password)) {
        //     return response()->json([
        //         'status'=>0,
        //         'message'=>"The provided password does not match your current password."
        //     ]);
        // }
    
        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
                'status'=>"success",
                'message'=>"Password updated successfully"
        ]);
    }
}
