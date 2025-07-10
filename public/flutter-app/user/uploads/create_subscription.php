<?php
// $apiKey = "rzp_test_FZs1ciE3h1febU";
// $apiSecret = "Np504J9Rq1L28GkNdPhqNita";

// $planId = $_POST['planId']; // Get the amount from the request
// $totalCount = $_POST['totalCount']; // Get the user ID from the request

// echo $planId;
// echo $totalCount;


// // API endpoint to create order
// $url = 'https://api.razorpay.com/v1/subscriptions';


// // Prepare the data for the API request
// $data = [
//     'plan_id' => $planId, // Already in paise from frontend
//     'total_count' => intval($totalCount),
// ];

// // Initiate cURL session
// $ch = curl_init($url);

// // Set cURL options
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");
// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
// curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// // Execute the request and get the response
// $response = curl_exec($ch);
// curl_close($ch);

// // Decode the JSON response
// $order = json_decode($response, true);
// echo "Done";
// // Return the order ID back to the client
// echo json_encode($order);
?>

<?php
header('Content-Type: application/json'); // Set response header to JSON

$apiKey = "rzp_test_FZs1ciE3h1febU";
$apiSecret = "Np504J9Rq1L28GkNdPhqNita";

$planId = $_POST['planId']; // Get planId from request
$totalCount = intval($_POST['totalCount']); // Convert totalCount to integer

// API endpoint to create a subscription
$url = 'https://api.razorpay.com/v1/subscriptions';

// Prepare the data for the API request
$data = [
    'plan_id' => $planId,
    'total_count' => $totalCount,
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

// Decode JSON response
$order = json_decode($response, true);

// Send only the JSON response to the client
echo json_encode($order);
?>