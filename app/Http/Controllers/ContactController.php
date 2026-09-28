<?php
namespace App\Http\Controllers;
use App\Models\Contact;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function submitForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s]*$/'],
            'lastname'  => ['required', 'string', 'max:255', 'regex:/^[A-Za-z][A-Za-z\s]*$/'],
            'email'     => ['required', 'string', 'email', 'max:255', 'regex:/^[A-Za-z]/'],
            'mobile'    => ['required', 'numeric', 'digits:10'],
            'message'   => ['required', 'string', 'min:10', 'max:500'],
            'g-recaptcha-response' => ['required', function ($attr, $value, $fail) {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

                if (! ($response->json()['success'] ?? false)) {
                    $fail('reCAPTCHA verification failed. Please try again.');
                }
                }],
        ], [
            'firstname.required' => 'First name is required.',
            'firstname.regex'    => 'First name must start with a letter and contain only alphabets.',
            'lastname.required'  => 'Last name is required.',
            'lastname.regex'     => 'Last name must start with a letter and contain only alphabets.',
            'email.required'     => 'Email is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.regex'        => 'Email must start with a letter.',
            'mobile.required'    => 'Mobile number is required.',
            'mobile.numeric'     => 'Mobile number must contain only digits.',
            'mobile.digits'      => 'Mobile number must be exactly 10 digits.',
            'message.required'   => 'Message is required.',
            'message.min'        => 'Message must be at least 10 characters.',
            'message.max'        => 'Message must not exceed 500 characters.',
             'g-recaptcha-response.required' => 'Please complete the reCAPTCHA.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Save to database
        Contact::create($request->only(['firstname', 'lastname', 'email', 'mobile', 'message']));

        if(env('APP_ENV') === 'production'){
        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'mail.alphastudioz.in';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'info@alphastudioz.in';
            $mail->Password   = 'Info#alpha@123';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('info@alphastudioz.in', config('app.project_name') . 'IT Media Pvt Ltd');
            $mail->addAddress('info@alphastudioz.in', config('app.project_full_name'));

            $mail->isHTML(true);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body    = "
            <html>
            <body>
                <table border='1' cellpadding='8' cellspacing='0'>
                    <tr><th>First Name</th><td>{$request->firstname}</td></tr>
                    <tr><th>Last Name</th><td>{$request->lastname}</td></tr>
                    <tr><th>Email</th><td>{$request->email}</td></tr>
                    <tr><th>Mobile</th><td>{$request->mobile}</td></tr>
                    <tr><th>Message</th><td>{$request->message}</td></tr>
                </table>
            </body>
            </html>";

            $mail->send();

            return response()->json([
                'status'  => true,
                'message' => 'Your message has been sent successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Message could not be sent. Please try again.'
            ], 500);
        }
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'Your message has been received successfully!'
            ]);
        }
    }
}
