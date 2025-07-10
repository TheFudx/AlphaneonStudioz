<?php 
    include 'database.php';
    
    // Get data from POST request
    $data = json_decode(file_get_contents('php://input'), true);
    $mobile_no = isset($data['mobile_no']) ? $data['mobile_no'] : null;
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
    
    $deviceName = isset($data['device_name']) ? $data['device_name'] : null;
    $deviceIp = $_SERVER['REMOTE_ADDR']; // Get IP address
    
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

    $device_id = getDeviceId();
    
    $payload = isset($data['payload']) ? $data['payload'] : null;
    $last_activity = isset($data['last_activity']) ? $data['last_activity'] : null;

    $session_id = bin2hex(random_bytes(16)); 

    // Basic validation
    if (empty($mobile_no) || empty($device_id) || empty($deviceName) || empty($payload) || empty($last_activity)) {
        echo json_encode([
            "status" => "error",
            "message" => "Please fill in all required fields",
        ]);
        exit();
    }


    // Check if user exists
    $sqlQuery = mysqli_prepare($connectNow, "SELECT * FROM users WHERE mobile_no = ?");
    mysqli_stmt_bind_param($sqlQuery, "s", $mobile_no);
    mysqli_stmt_execute($sqlQuery);
    $userData = mysqli_stmt_get_result($sqlQuery);

    if (mysqli_num_rows($userData) > 0) {
        $user = mysqli_fetch_assoc($userData);

        if($user['subscription'] === 'Yes'){
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

        $sqlQuerySession = mysqli_prepare($connectNow, "INSERT INTO sessions (id, user_id, ip_address, user_agent, payload, last_activity) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($sqlQuerySession, "sssssi", $session_id, $user['id'], $deviceIp, $user_agent, $payload, $last_activity);
            $resultSession = mysqli_stmt_execute($sqlQuerySession);
            
            
            // if($resultSession){
            //     echo json_encode([
            //         "status" => "sessionStored",
            //         "message" => "Session stored successfully",
            //         "session_id" => $session_id,
            //     ]);
            // }
            // else{
            //     echo json_encode([
            //         "status" => "Error",
            //         "message" => "Session is not stored ",
                    
            //     ]);
            // }

            // Success response
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "userId" => $user['id'],
                "session_id" => $session_id,
                "device_id"=>$device_id,
                "name" => $user['name'],
                "email" => $user['email'],
                "mobile_no" => $user['mobile_no'],
                "profile_picture" => $user['profile_picture'],
                "subscription" => $user['subscription'],
            ]);
    }else{
        // echo json_encode([
        //     "status" => "error",
        //     "message" => "User not found",
        // ]);
        $name ='Guest';
        $mobileTYpe = 3;
        $stmtInsert = $connectNow->prepare("INSERT INTO users (`name`,`mobile_no`,`type`) VALUES (?,?,?)");
         
        $stmtInsert->bind_param("ssi", $name , $mobile_no, $mobileTYpe );
     
        $stmtInsert->execute();
       
        $newUserId = $stmtInsert->insert_id;
        $newUserSession =  $connectNow->prepare("INSERT INTO sessions (id,user_id,ip_address,user_agent, payload, last_activity) VALUES (?, ?, ?, ?, ?, ?)");
        $newUserSession->bind_param(
            "sssssi",
            $session_id,
            $newUserId,
            $deviceIp,
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
        
    }
?>