<?php
include 'database.php';

$data =  json_decode(file_get_contents('php://input'), true);

$userEmail = isset($data['email']) ? $data['email'] : null;
$userName = isset($data['name']) ? $data['name'] : '';
$deviceName = isset($data['device_name']) ? $data['device_name'] : null; // user_agent
$ipAddress = isset($data['ip_address']) ? $data['ip_address'] : null;
$payload = isset($data['payload']) ? $data['payload'] : null;
$last_activity = isset($data['last_activity']) ? $data['last_activity'] : null;

$subscription = 'Yes';
$userType = 5;
$emailType = 2;

function getDeviceId()
{
    $device_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    $sec_ch_ua = $_SERVER['HTTP_SEC_CH_UA'] ?? '';
    $sec_ch_ua_platform = $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '';
    $sec_ch_ua_mobile = $_SERVER['HTTP_SEC_CH_UA_MOBILE'] ?? '';

    $raw_string = $user_agent . $accept_language . $sec_ch_ua . $sec_ch_ua_platform . $sec_ch_ua_mobile . $device_ip;

    return hash('sha256', $raw_string);
}
$device_id = getDeviceId();;
$session_id = bin2hex(random_bytes(16));

if (empty($userEmail) || empty($deviceName) || empty($ipAddress) || empty($payload) || empty($last_activity)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields",
    ]);
    exit();
}

$stmt = $connectNow->prepare("SELECT * FROM users WHERE email = ? AND `type` =?");
$stmt->bind_param("si", $userEmail, $userType);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "User Already Exits, Login with Email & Password",
    ]);
    $stmt->close();
    $connectNow->close();
    exit();
}

$stmt2 = $connectNow->prepare("SELECT * FROM users where email = ? AND `type` =?");
$stmt2->bind_param("si", $userEmail, $emailType);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row = $result2->fetch_assoc();

// Check if user exists
if (
    ($result2 && $result2->num_rows > 0)
    && $row['subscription'] === $subscription
) {
    $userID = $row['id'];
    $razorSubsId = $row['razorpay_subscription_id'];

    // Current Count
    $deviceCount = $connectNow->prepare("SELECT COUNT(*) as total FROM device_sessions where user_id= ?");
    $deviceCount->bind_param("i", $userID);
    $deviceCount->execute();
    $deviceCountResult = $deviceCount->get_result();
    $deviceCountnum =  $deviceCountResult->fetch_assoc()['total'];

    // Current Plan
    $subQuery = $connectNow->prepare("SELECT package_id FROM user_subscriptions WHERE user_id = ? AND razorpay_subscription_id = ? AND status = ?");
    $subQuery->bind_param("isi", $userID, $razorSubsId,1);
    $subQuery->execute();
    $subResult = $subQuery->get_result();
    $subResultRow = $subResult->fetch_assoc();
    $packageID  = $subResultRow['package_id'];

    // Checking for Adding new device limit
    $countQuery = $connectNow->prepare("SELECT no_of_device FROM package WHERE id = ?");
    $countQuery->bind_param("i", $packageID);
    $countQuery->execute();
    $deviceLimit = $countQuery->get_result()->fetch_assoc()['no_of_device'];

    if (
        $deviceCountnum >= $deviceLimit
    ) {
        //When device count reach limit
        $deviceCheck = $connectNow->prepare("SELECT id FROM device_sessions WHERE user_id = ? AND device_id = ?");
        $deviceCheck->bind_param("is", $userID, $device_id);
        $deviceCheck->execute();
        $countQueryResult = $deviceCheck->get_result();

        if (($countQueryResult->num_rows) === 0) {
            // All Devices for listing
            $allDevicesQuery = $connectNow->prepare("SELECT id, device_id, user_id, device_name, device_ip, updated_at FROM device_sessions WHERE user_id = ? ");
            $allDevicesQuery->bind_param("i", $userID);
            $allDevicesQuery->execute();
            $deviceListResult = $allDevicesQuery->get_result();

            $devices = [];
            while ($d = $deviceListResult->fetch_assoc()) {
                $devices[] = $d;
            }

            echo json_encode([
                "status" => "limitreached",
                "message" => "Device limit reached. Please logout from another device.",
                "devices" => $devices
            ]);
            $connectNow->close();
            exit();
        }
    }

    // Saving Device Session
    $deviceCheck = $connectNow->prepare("SELECT id FROM device_sessions WHERE user_id = ? AND device_id = ?");
    $deviceCheck->bind_param("is", $userID, $device_id);
    $deviceCheck->execute();
    $deviceCheckResult = $deviceCheck->get_result();

    if (($deviceCheckResult->num_rows) > 0) {
        $deviceDataID  = $deviceCheckResult->fetch_assoc()['id'];

        $updateDevice = $connectNow->prepare("UPDATE device_sessions SET updated_at = NOW() WHERE id = ?");
        $updateDevice->bind_param("i", $deviceDataID);
        $updateDevice->execute();
    } else {
        $inserDevice = $connectNow->prepare("INSERT INTO device_sessions (user_id, device_id, device_name, device_ip, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW() )");
        $inserDevice->bind_param("isss", $userID, $device_id, $deviceName, $ipAddress);
        $inserDevice->execute();
    }
    echo json_encode(["status" => "success", "message" => "Login successful"]);
    $connectNow->close();
    exit();
}

if (($result2 && $result2->num_rows > 0)) {
    $userID = $row['id'];
    // Current Working
    $inserDevice = $connectNow->prepare(
        "INSERT INTO sessions 
        (id, user_id, ip_address, user_agent, payload, last_activity)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $inserDevice->bind_param(
        "sssssi",
        $session_id,
        $userID,
        $ipAddress,
        $deviceName,
        $payload,
        $last_activity

    );
    $inserDevice->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "userId" => $userID,
        "session_id" => $session_id,
        "device_id" => $device_id,
    ]);
    $connectNow->close();
    exit();
}
// Create New User & adding them  into session
$stmtInsert = $connectNow->prepare("INSERT INTO users (`name`,email,`type`) VALUES (?,?,?)");
$stmtInsert->bind_param("ssi", $userName, $userEmail, $emailType);
$stmtInsert->execute();
$newUserId = $stmtInsert->insert_id;

$newUserSession =  $connectNow->prepare("INSERT INTO sessions (id,user_id,ip_address,user_agent, payload, last_activity) VALUES (?, ?, ?, ?, ?, ?)");
$newUserSession->bind_param(
    "sssssi",
    $session_id,
    $newUserId,
    $ipAddress,
    $deviceName,
    $payload,
    $last_activity
);
$newUserSession->execute();
echo json_encode([
    "status" => "success",
    "message" => "Registration successful",
    "userId" => $newUserId,
    "subscription" => $subscription,
    "session_id" => $session_id,
    "device_id" => $device_id,
]);
$connectNow->close();
