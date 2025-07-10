<?php
// Database connection
include 'database.php';

// Ensure $connectNow is defined in database.php
if (!isset($connectNow)) {
    die(json_encode(["status" => "error", "message" => "Database connection not established."]));
}

// Fetch series, seasons, and episodes data
$sql = "
    SELECT 
        s.id AS series_id,
        s.name AS series_name,
        s.image_url AS series_image_url,
        s.type AS series_type, 
        s.language AS series_language,
        s.description AS series_description,
        se.id AS season_id,
        se.season AS season,
        e.name AS episode_name,
        e.thumbnail_url AS episode_thumbnail_url,
        e.description AS episode_description,
        e.release_date AS episode_release_date,
        e.episode_mp4 AS episode_mp4,
        e.episode_duration AS episode_duration,
        e.episode_no AS episode_number
    FROM 
        series s
    LEFT JOIN 
        season se ON s.id = se.series_id
    LEFT JOIN 
        episodes e ON se.id = e.season_id
    ORDER BY 
        s.name, se.season, e.episode_no
";

$result = mysqli_query($connectNow, $sql);

// Check if the query was executed successfully
if ($result) {
    $seriesData = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $series_id = $row["series_id"];
        $season_id = $row["season_id"];

        // Initialize series entry if not exists
        if (!isset($seriesData[$series_id])) {
            $seriesData[$series_id] = [
                "series_id" => $row["series_id"],
                "series_name" => $row["series_name"],
                "series_image_url" => $row["series_image_url"],
                "series_type" => $row["series_type"],
                "series_language" => $row["series_language"],
                "series_description" => $row["series_description"],
                "seasons" => []
            ];
        }

        // Initialize season entry if not exists
        if ($season_id !== null && !isset($seriesData[$series_id]["seasons"][$season_id])) {
            $seriesData[$series_id]["seasons"][$season_id] = [
                "season_id" => $season_id,
                "season" => $row["season"],
                "episodes" => []
            ];
        }

        // Add episode if present
        if ($row["episode_name"] !== null) {
            $seriesData[$series_id]["seasons"][$season_id]["episodes"][] = [
                "episode_name" => $row["episode_name"],
                "episode_thumbnail_url" => $row["episode_thumbnail_url"],
                "episode_description" => $row["episode_description"],
                "episode_release_date" => $row["episode_release_date"],
                "episode_mp4" => $row["episode_mp4"],
                "episode_duration" => $row["episode_duration"],
                "episode_number" => $row["episode_number"]
            ];
        }
    }

    // Convert seriesData array to JSON format
    echo json_encode(array_values($seriesData));
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}

// Close connection
mysqli_close($connectNow);
?>
