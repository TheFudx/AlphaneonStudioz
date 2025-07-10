<?php
require __DIR__ . '/../../../vendor/autoload.php';
include 'database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



// Create connection
$mysqli = mysqli_connect($servername, $username, $password, $database);
if ($mysqli->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

// Get input from post method
 $data = json_decode(file_get_contents('php://input'), true);
    $email = isset($data['email']) ? $data['email'] : null;

// Validate email
if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email is Required."]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email address."]);
    exit;
}

// Check user
$stmt = $mysqli->prepare("SELECT id, email, type FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit;
}

$user = $result->fetch_assoc();

if ($user['type'] != 5) {
    echo json_encode(["status" => "error", "message" => "Please try with your Gmail account."]);
    exit;
}

// Generate OTP
$otp = rand(1000, 9999);

// Send email using PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'mail.alphastudioz.in';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@alphastudioz.in';
    $mail->Password   = 'Info#alpha@123';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('info@alphastudioz.in', 'Alphastudioz IT Media Pvt Ltd');
    $mail->addAddress($user['email'], 'User');

    $mail->isHTML(true);
    $mail->Subject = 'OTP for Forgot Password';
    $mail->Body = "
        <html>
        <body style='padding:5px'>
            <h5>Your email id is {$user['email']} please enter given OTP to reset your password </h5>
            <b>OTP - {$otp}</b>
        </body>
        </html>
    ";

    $mail->send();

    // Check if OTP already exists
    $otp_check = $mysqli->prepare("SELECT id FROM user_otps WHERE user_id = ?");
    $otp_check->bind_param("i", $user['id']);
    $otp_check->execute();
    $otp_result = $otp_check->get_result();

    if ($otp_result->num_rows > 0) {
        // Update OTP
        $update_otp = $mysqli->prepare("UPDATE user_otps SET otp = ? WHERE user_id = ?");
        $update_otp->bind_param("ii", $otp, $user['id']);
        $update_otp->execute();
        $user_otp_id = $otp_result->fetch_assoc()['id'];
    } else {
        // Insert OTP
        $insert_otp = $mysqli->prepare("INSERT INTO user_otps (user_id, otp) VALUES (?, ?)");
        $insert_otp->bind_param("ii", $user['id'], $otp);
        $insert_otp->execute();
        $user_otp_id = $insert_otp->insert_id;
    }

    echo json_encode([
        "status" => "success",
        "message" => "OTP sent successfully on email.",
        "data" => [
            "user_id" => $user['id'],
            "otp" => $otp
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Mailer Error: " . $mail->ErrorInfo
    ]);
}
?>
