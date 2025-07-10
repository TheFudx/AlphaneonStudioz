<?php
$apiKey = "rzp_live_JTMIRtru5PB84w";
$apiSecret = "Yb1YK9cc9wPvNErVMQSYYWDX";

$amount = $_POST['amount']; // Get the amount from the request
$userId = $_POST['userId']; // Get the user ID from the request

// API endpoint to create order
$url = 'https://api.razorpay.com/v1/orders';

// Prepare the data for the API request
$data = [
    'amount' => $amount, // Already in paise from frontend
    'currency' => 'INR',
    'receipt' => 'receipt_' . $userId,
    'payment_capture' => 1 // Auto-capture payment
];

// Initiate cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// Execute the request and get the response
$response = curl_exec($ch);
curl_close($ch);

// Decode the JSON response
$order = json_decode($response, true);

// Return the order ID back to the client
echo json_encode($order);
?>