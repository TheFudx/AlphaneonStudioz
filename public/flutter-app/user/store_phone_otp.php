<?php
header('Content-Type: application/json');

include 'database.php';


// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

$phoneNumber = isset($data['phone_number']) ? strip_tags($data['phone_number']) : null;

// Basic validation
if (empty($phoneNumber)) {
    echo json_encode([
        "status" => "error",
        "message" => "Please provide a phone number",
    ]);
    exit();
}

// Prepare and execute SQL query
$sqlQuery = mysqli_prepare($connectNow, "INSERT INTO users (`mobile_no`) VALUES (?)");
mysqli_stmt_bind_param($sqlQuery, "s", $phoneNumber);
$result = mysqli_stmt_execute($sqlQuery);

if ($result) {
    $userId = mysqli_insert_id($connectNow); // Get the ID of the inserted row
    echo json_encode([
        "status" => "success",
        "message" => "Phone number inserted successfully",
        "userId" => $userId, // Include the userId in the response
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to store phone number: " . mysqli_error($connectNow),
    ]);
}

mysqli_stmt_close($sqlQuery);
mysqli_close($connectNow);
?>
