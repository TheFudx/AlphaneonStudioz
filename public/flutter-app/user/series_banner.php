<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'database.php';

if (!isset($connectNow)) {
    die(json_encode(["status" => "error", "message" => "Database connection not established."]));
}

$sql = "
SELECT 
    s.id AS series_id,
    s.name AS series_name,
    s.image_url AS series_image_url,
    s.landscape_image_url AS series_main_image_url,
    s.type AS series_type, 
    s.language AS series_language,
    s.description AS series_description,
    COALESCE(se.id, 0) AS season_id,
    se.season AS season,
    e.name AS episode_name,
    e.landscape_url AS episode_thumbnail_url,
    e.description AS episode_description,
    e.release_date AS episode_release_date,
    e.episode_mp4 AS episode_mp4,
    e.episode_duration AS episode_duration,
    e.episode_no AS episode_number,
    e.cast_id,
    (
        SELECT GROUP_CONCAT(c.name SEPARATOR ', ')
        FROM cast c 
        WHERE FIND_IN_SET(c.id, e.cast_id)
    ) AS cast_names,
    (
        SELECT GROUP_CONCAT(c.image_url SEPARATOR ', ')
        FROM cast c 
        WHERE FIND_IN_SET(c.id, e.cast_id)
    ) AS cast_image_urls
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

if (!$result) {
    die(json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]));
}

$seriesData = [];

while ($row = mysqli_fetch_assoc($result)) {
    $series_id = $row["series_id"];
    $season_id = (int) $row["season_id"];

    if (!isset($seriesData[$series_id])) {
        $seriesData[$series_id] = [
            "series_id" => $row["series_id"],
            "series_name" => $row["series_name"],
            "series_image_url" => $row["series_image_url"],
            "series_main_image_url" => $row["series_main_image_url"],
            "series_type" => $row["series_type"],
            "series_language" => $row["series_language"],
            "series_description" => $row["series_description"],
            "seasons" => []
        ];
    }

    if ($season_id > 0 && !isset($seriesData[$series_id]["seasons"][$season_id])) {
        $seriesData[$series_id]["seasons"][$season_id] = [
            "season_id" => $season_id,
            "season" => $row["season"],
            "episodes" => []
        ];
    }

    // Debug: Check Episode Data Before Adding
    if (!empty($row["episode_name"]) && isset($seriesData[$series_id]["seasons"][$season_id])) {
        // echo "Adding Episode: " . $row["episode_name"] . " to Season ID: " . $season_id . "<br>";
        flush();

       $seriesData[$series_id]["seasons"][$season_id]["episodes"][] = [
        "episode_name" => $row["episode_name"] ?? "N/A",
        "episode_thumbnail_url" => $row["episode_thumbnail_url"] ?? "",
        "episode_description" => $row["episode_description"] ?? "",
        "episode_release_date" => $row["episode_release_date"] ?? "",
        "episode_mp4" => $row["episode_mp4"] ?? "",
        "episode_duration" => $row["episode_duration"] ?? "",
        "episode_number" => $row["episode_number"] ?? 0,
        "cast_id" => $row["cast_id"] ?? "",
        "cast_names" => $row["cast_names"] ?? "",
        "cast_image_urls" => $row["cast_image_urls"] ?? ""
    ];

    }
}

function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
    }
    return $mixed;
}


$utf8SeriesData = utf8ize($seriesData);
$jsonResponse = json_encode(array_values($utf8SeriesData), JSON_PRETTY_PRINT);

if ($jsonResponse === false) {
    die("JSON Encoding Error: " . json_last_error_msg());
}
// Output JSON
echo $jsonResponse;
mysqli_close($connectNow);
?>
