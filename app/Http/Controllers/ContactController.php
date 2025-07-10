<?php
namespace App\Http\Controllers;
use App\Models\Contact;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Validator;
use Illuminate\Http\Request;
class ContactController extends Controller
{
    public function submitForm(Request $request)
    {
         $data = $request->all();
        $rules = [
           'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'mobile'    => 'required|string|max:10',
            'message'   => 'required|string|max:500',
        ];
        $validator = Validator::make($data, $rules);
     
        
        if($validator->fails()){
            return back()->with('error',  $validator->errors()->first());
        }
        // Save data to database
        Contact::create($request->all());
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
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body    = "
            <html>
            <body>
                <table border='1' cellpadding='8' cellspacing='0'>
                    <tr>
                        <th>First Name</th>
                        <td>{$request->firstname}</td>
                    </tr>
                    <tr>
                        <th>Last Name</th>
                        <td>{$request->lastname}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{$request->email}</td>
                    </tr>
                    <tr>
                        <th>Mobile</th>
                        <td>{$request->mobile}</td>
                    </tr>
                    <tr>
                        <th>Message</th>
                        <td>{$request->message}</td>
                    </tr>
                </table>
            </body>
            </html>";
            $mail->send();
            return back()->with('success', 'Message has been sent');
        } catch (Exception $e) {
            return back()->with('error', "Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
