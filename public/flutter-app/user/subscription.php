<?php
include 'database.php';

// Get data from POST request
$userId = isset($_POST['userId']) ? $_POST['userId'] : null;
$subscription_status = isset($_POST['subscription']) ? $_POST['subscription'] : null;
$paymentId = isset($_POST['paymentId']) ? $_POST['paymentId'] : null;

// Additional data from Flutter app
$amount = isset($_POST['amount']) ? $_POST['amount'] : null;

// Basic validation
if (empty($userId) || empty($subscription_status) || empty($paymentId) || empty($amount)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input",
    ]);
    exit();
}

// Calculate the subscription end date (28 days from now)
$subscription_end_date = date('Y-m-d', strtotime('+28 days'));

// Begin a transaction
mysqli_begin_transaction($connectNow);

try {
    // Prepare and execute SQL query to update the users table
    $sqlUpdateUser = mysqli_prepare($connectNow, "UPDATE users SET subscription = ?, subscription_end_date = ? WHERE id = ?");
    mysqli_stmt_bind_param($sqlUpdateUser, "ssi", $subscription_status, $subscription_end_date, $userId);
    $resultUser = mysqli_stmt_execute($sqlUpdateUser);

    if (!$resultUser) {
        throw new Exception("Error updating subscription: " . mysqli_stmt_error($sqlUpdateUser));
    }

    // Prepare and execute SQL query to insert into the transaction table
    $sqlInsertTransaction = mysqli_prepare($connectNow, "INSERT INTO transaction (user_id, payment_id, expiry_date, status, package_id, description, amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $status = 1; // Set status to 1 for success
    $packageId = 2; // Assuming package_id is always 2 after a successful transaction
    $description = 'Subscription';
    mysqli_stmt_bind_param($sqlInsertTransaction, "issiiis", $userId, $paymentId, $subscription_end_date, $status, $packageId, $description, $amount);
    $resultTransaction = mysqli_stmt_execute($sqlInsertTransaction);

    if (!$resultTransaction) {
        throw new Exception("Error inserting transaction: " . mysqli_stmt_error($sqlInsertTransaction));
    }

    // Commit the transaction if both queries were successful
    mysqli_commit($connectNow);

    echo json_encode([
        "status" => "success",
        "message" => "Subscription and transaction updated successfully",
    ]);
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    mysqli_rollback($connectNow);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
    ]);
}

// Close the statement and connection
mysqli_stmt_close($sqlUpdateUser);
mysqli_stmt_close($sqlInsertTransaction);
mysqli_close($connectNow);
?>
