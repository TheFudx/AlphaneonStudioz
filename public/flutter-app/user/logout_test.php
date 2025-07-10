<?php
include 'database.php';

// Get data from POST
$data = json_decode(file_get_contents('php://input'), true);
$deviceIdToLogout = isset($data['device_id']) ? $data['device_id'] : null;
$currentDeviceId = isset($data['current_device_id']) ? $data['current_device_id'] : null;
$userId = isset($data['user_id']) ? $data['user_id'] : null;

if (!$deviceIdToLogout || !$userId) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameters."
    ]);
    exit();
}

// Step 1: Validate device belongs to user
$checkQuery = mysqli_prepare($connectNow, "SELECT * FROM device_sessions WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($checkQuery, "ii", $deviceIdToLogout, $userId);
mysqli_stmt_execute($checkQuery);
$result = mysqli_stmt_get_result($checkQuery);
$device = mysqli_fetch_assoc($result);

if (!$device) {
    echo json_encode([
        "status" => "error",
        "message" => "Device not found or unauthorized."
    ]);
    exit();
}



// Step 2: Invalidate from sessions (optional - only if your app has a custom sessions table)
$deleteSession = mysqli_prepare($connectNow, "DELETE FROM sessions WHERE user_id = ? AND ip_address = ? AND user_agent = ?");
mysqli_stmt_bind_param($deleteSession, "iss", $userId, $device['device_ip'], $device['device_name']);
mysqli_stmt_execute($deleteSession);

// Step 3: Delete from device_sessions
$deleteDevice = mysqli_prepare($connectNow, "DELETE FROM device_sessions WHERE device_id = ?");
mysqli_stmt_bind_param($deleteDevice, "s", $currentDeviceId);
mysqli_stmt_execute($deleteDevice);

// Step 4: If the removed device is the current one, simulate logout
if ($device['device_id'] === $currentDeviceId) {
    echo json_encode([
        "status" => "success",
        "message" => "You were logged out from this device."
    ]);
    exit();
}

// Step 5: Otherwise, just remove and continue
echo json_encode([
    "status" => "success",
    "message" => "Device successfully logged out."
]);
exit();
