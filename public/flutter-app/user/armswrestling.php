<?php
include 'database.php';

// Get data from POST request
$paymentId = isset($_POST['paymentId']) ? $_POST['paymentId'] : null;
$fullName = isset($_POST['fullName']) ? $_POST['fullName'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$age = isset($_POST['age']) ? $_POST['age'] : null;
$weight = isset($_POST['weight']) ? $_POST['weight'] : null;
$dominantHand = isset($_POST['dominantHand']) ? $_POST['dominantHand'] : null;
$experienceLevel = isset($_POST['experienceLevel']) ? $_POST['experienceLevel'] : null;
$injuryHistory = isset($_POST['injuryHistory']) ? $_POST['injuryHistory'] : null;
$phoneNumber = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : null;
$amount = isset($_POST['amount']) ? $_POST['amount'] : null;

// Basic validation
if (empty($paymentId) || empty($fullName) || empty($email) || empty($age) || empty($weight) || empty($dominantHand) || empty($experienceLevel) || empty($phoneNumber) || empty($amount)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input",
    ]);
    exit();
}

// Begin a transaction
mysqli_begin_transaction($connectNow);

try {
    // Prepare and execute SQL query to insert into the sports_arm_wrestling_registration table
    $sqlInsertSports = mysqli_prepare($connectNow, "INSERT INTO sports_arm_wrestling_registration (payment_id, fullname, email, age, weight, dhand, experience, injury, mobile, amount, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    // Bind parameters according to the table structure
    mysqli_stmt_bind_param($sqlInsertSports, "sssisssssi", $paymentId, $fullName, $email, $age, $weight, $dominantHand, $experienceLevel, $injuryHistory, $phoneNumber, $amount);
    
    $resultSports = mysqli_stmt_execute($sqlInsertSports);

    if (!$resultSports) {
        throw new Exception("Error inserting sports registration: " . mysqli_stmt_error($sqlInsertSports));
    }

    // Commit the transaction if the query was successful
    mysqli_commit($connectNow);

    echo json_encode([
        "status" => "success",
        "message" => "Registration data inserted successfully",
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
mysqli_stmt_close($sqlInsertSports);
mysqli_close($connectNow);
?>
