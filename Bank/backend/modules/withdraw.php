<?php
// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include the database connection file
include '../config/connection.php';

// Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check if necessary data is present
if (isset($input['user-id']) && isset($input['balance'])) {
    $user_id = $input['user-id']; // Correct key from JSON
    $balance = $input['balance'];

    // Prepare the SQL query
    $sql = "UPDATE user SET balance = ? WHERE `user-id` = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("di", $balance, $user_id);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Balance updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update balance."]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare the query."]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid input."]);
}


    // Close the database connection
    $conn->close();
    ?>

