<?php

include 'database.php';


// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);
$email = isset($data['email']) ? $data['email'] : null;
$password = isset($data['password']) ? $data['password'] : null;

// Basic validation (example)
if (empty($email) || empty($password)) {
  echo json_encode([
    "status" => "error",
    "message" => "Please fill in all required fields",
  ]);
  exit();
}

// Prepare and execute SQL query to check if user exists
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
  $user = mysqli_fetch_assoc($userData);
  // Verify password using password_verify
  if (password_verify($password, $user['password'])) {
    echo json_encode([
      "status" => "success",
      "message" => "Login successful",
      "id" => $user['id'],
      "name" => $user['name'], // Include the name of the user in the response
      "email" => $user['email'],
      "mobile_no" => $user['mobile_no'],
      "profile_picture" => $user['profile_picture'],
      "subscription" => $user['subscription'],
    ]);
  } else {
    echo json_encode([
      "status" => "error",
      "message" => "Invalid email or password",
    ]);
  }
} else {
  echo json_encode([
    "status" => "error",
    "message" => "User not found",
  ]);
}

mysqli_close($connectNow);
?>
