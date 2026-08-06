<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../../config/database.php";

$db = new Database();
$conn = $db->connect();

$lecturer_id = $_SESSION['user_id'];

/* ===========================
   STATISTICS
=========================== */

// Total Courses
$totalCourses = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM courses
WHERE lecturer_id = $lecturer_id
"))['total'];

// Total Materials
$totalMaterials = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM materials m
INNER JOIN courses c
ON m.course_id = c.course_id
WHERE c.lecturer_id = $lecturer_id
"))['total'];

// Total Assignments
$totalAssignments = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM assignments a
INNER JOIN courses c
ON a.course_id = c.course_id
WHERE c.lecturer_id = $lecturer_id
"))['total'];

// Total Submissions
$totalSubmissions = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM submissions s
INNER JOIN assignments a
ON s.assignment_id = a.assignment_id
INNER JOIN courses c
ON a.course_id = c.course_id
WHERE c.lecturer_id = $lecturer_id
"))['total'];


/* ===========================
   MY COURSES
=========================== */

$courses = mysqli_query($conn,"
SELECT
course_name,
start_date,
end_date
FROM courses
WHERE lecturer_id = $lecturer_id
ORDER BY course_id DESC
");


include "../lecturer/sidebar.php";
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Lecturer Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link rel="stylesheet"
href="../../../Public/css/style.css">

<link rel="stylesheet"
href="../../../Public/css/lecturer.css">

</head>

<body>

<div class="main">

<div class="container-fluid">

<!-- HERO -->

<div class="hero">

<h2>

Welcome,
<?= $_SESSION['name']; ?> 👋

</h2>

<p class="mb-0">

Manage your courses, assignments and student submissions.

</p>

</div>

<!-- DASHBOARD CARDS -->

<div class="row g-4">

<div class="col-lg-3">

<div class="dashboard-card">

<i class="fa-solid fa-book-open text-primary mb-3"></i>

<h2><?= $totalCourses ?></h2>

<p>My Courses</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card">

<i class="fa-solid fa-folder-open text-success mb-3"></i>

<h2><?= $totalMaterials ?></h2>

<p>Materials</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card">

<i class="fa-solid fa-file-lines text-warning mb-3"></i>

<h2><?= $totalAssignments ?></h2>

<p>Assignments</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card">

<i class="fa-solid fa-upload text-danger mb-3"></i>

<h2><?= $totalSubmissions ?></h2>

<p>Submissions</p>

</div>

</div>

</div>

<br>

<!-- MY COURSES -->

<div class="row">

<div class="col-lg-8">

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fa-solid fa-book-open me-2"></i>

My Courses

</h5>

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Course</th>

<th>Start Date</th>

<th>End Date</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($courses)){ ?>

<tr>

<td><?= $row['course_name']; ?></td>

<td><?= date("d M Y",strtotime($row['start_date'])); ?></td>

<td><?= date("d M Y",strtotime($row['end_date'])); ?></td>

<td>

<?php

$status = (strtotime($row['end_date']) >= time())
? "Teaching"
: "Finished";

?>

<?php if($status=="Teaching"){ ?>

<span class="badge bg-success">

<?= $status ?>

</span>

<?php } else { ?>

<span class="badge bg-secondary">

<?= $status ?>

</span>

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>
<!-- QUICK ACTION -->

<div class="col-lg-4">

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fa-solid fa-bolt me-2"></i>

Quick Actions

</h5>

</div>

<div class="card-body">

<a href="materials.php" class="btn btn-primary w-100 mb-3">

<i class="fa-solid fa-folder-open me-2"></i>

Manage Materials

</a>

<a href="assignments.php" class="btn btn-success w-100 mb-3">

<i class="fa-solid fa-file-circle-plus me-2"></i>

Create Assignment

</a>

<a href="submissions.php" class="btn btn-warning w-100 mb-3">

<i class="fa-solid fa-upload me-2"></i>

View Submissions

</a>

<a href="grades.php" class="btn btn-danger w-100">

<i class="fa-solid fa-marker me-2"></i>

Grade Students

</a>

</div>

</div>

</div>

</div>

<?php

/* ===========================
   RECENT SUBMISSIONS
=========================== */

$submissions = mysqli_query($conn,"

SELECT

u.full_name,
a.title,
s.submitted_at

FROM submissions s

INNER JOIN assignments a
ON s.assignment_id=a.assignment_id

INNER JOIN users u
ON s.student_id=u.user_id

INNER JOIN courses c
ON a.course_id=c.course_id

WHERE c.lecturer_id=$lecturer_id

ORDER BY s.submitted_at DESC

LIMIT 5

");


/* ===========================
   NOTIFICATIONS
=========================== */

$notifications = mysqli_query($conn,"

SELECT
message,
created_at

FROM notifications

WHERE user_id=$lecturer_id

ORDER BY notification_id DESC

LIMIT 5

");

?>

<br>

<div class="row">

<div class="col-lg-7">

<div class="card">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fa-solid fa-upload me-2"></i>

Recent Submissions

</h5>

</div>

<div class="card-body">

<table class="table table-hover">

<thead>

<tr>

<th>Student</th>

<th>Assignment</th>

<th>Submitted</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($submissions)){ ?>

<tr>

<td><?= $row['full_name']; ?></td>

<td><?= $row['title']; ?></td>

<td><?= date("d M Y H:i",strtotime($row['submitted_at'])); ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<div class="col-lg-5">

<div class="card">

<div class="card-header bg-warning">

<h5 class="mb-0">

<i class="fa-solid fa-bell me-2"></i>

Latest Notifications

</h5>

</div>

<div class="card-body">

<?php if(mysqli_num_rows($notifications)>0){ ?>

<table class="table table-hover">

<thead>

<tr>

<th>Message</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php while($noti=mysqli_fetch_assoc($notifications)){ ?>

<tr>

<td>

<i class="fa-solid fa-circle-info text-primary me-2"></i>

<?= $noti['message']; ?>

</td>

<td>

<?= date("d M Y H:i",strtotime($noti['created_at'])); ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<?php }else{ ?>

<div class="text-center py-5">

<i class="fa-solid fa-bell-slash fa-3x text-secondary mb-3"></i>

<h5>No Notifications</h5>

<p class="text-muted">

There are no notifications.

</p>

</div>

<?php } ?>

</div>

</div>

</div>

</div>

<br>

<div class="row">

<div class="col-lg-12">

<div class="card">

<div class="card-header bg-info text-white">

<h5 class="mb-0">

<i class="fa-solid fa-chart-column me-2"></i>

Teaching Summary

</h5>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<h2 class="text-primary">

<?= $totalCourses ?>

</h2>

<p>Courses</p>

</div>

<div class="col-md-3">

<h2 class="text-success">

<?= $totalMaterials ?>

</h2>

<p>Materials</p>

</div>

<div class="col-md-3">

<h2 class="text-warning">

<?= $totalAssignments ?>

</h2>

<p>Assignments</p>

</div>

<div class="col-md-3">

<h2 class="text-danger">

<?= $totalSubmissions ?>

</h2>

<p>Submissions</p>

</div>

</div>

</div>

</div>

</div>

</div>

<br>

<footer class="text-center py-4">

<p class="text-muted mb-0">

© <?= date('Y'); ?>

Learning Management System |
Lecturer Dashboard

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>