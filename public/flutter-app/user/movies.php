<?php
// Database connection details
include 'database.php';

// Fetch banners from the database based on category IDs
$sql = "SELECT 
v.name AS video_name, 
v.thumbnail_url, 
v.release_date, 
v.description, 
v.video_duration, 
v.video_1080_url, 
v.landscape_url, 
v.category_id, 
l.name AS language_name, 
v.cast_id,
(SELECT GROUP_CONCAT(c.name SEPARATOR ', ') 
 FROM cast c 
 WHERE FIND_IN_SET(c.id, v.cast_id)) AS cast_names,
(SELECT GROUP_CONCAT(c.image_url SEPARATOR ', ') 
 FROM cast c 
 WHERE FIND_IN_SET(c.id, v.cast_id)) AS cast_image_urls,
cat.name AS category_name,
cat.image_url AS category_image_url
FROM 
video v
INNER JOIN 
language l ON v.language_id = l.id
LEFT JOIN 
category cat ON v.category_id = cat.id
WHERE 
v.type_id = 6
GROUP BY 
v.name, 
v.thumbnail_url, 
v.release_date, 
v.description, 
v.video_duration, 
v.video_1080_url, 
v.landscape_url, 
v.category_id, 
l.name, 
v.cast_id, 
cat.name, 
cat.image_url";

$result = mysqli_query($connectNow, $sql);

// Initialize an array for banners
$banners = array();

// Check if the query was executed successfully
if ($result) {
    // Check if any videos are found
    if (mysqli_num_rows($result) > 0) {
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

            $categoryIds = explode(',', $row["category_id"]);
            foreach ($categoryIds as $categoryId) {
                $categoryId = trim($categoryId);
                // Fetch category name from the category table based on category ID
                $categoryName = getCategoryNameFromID($categoryId);
                // Only include categories with non-empty thumbnail URLs
                if (!empty($row["thumbnail_url"])) {
                    if (!isset($banners[$categoryName])) {
                        $banners[$categoryName] = array();
                    }
                    $banners[$categoryName][] = [
                        "thumbnail_url" => $row["thumbnail_url"],
                        "video_name" => $row["video_name"],
                        "release_date" => $row["release_date"],
                        "description" => $row["description"],
                        "video_duration" => $row["video_duration"],
                        "language" => $row["language_name"], // Correct key for language
                        "cast_image_urls" => $row["cast_image_urls"],
                        "cast_names" => $row["cast_names"],
                        "landscape_url" => $row["landscape_url"],
                        "video_url" => $row["video_1080_url"], // Correct key for video URL
                        "category_name" => $categoryName,
                        "category_image_url" => $row["category_image_url"],
                    ];
                }
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No videos found for the specified categories"]);
        exit();
    }
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
    exit();
}

// Convert banners array to JSON format
echo json_encode($banners);

// Close connection
mysqli_close($connectNow);

// Function to fetch category name based on category ID
function getCategoryNameFromID($categoryId) {
    global $connectNow;
    // Fetch category name from the category table based on category ID
    $sql = "SELECT name FROM category WHERE id = '$categoryId'";
    $result = mysqli_query($connectNow, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['name'];
    } else {
        return ''; // Return empty string if category name not found
    }
}
?>
