<?php
// Allow cross-origin requests (CORS)
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

// Check if the 'userid' parameter is provided
if (isset($_GET['userid'])) {
    $userid = $_GET['userid']; // Get the user ID

    // Validate that the user ID is not empty
    if (empty($userid)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User ID cannot be empty.'
        ]);
        exit;
    }

    // Prepare the SQL query
    $sql = "SELECT transaction_id, `user-id`, type, CAST(amount AS DECIMAL(10,2)) AS amount, 
                   CONVERT_TZ(date, '+00:00', '+08:00') AS date, recipient_account 
            FROM transactions WHERE `user-id` = ?";

    // Attempt to prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $userid); // Bind the parameter (string type)

        // Execute the query
        if ($stmt->execute()) {
            // Fetch the results
            $result = $stmt->get_result();
            $transactions = [];
            
            // Check if transactions were found
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Format the date to a user-friendly format
                    $row['date'] = date('Y-m-d H:i:s', strtotime($row['date']));
                    $transactions[] = $row; // Add each transaction to the array
                }
                // Return the transactions as JSON
                echo json_encode([
                    'status' => 'success',
                    'transactions' => $transactions
                ]);
            } else {
                // No transactions found for the user
                echo json_encode([
                    'status' => 'success',
                    'message' => 'No transactions found for this user.',
                    'transactions' => []
                ]);
            }
        } else {
            // Query execution failed
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to execute the query.',
                'error' => $stmt->error // Debugging information
            ]);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Query preparation failed
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to prepare the query.',
            'error' => $conn->error // Debugging information
        ]);
    }
} else {
    // User ID parameter is missing
    echo json_encode([
        'status' => 'error',
        'message' => 'User ID not provided.'
    ]);
}

// Close the database connection
$conn->close();
?>
