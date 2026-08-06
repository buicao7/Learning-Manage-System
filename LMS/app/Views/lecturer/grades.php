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
   SAVE GRADE
========================================= */

if(isset($_POST['save_grade']))
{

    $submission_id = intval($_POST['submission_id']);
    $score = $_POST['score'];
    $feedback = mysqli_real_escape_string($conn,$_POST['feedback']);

    $check = mysqli_query($conn,"
    SELECT *
    FROM grades
    WHERE submission_id='$submission_id'
    ");

    if(mysqli_num_rows($check)>0)
    {

        mysqli_query($conn,"
        UPDATE grades
        SET
            score='$score',
            feedback='$feedback',
            graded_at=NOW()
        WHERE submission_id='$submission_id'
        ");

    }
    else
    {

        mysqli_query($conn,"
        INSERT INTO grades
        (
            submission_id,
            score,
            feedback
        )
        VALUES
        (
            '$submission_id',
            '$score',
            '$feedback'
        )
        ");

    }

    header("Location: grades.php");
    exit();

}


/* =========================================
   LOAD SUBMISSIONS
========================================= */

$grades = mysqli_query($conn,"

SELECT

s.submission_id,

u.full_name,

c.course_name,

a.title,

s.submitted_at,

g.score,

g.feedback

FROM submissions s

INNER JOIN assignments a
ON s.assignment_id=a.assignment_id

INNER JOIN courses c
ON a.course_id=c.course_id

INNER JOIN users u
ON s.student_id=u.user_id

LEFT JOIN grades g
ON s.submission_id=g.submission_id

WHERE c.lecturer_id='$lecturer_id'

ORDER BY s.submitted_at DESC

");

include "sidebar.php";
?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Grade Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

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

<h2>

Grade Management

</h2>

<p>

View submissions and grade students.

</p>

</div>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fa-solid fa-marker me-2"></i>

Student Grades

</h5>

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Student</th>

<th>Course</th>

<th>Assignment</th>

<th>Submitted</th>

<th>Score</th>

<th>Status</th>

<th width="120">

Action

</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($grades)){ ?>

<tr>

<td>

<?= htmlspecialchars($row['full_name']); ?>

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

<?= $row['score']=="" ? "-" : $row['score']; ?>

</td>

<td>

<?php if($row['score']==""){ ?>

<span class="badge bg-danger">

Not Graded

</span>

<?php }else{ ?>

<span class="badge bg-success">

Graded

</span>

<?php } ?>

</td>

<td>

<button

class="btn btn-warning btn-sm gradeBtn"

data-id="<?= $row['submission_id']; ?>"

data-score="<?= $row['score']; ?>"

data-feedback="<?= htmlspecialchars($row['feedback']); ?>">

<i class="fa-solid fa-pen"></i>

</button>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>
<!-- =========================
     GRADE MODAL
========================= -->

<div class="modal fade" id="gradeModal" tabindex="-1">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form method="POST">

<div class="modal-header bg-warning">

<h5 class="modal-title">

<i class="fa-solid fa-marker me-2"></i>

Grade Student

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
name="submission_id"
id="submission_id">

<div class="row">

<div class="col-md-6">

<div class="mb-3">

<label class="form-label">

Score

</label>

<input
type="number"
class="form-control"
name="score"
id="score"
step="0.01"
min="0"
max="100"
placeholder="0 - 100"
required>

<small class="text-muted">

Enter score from 0 to 100

</small>

</div>

</div>

<div class="col-md-6">

<div class="mb-3">

<label class="form-label">

Grade Status

</label>

<input
type="text"
class="form-control"
value="Graded"
readonly>

</div>

</div>

</div>

<div class="mb-3">

<label class="form-label">

Feedback

</label>

<textarea
class="form-control"
name="feedback"
id="feedback"
rows="6"
placeholder="Enter feedback for student..."></textarea>

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
name="save_grade"
class="btn btn-success">

<i class="fa-solid fa-floppy-disk me-2"></i>

Save Grade

</button>

</div>

</form>

</div>

</div>

</div>

<br>

<div class="row">

<div class="col-lg-12">

<div class="card">

<div class="card-header bg-info text-white">

<h5 class="mb-0">

<i class="fa-solid fa-chart-column me-2"></i>

Grade Summary

</h5>

</div>

<div class="card-body">

<div class="row text-center">

<?php

$totalSubmission = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) total
FROM submissions s
INNER JOIN assignments a
ON s.assignment_id=a.assignment_id
INNER JOIN courses c
ON a.course_id=c.course_id
WHERE c.lecturer_id='$lecturer_id'
"))['total'];

$totalGraded = mysqli_fetch_assoc(mysqli_query($conn,"
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

$totalPending = $totalSubmission - $totalGraded;

$average = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT ROUND(AVG(score),2) avgScore
FROM grades g
INNER JOIN submissions s
ON g.submission_id=s.submission_id
INNER JOIN assignments a
ON s.assignment_id=a.assignment_id
INNER JOIN courses c
ON a.course_id=c.course_id
WHERE c.lecturer_id='$lecturer_id'
"));

$avg = $average['avgScore'];

if($avg=="")
{
    $avg="0.00";
}

?>

<div class="col-md-3">

<h2 class="text-primary">

<?= $totalSubmission ?>

</h2>

<p>Total Submissions</p>

</div>

<div class="col-md-3">

<h2 class="text-success">

<?= $totalGraded ?>

</h2>

<p>Graded</p>

</div>

<div class="col-md-3">

<h2 class="text-danger">

<?= $totalPending ?>

</h2>

<p>Pending</p>

</div>

<div class="col-md-3">

<h2 class="text-warning">

<?= $avg ?>

</h2>

<p>Average Score</p>

</div>

</div>

</div>

</div>

</div>

</div>
<footer class="text-center py-4">

<p class="text-muted mb-0">

© <?= date('Y'); ?>

Learning Management System |
Lecturer Grade Management

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

document.querySelectorAll(".gradeBtn").forEach(function(button){

    button.addEventListener("click",function(){

        document.getElementById("submission_id").value =
        this.dataset.id;

        document.getElementById("score").value =
        this.dataset.score;

        document.getElementById("feedback").value =
        this.dataset.feedback;

        var modal =
        new bootstrap.Modal(document.getElementById("gradeModal"));

        modal.show();

    });

});


/* =========================
   Score Validation
========================= */

document.getElementById("score").addEventListener("input",function(){

    let score=parseFloat(this.value);

    if(score<0)
    {
        this.value=0;
    }

    if(score>100)
    {
        this.value=100;
    }

});


/* =========================
   Table Search
========================= */

const search=document.createElement("input");

search.className="form-control mb-3";

search.placeholder="Search student or assignment...";

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

</script>

</body>

</html>