<?php
session_start();

include 'database.php';


// Function to fetch session data
function fetchSessionData($sessionId) {
    global $connectNow;
    $sqlQuery = "SELECT * FROM sessions WHERE id = ?";
    $stmt = mysqli_prepare($connectNow, $sqlQuery);
    mysqli_stmt_bind_param($stmt, "s", $sessionId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return json_decode($row['payload'], true);
    } else {
        return null;
    }
}

$data = json_decode(file_get_contents('php://input'), true);
$sessionId = isset($data['session_id']) ? $data['session_id'] : null;

if (empty($sessionId)) {
    echo json_encode([
        "status" => "error",
        "message" => "Session ID is required",
    ]);
    exit();
}

$sessionData = fetchSessionData($sessionId);

if ($sessionData !== null) {
    echo json_encode([
        "status" => "success",
        "message" => "Session data retrieved successfully",
        "session_data" => $sessionData,
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Session not found",
    ]);
}

mysqli_close($connectNow);
?>
