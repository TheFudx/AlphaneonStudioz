<?php
include 'database.php';

// Get current date
$current_date = date('Y-m-d');

// Query to update expired subscriptions
$sqlQuery = "UPDATE users SET subscription = 'No' WHERE subscription_end_date < ? AND subscription = 'Yes'";
$stmt = mysqli_prepare($connectNow, $sqlQuery);
mysqli_stmt_bind_param($stmt, "s", $current_date);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    echo "Expired subscriptions updated successfully.";
} else {
    echo "Error updating expired subscriptions: " . mysqli_stmt_error($stmt);
}

mysqli_close($connectNow);
?>
