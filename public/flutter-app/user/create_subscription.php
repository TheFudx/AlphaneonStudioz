<?php
header('Content-Type: application/json'); // Set response header to JSON

// Razorpay API Credentials
$apiKey = "rzp_live_JTMIRtru5PB84w";
$apiSecret = "Yb1YK9cc9wPvNErVMQSYYWDX";

// Get packageId and userId from POST request
$planId = $_POST['planId'] ?? null;
$userId = intval($_POST['userId'] ?? 0);
$packageId = $_POST['packageId'] ?? null;


// Validate input
if ($planId <= 0 || $userId <= 0) {
    echo json_encode(['error' => 'Invalid input: packageId and userId are required and must be valid.']);
    exit;
}

// Database connection parameters
$host = '69.16.233.70';
$db = 'alphastudioz_ott';
$user = 'alphastudioz_ott';
$pass = 'Pass@66466';

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}



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
?>
