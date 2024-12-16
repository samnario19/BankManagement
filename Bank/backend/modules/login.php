<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/connection.php'; // Include your database connection

$data = json_decode(file_get_contents("php://input")); // Get the JSON data

$email = $data->email;
$password = $data->password;

// Prepare and bind
$stmt = $conn->prepare("SELECT `user-password`, `user-id` FROM user WHERE `user-email` = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashed_password, $user_id);
    $stmt->fetch();

    // Verify the password
    if (password_verify($password, $hashed_password)) {
        $response = ["message" => "Login successful", "userId" => $user_id];
        error_log(json_encode($response)); // Log the response for debugging
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