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
   Upload Assignment
========================= */

if(isset($_POST['submit_assignment']))
{
    $assignment_id = intval($_POST['assignment_id']);

    $check = mysqli_query($conn,"
        SELECT *
        FROM submissions
        WHERE assignment_id='$assignment_id'
        AND student_id='$student_id'
    ");

    if(mysqli_num_rows($check)==0)
    {

        if(isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error']==0)
        {

            $folder="../../../Public/uploads/assignments/";

            if(!is_dir($folder))
            {
                mkdir($folder,0777,true);
            }

            $filename=time()."_".basename($_FILES['assignment_file']['name']);

            $target=$folder.$filename;

            if(move_uploaded_file($_FILES['assignment_file']['tmp_name'],$target))
            {

                mysqli_query($conn,"
                    INSERT INTO submissions
                    (
                        assignment_id,
                        student_id,
                        file_path
                    )
                    VALUES
                    (
                        '$assignment_id',
                        '$student_id',
                        '$filename'
                    )
                ");

                $success="Assignment submitted successfully.";

            }

        }

    }
    else
    {
        $error="You have already submitted this assignment.";
    }

}

/* =========================
   Load Assignments
========================= */

$sql="
SELECT

a.assignment_id,
a.title,
a.description,
a.due_date,

c.course_name,

u.full_name,

s.submission_id,
s.file_path,
s.submitted_at,

g.score,
g.feedback

FROM assignments a

INNER JOIN courses c
ON a.course_id=c.course_id

INNER JOIN users u
ON c.lecturer_id=u.user_id

INNER JOIN enrollments e
ON c.course_id=e.course_id

LEFT JOIN submissions s
ON
a.assignment_id=s.assignment_id
AND
s.student_id='$student_id'

LEFT JOIN grades g
ON s.submission_id=g.submission_id

WHERE
e.student_id='$student_id'

ORDER BY
a.due_date ASC
";

$result=mysqli_query($conn,$sql);
?>
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Assignments</title>

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

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="fa-solid fa-file-lines me-2"></i>

Assignments

</h4>

</div>

<div class="card-body">

<?php
if(isset($success))
{
?>

<div class="alert alert-success">

<?= $success ?>

</div>

<?php
}
?>

<?php
if(isset($error))
{
?>

<div class="alert alert-danger">

<?= $error ?>

</div>

<?php
}
?>

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Course</th>

<th>Assignment</th>

<th>Lecturer</th>

<th>Due Date</th>

<th>Status</th>

<th>Grade</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php
while($row=mysqli_fetch_assoc($result))
{

$status="Pending";

if($row['submission_id'])
{

$status="Submitted";

}

if(
strtotime($row['due_date'])<time()
&&
!$row['submission_id']
)
{

$status="Overdue";

}
?>

<tr>

<td>

<?= $row['course_name']; ?>

</td>

<td>

<b>

<?= $row['title']; ?>

</b>

<br>

<small>

<?= $row['description']; ?>

</small>

</td>

<td>

<?= $row['full_name']; ?>

</td>

<td>

<?= date("d/m/Y H:i",strtotime($row['due_date'])); ?>

</td>

<td>

<?php

if($status=="Submitted")
{

echo "<span class='badge bg-success'>Submitted</span>";

}
elseif($status=="Pending")
{

echo "<span class='badge bg-warning'>Pending</span>";

}
else
{

echo "<span class='badge bg-danger'>Overdue</span>";

}

?>

</td>

<td>

<?php

if($row['score']=="")
{

echo "-";

}
else
{

echo $row['score'];

}

?>

</td>

<td>
<?php
if(!$row['submission_id'] && strtotime($row['due_date'])>=time())
{
?>

<form method="POST"
      enctype="multipart/form-data">

    <input
        type="hidden"
        name="assignment_id"
        value="<?= $row['assignment_id']; ?>">

    <input
        type="file"
        name="assignment_file"
        class="form-control form-control-sm mb-2"
        required>

    <button
        type="submit"
        name="submit_assignment"
        class="btn btn-primary btn-sm w-100">

        <i class="fa-solid fa-upload"></i>

        Submit

    </button>

</form>

<?php
}
elseif($row['submission_id'])
{
?>

<a
href="../../../Public/uploads/assignments/<?= $row['file_path']; ?>"
target="_blank"
class="btn btn-success btn-sm">

<i class="fa-solid fa-download"></i>

Download

</a>

<?php
}
else
{
?>

<span class="text-danger">

Deadline Passed

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

<br>

<div class="card shadow">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fa-solid fa-chart-line me-2"></i>

Submission Summary

</h5>

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>

<th>Assignment</th>

<th>Status</th>

<th>Score</th>

<th>Feedback</th>

</tr>

</thead>

<tbody>

<?php

mysqli_data_seek($result,0);

while($row=mysqli_fetch_assoc($result))
{

$status="Pending";

if($row['submission_id'])
{
    $status="Submitted";
}

if(
strtotime($row['due_date'])<time()
&&
!$row['submission_id']
)
{
    $status="Overdue";
}

?>

<tr>

<td>

<?= $row['title']; ?>

</td>

<td>

<?php

if($status=="Submitted")
{
    echo "<span class='badge bg-success'>Submitted</span>";
}
elseif($status=="Pending")
{
    echo "<span class='badge bg-warning text-dark'>Pending</span>";
}
else
{
    echo "<span class='badge bg-danger'>Overdue</span>";
}

?>

</td>

<td>

<?= $row['score']!="" ? $row['score'] : "-" ?>

</td>

<td>

<?= $row['feedback']!="" ? $row['feedback'] : "-" ?>

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

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>