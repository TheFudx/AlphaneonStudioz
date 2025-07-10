<?php
include 'database.php';


// Type ID to filter
$typeId = 6; // Type ID to filter

// Fetch videos from the database for the specified type ID
$sql = "SELECT 
v.video_mp4, 
v.landscape_url, 
v.release_date, 
GROUP_CONCAT(c.name SEPARATOR ', ') AS category_names
FROM 
video v
LEFT JOIN 
category c ON FIND_IN_SET(c.id, v.category_id)
WHERE 
v.type_id = '$typeId'
GROUP BY
v.video_mp4, v.landscape_url, v.release_date
ORDER BY
v.created_at DESC
LIMIT 4";
$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Fetch video URLs and image URLs
    $videoData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $videoData[] = $row;
    }
    // Output video data in JSON format
    echo json_encode($videoData);
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
