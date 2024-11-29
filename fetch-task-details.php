<?php
include 'php/dbconn.php';


// Get the record ID from the request
$recordId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($recordId > 0) {
    // Fetch data from the database
    $stmt = $conn->prepare("SELECT taskdescription, document FROM tasks WHERE taskid = ?");
    $stmt->bind_param("i", $recordId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'data' => [
                'description' => $row['taskdescription'],
                'document_url' => $row['document'] ? 'assets/files/submitted/' . $row['document'] : null
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Record not found.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid ID provided.']);
}

$conn->close();
