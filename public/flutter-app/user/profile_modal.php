<?php
include 'database.php';


// Fetch all data (name and URL) from the database
$sql = "SELECT id, name, image_url FROM category ORDER BY id DESC";
$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Check if any data is found
    if (mysqli_num_rows($result) > 0) {
        // Output data of each row
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = array("id" => $row["id"], "name" => $row["name"], "url" => $row["image_url"]);
        }
        // Convert data array to JSON format
        echo json_encode($data);
    } else {
        echo json_encode(["status" => "error", "message" => "No data found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
