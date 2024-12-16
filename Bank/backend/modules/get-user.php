<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/connection.php'; // Include your database connection

// Check if userid parameter exists
if (!isset($_GET['userid'])) {
    echo json_encode(['error' => 'Missing userid parameter']);
    exit;
}

$userid = $_GET['userid'];

// Prepare and bind using prepared statements
$query = "SELECT `user-id`, `user-name`, `user-email`, `user-role`, `balance`, `accountNum`, `history` FROM `user` WHERE `user-id` = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

// Check if user is found
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Prepare the user data to return
    $userData = [
        "userID" => $user["user-id"],
        "name" => $user["user-name"],
        "email" => $user["user-email"],
        "role" => $user["user-role"],
        "balance" => (float)$user["balance"], // Ensuring balance is a float
        "accountNum" => $user["accountNum"],
        "history" => $user["history"]
    ];

    // Return the user data as JSON
    echo json_encode($userData);
} else {
    // If no user found
    echo json_encode(["error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
