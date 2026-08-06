<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../../../config/database.php";

$db = new Database();
$conn = $db->connect();

/* =====================================
   ADD COURSE
===================================== */

if (isset($_POST['add_course'])) {

    $course_name = trim($_POST['course_name']);
    $description = trim($_POST['description']);
    $lecturer_id = (int)$_POST['lecturer_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("
        INSERT INTO courses
        (course_name,description,lecturer_id,start_date,end_date)
        VALUES (?,?,?,?,?)
    ");

    $stmt->bind_param(
        "ssiss",
        $course_name,
        $description,
        $lecturer_id,
        $start_date,
        $end_date
    );

    if ($stmt->execute()) {
        header("Location: courses.php?success=added");
    } else {
        header("Location: courses.php?error=1");
    }

    exit();
}


/* =====================================
   UPDATE COURSE
===================================== */

if (isset($_POST['update_course'])) {

    $course_id = (int)$_POST['course_id'];

    $course_name = trim($_POST['course_name']);
    $description = trim($_POST['description']);
    $lecturer_id = (int)$_POST['lecturer_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("
        UPDATE courses
        SET
            course_name=?,
            description=?,
            lecturer_id=?,
            start_date=?,
            end_date=?
        WHERE course_id=?
    ");

    $stmt->bind_param(
        "ssissi",
        $course_name,
        $description,
        $lecturer_id,
        $start_date,
        $end_date,
        $course_id
    );

    if ($stmt->execute()) {
        header("Location: courses.php?success=updated");
    } else {
        header("Location: courses.php?error=1");
    }

    exit();
}


/* =====================================
   DELETE COURSE
===================================== */

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    mysqli_query(
        $conn,
        "DELETE FROM courses WHERE course_id=$id"
    );

    header("Location: courses.php?success=deleted");
    exit();
}


/* =====================================
   SEARCH
===================================== */

$search = "";

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);

}

if ($search != "") {

    $keyword = "%".$search."%";

    $stmt = $conn->prepare("
        SELECT
            c.*,
            u.full_name
        FROM courses c
        LEFT JOIN users u
        ON c.lecturer_id=u.user_id
        WHERE
            c.course_name LIKE ?
            OR
            u.full_name LIKE ?
        ORDER BY c.course_id DESC
    ");

    $stmt->bind_param(
        "ss",
        $keyword,
        $keyword
    );

    $stmt->execute();

    $courses = $stmt->get_result();

} else {

    $courses = mysqli_query($conn,"
        SELECT
            c.*,
            u.full_name
        FROM courses c
        LEFT JOIN users u
        ON c.lecturer_id=u.user_id
        ORDER BY c.course_id DESC
    ");

}


/* =====================================
   LECTURERS
===================================== */

$lecturers = mysqli_query($conn,"
    SELECT
        user_id,
        full_name
    FROM users
    WHERE role='lecturer'
    ORDER BY full_name
");

include "../layouts/sidebar.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Courses</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link rel="stylesheet" href="../../../Public/css/style.css">

<link rel="stylesheet" href="../../../Public/css/admin.css">

</head>

<body>

<div class="main">

<?php include "../layouts/navbar.php"; ?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>

<i class="fa-solid fa-book-open text-primary me-2"></i>

Manage Courses

</h2>

<button
class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addCourse">

<i class="fa-solid fa-plus me-2"></i>

Add Course

</button>

</div>

<?php if(isset($_GET['success'])){ ?>

<div class="alert alert-success alert-dismissible fade show">

Operation completed successfully.

<button
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<div class="card shadow-lg border-0">

<div class="card-body">

<form method="GET" class="row mb-4">

<div class="col-md-6">

<div class="input-group">

<span class="input-group-text">

<i class="fa fa-search"></i>

</span>

<input
type="text"
name="search"
class="form-control"
placeholder="Search course or lecturer..."
value="<?= htmlspecialchars($search) ?>">

<button class="btn btn-primary">

Search

</button>

</div>

</div>

</form>

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-primary">

<tr>

<th>ID</th>

<th>Course</th>

<th>Description</th>

<th>Lecturer</th>

<th>Start</th>

<th>End</th>

<th width="150" class="text-center">

Action

</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($courses)){ ?>

<tr>

<td>

<?= $row['course_id']; ?>

</td>

<td>

<?= htmlspecialchars($row['course_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['description']); ?>

</td>

<td>

<?= htmlspecialchars($row['full_name']); ?>

</td>

<td>

<?= date("d/m/Y",strtotime($row['start_date'])); ?>

</td>

<td>

<?= date("d/m/Y",strtotime($row['end_date'])); ?>

</td>

<td class="text-center">

<button
class="btn btn-warning btn-sm editBtn"

data-bs-toggle="modal"
data-bs-target="#editCourse"

data-id="<?= $row['course_id']; ?>"

data-name="<?= htmlspecialchars($row['course_name']); ?>"

data-description="<?= htmlspecialchars($row['description']); ?>"

data-lecturer="<?= $row['lecturer_id']; ?>"

data-start="<?= $row['start_date']; ?>"

data-end="<?= $row['end_date']; ?>">

<i class="fa fa-pen"></i>

</button>

<a
href="?delete=<?= $row['course_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this course?')">

<i class="fa fa-trash"></i>

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>
<!-- =========================
ADD COURSE
========================= -->

<div class="modal fade" id="addCourse" tabindex="-1">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form method="POST">

<div class="modal-header bg-primary text-white">

<h5 class="modal-title">

Add Course

</h5>

<button
type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Course Name

</label>

<input
type="text"
name="course_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Lecturer

</label>

<select
name="lecturer_id"
class="form-select"
required>

<option value="">Select Lecturer</option>

<?php mysqli_data_seek($lecturers,0); ?>

<?php while($lec=mysqli_fetch_assoc($lecturers)){ ?>

<option value="<?= $lec['user_id']; ?>">

<?= htmlspecialchars($lec['full_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
rows="4"
class="form-control"></textarea>

</div>

<div class="col-md-6">

<label class="form-label">

Start Date

</label>

<input
type="date"
name="start_date"
class="form-control"
required>

</div>

<div class="col-md-6">

<label class="form-label">

End Date

</label>

<input
type="date"
name="end_date"
class="form-control"
required>

</div>

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
name="add_course"
class="btn btn-primary">

<i class="fa-solid fa-save me-2"></i>

Save Course

</button>

</div>

</form>

</div>

</div>

</div>
<!-- =========================
EDIT COURSE
========================= -->

<div class="modal fade" id="editCourse" tabindex="-1">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form method="POST">

<input
type="hidden"
name="course_id"
id="edit_id">

<div class="modal-header bg-warning">

<h5 class="modal-title">

Edit Course

</h5>

<button
type="button"
class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Course Name

</label>

<input
type="text"
name="course_name"
id="edit_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Lecturer

</label>

<select
name="lecturer_id"
id="edit_lecturer"
class="form-select"
required>

<?php mysqli_data_seek($lecturers,0); ?>

<?php while($lec=mysqli_fetch_assoc($lecturers)){ ?>

<option value="<?= $lec['user_id']; ?>">

<?= htmlspecialchars($lec['full_name']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
id="edit_description"
rows="4"
class="form-control"></textarea>

</div>

<div class="col-md-6">

<label class="form-label">

Start Date

</label>

<input
type="date"
name="start_date"
id="edit_start"
class="form-control"
required>

</div>

<div class="col-md-6">

<label class="form-label">

End Date

</label>

<input
type="date"
name="end_date"
id="edit_end"
class="form-control"
required>

</div>

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
name="update_course"
class="btn btn-warning">

<i class="fa-solid fa-pen me-2"></i>

Update Course

</button>

</div>

</form>

</div>

</div>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

document.querySelectorAll(".editBtn").forEach(function(btn){

    btn.addEventListener("click",function(){

        document.getElementById("edit_id").value =
            this.dataset.id;

        document.getElementById("edit_name").value =
            this.dataset.name;

        document.getElementById("edit_description").value =
            this.dataset.description;

        document.getElementById("edit_lecturer").value =
            this.dataset.lecturer;

        document.getElementById("edit_start").value =
            this.dataset.start;

        document.getElementById("edit_end").value =
            this.dataset.end;

    });

});

</script>

</body>

</html>