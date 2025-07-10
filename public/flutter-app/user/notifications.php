<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Adjust the path to point to your vendor/autoload.php file
require '/Users/best/Development/Flutter_Projects/Projects/alphastudioz/vendor/autoload.php';

// Database connection
include 'database.php';

// Path to the service account JSON file
$serviceAccountPath = '/Users/best/Development/service-account-file.json';

// Fetch notifications from the database
$sql = "SELECT 
    n.title, n.message
FROM 
    notification n
ORDER BY
    n.created_at DESC
LIMIT 10"; // Adjust the limit as needed

$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    $notificationData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $title = $row['title'];
        $message = $row['message'];

        // Send notification to Firebase Cloud Messaging
        sendNotificationToFirebase($title, $message, $serviceAccountPath);
        
        // Optionally, you can store or process fetched data here
        $notificationData[] = $row;
    }

    // Output notification data if needed
    echo json_encode(['notifications' => $notificationData]);
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close database connection
mysqli_close($connectNow);

function sendNotificationToFirebase($title, $message, $serviceAccountPath) {
    $client = new Google_Client();
    $client->setAuthConfig($serviceAccountPath); // Use the correct path to your service account file
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithAssertion();
    }

    $accessToken = $client->getAccessToken()['access_token'];

    $url = 'https://fcm.googleapis.com/v1/projects/alphastudioz-425304/messages:send'; // Update YOUR_PROJECT_ID

    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ];

    $notification = [
        'title' => $title,
        'body' => $message,
    ];

    $message = [
        'message' => [
            'topic' => 'all',
            'notification' => $notification,
        ],
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

    $result = curl_exec($ch);
    if ($result === false) {
        die('Curl failed: ' . curl_error($ch));
    }

    curl_close($ch);

    // Debugging - Uncomment to view FCM response
    // echo 'FCM response: ' . $result;
}
?>
