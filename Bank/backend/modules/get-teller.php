<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/connection.php'; // Include your database connection

// Check if teller-id parameter exists
if (!isset($_GET['tellerid'])) {
    echo json_encode(['error' => 'Missing tellerid parameter']);
    exit;
}

$tellerid = $_GET['tellerid'];

// Prepare and bind using prepared statements
$query = "SELECT `teller-id`, `teller-name`, `teller-email` FROM `teller` WHERE `teller-id` = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $tellerid);
$stmt->execute();
$result = $stmt->get_result();

// Check if teller is found
if ($result && $result->num_rows > 0) {
    $teller = $result->fetch_assoc();

    // Prepare the teller data to return
    $tellerData = [
        "tellerID" => $teller["teller-id"],
        "name" => $teller["teller-name"],
        "email" => $teller["teller-email"],
    ];

    // Return the teller data as JSON
    echo json_encode($tellerData);
} else {
    // If no teller found
    echo json_encode(["error" => "Teller not found"]);
}

$stmt->close();
$conn->close();
?>
