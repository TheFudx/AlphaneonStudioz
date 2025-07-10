<?php
header('Content-Type: application/json');

// Get required values
$razorpayPaymentId = $_POST['razorpayPaymentId'] ?? null;
$razorpaySignature = trim($_POST['razorpaySignature'] ?? '');
$userId = intval($_POST['userId'] ?? 0);
$planId = $_POST['planId'] ?? ''; 

if (!$razorpayPaymentId || !$razorpaySignature || !$userId || !$planId) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Database connection
$servername = "69.16.233.70";
$username = "alphastudioz_ott";
$password = "Pass@66466";
$dbname = "alphastudioz_ott";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Fetch order_id from DB
$stmt = $conn->prepare("SELECT razorpay_subscription_id FROM users_subscriptions WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User not found or no subscription found']);
    $stmt->close();
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$order_id = $row['razorpay_subscription_id'];
$stmt->close();

// Razorpay Secret Key
$key_secret = 'Yb1YK9cc9wPvNErVMQSYYWDX'; 

// Verify signature
$body = $razorpayPaymentId . '|' . $order_id;
$expectedSignature = hash_hmac('sha256', $body, $key_secret);

if ($expectedSignature !== $razorpaySignature) {
    $updateStmt = $conn->prepare("UPDATE users SET subscription = 'Failed' WHERE id = ?");
    $updateStmt->bind_param("i", $userId);
    $updateStmt->execute();
    $updateStmt->close();

    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid signature. Payment verification failed',
        'expected' => $expectedSignature,
        'received' => $razorpaySignature
    ]);
    $conn->close();
    exit;
}

// Fetch package details using the plan_id
$packageStmt = $conn->prepare("SELECT id, time, price FROM package WHERE razorpay_plan_id = ? AND status = 1 AND is_delete = 0");
$packageStmt->bind_param("s", $planId);
$packageStmt->execute();
$packageResult = $packageStmt->get_result();

if ($packageResult->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Package not found for the given plan ID.']);
    $packageStmt->close();
    $conn->close();
    exit;
}

$package = $packageResult->fetch_assoc();
$duration = intval($package['time']); // subscription duration in months
$package_id = $package['id'];
$amount = $package['price'];
$packageStmt->close();

// Fetch current expiry date
$stmtExpiry = $conn->prepare("SELECT subscription_end_date FROM users WHERE id = ?");
$stmtExpiry->bind_param("i", $userId);
$stmtExpiry->execute();
$resultExpiry = $stmtExpiry->get_result();
$rowExpiry = $resultExpiry->fetch_assoc();
$currentExpiryDate = $rowExpiry['subscription_end_date'] ?? date('Y-m-d');
$stmtExpiry->close();

// Calculate new expiry date
$newExpiryDate = date('Y-m-d', strtotime("+$duration months", strtotime($currentExpiryDate)));

// Update users table
$updateStmt = $conn->prepare("UPDATE users SET subscription = 'Yes', subscription_end_date = ?, razorpay_signature_id =? WHERE id = ?");
$updateStmt->bind_param("si", $newExpiryDate,$order_id, $userId);

if ($updateStmt->execute()) {
    // Insert into transactions table
    $tStatus = 1;
    $insertStmt = $conn->prepare("INSERT INTO `transaction` (user_id, package_id, amount, payment_id, expiry_date, status) VALUES (?, ?, ?, ?, ?, ?)");
    $insertStmt->bind_param("iisssi", $userId, $package_id, $amount, $razorpayPaymentId, $newExpiryDate, $tStatus);
    $pending_status = 0;
    $insertStmt = $conn->prepare("UPDATE users_subscriptions SET status = 1 WHERE id = ? AND razorpay_signature_id = ? AND status = ?");
    $insertStmt->bind_param("is", $userId,  $order_id,$pending_status);
    
    
    if ($insertStmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Subscription updated", "expiry_date" => $newExpiryDate]);
    } else {
        echo json_encode(["status" => "error", "message" => "Transaction insert failed", "error" => $insertStmt->error]);
    }
    $insertStmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Database update failed"]);
}
$updateStmt->close();

$conn->close();
?>
