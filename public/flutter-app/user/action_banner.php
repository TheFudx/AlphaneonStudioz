<?php
// Database connection
include 'database.php';

// Ensure $connectNow is defined in database.php
if (!isset($connectNow)) {
    die(json_encode(["status" => "error", "message" => "Database connection not established."]));
}

// Category ID and Type ID to check for
$categoryId = 13;
$typeId = 6;

// Fetch videos from the database for the specified category ID and type ID
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
            FIND_IN_SET('$categoryId', v.category_id) 
            AND v.type_id = '$typeId'"; // Ensure you're using the correct column name for type

$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Check if any videos are found
    if (mysqli_num_rows($result) > 0) {
        // Output data of each row
        $videos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Convert video duration from milliseconds to hours and minutes
            $videoDurationMilliseconds = $row["video_duration"];
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

            $videos[] = [
                "name" => $row["video_name"],
                "thumbnail_url" => $row["thumbnail_url"],
                "release_date" => $row["release_date"],
                "description" => $row["description"],
                "video_duration" => $durationString,
                "language_name" => $row["language_name"],
                "cast_id" => $row["cast_id"],
                "cast_names" => $row["cast_names"], // Use the result of the subquery
                "cast_image_urls" => $row["cast_image_urls"],
                "video_1080_url" => $row["video_1080_url"],
                "landscape_url" => $row["landscape_url"],
                "category_id" => $row["category_id"],
                "category_names" => $row["category_names"], // Fetch category names
                "category_urls" => $row["category_urls"],
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
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
