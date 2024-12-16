<?php
// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include the database connection file
include '../config/connection.php';

// Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check if all necessary data is provided
if (isset($input['sender_id'], $input['recipient_accountnum'], $input['transfer_amount'])) {
    $sender_id = $input['sender_id'];
    $recipient_accountnum = $input['recipient_accountnum'];
    $transfer_amount = $input['transfer_amount'];

    // Validate transfer amount
    if ($transfer_amount <= 0) {
        echo json_encode(["status" => "error", "message" => "Transfer amount must be greater than zero."]);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get sender's balance
        $sqlSender = "SELECT balance FROM user WHERE `user-id` = ?";
        $stmtSender = $conn->prepare($sqlSender);
        $stmtSender->bind_param("i", $sender_id);
        $stmtSender->execute();
        $resultSender = $stmtSender->get_result();

        if ($resultSender->num_rows === 0) {
            throw new Exception("Sender not found.");
        }

        $sender = $resultSender->fetch_assoc();
        $sender_balance = $sender['balance'];

        // Check if sender has enough balance
        if ($sender_balance < $transfer_amount) {
            throw new Exception("Insufficient balance.");
        }

        // Get recipient's user-id using accountnum
        $sqlRecipient = "SELECT `user-id`, balance FROM user WHERE accountNum = ?";
        $stmtRecipient = $conn->prepare($sqlRecipient);
        $stmtRecipient->bind_param("s", $recipient_accountnum);
        $stmtRecipient->execute();
        $resultRecipient = $stmtRecipient->get_result();

        if ($resultRecipient->num_rows === 0) {
            throw new Exception("Recipient account number not found.");
        }

        $recipient = $resultRecipient->fetch_assoc();
        $recipient_id = $recipient['user-id'];
        $recipient_balance = $recipient['balance'];

        // Deduct from sender's balance
        $new_sender_balance = $sender_balance - $transfer_amount;
        $sqlUpdateSender = "UPDATE user SET balance = ? WHERE `user-id` = ?";
        $stmtUpdateSender = $conn->prepare($sqlUpdateSender);
        $stmtUpdateSender->bind_param("di", $new_sender_balance, $sender_id);
        if (!$stmtUpdateSender->execute()) {
            throw new Exception("Failed to update sender's balance.");
        }

        // Add to recipient's balance
        $new_recipient_balance = $recipient_balance + $transfer_amount;
        $sqlUpdateRecipient = "UPDATE user SET balance = ? WHERE `user-id` = ?";
        $stmtUpdateRecipient = $conn->prepare($sqlUpdateRecipient);
        $stmtUpdateRecipient->bind_param("di", $new_recipient_balance, $recipient_id);
        if (!$stmtUpdateRecipient->execute()) {
            throw new Exception("Failed to update recipient's balance.");
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            "status" => "success",
            "message" => "Transfer completed successfully.",
            "sender_new_balance" => $new_sender_balance,
            "recipient_new_balance" => $new_recipient_balance
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    } finally {
        // Close statements and connection
        $stmtSender->close();
        $stmtRecipient->close();
        if (isset($stmtUpdateSender)) $stmtUpdateSender->close();
        if (isset($stmtUpdateRecipient)) $stmtUpdateRecipient->close();
        $conn->close();
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input."]);
}
?>
