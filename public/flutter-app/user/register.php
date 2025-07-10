<?php

include 'database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents('php://input'), true);

$name = isset($data['name']) ? strip_tags($data['name']) : null;
$email = isset($data['email']) ? strip_tags($data['email']) : null;
$password = isset($data['password']) ? strip_tags($data['password']) : null;

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields",
    ]);
    exit();
}

// Check if email already exists
$checkQuery = mysqli_prepare($connectNow, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($checkQuery, "s", $email);
mysqli_stmt_execute($checkQuery);
mysqli_stmt_store_result($checkQuery);

if (mysqli_stmt_num_rows($checkQuery) > 0) {
    echo json_encode([
        "status" => "fail",
        "message" => "Email Id already taken"
    ]);
    mysqli_stmt_close($checkQuery);
    mysqli_close($connectNow);
    exit();
}
mysqli_stmt_close($checkQuery);

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$type = 5;
// Insert the new user
$sqlQuery = mysqli_prepare($connectNow, "INSERT INTO users (`name`, email, `password`, `type`) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($sqlQuery, "sssi", $name, $email, $hashedPassword, $type);
$result = mysqli_stmt_execute($sqlQuery);

if ($result) {
    $userId = mysqli_insert_id($connectNow);

    // Fetch subscription info
    $subscriptionQuery = mysqli_prepare($connectNow, "SELECT subscription FROM users WHERE id = ?");
    mysqli_stmt_bind_param($subscriptionQuery, "i", $userId);
    mysqli_stmt_execute($subscriptionQuery);
    mysqli_stmt_bind_result($subscriptionQuery, $subscription);
    mysqli_stmt_fetch($subscriptionQuery);
    mysqli_stmt_close($subscriptionQuery);

    echo json_encode([
        "status" => "success",
        "message" => "Registration successful",
        "userId" => $userId,
        "subscription" => $subscription,
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Registration failed: " . mysqli_error($connectNow),
    ]);
}

mysqli_close($connectNow);
?>
