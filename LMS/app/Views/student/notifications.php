<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "student") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../../config/database.php";

$db = new Database();
$conn = $db->connect();

$student_id = $_SESSION['user_id'];

/* ===========================
   Mark As Read
=========================== */

if(isset($_GET['read']))
{
    $id = intval($_GET['read']);

    mysqli_query($conn,"
        UPDATE notifications
        SET is_read=1
        WHERE
        notification_id='$id'
        AND
        user_id='$student_id'
    ");

    header("Location: notifications.php");
    exit();
}

/* ===========================
   Load Notifications
=========================== */

$result = mysqli_query($conn,"
SELECT
notification_id,
message,
is_read,
created_at
FROM notifications
WHERE user_id='$student_id'
ORDER BY created_at DESC
");

/* ===========================
   Statistics
=========================== */

$total = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM notifications
WHERE user_id='$student_id'
"));

$read = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM notifications
WHERE
user_id='$student_id'
AND
is_read=1
"));

$unread = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM notifications
WHERE
user_id='$student_id'
AND
is_read=0
"));

$latest = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT MAX(created_at) latest
FROM notifications
WHERE user_id='$student_id'
"));
?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Notifications</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link
rel="stylesheet"
href="../../../Public/css/student.css">

</head>

<body>

<div class="main">

<?php include "../student/sidebar.php"; ?>

<div class="content">

<?php include "../student/navbar.php"; ?>

<div class="container-fluid">

<div class="hero">

<h2>

Notifications

</h2>

<p>

Stay updated with the latest announcements and messages.

</p>

</div>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

<i class="fa-solid fa-bell me-2"></i>

My Notifications

</h4>

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>#</th>

<th>Message</th>

<th>Status</th>

<th>Date</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

$no = 1;

while($row = mysqli_fetch_assoc($result))
{

?>

<tr>

<td>

<?= $no++ ?>

</td>

<td>

<i class="fa-solid fa-circle-info text-primary me-2"></i>

<?= htmlspecialchars($row['message']) ?>

</td>

<td>

<?php

if($row['is_read'])
{

?>

<span class="badge bg-success">

Read

</span>

<?php

}
else
{

?>

<span class="badge bg-warning text-dark">

Unread

</span>

<?php

}

?>

</td>

<td>

<?= date("d/m/Y H:i",strtotime($row['created_at'])) ?>

</td>

<td>

<?php

if(!$row['is_read'])
{

?>

<a
href="?read=<?= $row['notification_id'] ?>"
class="btn btn-primary btn-sm">

<i class="fa-solid fa-check"></i>

Mark as Read

</a>

<?php

}
else
{

?>

<span class="badge bg-secondary">

Completed

</span>

<?php

}

?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>
</div>

<br>

<div class="row">

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-bell text-primary mb-3"></i>

            <h2><?= $total['total']; ?></h2>

            <p>Total Notifications</p>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-envelope-open text-success mb-3"></i>

            <h2><?= $read['total']; ?></h2>

            <p>Read</p>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-envelope text-warning mb-3"></i>

            <h2><?= $unread['total']; ?></h2>

            <p>Unread</p>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-clock text-danger mb-3"></i>

            <h5>

                <?= $latest['latest'] ? date("d/m/Y", strtotime($latest['latest'])) : "-" ?>

            </h5>

            <p>Latest Notification</p>

        </div>

    </div>

</div>

<br>

<div class="row">

<?php

mysqli_data_seek($result, 0);

while($noti = mysqli_fetch_assoc($result))
{

?>

<div class="col-lg-6 mb-4">

    <div class="card shadow h-100">

        <div class="card-body">

            <h5>

                <i class="fa-solid fa-bell text-primary me-2"></i>

                Notification

            </h5>

            <hr>

            <p>

                <?= htmlspecialchars($noti['message']) ?>

            </p>

            <p>

                <i class="fa-solid fa-calendar-days text-success"></i>

                <?= date("d/m/Y H:i", strtotime($noti['created_at'])) ?>

            </p>

            <?php if($noti['is_read']){ ?>

                <span class="badge bg-success">

                    Read

                </span>

            <?php }else{ ?>

                <span class="badge bg-warning text-dark">

                    Unread

                </span>

                <a
                href="?read=<?= $noti['notification_id']; ?>"
                class="btn btn-primary btn-sm float-end">

                    <i class="fa-solid fa-check"></i>

                    Mark as Read

                </a>

            <?php } ?>

        </div>

    </div>

</div>

<?php

}

?>

</div>

<br>

<div class="card shadow">

    <div class="card-header bg-info text-white">

        <h5 class="mb-0">

            <i class="fa-solid fa-chart-pie me-2"></i>

            Notification Summary

        </h5>

    </div>

    <div class="card-body">

        <div class="row text-center">

            <div class="col-md-3">

                <h2 class="text-primary">

                    <?= $total['total']; ?>

                </h2>

                <p>Total Notifications</p>

            </div>

            <div class="col-md-3">

                <h2 class="text-success">

                    <?= $read['total']; ?>

                </h2>

                <p>Read</p>

            </div>

            <div class="col-md-3">

                <h2 class="text-warning">

                    <?= $unread['total']; ?>

                </h2>

                <p>Unread</p>

            </div>

            <div class="col-md-3">

                <h5 class="text-danger">

                    <?= $latest['latest'] ? date("d/m/Y", strtotime($latest['latest'])) : "-" ?>

                </h5>

                <p>Latest Notification</p>

            </div>

        </div>

    </div>

</div>

<br>

<footer class="text-center py-4">

<p class="text-muted mb-0">

© <?= date("Y"); ?>

Learning Management System |
Student Notifications

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>