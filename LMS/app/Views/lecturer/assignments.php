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

/* =========================================
   ADD ASSIGNMENT
========================================= */

if (isset($_POST['add_assignment'])) {

    $course_id = (int)$_POST['course_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = date("Y-m-d H:i:s", strtotime($_POST['due_date']));

    $stmt = $conn->prepare("
        INSERT INTO assignments
        (course_id,title,description,due_date)
        VALUES(?,?,?,?)
    ");

    if(!$stmt){
        die($conn->error);
    }

    $stmt->bind_param(
        "isss",
        $course_id,
        $title,
        $description,
        $due_date
    );

    if($stmt->execute()){

        $_SESSION['success']="Assignment added successfully.";

    }else{

        $_SESSION['error']=$stmt->error;

    }

    $stmt->close();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

  
/* =========================================
   UPDATE ASSIGNMENT
========================================= */

if (isset($_POST['update_assignment'])) {

    $assignment_id = intval($_POST['assignment_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = date("Y-m-d H:i:s", strtotime($_POST['due_date']));

    $stmt = mysqli_prepare($conn, "
        UPDATE assignments
        SET title = ?, description = ?, due_date = ?
        WHERE assignment_id = ?
    ");

    if (!$stmt) {
        die("Prepare Error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sssi",
        $title,
        $description,
        $due_date,
        $assignment_id
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: assignments.php");
        exit();

    } else {

        die("Update Error: " . mysqli_stmt_error($stmt));

    }
}

/* =========================================
   DELETE ASSIGNMENT
========================================= */

if (isset($_GET['delete'])) {

    $assignment_id = intval($_GET['delete']);

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM assignments WHERE assignment_id = ?"
    );

    if (!$stmt) {
        die("Prepare Error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $assignment_id);

    if (mysqli_stmt_execute($stmt)) {

        header("Location: assignments.php");
        exit();

    } else {

        die("Delete Error: " . mysqli_stmt_error($stmt));

    }
}
/* =========================================
   GET COURSES
========================================= */

$courses = mysqli_query($conn, "
SELECT
    course_id,
    course_name
FROM courses
WHERE lecturer_id='$lecturer_id'
ORDER BY course_name
");

/* =========================================
   GET ASSIGNMENTS
========================================= */

$assignments = mysqli_query($conn, "
SELECT

a.assignment_id,
a.title,
a.description,
a.due_date,

c.course_id,
c.course_name,

(
SELECT COUNT(*)
FROM submissions s
WHERE s.assignment_id=a.assignment_id
) AS total_submit

FROM assignments a

INNER JOIN courses c
ON a.course_id=c.course_id

WHERE c.lecturer_id='$lecturer_id'

ORDER BY a.assignment_id DESC
");

include "sidebar.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Assignment Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

<div class="hero">

<h2>Assignment Management</h2>

<p>Create, edit and manage assignments.</p>

</div>

<div class="card">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="fa-solid fa-file-lines me-2"></i>

Assignment List

</h5>

<button
class="btn btn-light btn-sm"
data-bs-toggle="modal"
data-bs-target="#addModal">

<i class="fa-solid fa-plus"></i>

Add Assignment

</button>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-light">

<tr>

<th>ID</th>

<th>Course</th>

<th>Title</th>

<th>Deadline</th>

<th>Submissions</th>

<th width="180">Action</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($assignments)){ ?>

<tr>

<td>

<?= $row['assignment_id']; ?>

</td>

<td>

<?= htmlspecialchars($row['course_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['title']); ?>

</td>

<td>

<?= date("d M Y H:i",strtotime($row['due_date'])); ?>

</td>

<td>

<span class="badge bg-success">

<?= $row['total_submit']; ?>

</span>

</td>

<td>

<button

class="btn btn-warning btn-sm editBtn"

data-id="<?= $row['assignment_id']; ?>"

data-title="<?= htmlspecialchars($row['title']); ?>"

data-description="<?= htmlspecialchars($row['description']); ?>"

data-date="<?= date('Y-m-d\TH:i',strtotime($row['due_date'])); ?>">

<i class="fa-solid fa-pen"></i>

</button>

<a

href="?delete=<?= $row['assignment_id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this assignment?')">

<i class="fa-solid fa-trash"></i>

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>
<!-- ================= ADD MODAL ================= -->

<div class="modal fade" id="addModal" tabindex="-1">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form method="POST">

<div class="modal-header bg-primary text-white">

<h5 class="modal-title">

Add Assignment

</h5>

<button
type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="mb-3">

<label class="form-label">

Course

</label>

<select
name="course_id"
class="form-select"
required>

<?php
mysqli_data_seek($courses,0);

while($course=mysqli_fetch_assoc($courses)){
?>

<option value="<?= $course['course_id']; ?>">

<?= htmlspecialchars($course['course_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Title

</label>

<input
type="text"
name="title"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
rows="4"></textarea>

</div>

<div class="mb-3">

<label class="form-label">

Due Date

</label>

<input
type="datetime-local"
name="due_date"
class="form-control"
required>

</div>

</div>

<div class="modal-footer">

<button
type="submit"
name="add_assignment"
class="btn btn-success">

<i class="fa-solid fa-floppy-disk"></i>

Save

</button>

</div>

</form>

</div>

</div>

</div>


<!-- ================= EDIT MODAL ================= -->

<div class="modal fade" id="editModal" tabindex="-1">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form method="POST">

<div class="modal-header bg-warning">

<h5 class="modal-title">

Edit Assignment

</h5>

<button
type="button"
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<input
type="hidden"
name="assignment_id"
id="edit_id">

<div class="mb-3">

<label class="form-label">

Title

</label>

<input
type="text"
name="title"
id="edit_title"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
id="edit_description"
class="form-control"
rows="4"></textarea>

</div>

<div class="mb-3">

<label class="form-label">

Due Date

</label>

<input
type="datetime-local"
name="due_date"
id="edit_due_date"
class="form-control"
required>

</div>

</div>

<div class="modal-footer">

<button
type="submit"
name="update_assignment"
class="btn btn-warning">

<i class="fa-solid fa-pen"></i>

Update

</button>

</div>

</form>

</div>

</div>

</div>

</div>

<footer class="text-center py-4">

<p class="text-muted mb-0">

© <?= date('Y'); ?>

Learning Management System |
Lecturer Assignment Management

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

document.querySelectorAll(".editBtn").forEach(function(btn){

    btn.addEventListener("click",function(){

        document.getElementById("edit_id").value=this.dataset.id;

        document.getElementById("edit_title").value=this.dataset.title;

        document.getElementById("edit_description").value=this.dataset.description;

        document.getElementById("edit_due_date").value=this.dataset.date;

        new bootstrap.Modal(document.getElementById("editModal")).show();

    });

});

</script>

</body>
<?php

if(isset($_SESSION['success']))
{
?>

<div class="alert alert-success">

<?= $_SESSION['success']; ?>

</div>

<?php

unset($_SESSION['success']);

}

if(isset($_SESSION['error']))
{
?>

<div class="alert alert-danger">

<?= $_SESSION['error']; ?>

</div>

<?php

unset($_SESSION['error']);

}

?>

</html>