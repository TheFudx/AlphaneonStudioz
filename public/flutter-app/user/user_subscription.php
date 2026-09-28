<?php
include 'database.php';
header('Content-Type: application/json'); // Set response header to JSON
$data = json_decode(file_get_contents('php://input'), true);

function getOrNull($array, $key)
{
    return isset($array[$key]) && $array[$key] !== '' ? $array[$key] : null;
}

$userId = $data['user_id'] ?? null;
$packageId = $data['package_id'] ?? null;
$razorpayId =  $data['razorpay_subscription_id'] ?? null;
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');

if (empty($userId)) {
    echo json_encode(["status" => "error", "message" => "User ID is required"]);
}

if ($userId !== null && $packageId !== null && $razorpayId === null) {
    $sql = "SELECT * FROM user_subscriptions 
    WHERE user_id = $userId
    AND package_id = $packageId
     ";

    $result = mysqli_query($connectNow, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            echo json_encode(["status" => "success", "message" =>  $row['razorpay_subscription_id']]);
        } else {
            echo json_encode(["status" => "success", "message" => null]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
    }
} else {

    $sql = "INSERT INTO user_subscriptions (user_id, package_id, razorpay_subscription_id, created_at, updated_at) VALUES (?,?,?,?,?)";

    $stmt = mysqli_prepare($connectNow, $sql);
    mysqli_stmt_bind_param($stmt, "iisss", $userId, $packageId, $razorpayId, $created_at, $updated_at);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Subscription updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update"]);
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($connectNow);
