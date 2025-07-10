<?php
include 'database.php';


// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);
$email = isset($data['email']) ? $data['email'] : null;

// Basic validation (example)
if (empty($email)) {
  echo json_encode([
    "status" => "error",
    "message" => "Please provide an email address",
  ]);
  exit();
}

// Prepare and execute SQL query to check if user exists with the given email
$sqlQuery = mysqli_prepare($connectNow, "SELECT * FROM users WHERE email = ?");
mysqli_stmt_bind_param($sqlQuery, "s", $email);
$result = mysqli_stmt_execute($sqlQuery);

if (!$result) {
  echo json_encode([
    "status" => "error",
    "message" => "Error executing query: " . mysqli_stmt_error($sqlQuery),
  ]);
  exit();
}

$userData = mysqli_stmt_get_result($sqlQuery);

if (mysqli_num_rows($userData) > 0) {
  echo json_encode([
    "status" => "success",
    "message" => "Email exists",
  ]);
} else {
  echo json_encode([
    "status" => "error",
    "message" => "Email does not exist",
  ]);
}

mysqli_close($connectNow);
?>
