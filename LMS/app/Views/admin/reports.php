<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../../config/database.php";

$db = new Database();
$conn = $db->connect();

/* =========================
   SUMMARY
========================= */

$totalUsers=mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM users"))['total'];

$totalStudents=mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM users WHERE role='student'"))['total'];

$totalLecturers=mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM users WHERE role='lecturer'"))['total'];

$totalCourses=mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM courses"))['total'];

$totalAssignments=mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM assignments"))['total'];

$totalEnrollments=mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM enrollments"))['total'];

/* =========================
   COURSE REPORT
========================= */

$courseReport=mysqli_query($conn,"
SELECT
c.course_name,
COUNT(e.enrollment_id) total_student
FROM courses c
LEFT JOIN enrollments e
ON c.course_id=e.course_id
GROUP BY c.course_id
ORDER BY total_student DESC
");

/* =========================
   LECTURER REPORT
========================= */

$lecturerReport=mysqli_query($conn,"
SELECT
u.full_name,
COUNT(c.course_id) total_course
FROM users u
LEFT JOIN courses c
ON u.user_id=c.lecturer_id
WHERE u.role='lecturer'
GROUP BY u.user_id
ORDER BY total_course DESC
");

include "../layouts/sidebar.php";
?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="../../../Public/css/style.css">

<link rel="stylesheet"
href="../../../Public/css/admin.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<div class="main">

<?php include "../layouts/navbar.php"; ?>

<div class="container-fluid">

<h2 class="mb-4">

<i class="fa-solid fa-chart-column me-2"></i>

System Reports

</h2>

<div class="row g-4">

<div class="col-md-2">

<div class="dashboard-card">

<i class="fa-solid fa-users text-primary"></i>

<h3><?= $totalUsers ?></h3>

<p>Total Users</p>

</div>

</div>

<div class="col-md-2">

<div class="dashboard-card">

<i class="fa-solid fa-user-graduate text-success"></i>

<h3><?= $totalStudents ?></h3>

<p>Students</p>

</div>

</div>

<div class="col-md-2">

<div class="dashboard-card">

<i class="fa-solid fa-user-tie text-warning"></i>

<h3><?= $totalLecturers ?></h3>

<p>Lecturers</p>

</div>

</div>

<div class="col-md-2">

<div class="dashboard-card">

<i class="fa-solid fa-book-open text-info"></i>

<h3><?= $totalCourses ?></h3>

<p>Courses</p>

</div>

</div>

<div class="col-md-2">

<div class="dashboard-card">

<i class="fa-solid fa-file-lines text-danger"></i>

<h3><?= $totalAssignments ?></h3>

<p>Assignments</p>

</div>

</div>

<div class="col-md-2">

<div class="dashboard-card">

<i class="fa-solid fa-clipboard-check text-secondary"></i>

<h3><?= $totalEnrollments ?></h3>

<p>Enrollments</p>

</div>

</div>

</div>

<br>

<div class="row">

<div class="col-lg-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

Top Courses

</div>

<div class="card-body">

<table class="table table-hover">

<thead>

<tr>

<th>Course</th>

<th>Total Students</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($courseReport)){ ?>

<tr>

<td>

<?= htmlspecialchars($row['course_name']) ?>

</td>

<td>

<span class="badge bg-primary">

<?= $row['total_student'] ?>

</span>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<div class="col-lg-6">

<div class="card shadow">

<div class="card-header bg-success text-white">

Lecturer Statistics

</div>

<div class="card-body">

<table class="table table-hover">

<thead>

<tr>

<th>Lecturer</th>

<th>Total Courses</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($lecturerReport)){ ?>

<tr>

<td>

<?= htmlspecialchars($row['full_name']) ?>

</td>

<td>

<span class="badge bg-success">

<?= $row['total_course'] ?>

</span>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<br>

<div class="card shadow">

<div class="card-header bg-dark text-white">

Summary

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<h2 class="text-primary">

<?= $totalUsers ?>

</h2>

<p>Total Users</p>

</div>

<div class="col-md-3">

<h2 class="text-success">

<?= $totalCourses ?>

</h2>

<p>Total Courses</p>

</div>

<div class="col-md-3">

<h2 class="text-warning">

<?= $totalAssignments ?>

</h2>

<p>Total Assignments</p>

</div>

<div class="col-md-3">

<h2 class="text-danger">

<?= $totalEnrollments ?>

</h2>

<p>Total Enrollments</p>

</div>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>