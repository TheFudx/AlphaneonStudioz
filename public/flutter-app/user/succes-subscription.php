<?php
header("Content-Type: application/json");

// Database Connection

$host = "69.16.233.70";
$username = "alphastudioz_ott";
$password = "Pass@66466";
$database = "alphastudioz_ott";


$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Get Raw Webhook Data
$inputJSON = file_get_contents("php://input");
$payload = json_decode($inputJSON, true);

// Validate Webhook Event Type
if (!isset($payload["event"])) {
    echo json_encode(["status" => "error", "message" => "Invalid webhook payload"]);
    exit;
}

// Extract Subscription ID
$eventType = $payload["event"];
$subscriptionId = null;

if ($eventType == "subscription.charged") {
    $subscriptionId = $payload["payload"]["subscription"]["entity"]["id"];
} elseif ($eventType == "invoice.paid") {
    $subscriptionId = $payload["payload"]["invoice"]["entity"]["subscription_id"];
} elseif ($eventType == "payment.captured") {
    if (isset($payload["payload"]["payment"]["entity"]["order_id"])) {
        // Use order_id to fetch subscription details if needed
        $subscriptionId = $payload["payload"]["payment"]["entity"]["order_id"];
    }
}

// Update User Subscription Status
if ($subscriptionId) {
    $stmt = $conn->prepare("UPDATE users SET subscription = 'Yes' WHERE razorpay_subscription_id = ?");
    $stmt->bind_param("s", $subscriptionId);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Subscription updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database update failed"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "No valid subscription ID found"]);
}

$conn->close();
?>