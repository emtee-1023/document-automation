<?php include 'php/header.php'; ?>

<?php include 'notifications.php'; ?>

<?php
if (!isset($_GET['id'])) {
    header('location: case-updates');
    exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("
                        SELECT
                            c.*,
                            ca.casename,
                            ca.casenumber,
                            ct.courtname,
                            concat(u.fname,' ',u.lname) as adder
                        FROM case_updates c
                        JOIN cases ca ON c.caseid = ca.caseid
                        JOIN courts ct ON ca.courtid = ct.courtid
                        JOIN users u ON c.userid = u.userid
                        WHERE c.updateid = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

?>


<div id="layoutSidenav">
    <?php include 'php/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Case Update Details</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="case-updates">Case Updates</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
                <div>
                    <strong>Case:</strong> <?= $row['courtname'] . ' ' . $row['casenumber'] . ' ' . $row['casename'] ?>
                </div>
                <div class="mt-2">
                    <strong>Added On:</strong> <?= date('D d M, Y \a\t H:iA', strtotime($row['CreatedAt'])) ?> <strong>By</strong> <?= $row['adder'] ?>
                </div>
                <div class="mt-2">
                    <?php
                    if ($row['Document'] != '') {
                        echo '<a class="btn btn-primary" href="assets/files/submitted/' . $row['Document'] . '" target="_blank">View Document</a>';
                    } else {
                        echo 'No document attached';
                    }

                    ?>
                </div>
                <div class="mt-2">
                    <strong>Update Notes:</strong>
                    <p><?= $row['Details'] ?></p>
                </div>
            </div>
        </main>
        <?php include 'php/footer.php'; ?>