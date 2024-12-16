<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/connection.php'; // Include your database connection

// Check if accountNum parameter exists
if (!isset($_GET['accountNum'])) {
    echo json_encode(['error' => 'Missing accountNum parameter']);
    exit;
}

$accountNum = $_GET['accountNum'];

// Prepare and bind using prepared statements
$query = "SELECT `user-id`, `user-name`, `user-email`, `user-role`, `balance`, `accountNum`, `history` FROM `user` WHERE `accountNum` = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $accountNum); // Assuming accountNum is a string
$stmt->execute();
$result = $stmt->get_result();

// Check if account is found
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Prepare the account data to return
    $accountData = [
        "userID" => $user["user-id"],
        "name" => $user["user-name"],
        "email" => $user["user-email"],
        "balance" => (float)$user["balance"], // Ensuring balance is a float
        "accountNum" => $user["accountNum"],
    ];

    // Return the account data as JSON
    echo json_encode($accountData);
} else {
    // If no account found
    echo json_encode(["error" => "Account not found"]);
}

$stmt->close();
$conn->close();
?>
