<?php
// Database connection
include 'database.php';

$sql =  "SELECT device_name, device_version, `release` FROM app_version";

$result = mysqli_query($connectNow, $sql);
$data = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $device  = $row['device_name'];
            $releaseValue = isset($row['release']) ? $row['release'] : 0;

            $data[$device] = [
                'version_name' => $row['device_version'],
                'released' => $releaseValue == 1,
            ];
        }
        echo json_encode($data);
    } else {
        echo json_encode(["message" => "No records found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Query execution failed: " . mysqli_error($connectNow)]);
}
mysqli_close($connectNow);
