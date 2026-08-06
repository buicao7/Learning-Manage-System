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
   ENROLL COURSE
=========================== */
if (isset($_GET['enroll'])) {

    $course_id = (int)$_GET['enroll'];

    // Kiểm tra đã đăng ký chưa
    $check = mysqli_query($conn,"
        SELECT *
        FROM enrollments
        WHERE student_id='$student_id'
        AND course_id='$course_id'
    ");

    if(mysqli_num_rows($check)==0){

        mysqli_query($conn,"
            INSERT INTO enrollments(student_id,course_id)
            VALUES('$student_id','$course_id')
        ");

    }

    header("Location: courses.php");
    exit();
}

/* ===========================
   COURSE LIST
=========================== */

$sql = "
SELECT
    c.course_id,
    c.course_name,
    c.description,
    c.start_date,
    c.end_date,
    u.full_name,

    (
        SELECT COUNT(*)
        FROM materials m
        WHERE m.course_id=c.course_id
    ) total_materials,

    (
        SELECT COUNT(*)
        FROM assignments a
        WHERE a.course_id=c.course_id
    ) total_assignments,

    (
        SELECT COUNT(*)
        FROM enrollments e
        WHERE e.course_id=c.course_id
        AND e.student_id='$student_id'
    ) enrolled

FROM courses c

INNER JOIN users u
ON c.lecturer_id=u.user_id

ORDER BY c.course_name ASC
";

$result = mysqli_query($conn,$sql);

$totalCourses = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Courses</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link rel="stylesheet"
href="../../../Public/css/style.css">

<link rel="stylesheet"
href="../../../Public/css/student.css">

</head>

<body>

<div class="main">

<?php include "../student/sidebar.php"; ?>

<div class="content">

<div class="container-fluid">

<div class="hero">

<h2>Courses</h2>

<p>Enroll and study available courses.</p>

</div>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

<i class="fa-solid fa-book-open me-2"></i>

Course List

</h4>

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>#</th>

<th>Course</th>

<th>Lecturer</th>

<th>Materials</th>

<th>Assignments</th>

<th>Start</th>

<th>End</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

$no = 1;

while($row=mysqli_fetch_assoc($result))
{

?>


<tr>

<td><?= $no++ ?></td>

<td>

<b><?= htmlspecialchars($row['course_name']) ?></b>

<br>

<small class="text-muted">

<?= htmlspecialchars($row['description']) ?>

</small>

</td>

<td>

<?= htmlspecialchars($row['full_name']) ?>

</td>

<td>

<span class="badge bg-info">

<?= $row['total_materials'] ?>

</span>

</td>

<td>

<span class="badge bg-warning text-dark">

<?= $row['total_assignments'] ?>

</span>

</td>

<td>

<?= date("d/m/Y",strtotime($row['start_date'])) ?>

</td>

<td>

<?= date("d/m/Y",strtotime($row['end_date'])) ?>

</td>

<td>

<?php if($row['enrolled']){ ?>

<span class="badge bg-success">

Enrolled

</span>

<?php }else{ ?>

<span class="badge bg-secondary">

Not Enrolled

</span>

<?php } ?>

</td>

<td>

<?php if($row['enrolled']){ ?>

<a href="materials.php?course_id=<?= $row['course_id'] ?>"
class="btn btn-success btn-sm mb-1">

<i class="fa-solid fa-folder-open"></i>

Materials

</a>

<br>

<a href="assignments.php?course_id=<?= $row['course_id'] ?>"
class="btn btn-primary btn-sm">

<i class="fa-solid fa-file-lines"></i>

Assignments

</a>

<?php }else{ ?>

<a href="?enroll=<?= $row['course_id']; ?>"
class="btn btn-warning btn-sm"
onclick="return confirm('Enroll this course?')">

<i class="fa-solid fa-user-plus"></i>

Enroll

</a>

<?php } ?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

<br>

<div class="row">

<div class="col-lg-12">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fa-solid fa-chart-column me-2"></i>

Learning Summary

</h5>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<h2 class="text-primary">

<?= $totalCourses ?>

</h2>

<p>Total Courses</p>

</div>

<div class="col-md-3">

<?php

$materialCount = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM materials
"));

?>

<h2 class="text-success">

<?= $materialCount['total']; ?>

</h2>

<p>Total Materials</p>

</div>

<div class="col-md-3">

<?php

$assignmentCount = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM assignments
"));

?>

<h2 class="text-warning">

<?= $assignmentCount['total']; ?>

</h2>

<p>Total Assignments</p>

</div>

<div class="col-md-3">

<?php

$enrolledCount = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM enrollments
WHERE student_id='$student_id'
"));

?>

<h2 class="text-danger">

<?= $enrolledCount['total']; ?>

</h2>

<p>Enrolled Courses</p>

</div>

</div>

</div>

</div>

</div>

</div>

<br>

<div class="row">

<?php

mysqli_data_seek($result,0);

while($course=mysqli_fetch_assoc($result))
{

?>
<div class="col-lg-4 mb-4">

    <div class="card h-100 shadow-sm">

        <div class="card-body">

            <h4 class="text-primary">

                <?= htmlspecialchars($course['course_name']) ?>

            </h4>

            <p class="text-muted">

                <?= htmlspecialchars($course['description']) ?>

            </p>

            <hr>

            <p>

                <i class="fa-solid fa-user-tie text-primary"></i>

                <strong>Lecturer :</strong>

                <?= htmlspecialchars($course['full_name']) ?>

            </p>

            <p>

                <i class="fa-solid fa-book text-success"></i>

                <strong>Materials :</strong>

                <?= $course['total_materials']; ?>

            </p>

            <p>

                <i class="fa-solid fa-file-lines text-warning"></i>

                <strong>Assignments :</strong>

                <?= $course['total_assignments']; ?>

            </p>

            <p>

                <i class="fa-solid fa-calendar-days text-info"></i>

                <strong>Start :</strong>

                <?= date("d/m/Y", strtotime($course['start_date'])) ?>

            </p>

            <p>

                <i class="fa-solid fa-calendar-check text-danger"></i>

                <strong>End :</strong>

                <?= date("d/m/Y", strtotime($course['end_date'])) ?>

            </p>

            <?php if($course['enrolled']){ ?>

                <span class="badge bg-success">

                    Enrolled

                </span>

            <?php }else{ ?>

                <span class="badge bg-secondary">

                    Not Enrolled

                </span>

            <?php } ?>

            <hr>

            <div class="d-grid gap-2">

                <?php if($course['enrolled']){ ?>

                    <a href="materials.php?course_id=<?= $course['course_id']; ?>"
                    class="btn btn-success">

                        <i class="fa-solid fa-folder-open"></i>

                        View Materials

                    </a>

                    <a href="assignments.php?course_id=<?= $course['course_id']; ?>"
                    class="btn btn-primary">

                        <i class="fa-solid fa-file-lines"></i>

                        View Assignments

                    </a>

                <?php }else{ ?>

                    <a href="?enroll=<?= $course['course_id']; ?>"
                    class="btn btn-warning"
                    onclick="return confirm('Do you want to enroll in this course?')">

                        <i class="fa-solid fa-user-plus"></i>

                        Enroll Now

                    </a>

                <?php } ?>

            </div>

        </div>

    </div>

</div>

<?php

}

?>

</div>

<footer class="text-center py-4">

    <p class="text-muted mb-0">

        © <?= date("Y"); ?>

        Learning Management System | Student Courses

    </p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>