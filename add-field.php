<?php
include 'php/dbconn.php';

if (isset($_POST['submit'])) {
    $cid  = $_POST['caseid'];
    $fieldname = $_POST['fieldname'];
    $fieldvalue = $_POST['fieldvalue'];

    // Replace spaces with underscores and remove any unwanted characters
    $formattedFieldName = preg_replace('/\s+/', '_', $fieldname); // Replace spaces with underscores
    $formattedFieldName = preg_replace('/[^A-Za-z0-9_]/', '', $formattedFieldName); // Remove special characters

    // Wrap the formatted field name in double curly braces
    $cfcode = "{{" . $formattedFieldName . "}}";

    $stmt = $conn->prepare('INSERT INTO custom_fields (CFName, CFCode, CFValue, CaseID) values(?,?,?,?)');
    $stmt->bind_param('ssss', $fieldname, $cfcode, $fieldvalue, $cid);
    if (!$stmt->execute()) {
        header('location: doc-automation');
        exit();
    }
    header('location: custom-fields?caseid=' . $cid);
    exit();
}
