<?php
// Database connection
$servername = "69.16.233.70";
$username = "alphastudioz_ott";
$password = "Pass@66466";
$database = "alphastudioz_ott";

// Create connection
$connectNow = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$connectNow) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to connect to database: " . mysqli_connect_error(),
    ]);
    exit();
}

// Fetch categories from the database
$sql = "SELECT id, name FROM category";
$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Check if any categories are found
    if (mysqli_num_rows($result) > 0) {
        // Output data of each row
        $categories = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row["name"];
        }
        
        // Convert categories array to JSON format
        echo json_encode($categories);
    } else {
        echo json_encode(["status" => "error", "message" => "No categories found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
