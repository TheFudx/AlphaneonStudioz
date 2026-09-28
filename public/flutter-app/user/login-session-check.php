<?php



include 'database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit(); // Stop execution if connection fails
}
// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);
$user_id = isset($data['user_id']) ? $data['user_id'] : null;
$session_id = isset($data['session_id']) ? $data['session_id'] : null;

if (empty($user_id) ) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id field is  required.",
    ]);
    exit();
}
if(empty($session_id)){
 echo json_encode([
        "status" => "error",
        "message" => "session_id field is  required.",
    ]);
    exit();
}
// Check if user exists

$sqlQuery = mysqli_prepare($connectNow, "SELECT * FROM sessions WHERE user_id = ? AND  id = ?");
mysqli_stmt_bind_param($sqlQuery, "is",$user_id, $session_id);
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
