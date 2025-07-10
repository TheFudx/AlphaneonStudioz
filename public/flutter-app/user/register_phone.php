<?php
header('Content-Type: application/json');

include 'database.php';

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

$phoneNumber = isset($data['phone_number']) ? $data['phone_number'] : null;

// Basic validation
if (empty($phoneNumber)) {
    echo json_encode([
        "status" => "error",
        "message" => "Phone number missing",
    ]);
    exit();
}

// Check if the phone number already exists in the users table
$checkQuery = mysqli_prepare($connectNow, "SELECT id, subscription FROM users WHERE mobile_no = ?");
mysqli_stmt_bind_param($checkQuery, "s", $phoneNumber);
mysqli_stmt_execute($checkQuery);
mysqli_stmt_store_result($checkQuery);

if (mysqli_stmt_num_rows($checkQuery) > 0) {
    mysqli_stmt_bind_result($checkQuery, $userId, $subscription);
    mysqli_stmt_fetch($checkQuery);
    // Phone number already exists
    echo json_encode([
        "status" => "success",
        "message" => "Phone number already exists",
        "userId" => $userId, // Include the userId in the response
        "subscription" => $subscription, // Include the subscription in the response
    ]);
    mysqli_stmt_close($checkQuery);
    mysqli_close($connectNow);
    exit();
} else {
    // Insert the phone number into the users table
    $type = 3;
    $sqlQuery = mysqli_prepare($connectNow, "INSERT INTO users (name, mobile_no, type) VALUES (?, ?, ?)");
    $defaultName = "Default Name"; // Provide a default value for the name field
    mysqli_stmt_bind_param($sqlQuery, "ssi", $defaultName, $phoneNumber, $type);
    $result = mysqli_stmt_execute($sqlQuery);

    if ($result) {
        $userId = mysqli_insert_id($connectNow); // Get the ID of the inserted row
        
        // Fetch the subscription status for the new user
        $subscriptionQuery = mysqli_prepare($connectNow, "SELECT subscription FROM users WHERE id = ?");
        mysqli_stmt_bind_param($subscriptionQuery, "i", $userId);
        mysqli_stmt_execute($subscriptionQuery);
        mysqli_stmt_bind_result($subscriptionQuery, $subscription);
        mysqli_stmt_fetch($subscriptionQuery);
        mysqli_stmt_close($subscriptionQuery);

        echo json_encode([
            "status" => "success",
            "message" => "Phone number inserted successfully",
            "userId" => $userId, // Include the userId in the response
            "subscription" => $subscription, // Include the subscription in the response
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to insert phone number: " . mysqli_error($connectNow),
        ]);
    }

    mysqli_stmt_close($sqlQuery);
    mysqli_close($connectNow);
}
?>
