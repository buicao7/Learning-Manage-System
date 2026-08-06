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

/* =========================
   Load Grades
========================= */

$sql = "
SELECT

c.course_name,

a.title,

u.full_name,

g.score,

g.feedback,

g.graded_at

FROM grades g

INNER JOIN submissions s
ON g.submission_id=s.submission_id

INNER JOIN assignments a
ON s.assignment_id=a.assignment_id

INNER JOIN courses c
ON a.course_id=c.course_id

INNER JOIN users u
ON c.lecturer_id=u.user_id

WHERE s.student_id='$student_id'

ORDER BY g.graded_at DESC
";

$result = mysqli_query($conn,$sql);

/* =========================
   GPA
========================= */

$gpaResult = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT
ROUND(AVG(score),2) gpa
FROM grades g
INNER JOIN submissions s
ON g.submission_id=s.submission_id
WHERE s.student_id='$student_id'
"));

$gpa = $gpaResult['gpa'];

if($gpa=="")
{
    $gpa="0.00";
}

/* =========================
   Statistics
========================= */

$totalAssignments = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM assignments a
INNER JOIN enrollments e
ON a.course_id=e.course_id
WHERE e.student_id='$student_id'
"));

$totalGraded = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM grades g
INNER JOIN submissions s
ON g.submission_id=s.submission_id
WHERE s.student_id='$student_id'
"));

$pending = $totalAssignments['total'] - $totalGraded['total'];
?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Grades</title>

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

My Grades

</h2>

<p>

View all grades and lecturer feedback.

</p>

</div>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

<i class="fa-solid fa-chart-line me-2"></i>

Grade Report

</h4>

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>#</th>

<th>Course</th>

<th>Assignment</th>

<th>Lecturer</th>

<th>Score</th>

<th>Feedback</th>

<th>Graded Date</th>

</tr>

</thead>

<tbody>

<?php

$no = 1;

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td>

<?= $no++ ?>

</td>

<td>

<?= htmlspecialchars($row['course_name']) ?>

</td>

<td>

<?= htmlspecialchars($row['title']) ?>

</td>

<td>

<?= htmlspecialchars($row['full_name']) ?>

</td>

<td>

<?php

if($row['score']>=8)
{

?>

<span class="badge bg-success">

<?= $row['score'] ?>

</span>

<?php

}
elseif($row['score']>=5)
{

?>

<span class="badge bg-warning text-dark">

<?= $row['score'] ?>

</span>

<?php

}
else
{

?>

<span class="badge bg-danger">

<?= $row['score'] ?>

</span>

<?php

}

?>

</td>

<td>

<?= $row['feedback']!="" ? htmlspecialchars($row['feedback']) : "-" ?>

</td>

<td>

<?= date("d/m/Y H:i",strtotime($row['graded_at'])) ?>

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

            <i class="fa-solid fa-chart-line text-primary mb-3"></i>

            <h2><?= $gpa ?></h2>

            <p>Current GPA</p>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-file-lines text-success mb-3"></i>

            <h2><?= $totalAssignments['total']; ?></h2>

            <p>Total Assignments</p>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-circle-check text-warning mb-3"></i>

            <h2><?= $totalGraded['total']; ?></h2>

            <p>Graded</p>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="dashboard-card">

            <i class="fa-solid fa-clock text-danger mb-3"></i>

            <h2><?= $pending; ?></h2>

            <p>Pending</p>

        </div>

    </div>

</div>

<br>

<div class="row">

    <div class="col-lg-6">

        <div class="card shadow">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    <i class="fa-solid fa-chart-column me-2"></i>

                    GPA Summary

                </h5>

            </div>

            <div class="card-body">

                <h2 class="text-center text-primary">

                    <?= $gpa ?>

                </h2>

                <div class="progress mt-4" style="height:25px;">

                    <?php
                    $percent = ($gpa / 10) * 100;
                    ?>

                    <div
                        class="progress-bar bg-success"
                        style="width:<?= $percent ?>%">

                        <?= round($percent) ?>%

                    </div>

                </div>

                <div class="text-center mt-3">

                    Average Score Based On All Graded Assignments

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-6">

        <div class="card shadow">

            <div class="card-header bg-info text-white">

                <h5 class="mb-0">

                    <i class="fa-solid fa-award me-2"></i>

                    Academic Summary

                </h5>

            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>

                        <th>Total Assignments</th>

                        <td><?= $totalAssignments['total']; ?></td>

                    </tr>

                    <tr>

                        <th>Assignments Graded</th>

                        <td><?= $totalGraded['total']; ?></td>

                    </tr>

                    <tr>

                        <th>Pending Grades</th>

                        <td><?= $pending; ?></td>

                    </tr>

                    <tr>

                        <th>Current GPA</th>

                        <td>

                            <strong class="text-primary">

                                <?= $gpa ?>

                            </strong>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

<br>

<div class="card shadow">

    <div class="card-header bg-dark text-white">

        <h5 class="mb-0">

            <i class="fa-solid fa-circle-info me-2"></i>

            Grade Scale

        </h5>

    </div>

    <div class="card-body">

        <table class="table table-bordered text-center">

            <thead>

                <tr>

                    <th>Score</th>

                    <th>Classification</th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td class="text-success">

                        8.0 - 10

                    </td>

                    <td>

                        Excellent

                    </td>

                </tr>

                <tr>

                    <td class="text-warning">

                        5.0 - 7.9

                    </td>

                    <td>

                        Pass

                    </td>

                </tr>

                <tr>

                    <td class="text-danger">

                        Below 5.0

                    </td>

                    <td>

                        Fail

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

<br>

<footer class="text-center py-4">

<p class="text-muted mb-0">

© <?= date("Y"); ?>

Learning Management System |
Student Grades

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>