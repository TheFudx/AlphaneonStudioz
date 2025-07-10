<?php
// Database connection
include 'database.php';



// Fetch series from the database for the specified type ID
$sql = "SELECT 
    e.episode_mp4,
    e.landscape_url, 
    DATE_FORMAT(e.release_date, '%Y') AS release_date, 
    e.name,
    e.description,
    e.episode_duration,
    GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') AS category_names,
    GROUP_CONCAT(DISTINCT cs.name ORDER BY cs.name SEPARATOR ', ') AS cast_names,
    GROUP_CONCAT(DISTINCT cs.image_url ORDER BY cs.image_url SEPARATOR ', ') AS cast_image_urls,
    l.name AS language_name
FROM 
    episodes e
LEFT JOIN 
    category c ON FIND_IN_SET(c.id, e.category_id)
LEFT JOIN
    cast cs ON FIND_IN_SET(cs.id, e.cast_id)
LEFT JOIN 
    language l ON l.id = e.language_id
GROUP BY
    e.episode_mp4, e.landscape_url, release_date, e.name, e.episode_duration, e.description, l.name";

$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Fetch series URLs and image URLs
    $seriesData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Convert series duration from milliseconds to hours and minutes
        $seriesDurationMilliseconds = $row["episode_duration"];
        $seconds = floor($seriesDurationMilliseconds / 1000);
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

        $row["episode_duration"] = $durationString;
        $seriesData[] = $row;
    }
    // Output series data in JSON format
    echo json_encode($seriesData);
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
