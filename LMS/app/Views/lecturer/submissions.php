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

/* ==========================================
   DELETE SUBMISSION
========================================== */

if(isset($_GET['delete']))
{

    $id=intval($_GET['delete']);

    $sql=mysqli_query($conn,"
    SELECT file_path
    FROM submissions
    WHERE submission_id='$id'
    ");

    if($row=mysqli_fetch_assoc($sql))
    {

        if($row['file_path']!="")
        {

            $file="../../../".$row['file_path'];

            if(file_exists($file))
            {
                unlink($file);
            }

        }

    }

    mysqli_query($conn,"
    DELETE s
    FROM submissions s

    INNER JOIN assignments a
    ON s.assignment_id=a.assignment_id

    INNER JOIN courses c
    ON a.course_id=c.course_id

    WHERE
    s.submission_id='$id'

    AND

    c.lecturer_id='$lecturer_id'
    ");

    header("Location: submission.php");
    exit();

}

/* ==========================================
   LOAD SUBMISSIONS
========================================== */

$submissions=mysqli_query($conn,"

SELECT

s.submission_id,

u.full_name,

u.email,

c.course_name,

a.title,

s.file_path,

s.submitted_at,

g.score

FROM submissions s

INNER JOIN users u
ON s.student_id=u.user_id

INNER JOIN assignments a
ON s.assignment_id=a.assignment_id

INNER JOIN courses c
ON a.course_id=c.course_id

LEFT JOIN grades g
ON s.submission_id=g.submission_id

WHERE c.lecturer_id='$lecturer_id'

ORDER BY s.submitted_at DESC

");

include "sidebar.php";
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Submission Management</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
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

<h2>

Submission Management

</h2>

<p>

View and manage student submissions.

</p>

</div>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fa-solid fa-upload me-2"></i>

Student Submissions

</h5>

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>ID</th>

<th>Student</th>

<th>Course</th>

<th>Assignment</th>

<th>Submitted</th>

<th>Score</th>

<th>File</th>

<th width="180">

Action

</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($submissions)){ ?>

<tr>

<td>

<?= $row['submission_id']; ?>

</td>

<td>

<strong><?= htmlspecialchars($row['full_name']); ?></strong>

<br>

<small class="text-muted">

<?= htmlspecialchars($row['email']); ?>

</small>

</td>

<td>

<?= htmlspecialchars($row['course_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['title']); ?>

</td>

<td>

<?= date("d M Y H:i",strtotime($row['submitted_at'])); ?>

</td>

<td>

<?php if($row['score']==""){ ?>

<span class="badge bg-danger">

Not Graded

</span>

<?php }else{ ?>

<span class="badge bg-success">

<?= $row['score']; ?>

</span>

<?php } ?>

</td>

<td>

<?php if($row['file_path']!=""){ ?>

<a
href="../../../<?= $row['file_path']; ?>"
target="_blank"
class="btn btn-success btn-sm">

<i class="fa-solid fa-download"></i>

</a>

<?php }else{ ?>

<span class="badge bg-secondary">

No File

</span>

<?php } ?>

</td>

<td>

<button

class="btn btn-info btn-sm viewBtn"

data-id="<?= $row['submission_id']; ?>"

data-student="<?= htmlspecialchars($row['full_name']); ?>"

data-email="<?= htmlspecialchars($row['email']); ?>"

data-course="<?= htmlspecialchars($row['course_name']); ?>"

data-title="<?= htmlspecialchars($row['title']); ?>"

data-date="<?= date("d M Y H:i",strtotime($row['submitted_at'])); ?>"

data-score="<?= $row['score']=="" ? "Not Graded" : $row['score']; ?>"

data-file="<?= $row['file_path']; ?>">

<i class="fa-solid fa-eye"></i>

</button>

<a

href="?delete=<?= $row['submission_id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this submission?')">

<i class="fa-solid fa-trash"></i>

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>
<!-- =========================
     VIEW SUBMISSION MODAL
========================= -->

<div class="modal fade" id="viewModal">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<div class="modal-header bg-info text-white">

<h5>

<i class="fa-solid fa-eye me-2"></i>

Submission Details

</h5>

<button
type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="row">

<div class="col-md-6 mb-3">

<label class="fw-bold">

Student

</label>

<input
type="text"
id="student"
class="form-control"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">

Email

</label>

<input
type="text"
id="email"
class="form-control"
readonly>

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="fw-bold">

Course

</label>

<input
type="text"
id="course"
class="form-control"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold">

Assignment

</label>

<input
type="text"
id="assignment"
class="form-control"
readonly>

</div>

</div>

<div class="mb-3">

<label class="fw-bold">

Submitted At

</label>

<input
type="text"
id="submitted"
class="form-control"
readonly>

</div>

<div class="mb-3">

<label class="fw-bold">

Current Score

</label>

<input
type="text"
id="score"
class="form-control"
readonly>

</div>

<div class="mb-3">

<label class="fw-bold">

Submission File

</label>

<br>

<a
id="downloadFile"
href="#"
target="_blank"
class="btn btn-success">

<i class="fa-solid fa-download me-2"></i>

Download Submission

</a>

</div>

</div>

<div class="modal-footer">

<button
class="btn btn-secondary"
data-bs-dismiss="modal">

Close

</button>

</div>

</div>

</div>

</div>

<br>

<?php

$totalSubmission=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM submissions s
INNER JOIN assignments a
ON s.assignment_id=a.assignment_id
INNER JOIN courses c
ON a.course_id=c.course_id
WHERE c.lecturer_id='$lecturer_id'
"))['total'];

$totalGraded=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM grades g
INNER JOIN submissions s
ON g.submission_id=s.submission_id
INNER JOIN assignments a
ON s.assignment_id=a.assignment_id
INNER JOIN courses c
ON a.course_id=c.course_id
WHERE c.lecturer_id='$lecturer_id'
"))['total'];

$totalPending=$totalSubmission-$totalGraded;

?>

<div class="row">

<div class="col-md-4">

<div class="dashboard-card">

<i class="fa-solid fa-upload text-primary mb-3"></i>

<h2><?= $totalSubmission ?></h2>

<p>Total Submissions</p>

</div>

</div>

<div class="col-md-4">

<div class="dashboard-card">

<i class="fa-solid fa-circle-check text-success mb-3"></i>

<h2><?= $totalGraded ?></h2>

<p>Graded</p>

</div>

</div>

<div class="col-md-4">

<div class="dashboard-card">

<i class="fa-solid fa-clock text-danger mb-3"></i>

<h2><?= $totalPending ?></h2>

<p>Pending</p>

</div>

</div>

</div>
<footer class="text-center py-4">

<p class="text-muted mb-0">

© <?= date('Y'); ?>

Learning Management System |
Lecturer Submission Management

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* ==========================================
   VIEW SUBMISSION
========================================== */

document.querySelectorAll(".viewBtn").forEach(function(button){

    button.addEventListener("click",function(){

        document.getElementById("student").value =
        this.dataset.student;

        document.getElementById("email").value =
        this.dataset.email;

        document.getElementById("course").value =
        this.dataset.course;

        document.getElementById("assignment").value =
        this.dataset.title;

        document.getElementById("submitted").value =
        this.dataset.date;

        document.getElementById("score").value =
        this.dataset.score;

        let file=this.dataset.file;

        let download=document.getElementById("downloadFile");

        if(file!="")
        {

            download.href="../../../"+file;

            download.style.display="inline-block";

        }
        else
        {

            download.style.display="none";

        }

        new bootstrap.Modal(
            document.getElementById("viewModal")
        ).show();

    });

});


/* ==========================================
   SEARCH SUBMISSION
========================================== */

const search=document.createElement("input");

search.className="form-control mb-3";

search.placeholder="Search student, course or assignment...";

const cardBody=document.querySelector(".card-body");

cardBody.prepend(search);

search.addEventListener("keyup",function(){

    let value=this.value.toLowerCase();

    document.querySelectorAll("tbody tr").forEach(function(row){

        row.style.display=
        row.innerText.toLowerCase().includes(value)
        ? ""
        : "none";

    });

});


/* ==========================================
   HIGHLIGHT NOT GRADED
========================================== */

document.querySelectorAll("tbody tr").forEach(function(row){

    let score=row.cells[5].innerText.trim();

    if(score==="Not Graded")
    {
        row.style.background="#fff8f8";
    }

});

</script>

</body>

</html>