<?php

include 'database.php';

// Create connection
$mysqli = mysqli_connect($servername, $username, $password, $database);
if ($mysqli->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
$otp = isset($data['otp']) ? trim($data['otp']) : null;

// Validate input
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "user_id is required."]);
    exit;
}
if (!$otp) {
    echo json_encode(["status" => "error", "message" => "otp is required."]);
    exit;
}

// Check if user exists
$stmt = $mysqli->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit;
}

// Check if OTP matches
$stmt = $mysqli->prepare("SELECT id FROM user_otps WHERE user_id = ? AND otp = ?");
$stmt->bind_param("is", $user_id, $otp);
$stmt->execute();
$otp_result = $stmt->get_result();

if ($otp_result->num_rows > 0) {
    // OTP is valid – delete it
    $stmt = $mysqli->prepare("DELETE FROM user_otps WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Otp Verified Successfully",
        "data" => [
            "user_id" => $user_id
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Please enter valid otp."]);
}

?>