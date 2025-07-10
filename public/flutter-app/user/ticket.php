<?php
// Database connection
include 'database.php';

// Fetch images from the database
$sql = "SELECT 
    v.landscape_url
FROM 
    video v
ORDER BY
    v.created_at DESC
LIMIT 4"; // Adjust the limit as needed

$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Fetch image URLs
    $imageData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $imageData[] = $row['landscape_url'];
    }
    // Output image data in JSON format
    echo json_encode(['images' => $imageData]);
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
