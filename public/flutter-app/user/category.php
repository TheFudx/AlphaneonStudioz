<?php
// Database connection
include 'database.php';


// Fetch categories from the database
$sql = "SELECT name FROM category WHERE id IN (11, 12, 27, 13, 14, 26,16,21)";
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
