<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/connection.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);



// Debugging: Log the received data
file_put_contents('debug.log', print_r($data, true));

if (isset($data['email'], $data['password'], $data['userName'])) {
    // Get data from the decoded JSON
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the password
    $userName = $data['userName'];

    // Prepare and bind for the users table with default values
    $stmt = $conn->prepare("INSERT INTO user (`user-name`, `user-email`, `user-password`, `user-role`) VALUES (?, ?, ?, ?)");
    
    $userRole= 'customer';

    $stmt->bind_param("ssss", $userName, $email, $password, $userRole);

    // Execute the statement
    if ($stmt->execute()) {
        // Get the last inserted ID
        $user_id = $conn->insert_id;

        echo "Registration successful! User ID: " . $user_id;
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: Missing required fields.";
}

$conn->close();