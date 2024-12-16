<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/connection.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Debugging: Log the received data
file_put_contents('debug.log', print_r($data, true));

if (isset($data['teller-name'], $data['teller-email'], $data['teller-password'])) {
    // Get data from the decoded JSON
    $email = $data['teller-email'];
    $password = password_hash($data['teller-password'], PASSWORD_DEFAULT); // Hash the password
    $userName = $data['teller-name'];

    // Prepare and bind for the teller table
    $stmt = $conn->prepare("INSERT INTO teller (`teller-name`, `teller-email`, `teller-password`) VALUES (?, ?, ?)");
    
    $stmt->bind_param("sss", $userName, $email, $password);

    // Execute the statement
    if ($stmt->execute()) {
        // Get the last inserted ID
        $teller_id = $conn->insert_id;

        echo json_encode(["message" => "Teller registration successful! Teller ID: " . $teller_id]);
    } else {
        echo json_encode(["message" => "Error executing statement: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["message" => "Error: Missing required fields."]);
}

$conn->close();
?>
