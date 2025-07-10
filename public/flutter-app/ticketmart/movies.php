<?php
// Database connection
include 'db.php';

// Fetch images from the database
$sql = "SELECT 
    m.image_path
FROM 
    movies m";

$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Fetch image URLs
    $imageData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $imageData[] = $row["image_path"];
    }
    // Output image data in JSON format
    echo json_encode(['images' => $imageData]);
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
