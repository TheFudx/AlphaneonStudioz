<?php
include 'database.php';

$categoryId = 16; // Comedy
$typeId     = 12; // Comedy Show

$sql = "SELECT 
            v.id,
            v.name,
            v.season_no,
            v.episode_no,
            v.thumbnail_url,
            v.release_date,
            v.description,
            v.video_duration,
            v.video_480_url,
            v.video_720_url,
            v.video_1080_url,
            v.landscape_url,
            l.name as language_name,

            (SELECT GROUP_CONCAT(c.name SEPARATOR ', ')
             FROM cast c  
             WHERE FIND_IN_SET(c.id, v.cast_id)
            ) as cast_names

        FROM video v
        INNER JOIN language l ON v.language_id = l.id
        WHERE FIND_IN_SET('$categoryId', v.category_id)
        AND v.type_id = '$typeId'
        ORDER BY v.season_no ASC, v.episode_no ASC";

$result = mysqli_query($connectNow, $sql);

$seasonData = [];

if ($result && mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        $seasonNo = $row['season_no'];

        // Format Duration
        $videoDurationSeconds = (float) $row["video_duration"];
        $seconds = (int) floor($videoDurationSeconds / 1000);
        $hours = (int) floor($seconds / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);

        $durationString = "";
        if ($hours > 0) {
            $durationString .= $hours . "h ";
        }
        if ($minutes > 0) {
            $durationString .= $minutes . "min";
        }

        $episodeData = [
            "id" => $row["id"],
            "name" => $row["name"],
            "episode_no" => $row["episode_no"],
            "thumbnail_url" => $row["thumbnail_url"],
            "release_date" => $row["release_date"],
            "description" => $row["description"],
            "video_duration" => trim($durationString),
            "language_name" => $row["language_name"],
            "cast_names" => $row["cast_names"],
            "video_480_url" => $row["video_480_url"],
            "video_720_url" => $row["video_720_url"],
            "video_1080_url" => $row["video_1080_url"],
            "landscape_url" => $row["landscape_url"]
        ];

        // ✅ Group by season
        if (!isset($seasonData[$seasonNo])) {
            $seasonData[$seasonNo] = [
                "season_no" => $seasonNo,
                "episodes" => []
            ];
        }

        $seasonData[$seasonNo]["episodes"][] = $episodeData;
    }

    // Reset array keys
    $seasonData = array_values($seasonData);

    echo json_encode([
        "status" => "success",
        "data" => $seasonData
    ], JSON_UNESCAPED_SLASHES);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "No comedy show data found"
    ]);
}

mysqli_close($connectNow);
?>