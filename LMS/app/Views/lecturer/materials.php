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
   UPLOAD MATERIAL
========================================== */

if(isset($_POST['add_material']))
{

    $course_id = intval($_POST['course_id']);
    $title = mysqli_real_escape_string($conn,$_POST['title']);
    $description = mysqli_real_escape_string($conn,$_POST['description']);

    $file_path="";

    if(isset($_FILES['material']) && $_FILES['material']['error']==0)
    {

        $folder="../../../uploads/materials/";

        if(!file_exists($folder))
        {
            mkdir($folder,0777,true);
        }

        $filename=time()."_".basename($_FILES['material']['name']);

        move_uploaded_file(
            $_FILES['material']['tmp_name'],
            $folder.$filename
        );

        $file_path="uploads/materials/".$filename;

    }

    mysqli_query($conn,"
    INSERT INTO materials
    (
        course_id,
        title,
        description,
        file_path
    )
    VALUES
    (
        '$course_id',
        '$title',
        '$description',
        '$file_path'
    )
    ");

    header("Location: materials.php");
    exit();

}

/* ==========================================
   DELETE MATERIAL
========================================== */

if(isset($_GET['delete']))
{

    $id=intval($_GET['delete']);

    $sql=mysqli_query($conn,"
    SELECT file_path
    FROM materials
    WHERE material_id='$id'
    ");

    if($row=mysqli_fetch_assoc($sql))
    {

        $file="../../../".$row['file_path'];

        if(file_exists($file))
        {
            unlink($file);
        }

    }

    mysqli_query($conn,"
    DELETE m
    FROM materials m
    INNER JOIN courses c
    ON m.course_id=c.course_id
    WHERE
        m.material_id='$id'
    AND
        c.lecturer_id='$lecturer_id'
    ");

    header("Location: materials.php");
    exit();

}

/* ==========================================
   LOAD COURSES
========================================== */

$courses=mysqli_query($conn,"
SELECT
course_id,
course_name
FROM courses
WHERE lecturer_id='$lecturer_id'
ORDER BY course_name
");

/* ==========================================
   LOAD MATERIALS
========================================== */

$materials=mysqli_query($conn,"
SELECT

m.material_id,

m.title,

m.description,

m.file_path,

m.upload_date,

c.course_name

FROM materials m

INNER JOIN courses c
ON m.course_id=c.course_id

WHERE c.lecturer_id='$lecturer_id'

ORDER BY m.material_id DESC

");

include "sidebar.php";
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Material Management</title>

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

Material Management

</h2>

<p>

Upload and manage learning materials.

</p>

</div>

<div class="card">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="fa-solid fa-folder-open me-2"></i>

Course Materials

</h5>

<button
class="btn btn-light btn-sm"
data-bs-toggle="modal"
data-bs-target="#addModal">

<i class="fa-solid fa-plus"></i>

Upload Material

</button>

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>ID</th>

<th>Course</th>

<th>Title</th>

<th>Upload Date</th>

<th>File</th>

<th width="170">

Action

</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($materials)){ ?>

<tr>

<td>

<?= $row['material_id']; ?>

</td>

<td>

<?= htmlspecialchars($row['course_name']); ?>

</td>

<td>

<?= htmlspecialchars($row['title']); ?>

</td>

<td>

<?= date("d M Y",strtotime($row['upload_date'])); ?>

</td>

<td>

<?php if($row['file_path']!=""){ ?>

<a
class="btn btn-success btn-sm"
href="../../../<?= $row['file_path']; ?>"
target="_blank">

<i class="fa-solid fa-download"></i>

Download

</a>

<?php }else{ ?>

<span class="badge bg-secondary">

No File

</span>

<?php } ?>

</td>

<td>

<button

class="btn btn-warning btn-sm editBtn"

data-id="<?= $row['material_id']; ?>"

data-title="<?= htmlspecialchars($row['title']); ?>"

data-description="<?= htmlspecialchars($row['description']); ?>">

<i class="fa-solid fa-pen"></i>

</button>

<a

class="btn btn-danger btn-sm"

href="?delete=<?= $row['material_id']; ?>"

onclick="return confirm('Delete this material?')">

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
     FOOTER
========================= -->

<footer class="text-center py-4">

<p class="text-muted mb-0">

© <?= date('Y'); ?>

Learning Management System |
Lecturer Material Management

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* =========================
   EDIT MATERIAL
========================= */

document.querySelectorAll(".editBtn").forEach(function(button){

    button.addEventListener("click",function(){

        document.getElementById("edit_material_id").value =
        this.dataset.id;

        document.getElementById("edit_title").value =
        this.dataset.title;

        document.getElementById("edit_description").value =
        this.dataset.description;

        new bootstrap.Modal(
            document.getElementById("editModal")
        ).show();

    });

});

/* =========================
   SEARCH MATERIAL
========================= */

const search = document.createElement("input");

search.className = "form-control mb-3";

search.placeholder = "Search material...";

const cardBody = document.querySelector(".card-body");

cardBody.prepend(search);

search.addEventListener("keyup",function(){

    let value = this.value.toLowerCase();

    document.querySelectorAll("tbody tr").forEach(function(row){

        row.style.display =
        row.innerText.toLowerCase().includes(value)
        ? ""
        : "none";

    });

});

</script>

</body>

</html>
<?php
/*==========================================
UPDATE MATERIAL
==========================================*/

if(isset($_POST['update_material']))
{

    $material_id=intval($_POST['material_id']);

    $title=mysqli_real_escape_string($conn,$_POST['title']);

    $description=mysqli_real_escape_string($conn,$_POST['description']);

    $old=mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT file_path
    FROM materials
    WHERE material_id='$material_id'
    "));

    $file_path=$old['file_path'];

    if(isset($_FILES['material']) && $_FILES['material']['error']==0)
    {

        if($file_path!="")
        {
            $oldFile="../../../".$file_path;

            if(file_exists($oldFile))
            {
                unlink($oldFile);
            }
        }

        $folder="../../../uploads/materials/";

        if(!file_exists($folder))
        {
            mkdir($folder,0777,true);
        }

        $filename=time()."_".basename($_FILES['material']['name']);

        move_uploaded_file(
        $_FILES['material']['tmp_name'],
        $folder.$filename);

        $file_path="uploads/materials/".$filename;

    }

    mysqli_query($conn,"
    UPDATE materials
    SET

    title='$title',

    description='$description',

    file_path='$file_path'

    WHERE material_id='$material_id'
    ");

    header("Location: materials.php");

    exit();

}
?>

<!-- ==========================
ADD MATERIAL
=========================== -->

<div class="modal fade" id="addModal">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form method="POST" enctype="multipart/form-data">

<div class="modal-header bg-primary text-white">

<h5>

Upload Material

</h5>

<button
class="btn-close btn-close-white"
type="button"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="mb-3">

<label>

Course

</label>

<select
name="course_id"
class="form-select"
required>

<?php
mysqli_data_seek($courses,0);

while($course=mysqli_fetch_assoc($courses))
{
?>

<option value="<?= $course['course_id']?>">

<?= $course['course_name']?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label>

Title

</label>

<input
type="text"
name="title"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Description

</label>

<textarea
name="description"
class="form-control"
rows="4"></textarea>

</div>

<div class="mb-3">

<label>

Material File

</label>

<input
type="file"
name="material"
class="form-control"
accept=".pdf,.doc,.docx,.ppt,.pptx,.zip">

</div>

</div>

<div class="modal-footer">

<button
class="btn btn-success"
name="add_material">

Upload

</button>

</div>

</form>

</div>

</div>

</div>

<!-- ==========================
EDIT MATERIAL
=========================== -->

<div class="modal fade" id="editModal">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form method="POST" enctype="multipart/form-data">

<div class="modal-header bg-warning">

<h5>

Edit Material

</h5>

<button
class="btn-close"
type="button"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<input
type="hidden"
name="material_id"
id="edit_material_id">

<div class="mb-3">

<label>

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

<label>

Description

</label>

<textarea
name="description"
id="edit_description"
class="form-control"
rows="4"></textarea>

</div>

<div class="mb-3">

<label>

Replace File (optional)

</label>

<input
type="file"
name="material"
class="form-control"
accept=".pdf,.doc,.docx,.ppt,.pptx,.zip">

</div>

</div>

<div class="modal-footer">

<button
class="btn btn-warning"
name="update_material">

Update

</button>

</div>

</form>

</div>

</div>

</div>