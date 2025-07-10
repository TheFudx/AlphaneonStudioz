<?php
// Database connection
include 'database.php';

// Type ID to filter
$typeId = 6; // Type ID to filter

// Fetch videos from the database for the specified type ID
$sql = "SELECT 
    v.video_1080_url, v.video_480_url, v.video_720_url, v.video_1080_url,
    v.landscape_url, 
    DATE_FORMAT(v.release_date, '%Y') AS release_date, 
    v.name,
    v.description,
    v.video_duration,
    GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') AS category_names,
    GROUP_CONCAT(DISTINCT cs.name ORDER BY cs.name SEPARATOR ', ') AS cast_names,
    GROUP_CONCAT(DISTINCT cs.image_url ORDER BY cs.image_url SEPARATOR ', ') AS cast_image_urls,
    l.name AS language_name
FROM 
    video v
LEFT JOIN 
    category c ON FIND_IN_SET(c.id, v.category_id)
LEFT JOIN
    cast cs ON FIND_IN_SET(cs.id, v.cast_id)
LEFT JOIN 
    language l ON l.id = v.language_id
WHERE 
    v.type_id = $typeId
GROUP BY
    v.video_1080_url, v.video_480_url, v.video_720_url, v.video_1080_url, v.landscape_url, release_date, v.name, v.video_duration, v.description, l.name
ORDER BY
    v.created_at DESC
    LIMIT 4";

$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Fetch video URLs and image URLs
    $videoData = [];
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
            $durationString .= $minutes . "mins";
        }

        $row["video_duration"] = $durationString;
        $videoData[] = $row;
    }

    // Convert video data to JSON format
    $jsonVideoData = json_encode($videoData);

    // Manually replace escaped slashes to ensure proper URL handling
    $jsonVideoData = str_replace('\/', '/', $jsonVideoData);

    // Output the properly formatted JSON
    echo $jsonVideoData;
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
