<?php
include 'php/dbconn.php';

if (isset($_GET['reminderid'])) {
    $remId = intval($_GET['reminderid']);
    $query = "
    SELECT
        c1.courtname,
        c2.casename, c2.casenumber,
        r.nextdate, r.bringupdate, r.meetinglink, r.notes
    FROM reminders r 
    JOIN cases c2 ON c2.caseid = r.caseid
    JOIN courts c1 ON c2.courtid = c1.courtid
    WHERE reminderid = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $remId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Output JSON response
        echo json_encode($row);
    } else {
        // Return error if no data found
        echo json_encode(['error' => 'No reminder found']);
    }
} else {
    // Return error if reminderid is missing
    echo json_encode(['error' => 'Invalid request']);
}
