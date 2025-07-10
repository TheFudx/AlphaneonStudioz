<?php
header('Content-Type: application/json'); // Set response header to JSON
// Replace these with actual values
$apiKey = "rzp_live_JTMIRtru5PB84w";
$apiSecret = "Yb1YK9cc9wPvNErVMQSYYWDX";

$packageId = $_POST['packageId'] ?? null;
$planId = $_POST['planId'] ?? null;
$userId = intval($_POST['userId'] ?? 0);

if (!$packageId || !$userId) {
    echo json_encode(['error' => true, 'message' => 'Missing packageId or userId']);
    exit;
}

// DB connection
$host = '69.16.233.70';
$db = 'alphastudioz_ott';
$user = 'alphastudioz_ott';
$pass = 'Pass@66466';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => true, 'message' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}


// Check if user exists
$stmtUser = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$stmtUser->store_result();
if ($stmtUser->num_rows === 0) {
    echo json_encode(['error' => true, 'message' => 'User not found']);
    exit;
}
$stmtUser->close();

// Check existing active or pending subscription
$stmt = $conn->prepare("SELECT razorpay_subscription_id FROM user_subscriptions WHERE user_id = ? AND package_id = ? AND status = 1 ORDER BY id DESC LIMIT 1");
$stmt->bind_param("ii", $userId, $packageId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $sub = $result->fetch_assoc();
    $existingSubId = $sub['razorpay_subscription_id'];

    // Fetch subscription from Razorpay
    $ch = curl_init("https://api.razorpay.com/v1/subscriptions/$existingSubId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
    $razorpayResponse = curl_exec($ch);
    curl_close($ch);

    $subscription = json_decode($razorpayResponse, true);

    if (isset($subscription['status']) && in_array($subscription['status'], ['created', 'pending'])) {
        echo json_encode([
            'subscription_id' => $existingSubId,
            'status' => 'existing',
            'message' => 'Using existing pending subscription.'
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
}
$stmt->close();



// Fetch Razorpay Plan ID from package
$stmtPlan = $conn->prepare("SELECT razorpay_plan_id FROM package WHERE id = ? AND status = 1 AND is_delete = 0");
$stmtPlan->bind_param("i", $packageId);
$stmtPlan->execute();
$resultPlan = $stmtPlan->get_result();
if ($resultPlan->num_rows === 0) {
    echo json_encode(['error' => true, 'message' => 'Package not found']);
    exit;
}
$package = $resultPlan->fetch_assoc();
$planId = $package['razorpay_plan_id'];
$stmtPlan->close();

// Create new Razorpay subscription
$subscriptionData = [
    'plan_id' => $planId,
    'customer_notify' => 1,
    'total_count' => 12,
    'quantity' => 1,
];

$ch = curl_init('https://api.razorpay.com/v1/subscriptions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($subscriptionData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order = json_decode($response, true);



if ($httpStatusCode === 200 && isset($order['id'])) {
    $subscriptionId = $order['id'];

    // Save new subscription to DB
    $stmtInsert = $conn->prepare("INSERT INTO user_subscriptions (user_id, razorpay_subscription_id, package_id, status) VALUES (?, ?, ?, 0)");
    $stmtInsert->bind_param("isi", $userId, $subscriptionId, $packageId);
    if ($stmtInsert->execute()) {
        echo json_encode([
            'subscription_id' => $subscriptionId,
            'status' => 'created',
            'message' => 'New subscription created.'
        ]);
    } else {
        echo json_encode(['error' => true, 'message' => 'Failed to save subscription in DB']);
    }
    $stmtInsert->close();
} else {
    echo json_encode([
        'error' => true,
        'message' => 'Failed to create subscription on Razorpay',
        'http_status' => $httpStatusCode,
        'razorpay_response' => $order
    ]);
}

$conn->close();
?>

<!--
// Fetch plan_id and duration from packages table
 $stmt = $conn->prepare("SELECT razorpay_plan_id FROM package WHERE id = ? AND status = 1 AND is_delete = 0");
$stmt->bind_param("i", $packageId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Invalid package: no active package found.']);
    $stmt->close();
    $conn->close();
    exit;
}

$package = $result->fetch_assoc();
$planId = $package['razorpay_plan_id'];
$totalCount = $_POST['totalCount'] ?? null;

$stmt->close();

// Razorpay API endpoint to create a subscription
$url = 'https://api.razorpay.com/v1/subscriptions';
$data = [
    'plan_id' => $planId,
    'total_count' => $totalCount,
];

// Initiate cURL session
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)]);
    curl_close($ch);
    $conn->close();
    exit;
}

$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order = json_decode($response, true);
if ($httpStatusCode === 200 && isset($order['id'])) {
    $subscriptionId = $order['id'];

    // Verify user exists before updating
    $stmtCheck = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmtCheck->bind_param("i", $userId);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        $updateQuery = "UPDATE users SET razorpay_subscription_id = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($updateQuery);
        $stmtUpdate->bind_param("si", $subscriptionId, $userId);

        if ($stmtUpdate->execute()) {
            echo json_encode($order);
        } else {
            echo json_encode(['error' => 'Failed to store the subscription ID in the database.']);
        }
        $stmtUpdate->close();
    } else {
        echo json_encode(['error' => 'User not found.']);
    }
    $stmtCheck->close();
} else {
    echo json_encode([
        'error' => 'Failed to create subscription.',
        'http_status' => $httpStatusCode,
        'response' => $order
    ]);
}

$conn->close();
?> -->
