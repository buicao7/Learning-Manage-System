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
   Load Materials
=========================== */

$sql = "
SELECT

m.material_id,
m.title,
m.description,
m.file_path,
m.upload_date,

c.course_id,
c.course_name,

u.full_name

FROM materials m

INNER JOIN courses c
ON m.course_id=c.course_id

INNER JOIN enrollments e
ON c.course_id=e.course_id

INNER JOIN users u
ON c.lecturer_id=u.user_id

WHERE e.student_id='$student_id'

ORDER BY m.upload_date DESC
";

$result = mysqli_query($conn,$sql);

/* ===========================
   Statistics
=========================== */

$totalMaterials = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM materials m
INNER JOIN enrollments e
ON m.course_id=e.course_id
WHERE e.student_id='$student_id'
"));

$totalCourses = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments
WHERE student_id='$student_id'
"));

$latestUpload = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT MAX(upload_date) latest
FROM materials m
INNER JOIN enrollments e
ON m.course_id=e.course_id
WHERE e.student_id='$student_id'
"));

$totalFiles = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(file_path) total
FROM materials m
INNER JOIN enrollments e
ON m.course_id=e.course_id
WHERE
e.student_id='$student_id'
AND
m.file_path IS NOT NULL
"));
?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Learning Materials</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link rel="stylesheet" href="../../../Public/css/style.css">
<link rel="stylesheet" href="../../../Public/css/student.css">


</head>

<body>

<div class="main">

<?php include "../student/sidebar.php"; ?>

<div class="content">



<div class="container-fluid">

<div class="hero">

<h2>

Learning Materials

</h2>

<p>

Access course documents, slides and learning resources.

</p>

</div>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

<i class="fa-solid fa-folder-open me-2"></i>

Course Materials

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle text-center">

<thead>

<tr>

<th>#</th>

<th>Course</th>

<th>Title</th>

<th>Lecturer</th>

<th>Upload Date</th>

<th>Download</th>

</tr>

</thead>

<tbody>

<?php

$no=1;

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td>

<?= $no++ ?>

</td>

<td>

<strong>

<?= htmlspecialchars($row['course_name']) ?>

</strong>

</td>

<td>

<b>

<?= htmlspecialchars($row['title']) ?>

</b>

<br>

<small class="text-muted">

<?= htmlspecialchars($row['description']) ?>

</small>

</td>

<td>

<?= htmlspecialchars($row['full_name']) ?>

</td>

<td>

<?= date("d/m/Y H:i",strtotime($row['upload_date'])) ?>

</td>

<td>

<?php
if(!empty($row['file_path']))
{
?>

<a
href="../../../Public/uploads/materials/<?= htmlspecialchars($row['file_path']) ?>"
class="btn btn-success btn-sm"
download>

<i class="fa-solid fa-download"></i>

Download

</a>

<?php
}
else
{
?>

<span class="badge bg-secondary">

No File

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

            <i class="fa-solid fa-folder-open text-primary mb-3"></i>

            <h2><?= $totalMaterials['total']; ?></h2>

            <p>Total Materials</p>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-book text-success mb-3"></i>

            <h2><?= $totalCourses['total']; ?></h2>

            <p>Enrolled Courses</p>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-file text-warning mb-3"></i>

            <h2><?= $totalFiles['total']; ?></h2>

            <p>Available Files</p>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-calendar-days text-danger mb-3"></i>

            <h5>

                <?= $latestUpload['latest'] ? date("d/m/Y",strtotime($latestUpload['latest'])) : "-" ?>

            </h5>

            <p>Latest Upload</p>

        </div>

    </div>

</div>

<br>

<div class="row">

<?php

mysqli_data_seek($result,0);

while($material=mysqli_fetch_assoc($result))
{

?>

<div class="col-lg-4 mb-4">

<div class="card shadow h-100">

<div class="card-body">

<h4 class="text-primary">

<?= htmlspecialchars($material['title']) ?>

</h4>

<p class="text-muted">

<?= htmlspecialchars($material['description']) ?>

</p>

<hr>

<p>

<i class="fa-solid fa-book text-primary"></i>

<strong> Course :</strong>

<?= htmlspecialchars($material['course_name']) ?>

</p>

<p>

<i class="fa-solid fa-user text-success"></i>

<strong> Lecturer :</strong>

<?= htmlspecialchars($material['full_name']) ?>

</p>

<p>

<i class="fa-solid fa-clock text-warning"></i>

<strong> Upload :</strong>

<?= date("d/m/Y",strtotime($material['upload_date'])) ?>

</p>

<hr>

<?php

if(!empty($material['file_path']))
{

?>

<a
href="../../../Public/uploads/materials/<?= htmlspecialchars($material['file_path']) ?>"
download
class="btn btn-success w-100">

<i class="fa-solid fa-download me-2"></i>

Download Material

</a>

<?php

}
else
{

?>

<button
class="btn btn-secondary w-100"
disabled>

<i class="fa-solid fa-ban me-2"></i>

No File Available

</button>

<?php

}

?>

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

Learning Resources Summary

</h5>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<h2 class="text-primary">

<?= $totalMaterials['total']; ?>

</h2>

<p>Materials</p>

</div>

<div class="col-md-3">

<h2 class="text-success">

<?= $totalCourses['total']; ?>

</h2>

<p>Courses</p>

</div>

<div class="col-md-3">

<h2 class="text-warning">

<?= $totalFiles['total']; ?>

</h2>

<p>Downloadable Files</p>

</div>

<div class="col-md-3">

<h5 class="text-danger">

<?= $latestUpload['latest'] ? date("d/m/Y",strtotime($latestUpload['latest'])) : "-" ?>

</h5>

<p>Latest Upload</p>

</div>

</div>

</div>

</div>

<br>

<footer class="text-center py-4">

<p class="text-muted mb-0">

© <?= date("Y"); ?>

Learning Management System |
Student Materials

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>