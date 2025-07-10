<?php
require 'database.php';

error_log("Incoming data: " . print_r($input, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'];
    $name = $input['name'] ?? '';
    $phone = $input['phone'] ?? null;  // Use null if not provided
    $profile = $input['profile'] ?? null;  // Use null if not provided

    // Check if the user already exists
    $query = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userId = $user['id'];

        // Update existing user with new data (optional fields are handled)
        $updateQuery = $conn->prepare("UPDATE users SET name = ?, phone = COALESCE(?, phone), profile = COALESCE(?, profile) WHERE id = ?");
        $updateQuery->bind_param("sssi", $name, $phone, $profile, $userId);
        $updateQuery->execute();

        // Respond with user data
        echo json_encode([
            'userId' => $userId,
            'email' => $email,
            'name' => $name,
            'phone' => $phone,
            'profile' => $profile,
        ]);
    } else {
        // If the user doesn't exist, insert the user data into the database
        $insertQuery = $conn->prepare("INSERT INTO users (email, name, phone, profile) VALUES (?, ?, ?, ?)");
        $insertQuery->bind_param("ssss", $email, $name, $phone, $profile);
        $insertQuery->execute();
        $userId = $insertQuery->insert_id;

        // Respond with user data
        echo json_encode([
            'userId' => $userId,
            'email' => $email,
            'name' => $name,
            'phone' => $phone,
            'profile' => $profile,
        ]);
    }
} else {
    // Respond with error for method not allowed
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
