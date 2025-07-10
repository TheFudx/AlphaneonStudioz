<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'];
    $name = $input['name'] ?? '';

    // Check if the user already exists
    $query = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // User already exists, you can choose to update any additional data here
        $user = $result->fetch_assoc();
        $userId = $user['id'];
    } else {
        // If the user doesn't exist, insert the user data into the database
        $insertQuery = $conn->prepare("INSERT INTO users (email, name) VALUES (?, ?)");
        $insertQuery->bind_param("ss", $email, $name);
        $insertQuery->execute();
        $userId = $insertQuery->insert_id;
    }

    // Respond with user data (email and user ID)
    echo json_encode([
        'id' => $userId,
        'email' => $email
    ]);
} else {
    // Respond with error for method not allowed
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
