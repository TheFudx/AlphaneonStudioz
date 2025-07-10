<?php
include 'database.php';

// Get data from POST
$data = json_decode(file_get_contents('php://input'), true);

$userId = isset($data['user_id']) ? $data['user_id'] : null;
$currentDeviceId = isset($data['current_device_id']) ? $data['current_device_id'] : null;

if (!$userId) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing user ID."
    ]);
    exit();
}

// Get current device IP and user agent from request
$currentIp = $_SERVER['REMOTE_ADDR'] ?? '';
$currentUA = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Default values to use for deletion
$ipToUse = $currentIp;
$uaToUse = $currentUA;

// If currentDeviceId is provided, try to get device data from device_sessions (only if user is subscribed)
if ($currentDeviceId) {
    $deviceQuery = mysqli_prepare($connectNow, "SELECT device_ip, device_name FROM device_sessions WHERE device_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($deviceQuery, "si", $currentDeviceId, $userId);
    mysqli_stmt_execute($deviceQuery);
    $result = mysqli_stmt_get_result($deviceQuery);
    $device = mysqli_fetch_assoc($result);

    if ($device) {
        $ipToUse = $device['device_ip'];
        $uaToUse = $device['device_name'];
    }
}

// Step 1: Delete session from sessions table (based on current device's IP and UA)
$deleteSession = mysqli_prepare($connectNow, "DELETE FROM sessions WHERE user_id = ? AND ip_address = ? AND user_agent = ?");
mysqli_stmt_bind_param($deleteSession, "iss", $userId, $ipToUse, $uaToUse);
mysqli_stmt_execute($deleteSession);

// Step 2: Optional - delete from device_sessions if device_id is provided
if ($currentDeviceId) {
    $deleteDevice = mysqli_prepare($connectNow, "DELETE FROM device_sessions WHERE device_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($deleteDevice, "si", $currentDeviceId, $userId);
    mysqli_stmt_execute($deleteDevice);
}

// ✅ Final response
echo json_encode([
    "status" => "success",
    "message" => "User successfully logged out from this device."
]);
exit();
?>
