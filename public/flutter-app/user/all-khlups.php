<?php
header('Content-Type: application/json; charset=utf-8');
// ini_set('display_errors', 0);
// error_reporting(E_ALL);

include 'database.php';

// Create connection
$mysqli = new mysqli($servername, $username, $password, $database);
if ($mysqli->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}
$mysqli->set_charset("utf8");

// Set tokengen as null (or use GET if needed)
$tokengen = null;

// Get selected video
$selectedVideoQuery = $tokengen
    ? "SELECT k.*, u.name as user_name, u.email as user_email FROM kluphs k LEFT JOIN users u ON k.user_id = u.id WHERE k.id = ?"
    : "SELECT k.*, u.name as user_name, u.email as user_email FROM kluphs k LEFT JOIN users u ON k.user_id = u.id WHERE k.type_id = 2 ORDER BY k.id DESC LIMIT 1";

if ($tokengen) {
    $stmt = $mysqli->prepare($selectedVideoQuery);
    $stmt->bind_param("i", $tokengen);
} else {
    $stmt = $mysqli->prepare($selectedVideoQuery);
}
$stmt->execute();
$selectedResult = $stmt->get_result();
$selectedVideo = $selectedResult->fetch_assoc();

if (!$selectedVideo) {
    echo json_encode(["status" => "error", "message" => "Video not found."]);
    exit;
}

$selectedId = $selectedVideo['id'];

// Fetch other videos
$otherQuery = "SELECT k.*, u.name as user_name, u.email as user_email FROM kluphs k LEFT JOIN users u ON k.user_id = u.id WHERE k.type_id = 2 AND k.id != ? ORDER BY k.id DESC";
$stmt = $mysqli->prepare($otherQuery);
$stmt->bind_param("i", $selectedId);
$stmt->execute();
$otherResult = $stmt->get_result();

$videos = [];
$videos[] = $selectedVideo;
while ($row = $otherResult->fetch_assoc()) {
    $videos[] = $row;
}

// Final JSON response
echo json_encode($videos);
?>
