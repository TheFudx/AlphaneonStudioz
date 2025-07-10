<?php
header("Content-Type: application/json");

include 'database.php';

$id = isset($_POST['id']) ? $_POST['id'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$mobile_no = isset($_POST['mobile_no']) ? $_POST['mobile_no'] : null;
$profile_picture = isset($_FILES['profile_picture']) ? $_FILES['profile_picture'] : null;

// Basic validation
if (empty($id)) {
    echo json_encode([
        "status" => "error",
        "message" => "ID is required",
    ]);
    exit();
}

// Handle file upload if present
$profile_picture_url = null;
if ($profile_picture) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_picture["name"]);
    if (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
        $profile_picture_url = "https://example.com/" . $target_file;
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to upload profile picture",
        ]);
        exit();
    }
}

// Prepare dynamic SQL query
$fields = [];
$params = [];
$types = "";

if (!empty($name)) {
    $fields[] = "name = ?";
    $params[] = $name;
    $types .= "s";
}
if (!empty($email)) {
    $fields[] = "email = ?";
    $params[] = $email;
    $types .= "s";
}
if (!empty($mobile_no)) {
    $fields[] = "mobile_no = ?";
    $params[] = $mobile_no;
    $types .= "s";
}
if (!empty($profile_picture_url)) {
    $fields[] = "profile_picture = ?";
    $params[] = $profile_picture_url;
    $types .= "s";
}

// Check if there's at least one field to update
if (count($fields) === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "No fields to update",
    ]);
    exit();
}

// Construct the SQL query
$sqlQuery = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";

$params[] = $id;
$types .= "s";

$stmt = mysqli_prepare($connectNow, $sqlQuery);
mysqli_stmt_bind_param($stmt, $types, ...$params);
$result = mysqli_stmt_execute($stmt);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => "Error executing query: " . mysqli_stmt_error($stmt),
    ]);
    exit();
}

echo json_encode([
    "status" => "success",
    "message" => "User details updated successfully",
    "profile_picture" => $profile_picture_url,
]);

mysqli_close($connectNow);
?>
