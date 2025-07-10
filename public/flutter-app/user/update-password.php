<?php

include 'database.php';

// Create connection
$mysqli = mysqli_connect($servername, $username, $password, $database);
if ($mysqli->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
$password = isset($data['password']) ? trim($data['password']) : null;

// Validate input
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "user_id is required."]);
    exit;
}
if (!$password) {
    echo json_encode(["status" => "error", "message" => "password is required."]);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long."]);
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

// Hash the new password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update password
$stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashed_password, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Password update failed or no change detected."]);
}
?>