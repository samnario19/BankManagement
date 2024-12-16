<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include the database connection
include '../config/connection.php';

// Check the database connection
if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Database connection failed.',
        'error' => $conn->connect_error
    ]));
}

// Get the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Log the incoming request for debugging
file_put_contents('log.txt', "Request Payload: " . json_encode($data) . "\n", FILE_APPEND);

// Check if required fields are provided
if (isset($data['user-id'], $data['type'], $data['amount'])) {
    $userId = $data['user-id'];
    $type = $data['type'];
    $amount = $data['amount'];
    $date = date('Y-m-d H:i:s'); // Get the current date and time

    // Check if recipient_account is provided (it might not be for some transactions)
    $recipientAccount = isset($data['recipient_account']) ? $data['recipient_account'] : NULL;

    // Prepare the SQL query to insert a new transaction
    if ($recipientAccount !== NULL) {
        $sql = "INSERT INTO transactions (`user-id`, `type`, `amount`, `date`, `recipient_account`) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssds", $userId, $type, $amount, $date, $recipientAccount); // Bind parameters including recipient_account

            // Log the SQL statement and parameters for debugging
            file_put_contents('log.txt', "SQL: $sql\nUserID: $userId, Type: $type, Amount: $amount, Date: $date, RecipientAccount: $recipientAccount\n", FILE_APPEND);

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Transaction recorded successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to execute query.',
                    'error' => $stmt->error
                ]);
            }
            $stmt->close();
        } else {
            // Query preparation failed
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to prepare the query.',
                'error' => $conn->error
            ]);
        }
    } else {
        // If recipient_account is not provided, insert without it
        $sql = "INSERT INTO transactions (`user-id`, `type`, `amount`, `date`) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssds", $userId, $type, $amount, $date); // Bind parameters

            // Log the SQL statement and parameters for debugging
            file_put_contents('log.txt', "SQL: $sql\nUserID: $userId, Type: $type, Amount: $amount, Date: $date\n", FILE_APPEND);

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Transaction recorded successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to execute query.',
                    'error' => $stmt->error
                ]);
            }
            $stmt->close();
        } else {
            // Query preparation failed
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to prepare the query.',
                'error' => $conn->error
            ]);
        }
    }
} else {
    // Missing required fields
    echo json_encode([
        'status' => 'error',
        'message' => 'Required fields are missing (user-id, type, amount).'
    ]);
}

// Close the database connection
$conn->close();
?>
