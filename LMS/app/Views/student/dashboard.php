<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!="student")
{
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../../config/database.php";

$db=new Database();
$conn=$db->connect();

$student_id=$_SESSION['user_id'];

$totalCourses=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments
WHERE student_id=$student_id
"))['total'];

$totalAssignments=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM assignments a
INNER JOIN enrollments e
ON a.course_id=e.course_id
WHERE e.student_id=$student_id
"))['total'];

$totalNotifications=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM notifications
WHERE user_id=$student_id
"))['total'];

$currentGPA=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT ROUND(AVG(score),2) gpa
FROM grades g
INNER JOIN submissions s
ON g.submission_id=s.submission_id
WHERE s.student_id=$student_id
"));

$gpa=$currentGPA['gpa'];

if($gpa=="")
{
    $gpa="0.00";
}

$courses=mysqli_query($conn,"
SELECT
c.course_name,
u.full_name,
c.end_date
FROM enrollments e
INNER JOIN courses c
ON e.course_id=c.course_id
INNER JOIN users u
ON c.lecturer_id=u.user_id
WHERE e.student_id=$student_id
ORDER BY c.course_id DESC
");

$tasks=mysqli_query($conn,"
SELECT
a.title,
a.due_date
FROM assignments a
INNER JOIN enrollments e
ON a.course_id=e.course_id
WHERE e.student_id=$student_id
ORDER BY a.due_date ASC
LIMIT 5
");

$notifications=mysqli_query($conn,"
SELECT
message,
created_at
FROM notifications
WHERE user_id=$student_id
ORDER BY notification_id DESC
LIMIT 5
");

include "../student/sidebar.php";
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Student Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


<link rel="stylesheet" href="../../../Public/css/style.css">
<link rel="stylesheet" href="../../../Public/css/student.css">

</head>

<body>

<div class="main">



<div class="container-fluid">

<div class="hero">

<h2>

Welcome,
<?= $_SESSION['name']; ?> 👋

</h2>

<p class="mb-0">



</p>

</div>

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

<i class="fa-solid fa-file-lines text-success mb-3"></i>

<h2><?= $totalAssignments ?></h2>

<p>Assignments</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card">

<i class="fa-solid fa-chart-line text-warning mb-3"></i>

<h2><?= $gpa ?></h2>

<p>Current GPA</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card">

<i class="fa-solid fa-bell text-danger mb-3"></i>

<h2><?= $totalNotifications ?></h2>

<p>Notifications</p>

</div>

</div>

</div>

<br>

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

<th>Lecturer</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($courses)){ ?>

<tr>

<td>

<?= $row['course_name']; ?>

</td>

<td>

<?= $row['full_name']; ?>

</td>

<td>

<?php

$status = (strtotime($row['end_date']) >= time())
? "Studying"
: "Completed";

?>

<?php if($status=="Studying"){ ?>

<span class="badge bg-success">

<?= $status ?>

</span>

<?php }else{ ?>

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

<div class="col-lg-4">

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fa-solid fa-bolt me-2"></i>

Quick Actions

</h5>

</div>

<div class="card-body">

<a href="courses.php"
class="btn btn-primary w-100 mb-3">

<i class="fa-solid fa-book me-2"></i>

View Courses

</a>

<a href="materials.php"
class="btn btn-success w-100 mb-3">

<i class="fa-solid fa-folder-open me-2"></i>

View Materials

</a>

<a href="assignments.php"
class="btn btn-warning w-100 mb-3">

<i class="fa-solid fa-file-lines me-2"></i>

Assignments

</a>

<a href="grades.php"
class="btn btn-danger w-100">

<i class="fa-solid fa-chart-line me-2"></i>

View Grades

</a>

</div>

</div>

</div>

</div>

<br>

<div class="row">

<div class="col-lg-7">

<div class="card">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fa-solid fa-list-check me-2"></i>

Upcoming Assignments

</h5>

</div>

<div class="card-body">

<table class="table table-hover">

<thead>

<tr>

<th>Assignment</th>

<th>Due Date</th>

</tr>

</thead>

<tbody>

<?php while($task=mysqli_fetch_assoc($tasks)){ ?>

<tr>

<td>

<?= $task['title']; ?>

</td>

<td>

<?= date("d M Y",strtotime($task['due_date'])); ?>

</td>

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

You don't have any notifications.

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

<i class="fa-solid fa-chart-pie me-2"></i>

Learning Summary

</h5>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<h2 class="text-primary">

<?= $totalCourses ?>

</h2>

<p>Enrolled Courses</p>

</div>

<div class="col-md-3">

<h2 class="text-success">

<?= $totalAssignments ?>

</h2>

<p>Total Assignments</p>

</div>

<div class="col-md-3">

<h2 class="text-warning">

<?= $gpa ?>

</h2>

<p>Current GPA</p>

</div>

<div class="col-md-3">

<h2 class="text-danger">

<?= $totalNotifications ?>

</h2>

<p>Notifications</p>

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
Student Dashboard

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>