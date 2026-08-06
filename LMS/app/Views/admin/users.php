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
   CREATE USER
=========================== */

if(isset($_POST['add_user'])){

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $check = $conn->prepare("SELECT user_id FROM users WHERE email=?");
    $check->bind_param("s",$email);
    $check->execute();

    if($check->get_result()->num_rows==0){

        $stmt = $conn->prepare("
            INSERT INTO users
            (full_name,email,password,role)
            VALUES(?,?,?,?)
        ");

        $stmt->bind_param(
            "ssss",
            $full_name,
            $email,
            $password,
            $role
        );

        $stmt->execute();

        header("Location: users.php?success=added");
        exit();

    }else{

        header("Location: users.php?error=email");
        exit();

    }

}

/* ===========================
   UPDATE USER
=========================== */

if(isset($_POST['update_user'])){

    $id = $_POST['user_id'];

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if(!empty($_POST['password'])){

        $password = password_hash($_POST['password'],PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users
            SET
            full_name=?,
            email=?,
            password=?,
            role=?
            WHERE user_id=?
        ");

        $stmt->bind_param(
            "ssssi",
            $full_name,
            $email,
            $password,
            $role,
            $id
        );

    }else{

        $stmt = $conn->prepare("
            UPDATE users
            SET
            full_name=?,
            email=?,
            role=?
            WHERE user_id=?
        ");

        $stmt->bind_param(
            "sssi",
            $full_name,
            $email,
            $role,
            $id
        );

    }

    $stmt->execute();

    header("Location: users.php?success=updated");
    exit();

}

/* ===========================
   DELETE USER
=========================== */

if(isset($_GET['delete'])){

    $id = (int)$_GET['delete'];

    mysqli_query($conn,"
        DELETE FROM users
        WHERE user_id=$id
    ");

    header("Location: users.php?success=deleted");
    exit();

}

/* ===========================
   SEARCH
=========================== */

$search="";

if(isset($_GET['search'])){

    $search = trim($_GET['search']);

}

if($search!=""){

    $stmt=$conn->prepare("
        SELECT *
        FROM users
        WHERE
        full_name LIKE ?
        OR email LIKE ?
        ORDER BY user_id DESC
    ");

    $keyword="%".$search."%";

    $stmt->bind_param(
        "ss",
        $keyword,
        $keyword
    );

    $stmt->execute();

    $users=$stmt->get_result();

}else{

    $users=mysqli_query($conn,"
        SELECT *
        FROM users
        ORDER BY user_id DESC
    ");

}
?>
<?php include "../layouts/sidebar.php"; ?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Manage Users</title>

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

echo "User added successfully.";

break;

case "updated":

echo "User updated successfully.";

break;

case "deleted":

echo "User deleted successfully.";

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

Email already exists.

</div>

<?php } ?>

<div class="card shadow">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h4 class="mb-0">

<i class="fa-solid fa-users me-2"></i>

Manage Users

</h4>

<button
class="btn btn-light"
data-bs-toggle="modal"
data-bs-target="#addUser">

<i class="fa-solid fa-plus"></i>

Add User

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
placeholder="Search user..."
value="<?= htmlspecialchars($search) ?>">

</div>

<div class="col-md-2">

<button
class="btn btn-primary w-100">

Search

</button>

</div>

</form>

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Full Name</th>

<th>Email</th>

<th>Role</th>

<th>Created</th>

<th width="220">

Action

</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($users)){ ?>

<tr>

<td><?= $row['user_id']; ?></td>

<td><?= htmlspecialchars($row['full_name']); ?></td>

<td><?= htmlspecialchars($row['email']); ?></td>

<td>

<?php

if($row['role']=="admin"){

echo "<span class='badge bg-danger'>Admin</span>";

}elseif($row['role']=="lecturer"){

echo "<span class='badge bg-success'>Lecturer</span>";

}else{

echo "<span class='badge bg-primary'>Student</span>";

}

?>

</td>

<td>

<?= date("d/m/Y",strtotime($row['created_at'])) ?>

</td>

<td>

<button

class="btn btn-warning btn-sm editBtn"

data-id="<?= $row['user_id']; ?>"

data-name="<?= htmlspecialchars($row['full_name']); ?>"

data-email="<?= htmlspecialchars($row['email']); ?>"

data-role="<?= $row['role']; ?>"

data-bs-toggle="modal"

data-bs-target="#editUser">

<i class="fa-solid fa-pen"></i>

Edit

</button>

<a
href="?delete=<?= $row['user_id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this user?')">

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
     ADD USER MODAL
=========================== -->

<div class="modal fade" id="addUser" tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">

<form method="POST">

<div class="modal-header bg-primary text-white">

<h5 class="modal-title">

Add User

</h5>

<button
type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">

<label class="form-label">

Full Name

</label>

<input
type="text"
name="full_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Email

</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Password

</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Role

</label>

<select
name="role"
class="form-select"
required>

<option value="student">

Student

</option>

<option value="lecturer">

Lecturer

</option>

<option value="admin">

Admin

</option>

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
name="add_user"
class="btn btn-primary">

Save

</button>

</div>

</form>

</div>

</div>

</div>

<!-- ===========================
     EDIT USER MODAL
=========================== -->

<div class="modal fade" id="editUser" tabindex="-1">

<div class="modal-dialog">

<div class="modal-content">

<form method="POST">

<input
type="hidden"
name="user_id"
id="edit_id">

<div class="modal-header bg-warning">

<h5 class="modal-title">

Edit User

</h5>

<button
type="button"
class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<div class="mb-3">

<label>

Full Name

</label>

<input
type="text"
name="full_name"
id="edit_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Email

</label>

<input
type="email"
name="email"
id="edit_email"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

New Password

</label>

<input
type="password"
name="password"
class="form-control">

<small class="text-muted">

Leave blank to keep current password.

</small>

</div>

<div class="mb-3">

<label>

Role

</label>

<select
name="role"
id="edit_role"
class="form-select">

<option value="student">

Student

</option>

<option value="lecturer">

Lecturer

</option>

<option value="admin">

Admin

</option>

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
name="update_user"
class="btn btn-warning">

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

        document.getElementById("edit_name").value =
            this.dataset.name;

        document.getElementById("edit_email").value =
            this.dataset.email;

        document.getElementById("edit_role").value =
            this.dataset.role;

    });

});

</script>

</body>

</html>