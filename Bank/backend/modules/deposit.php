<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include the database connection file
include '../config/connection.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['user-id']) && isset($input['depositAmount'])) {
    $user_id = $input['user-id'];
    $depositAmount = $input['depositAmount'];

    if ($depositAmount <= 0) {
        echo json_encode(["status" => "error", "message" => "Deposit amount must be greater than zero."]);
        exit;
    }

    $sqlSelect = "SELECT balance FROM user WHERE `user-id` = ?";
    $stmtSelect = $conn->prepare($sqlSelect);

    if ($stmtSelect) {
        $stmtSelect->bind_param("i", $user_id);
        $stmtSelect->execute();
        $result = $stmtSelect->get_result();
        $stmtSelect->close();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $currentBalance = $user['balance'];
            $newBalance = $currentBalance + $depositAmount;

            $sqlUpdate = "UPDATE user SET balance = ? WHERE `user-id` = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);

            if ($stmtUpdate) {
                $stmtUpdate->bind_param("di", $newBalance, $user_id);

                if ($stmtUpdate->execute()) {
                    echo json_encode(["status" => "success", "message" => "Deposit successful.", "new_balance" => $newBalance]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to update balance."]);
                }

                $stmtUpdate->close();
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to prepare the update query."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "User not found."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare the select query."]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input."]);
}
?>
