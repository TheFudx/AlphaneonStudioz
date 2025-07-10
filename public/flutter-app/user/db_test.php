<?php

$servername = "69.16.233.70";
$username = "alphastudioz_copy";
$password = "w)%LNxy#05+X";
$database = "alphastudioz_copy";

// Create connection
$connectNow = mysqli_connect($servername, $username, $password, $database);

if (!$connectNow) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to connect to database: " . mysqli_connect_error(),
    ]);
    exit();
} else {
    echo json_encode([
        "status" => "success",
        "message" => "Successfully connected.",
    ]);
}

// Close the connection
mysqli_close($connectNow);
?>
