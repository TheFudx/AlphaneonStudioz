<?php
// Database connection details
$servername = "69.16.233.70";
$username = "alphastudioz_copy";
$password = "w)%LNxy#05+X";
$database = "alphastudioz_copy";

// Create connection
$connectNow = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$connectNow) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to connect to database: " . mysqli_connect_error(),
    ]);
    exit();
}

// Fetch artists from the database
$sql = "SELECT id, name, image_small, image_large FROM artists";

$result = mysqli_query($connectNow, $sql);

// Initialize an array for artists
$artists = array();

// Check if the query was executed successfully
if ($result) {
    // Check if any artists are found
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $artists[] = [
                "id" => $row["id"],
                "name" => $row["name"],
                "image_small" => $row["image_small"],
                "image_large" => $row["image_large"],
            ];
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No artists found"]);
        exit();
    }
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
    exit();
}

// Convert artists array to JSON format
echo json_encode($artists);

// Close connection
mysqli_close($connectNow);
?>
