<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../../config/database.php";

$db = new Database();
$conn = $db->connect();

/* ===========================
   CREATE ENROLLMENT
=========================== */

if(isset($_POST['add_enrollment'])){

    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];

    $check = $conn->prepare("
        SELECT enrollment_id
        FROM enrollments
        WHERE student_id=? AND course_id=?
    ");

    $check->bind_param("ii",$student_id,$course_id);

    $check->execute();

    if($check->get_result()->num_rows==0){

        $stmt = $conn->prepare("
            INSERT INTO enrollments(student_id,course_id)
            VALUES(?,?)
        ");

        $stmt->bind_param("ii",$student_id,$course_id);

        $stmt->execute();

        header("Location: enrollments.php?success=added");
        exit();

    }else{

        header("Location: enrollments.php?error=exists");
        exit();

    }

}

/* ===========================
   UPDATE ENROLLMENT
=========================== */

if(isset($_POST['update_enrollment'])){

    $id = $_POST['enrollment_id'];

    $student_id = $_POST['student_id'];

    $course_id = $_POST['course_id'];

    $stmt = $conn->prepare("
        UPDATE enrollments
        SET
            student_id=?,
            course_id=?
        WHERE enrollment_id=?
    ");

    $stmt->bind_param(
        "iii",
        $student_id,
        $course_id,
        $id
    );

    $stmt->execute();

    header("Location: enrollments.php?success=updated");
    exit();

}

/* ===========================
   DELETE
=========================== */

if(isset($_GET['delete'])){

    $id = (int)$_GET['delete'];

    mysqli_query($conn,"
        DELETE FROM enrollments
        WHERE enrollment_id=$id
    ");

    header("Location: enrollments.php?success=deleted");
    exit();

}

/* ===========================
   SEARCH
=========================== */

$search="";

if(isset($_GET['search'])){

    $search=trim($_GET['search']);

}

if($search!=""){

    $stmt=$conn->prepare("
        SELECT
            e.*,
            u.full_name,
            c.course_name
        FROM enrollments e
        INNER JOIN users u
        ON e.student_id=u.user_id
        INNER JOIN courses c
        ON e.course_id=c.course_id
        WHERE
            u.full_name LIKE ?
            OR
            c.course_name LIKE ?
        ORDER BY enrollment_id DESC
    ");

    $keyword="%".$search."%";

    $stmt->bind_param(
        "ss",
        $keyword,
        $keyword
    );

    $stmt->execute();

    $enrollments=$stmt->get_result();

}else{

    $enrollments=mysqli_query($conn,"
        SELECT
            e.*,
            u.full_name,
            c.course_name
        FROM enrollments e
        INNER JOIN users u
        ON e.student_id=u.user_id
        INNER JOIN courses c
        ON e.course_id=c.course_id
        ORDER BY enrollment_id DESC
    ");

}

/* ===========================
   STUDENTS
=========================== */

$students=mysqli_query($conn,"
SELECT
user_id,
full_name
FROM users
WHERE role='student'
ORDER BY full_name
");

/* ===========================
   COURSES
=========================== */

$courses=mysqli_query($conn,"
SELECT
course_id,
course_name
FROM courses
ORDER BY course_name
");
?>
<?php include "../layouts/sidebar.php"; ?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Manage Enrollments</title>

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

<?php if(isset($_GET['success'])){ ?>

<div class="alert alert-success alert-dismissible fade show">

<?php

switch($_GET['success']){

case "added":

echo "Enrollment added successfully.";

break;

case "updated":

echo "Enrollment updated successfully.";

break;

case "deleted":

echo "Enrollment deleted successfully.";

break;

}

?>

<button
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<?php if(isset($_GET['error'])){ ?>

<div class="alert alert-danger">

Student already enrolled in this course.

</div>

<?php } ?>

<div class="card shadow">

<div class="card-header bg-warning d-flex justify-content-between align-items-center">

<h4 class="mb-0">

<i class="fa-solid fa-user-graduate me-2"></i>

Manage Enrollments

</h4>

<button

class="btn btn-dark"

data-bs-toggle="modal"

data-bs-target="#addEnrollment">

<i class="fa-solid fa-plus"></i>

Add Enrollment

</button>

</div>

<div class="card-body">

<form
method="GET"
class="row mb-4">

<div class="col-md-4">

<input

type="text"

name="search"

class="form-control"

placeholder="Search student or course..."

value="<?= htmlspecialchars($search) ?>">

</div>

<div class="col-md-2">

<button

class="btn btn-warning w-100">

Search

</button>

</div>

</form>

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Student</th>

<th>Course</th>

<th>Enroll Date</th>

<th width="220">

Action

</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($enrollments)){ ?>

<tr>

<td>

<?= $row['enrollment_id'] ?>

</td>

<td>

<?= htmlspecialchars($row['full_name']) ?>

</td>

<td>

<?= htmlspecialchars($row['course_name']) ?>

</td>

<td>

<?= date("d/m/Y H:i",strtotime($row['enroll_date'])) ?>

</td>

<td>

<button

class="btn btn-warning btn-sm editBtn"

data-id="<?= $row['enrollment_id'] ?>"

data-student="<?= $row['student_id'] ?>"

data-course="<?= $row['course_id'] ?>"

data-bs-toggle="modal"

data-bs-target="#editEnrollment">

<i class="fa-solid fa-pen"></i>

Edit

</button>

<a

href="?delete=<?= $row['enrollment_id'] ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this enrollment?')">

<i class="fa-solid fa-trash"></i>

Delete

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>
<!-- ===========================
     ADD ENROLLMENT MODAL
=========================== -->

<div class="modal fade" id="addEnrollment" tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">

<form method="POST">

<div class="modal-header bg-warning">

<h5 class="modal-title">

Add Enrollment

</h5>

<button
type="button"
class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">

<label class="form-label">

Student

</label>

<select
name="student_id"
class="form-select"
required>

<option value="">Select Student</option>

<?php mysqli_data_seek($students,0); ?>

<?php while($student=mysqli_fetch_assoc($students)){ ?>

<option value="<?= $student['user_id']; ?>">

<?= htmlspecialchars($student['full_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Course

</label>

<select
name="course_id"
class="form-select"
required>

<option value="">Select Course</option>

<?php mysqli_data_seek($courses,0); ?>

<?php while($course=mysqli_fetch_assoc($courses)){ ?>

<option value="<?= $course['course_id']; ?>">

<?= htmlspecialchars($course['course_name']); ?>

</option>

<?php } ?>

</select>

</div>

</div>

<div class="modal-footer">

<button
type="button"
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>

<button
type="submit"
name="add_enrollment"
class="btn btn-warning">

Save

</button>

</div>

</form>

</div>

</div>

</div>

<!-- ===========================
     EDIT ENROLLMENT MODAL
=========================== -->

<div class="modal fade" id="editEnrollment" tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">

<form method="POST">

<input
type="hidden"
name="enrollment_id"
id="edit_id">

<div class="modal-header bg-primary text-white">

<h5 class="modal-title">

Edit Enrollment

</h5>

<button
type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">

<label>

Student

</label>

<select
name="student_id"
id="edit_student"
class="form-select"
required>

<?php mysqli_data_seek($students,0); ?>

<?php while($student=mysqli_fetch_assoc($students)){ ?>

<option value="<?= $student['user_id']; ?>">

<?= htmlspecialchars($student['full_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label>

Course

</label>

<select
name="course_id"
id="edit_course"
class="form-select"
required>

<?php mysqli_data_seek($courses,0); ?>

<?php while($course=mysqli_fetch_assoc($courses)){ ?>

<option value="<?= $course['course_id']; ?>">

<?= htmlspecialchars($course['course_name']); ?>

</option>

<?php } ?>

</select>

</div>

</div>

<div class="modal-footer">

<button
type="button"
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>

<button
type="submit"
name="update_enrollment"
class="btn btn-primary">

Update

</button>

</div>

</form>

</div>

</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

document.querySelectorAll(".editBtn").forEach(function(btn){

    btn.addEventListener("click",function(){

        document.getElementById("edit_id").value =
            this.dataset.id;

        document.getElementById("edit_student").value =
            this.dataset.student;

        document.getElementById("edit_course").value =
            this.dataset.course;

    });

});

</script>

</body>

</html>