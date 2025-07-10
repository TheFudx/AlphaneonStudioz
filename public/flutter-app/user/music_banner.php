<?php

// Enable error reporting to catch all errors and warnings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'database.php';

// Ensure $connectNow is defined in database.php
if (!isset($connectNow)) {
    die(json_encode(["status" => "error", "message" => "Database connection not established."]));
}

// Debugging: Check if the connection was successful
if (mysqli_connect_errno()) {
    die(json_encode(["status" => "error", "message" => "Failed to connect to database: " . mysqli_connect_error()]));
}

// Category ID to check for
$categoryId = 20;

// Debugging: Check the category ID
echo "Category ID: $categoryId<br>";

// Fetch videos from the database for the specified category ID
$sql = "SELECT 
            v.name as video_name, 
            v.thumbnail_url, 
            v.release_date, 
            v.description, 
            v.video_duration, 
            v.video_1080_url, 
            v.landscape_url, 
            v.category_id, 
            l.name as language_name, 
            v.cast_id,
            (SELECT GROUP_CONCAT(c.name SEPARATOR ', ') FROM cast c WHERE FIND_IN_SET(c.id, v.cast_id)) as cast_names,
            (SELECT GROUP_CONCAT(c.image_url SEPARATOR ', ') FROM cast c WHERE FIND_IN_SET(c.id, v.cast_id)) as cast_image_urls,
            (SELECT GROUP_CONCAT(cat.name SEPARATOR ', ') FROM category cat WHERE FIND_IN_SET(cat.id, v.category_id)) as category_names,
            (SELECT GROUP_CONCAT(v.thumbnail_url SEPARATOR ', ') FROM video v WHERE FIND_IN_SET(v.id, v.category_id)) as category_urls
        FROM 
            video v
        INNER JOIN 
            language l ON v.language_id = l.id
        WHERE 
            FIND_IN_SET('$categoryId', v.category_id)";

$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Check if any videos are found
    if (mysqli_num_rows($result) > 0) {
        // Output data of each row
        $videos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Convert video duration from milliseconds to hours and minutes
            $videoDurationMilliseconds = (int)$row["video_duration"]; // Explicit cast to ensure proper type handling
            $seconds = floor($videoDurationMilliseconds / 1000);
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds / 60) % 60);

            // Format the duration string as "Xh Ymins"
            $durationString = "";
            if ($hours > 0) {
                $durationString .= $hours . "h ";
            }
            if ($minutes > 0) {
                $durationString .= $minutes . "min";
            }

            // Add video data to the array
            $videos[] = [
                "name" => $row["video_name"],
                "thumbnail_url" => $row["thumbnail_url"],
                "release_date" => $row["release_date"],
                "description" => $row["description"],
                "video_duration" => $durationString,
                "language_name" => $row["language_name"],
                "cast_id" => $row["cast_id"],
                "cast_names" => $row["cast_names"] ?: 'N/A', // Ensure proper handling of NULL or empty values
                "cast_image_urls" => $row["cast_image_urls"] ?: 'N/A',
                "video_1080_url" => $row["video_1080_url"],
                "landscape_url" => $row["landscape_url"],
                "category_id" => $row["category_id"],
                "category_names" => $row["category_names"] ?: 'N/A', // Fetch category names or show 'N/A'
                "category_urls" => $row["category_urls"] ?: 'N/A',
            ];
        }

        // Convert videos array to JSON format
        $jsonResponse = json_encode($videos);
        
        // Replace escaped slashes with regular slashes
        $jsonResponse = str_replace('\/', '/', $jsonResponse);
        
        // Output the properly formatted JSON
        echo $jsonResponse;
    } else {
        echo json_encode(["status" => "error", "message" => "No videos found for the specified category"]);
    }
} else {
    // Output SQL error for debugging
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close the database connection
mysqli_close($connectNow);
?>
