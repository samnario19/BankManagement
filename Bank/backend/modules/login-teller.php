<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/connection.php'; // Include your database connection

// Get the JSON data from the request
$data = json_decode(file_get_contents("php://input"));

// Log the incoming request data
error_log("Received data: " . json_encode($data)); // This will help us see the incoming request in the PHP error log

// Ensure data is not null
if ($data === null) {
    echo json_encode(["message" => "Invalid JSON format"]);
    exit;
}

// Extract email and password from the JSON data
$email = $data->{"teller-email"};
$password = $data->{"teller-password"};

// Prepare and binds
$stmt = $conn->prepare("SELECT `teller-password`, `teller-id` FROM teller WHERE `teller-email` = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashed_password, $user_id);
    $stmt->fetch();

    // Verify the password
    if (password_verify($password, $hashed_password)) {
        $response = ["message" => "Login successful", "userId" => $user_id];
        echo json_encode($response);
    } else {
        echo json_encode(["message" => "Invalid password"]);
    }
} else {
    echo json_encode(["message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
