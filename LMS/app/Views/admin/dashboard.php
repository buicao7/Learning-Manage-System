<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../../config/database.php";

$db = new Database();
$conn = $db->connect();

/* =============================
   Dashboard Statistics
============================= */

$totalUsers = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM users"))['total'];

$totalLecturers = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM users WHERE role='lecturer'"))['total'];

$totalStudents = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM users WHERE role='student'"))['total'];

$totalCourses = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM courses"))['total'];

$totalAssignments = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM assignments"))['total'];

$totalEnrollments = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM enrollments"))['total'];

$totalMaterials = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM materials"))['total'];

$totalSubmissions = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) total FROM submissions"))['total'];

/* =============================
   Recent Courses
============================= */

$courses = mysqli_query($conn,"
SELECT
c.course_name,
u.full_name,
c.start_date,
c.end_date,
COUNT(e.student_id) total_students

FROM courses c

LEFT JOIN users u
ON c.lecturer_id=u.user_id

LEFT JOIN enrollments e
ON c.course_id=e.course_id

GROUP BY c.course_id

ORDER BY c.course_id DESC

LIMIT 5
");

/* =============================
   Recent Users
============================= */

$recentUsers = mysqli_query($conn,"
SELECT
full_name,
email,
role,
created_at

FROM users

ORDER BY user_id DESC

LIMIT 5
");

/* =============================
   Latest Enrollments
============================= */

$latestEnrollments = mysqli_query($conn,"
SELECT

u.full_name,

c.course_name,

e.enroll_date

FROM enrollments e

INNER JOIN users u
ON e.student_id=u.user_id

INNER JOIN courses c
ON e.course_id=c.course_id

ORDER BY e.enrollment_id DESC

LIMIT 5
");

include "../layouts/sidebar.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link rel="stylesheet"
href="../../../Public/css/style.css">
<link rel="stylesheet" href="../../../Public/css/admin.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{

background:#f4f7fc;

}

.main{

padding:20px;

}

.hero{

background:linear-gradient(135deg,#2563eb,#4f46e5);

padding:35px;

border-radius:20px;

color:#fff;

margin-bottom:30px;

box-shadow:0 15px 30px rgba(0,0,0,.15);

}

.dashboard-card{

background:#fff;

border-radius:20px;

padding:25px;

text-align:center;

box-shadow:0 10px 20px rgba(0,0,0,.08);

transition:.3s;

height:100%;

}

.dashboard-card:hover{

transform:translateY(-8px);

}

.dashboard-card i{

font-size:42px;

margin-bottom:15px;

}

.dashboard-card h2{

font-size:34px;

font-weight:bold;

margin-bottom:8px;

}

.dashboard-card p{

margin:0;

font-weight:500;

color:#666;

}

.card{

border:none;

border-radius:18px;

box-shadow:0 10px 25px rgba(0,0,0,.08);

}

.card-header{

font-weight:bold;

font-size:18px;

}

.table tbody tr:hover{

background:#f8f9fa;

}

.quick-btn{

margin-bottom:15px;

}

.badge{

font-size:13px;

}

</style>

</head>

<body>

<div class="main">

<?php include "../layouts/navbar.php"; ?>

<div class="container-fluid">
<!-- ================= HERO ================= -->

<div class="hero">

    <div class="d-flex justify-content-between align-items-center flex-wrap">

        <div>

            <h2 class="fw-bold">

                Welcome Back,

                <?= htmlspecialchars($_SESSION['name']); ?> 👋

            </h2>

            <p class="mb-0 fs-5"
             style="color:#1e3a8a;font-weight:600;">

             Manage users, courses, materials, assignments and monitor your Learning Management System.

        </p>

        </div>

        <div class="text-end">

            <h4><?= date("d M Y"); ?></h4>

            <span><?= date("l"); ?></span>

        </div>

    </div>

</div>

<!-- ================= STATISTICS ================= -->

<div class="row g-4">

    <div class="col-xl-3 col-lg-4 col-md-6">

        <div class="dashboard-card">

            <i class="fa-solid fa-users text-primary"></i>

            <h2><?= $totalUsers ?></h2>

            <p>Total Users</p>

        </div>

    </div>

    <div class="col-xl-3 col-lg-4 col-md-6">

        <div class="dashboard-card">

            <i class="fa-solid fa-user-tie text-success"></i>

            <h2><?= $totalLecturers ?></h2>

            <p>Lecturers</p>

        </div>

    </div>

    <div class="col-xl-3 col-lg-4 col-md-6">

        <div class="dashboard-card">

            <i class="fa-solid fa-user-graduate text-warning"></i>

            <h2><?= $totalStudents ?></h2>

            <p>Students</p>

        </div>

    </div>

    <div class="col-xl-3 col-lg-4 col-md-6">

        <div class="dashboard-card">

            <i class="fa-solid fa-book-open text-info"></i>

            <h2><?= $totalCourses ?></h2>

            <p>Courses</p>

        </div>

    </div>

</div>

<br>

<div class="row g-4">

    <div class="col-xl-3 col-lg-4 col-md-6">

        <div class="dashboard-card">

            <i class="fa-solid fa-folder-open text-secondary"></i>

            <h2><?= $totalMaterials ?></h2>

            <p>Materials</p>

        </div>

    </div>

    <div class="col-xl-3 col-lg-4 col-md-6">

        <div class="dashboard-card">

            <i class="fa-solid fa-file-lines text-danger"></i>

            <h2><?= $totalAssignments ?></h2>

            <p>Assignments</p>

        </div>

    </div>

    <div class="col-xl-3 col-lg-4 col-md-6">

        <div class="dashboard-card">

            <i class="fa-solid fa-upload text-success"></i>

            <h2><?= $totalSubmissions ?></h2>

            <p>Submissions</p>

        </div>

    </div>

    <div class="col-xl-3 col-lg-4 col-md-6">

        <div class="dashboard-card">

            <i class="fa-solid fa-user-check text-dark"></i>

            <h2><?= $totalEnrollments ?></h2>

            <p>Enrollments</p>

        </div>

    </div>

</div>

<br>

<!-- ================= RECENT COURSES ================= -->

<div class="row">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header bg-primary text-white">

                <i class="fa-solid fa-book-open me-2"></i>

                Recent Courses

            </div>

            <div class="card-body">

                <table class="table table-hover align-middle">

                    <thead>

                    <tr>

                        <th>Course</th>

                        <th>Lecturer</th>

                        <th>Students</th>

                        <th>Start</th>

                        <th>End</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php while($row=mysqli_fetch_assoc($courses)){ ?>

                        <tr>

                            <td>

                                <?= htmlspecialchars($row['course_name']); ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($row['full_name']); ?>

                            </td>

                            <td>

                                <span class="badge bg-primary">

                                    <?= $row['total_students']; ?>

                                </span>

                            </td>

                            <td>

                                <?= $row['start_date']; ?>

                            </td>

                            <td>

                                <?= $row['end_date']; ?>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
        <!-- ================= QUICK ACTIONS ================= -->

    <div class="col-lg-4">

        <div class="card">

            <div class="card-header bg-success text-white">

                <i class="fa-solid fa-bolt me-2"></i>

                Quick Actions

            </div>

            <div class="card-body">

                <a href="../admin/users.php"
                        class="btn btn-primary w-100 quick-btn">

                    <i class="fa-solid fa-users me-2"></i>

                        Manage Users

                </a>

                <a href="../admin/courses.php"
                    class="btn btn-success w-100 quick-btn">
                    <i class="fa-solid fa-book-open me-2"></i>
                    Manage Courses
                </a>

                <a href="../admin/enrollments.php"
                    class="btn btn-warning w-100 quick-btn">
                    <i class="fa-solid fa-user-check me-2"></i>
                    Manage Enrollments
                </a>

                <a href="../auth/login.php"
                   class="btn btn-dark w-100">

                    <i class="fa-solid fa-right-from-bracket me-2"></i>

                    Logout

                </a>

            </div>

        </div>

    </div>

</div>

<br>

<!-- ================= RECENT USERS ================= -->

<div class="row">

<div class="col-lg-6">

<div class="card">

<div class="card-header bg-dark text-white">

<i class="fa-solid fa-users me-2"></i>

Recent Users

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Name</th>

<th>Email</th>

<th>Role</th>

</tr>

</thead>

<tbody>

<?php while($user=mysqli_fetch_assoc($recentUsers)){ ?>

<tr>

<td>

<?= htmlspecialchars($user['full_name']); ?>

</td>

<td>

<?= htmlspecialchars($user['email']); ?>

</td>

<td>

<?php

$role=$user['role'];

if($role=="admin"){

echo "<span class='badge bg-danger'>Admin</span>";

}elseif($role=="lecturer"){

echo "<span class='badge bg-success'>Lecturer</span>";

}else{

echo "<span class='badge bg-primary'>Student</span>";

}

?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- ================= LATEST ENROLLMENTS ================= -->

<div class="col-lg-6">

<div class="card">

<div class="card-header bg-warning">

<i class="fa-solid fa-user-check me-2"></i>

Latest Enrollments

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Student</th>

<th>Course</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php while($enroll=mysqli_fetch_assoc($latestEnrollments)){ ?>

<tr>

<td>

<?= htmlspecialchars($enroll['full_name']); ?>

</td>

<td>

<?= htmlspecialchars($enroll['course_name']); ?>

</td>

<td>

<?= date("d/m/Y",strtotime($enroll['enroll_date'])); ?>

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

<!-- ================= CHART ================= -->

<div class="card">

<div class="card-header bg-primary text-white">

<i class="fa-solid fa-chart-column me-2"></i>

System Statistics

</div>

<div class="card-body">

<canvas id="dashboardChart" height="90"></canvas>

</div>

</div>

<script>

const ctx=document.getElementById('dashboardChart');

new Chart(ctx,{

type:'bar',

data:{

labels:[

'Users',

'Lecturers',

'Students',

'Courses',

'Materials',

'Assignments',

'Submissions',

'Enrollments'

],

datasets:[{

label:'LMS Statistics',

data:[

<?= $totalUsers ?>,

<?= $totalLecturers ?>,

<?= $totalStudents ?>,

<?= $totalCourses ?>,

<?= $totalMaterials ?>,

<?= $totalAssignments ?>,

<?= $totalSubmissions ?>,

<?= $totalEnrollments ?>

]

}]

},

options:{

responsive:true,

plugins:{

legend:{

display:false

}

},

scales:{

y:{

beginAtZero:true

}

}

}

});

</script>
<br>

<!-- ================= SYSTEM INFORMATION ================= -->

<div class="row mt-4">

    <div class="col-lg-4">

        <div class="card">

            <div class="card-header bg-info text-white">

                <i class="fa-solid fa-circle-info me-2"></i>

                System Information

            </div>

            <div class="card-body">

                <table class="table table-borderless">

                    <tr>

                        <td><strong>PHP Version</strong></td>

                        <td><?= phpversion(); ?></td>

                    </tr>

                    <tr>

                        <td><strong>Server</strong></td>

                        <td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>

                    </tr>

                    <tr>

                        <td><strong>Database</strong></td>

                        <td>MySQL</td>

                    </tr>

                    <tr>

                        <td><strong>Date</strong></td>

                        <td><?= date("d/m/Y H:i"); ?></td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card">

            <div class="card-header bg-success text-white">

                <i class="fa-solid fa-list-check me-2"></i>

                Management Modules

            </div>

            <div class="card-body">

                <ul class="list-group">

                    <li class="list-group-item">✔ User Management</li>

                    <li class="list-group-item">✔ Course Management</li>

                    <li class="list-group-item">✔ Enrollment Management</li>

                    <li class="list-group-item">✔ Material Management</li>

                    <li class="list-group-item">✔ Assignment Management</li>

                    <li class="list-group-item">✔ Submission Management</li>

                    <li class="list-group-item">✔ Grade Management</li>

                </ul>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card">

            <div class="card-header bg-warning">

                <i class="fa-solid fa-user me-2"></i>

                Current Administrator

            </div>

            <div class="card-body text-center">

                <i class="fa-solid fa-user-shield fa-5x text-primary mb-3"></i>

                <h4><?= htmlspecialchars($_SESSION['name']); ?></h4>

                <span class="badge bg-danger">

                    Administrator

                </span>

                <hr>

                <p>

                    You are logged in with administrator privileges.

                </p>

            </div>

        </div>

    </div>

</div>

<br>

<footer class="text-center py-4">

    <hr>

    <h6>

        Learning Management System

    </h6>

    <p class="text-muted mb-0">

        © <?= date("Y"); ?>

        LMS Management System.

        All Rights Reserved.

    </p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>