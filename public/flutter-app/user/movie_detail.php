<?php
include 'database.php';


// Fetch movie details from the database
$sql = "SELECT id, cast_id, name, description, video_1080_url, release_date FROM video WHERE id = ?"; // Assuming 'video' is the table name
$videoId = $_GET['id']; // Assuming the video ID is passed as a parameter
$stmt = mysqli_prepare($connectNow, $sql);
mysqli_stmt_bind_param($stmt, "i", $videoId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if the query was executed successfully
if ($result) {
    // Check if any video details are found
    if (mysqli_num_rows($result) > 0) {
        // Fetch video details
        $videoDetails = mysqli_fetch_assoc($result);
        // Output video details in JSON format
        echo json_encode($videoDetails);
    } else {
        echo json_encode(["status" => "error", "message" => "No video found with the specified ID"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
