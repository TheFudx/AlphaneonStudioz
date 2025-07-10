<?php

// include 'database.php'; // Ensure this file connects to your database

// // Get data from POST request
// $data = json_decode(file_get_contents('php://input'), true);
// // print_r($data); // Shows all keys and values
// $user_id = isset($data['user_id']) ? $data['user_id'] : null;
// $ip_address = isset($data['ip_address']) ? $data['ip_address'] : null;
// $user_agent = isset($data['user_agent']) ? $data['user_agent'] : null;
// $payload = isset($data['payload']) ? $data['payload'] : null;
// $last_activity = isset($data['last_activity']) ? $data['last_activity'] : null;
// $device_id = isset($data['user_agent']) ? $data['user_agent'] : null;
// $device_ip = isset($data['ip_address']) ? $data['ip_address'] : null;
// $created_at = date('Y-m-d H:i:s'); // Current timestamp for created_at
// $updated_at = null; // Initially null for updated_at


// if (empty($user_id) || empty($ip_address) || empty($user_agent) || empty($payload) || empty($last_activity) || empty($device_id) || empty($device_ip)) {
//   echo json_encode([
//     "status" => "error",
//     "message" => "Please fill in all required fields",
//   ]);
//   exit();
// }

// $session_id = bin2hex(random_bytes(16)); 

// // Prepare and execute SQL query to insert session details
// $sqlQuerySession = mysqli_prepare($connectNow, "INSERT INTO sessions (id, user_id, ip_address, user_agent, payload, last_activity) VALUES (?, ?, ?, ?, ?, ?)");


// mysqli_stmt_bind_param($sqlQuerySession, "sssssi", $session_id, $user_id, $ip_address, $user_agent, $payload, $last_activity);

// // Prepare and execute SQL query to insert device details
// $sqlQueryDevice = mysqli_prepare($connectNow, "INSERT INTO devices (user_id, device_id, device_ip, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
// mysqli_stmt_bind_param($sqlQueryDevice, "issss", $user_id, $device_id, $device_ip, $created_at, $updated_at);

// // Execute both queries
// $resultSession = mysqli_stmt_execute($sqlQuerySession);
// $resultDevice = mysqli_stmt_execute($sqlQueryDevice);

// // Check results and handle errors
// if ($resultSession && $resultDevice) {
//   echo json_encode([
//     "status" => "success",
//     "message" => "Session and device details stored successfully",
//   ]);
// } else {
//   $errorMessageSession = "Error starting session: " . mysqli_stmt_error($sqlQuerySession);
//   $errorMessageDevice = "Error storing device details: " . mysqli_stmt_error($sqlQueryDevice);
//   echo json_encode([
//     "status" => "error",
//     "message" => $errorMessageSession . " | " . $errorMessageDevice,
//   ]);
//   error_log($errorMessageSession); // Log detailed error message
//   error_log($errorMessageDevice); // Log detailed error message
// }

// mysqli_stmt_close($sqlQuerySession); // Close prepared statement for session
// mysqli_stmt_close($sqlQueryDevice); // Close prepared statement for device
// mysqli_close($connectNow); // Close database connection


include 'database.php'; 
$data = json_decode(file_get_contents('php://input'), true);

$user_id = isset($data['user_id']) ? $data['user_id'] : null;
$ip_address = isset($data['ip_address']) ? $data['ip_address'] : null;
$user_agent = isset($data['user_agent']) ? $data['user_agent'] : null;
$payload = isset($data['payload']) ? $data['payload'] : null;
$last_activity = isset($data['last_activity']) ? $data['last_activity'] : null;

$device_ip = $ip_address;
$created_at = date('Y-m-d H:i:s');
$updated_at = null; 

if (empty($user_id) || empty($ip_address) || empty($user_agent) || empty($payload) || empty($last_activity)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields",
    ]);
    exit();
}


$session_id = bin2hex(random_bytes(16)); 
$device_id = hash('sha256', $ip_address . $user_agent);

$sqlQuerySession = mysqli_prepare($connectNow, "INSERT INTO sessions (id, user_id, ip_address, user_agent, payload, last_activity) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($sqlQuerySession, "sssssi", $session_id, $user_id, $ip_address, $user_agent, $payload, $last_activity);
$resultSession = mysqli_stmt_execute($sqlQuerySession);


if($resultSession){
    echo json_encode([
        "status" => "success",
        "message" => "Session stored successfully",
        "session_id" => $session_id,
    ]);
}
else{
     echo json_encode([
        "status" => "Error",
        "message" => "Session is not stored ",
        
    ]);
}

mysqli_stmt_close($subscriptionCheck);
if (isset($deviceCheck)) mysqli_stmt_close($deviceCheck);
mysqli_stmt_close($sqlQuerySession);
if (isset($sqlQueryDevice)) mysqli_stmt_close($sqlQueryDevice);
mysqli_close($connectNow);

?>
