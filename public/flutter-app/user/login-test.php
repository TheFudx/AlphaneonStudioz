<?php



include 'database.php';

// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);
$email = isset($data['email']) ? $data['email'] : null;
$password = isset($data['password']) ? $data['password'] : null;
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';

$deviceName = isset($data['device_name']) ? $data['device_name'] : null;
$deviceIp = $_SERVER['REMOTE_ADDR']; // Get IP address
$device_id = hash('sha256', $deviceIp . $user_agent);

// Basic validation
if (empty($email) || empty($password) || empty($device_id) || empty($deviceName)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please fill in all required fields",
    ]);
    exit();
}

// Check if user exists
$sqlQuery = mysqli_prepare($connectNow, "SELECT * FROM users WHERE email = ?");
mysqli_stmt_bind_param($sqlQuery, "s", $email);
mysqli_stmt_execute($sqlQuery);
$userData = mysqli_stmt_get_result($sqlQuery);

if (mysqli_num_rows($userData) > 0) {
    $user = mysqli_fetch_assoc($userData);

    // Verify password
    if (password_verify($password, $user['password'])) {

        // If user has subscription = 'Yes', check device limit
        if ($user['subscription'] === 'Yes') {
            // Step 1: Get device count
            $deviceCountQuery = mysqli_prepare($connectNow, "SELECT COUNT(*) as total FROM device_sessions WHERE user_id = ?");
            mysqli_stmt_bind_param($deviceCountQuery, "i", $user['id']);
            mysqli_stmt_execute($deviceCountQuery);
            $resultDeviceCount = mysqli_stmt_get_result($deviceCountQuery);
            $deviceCount = mysqli_fetch_assoc($resultDeviceCount)['total'];

            // Step 2: Get package_id from user_subscriptions table
            $subscriptionQuery = mysqli_prepare($connectNow, "SELECT package_id FROM user_subscriptions WHERE user_id = ? AND razorpay_subscription_id = ?");
            mysqli_stmt_bind_param($subscriptionQuery, "is", $user['id'], $user['razorpay_subscription_id']);
            mysqli_stmt_execute($subscriptionQuery);
            $subscriptionResult = mysqli_stmt_get_result($subscriptionQuery);
            $userSubscription = mysqli_fetch_assoc($subscriptionResult);

            if (!$userSubscription) {
                echo json_encode([
                    "status" => "error",
                    "message" => "User subscription record not found. Please contact support."
                ]);
                exit();
            }

            $package_id = $userSubscription['package_id'];

            // Step 3: Get allowed device count from package table
            $packageQuery = mysqli_prepare($connectNow, "SELECT no_of_device FROM package WHERE id = ?");
            mysqli_stmt_bind_param($packageQuery, "i", $package_id);
            mysqli_stmt_execute($packageQuery);
            $packageResult = mysqli_stmt_get_result($packageQuery);
            $package = mysqli_fetch_assoc($packageResult);

            if (!$package) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Package not found. Please contact support."
                ]);
                exit();
            }

            $deviceLimit = $package['no_of_device'];

            // Step 4: Check device limit
          // Step 4: Check device limit
            if ($deviceCount >= $deviceLimit) {
                // Check if device already exists (allowed if previously logged in on same device)
                $deviceCheck = mysqli_prepare($connectNow, "SELECT id FROM device_sessions WHERE user_id = ? AND device_id = ?");
                mysqli_stmt_bind_param($deviceCheck, "is", $user['id'], $device_id);
                mysqli_stmt_execute($deviceCheck);
                $existingDevice = mysqli_stmt_get_result($deviceCheck);
            
                if (mysqli_num_rows($existingDevice) === 0) {
                    // 🔁 Get all current devices for listing
                    $allDevicesQuery = mysqli_prepare($connectNow, "SELECT id, device_id, user_id, device_name, device_ip, updated_at FROM device_sessions WHERE user_id = ?");
                    mysqli_stmt_bind_param($allDevicesQuery, "i", $user['id']);
                    mysqli_stmt_execute($allDevicesQuery);
                    $deviceListResult = mysqli_stmt_get_result($allDevicesQuery);
            
                    $devices = [];
                    while ($row = mysqli_fetch_assoc($deviceListResult)) {
                        $devices[] = $row;
                    }
            
                    echo json_encode([
                        "status" => "limitreached",
                        "message" => "Device limit reached. Please logout from another device.",
                        "devices" => $devices
                    ]);
                    exit();
                }
            }


            // Step 5: Save or update device session
            $deviceCheck = mysqli_prepare($connectNow, "SELECT id FROM device_sessions WHERE user_id = ? AND device_id = ?");
            mysqli_stmt_bind_param($deviceCheck, "is", $user['id'], $device_id);
            mysqli_stmt_execute($deviceCheck);
            $existingDevice = mysqli_stmt_get_result($deviceCheck);

            if (mysqli_num_rows($existingDevice) > 0) {
                $deviceData = mysqli_fetch_assoc($existingDevice);
                $updateDevice = mysqli_prepare($connectNow, "UPDATE device_sessions SET updated_at = NOW() WHERE id = ?");
                mysqli_stmt_bind_param($updateDevice, "i", $deviceData['id']);
                mysqli_stmt_execute($updateDevice);
            } else {
                $insertDevice = mysqli_prepare($connectNow, "INSERT INTO device_sessions (user_id, device_id, device_name, device_ip, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                mysqli_stmt_bind_param($insertDevice, "isss", $user['id'], $device_id, $deviceName, $deviceIp);
                mysqli_stmt_execute($insertDevice);
            }
        }

        // Success response
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "id" => $user['id'],
            "name" => $user['name'],
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
