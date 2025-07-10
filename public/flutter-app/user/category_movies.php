<?php
// Database connection
include 'database.php';

// Check if category_id is set
if (!isset($_GET['category_id'])) {
    echo json_encode(["status" => "error", "message" => "Category ID not provided"]);
    exit();
}

$category_ids = explode(',', $_GET['category_id']); // Split the category IDs string into an array

// Construct the WHERE clause for the SQL query
$category_conditions = [];
foreach ($category_ids as $category_id) {
    $category_conditions[] = "category_id = $category_id";
}

// Join the conditions with OR for multiple category IDs
$where_clause = implode(' OR ', $category_conditions);

// Fetch movies for the specified categories with language and category names
$sql = "SELECT v.category_id, 
    v.name, 
    v.thumbnail_url, 
    v.release_date, 
    v.description,
    v.video_duration, 
    v.language_id, 
    v.video_mp4, 
    v.video_480_url, 
    v.video_720_url, 
    v.video_1080_url,v.landscape_url, l.name AS language_name, c.name AS category_name 
        FROM video v
        LEFT JOIN language l ON v.language_id = l.id
        LEFT JOIN category c ON v.category_id = c.id
        WHERE $where_clause 
        ORDER BY v.id DESC";

$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    // Check if any data is found
    if (mysqli_num_rows($result) > 0) {
        // Output data of each row
        $data = array();
        while ($row = mysqli_fetch_assoc($result)) {
            // Convert video duration from milliseconds to hours and minutes
            $videoDurationMilliseconds = (float) $row["video_duration"];
            $seconds =  (int)  floor($videoDurationMilliseconds / 1000);
            $hours = (int) floor($seconds / 3600);
            $minutes = intval(floor($seconds / 60));

            // Format the duration string as "Xh Ymins"
            $durationString = "";
            if ($hours > 0) {
                $durationString .= $hours . "h ";
            }
            if ($minutes > 0) {
                $durationString .= $minutes . "min";
            }

            // Format the release date to display only the year (YYYY)
            $releaseDate = date("Y", strtotime($row["release_date"]));

            $data[] = array(
                "category_id" => $row["category_id"],
                "name" => $row["name"],
                "thumbnail_url" => $row["thumbnail_url"],
                "release_date" => $releaseDate, // Use formatted release date
                "description" => $row["description"],
                "video_duration" => $durationString, // Use formatted video duration
                "language_id" => $row["language_id"],
                "language_name" => $row["language_name"],
                "video_mp4" => $row["video_1080_url"],
                "video_480_url" => $row["video_480_url"],
                "video_720_url" => $row["video_720_url"],
                "video_1080_url" => $row["video_1080_url"],
                "landscape_url" => $row["landscape_url"],
                "category_name" => $row["category_name"]
            );
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
