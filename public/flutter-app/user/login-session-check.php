<?php



include 'database.php';

// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);
$user_id = isset($data['user_id']) ? $data['email'] : null;
$device_ip = isset($data['device_ip']) ? $data['password'] : null;
$session_id = isset($_SERVER['session_id']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';


if (empty($user_id)  || empty($device_ip) || empty($session_id)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields",
    ]);
    exit();
}
// Check if user exists
$sqlQuery = mysqli_prepare($connectNow, "SELECT * FROM sessions WHERE user_id = ? AND ip_address = ? AND session_id = ?");
mysqli_stmt_bind_param($sqlQuery, "sss", $user_id,$device_ip,$session_id);
mysqli_stmt_execute($sqlQuery);
$session_data = mysqli_stmt_get_result($sqlQuery);

if (mysqli_num_rows($session_data) > 0) {
    echo json_encode([
        "status" => "success",
        "message" => "Active",
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Inactive",
    ]);
}

mysqli_close($connectNow);
