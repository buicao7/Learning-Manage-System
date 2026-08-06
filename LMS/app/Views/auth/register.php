<?php
session_start();

require_once "../../../config/database.php";

$db = new Database();
$conn = $db->connect();

$message = "";
$error = "";

if(isset($_POST['register']))
{
    $full_name = mysqli_real_escape_string($conn,$_POST['full_name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $check = mysqli_query($conn,
    "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check)>0)
    {
        $error="Email already exists.";
    }
    else
    {
        $sql="INSERT INTO users
        (full_name,email,password,role)
        VALUES
        ('$full_name','$email','$password','$role')";

        if(mysqli_query($conn,$sql))
        {
            header("refresh:2;url=login.php");
            $message="Registration successful. Redirecting to Login...";
        }
        else
        {
            $error="Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Create Account - LMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link rel="stylesheet"
href="../../../Public/css/style.css">

<style>

body{
background:#F8FAFC;
}

.register-section{
min-height:100vh;
display:flex;
align-items:center;
padding:40px 0;
}

.register-card{
background:#fff;
border-radius:20px;
padding:40px;
box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.register-left{
text-align:center;
}

.register-left img{
width:80%;
}

.logo{
font-size:32px;
font-weight:bold;
color:#4F46E5;
}

.form-control,
.form-select{
height:48px;
border-radius:12px;
}

.btn-register{
background:#4F46E5;
color:white;
height:50px;
border-radius:12px;
font-weight:600;
}

.btn-register:hover{
background:#4338CA;
color:white;
}

a{
text-decoration:none;
}

</style>

</head>

<body>

<section class="register-section">

<div class="container">

<div class="row align-items-center">

<div class="col-lg-6 register-left">

<img src="../../../Public/images/1785812286049_6627994817496837772_6627994817496837772_cae6ee4624bf0228492ea4117beeec8a.jpg">

<h3 class="mt-4">

Join Our Learning Community

</h3>

<p class="text-muted">

Create an account and start learning today.

</p>

</div>

<div class="col-lg-6">

<div class="register-card">

<div class="text-center mb-4">

<div class="logo">

🎓 LMS

</div>

<h2>Create Account</h2>

<p class="text-muted">

Fill in your information below.

</p>

</div>
<?php if($message!=""){ ?>

<div class="alert alert-success">

<?= $message; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?= $error; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Full Name

</label>

<input
type="text"
name="full_name"
class="form-control"
placeholder="Enter your full name"
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
placeholder="Enter your email"
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
placeholder="Enter your password"
required>

</div>

<div class="mb-4">

<label class="form-label">

Role

</label>

<select
class="form-select"
name="role"
required>

<option value="student">

Student

</option>

<option value="lecturer">

Lecturer

</option>

</select>

</div>

<button
type="submit"
name="register"
class="btn btn-register w-100">

<i class="fa-solid fa-user-plus me-2"></i>

Create Account

</button>

</form>

<div class="text-center mt-4">

Already have an account?

<a href="login.php">

Login

</a>

</div>

<div class="text-center mt-3">

<a href="../../../index.php">

<i class="fa-solid fa-arrow-left me-1"></i>

Back Home

</a>

</div>

</div>

</div>

</div>

</div>

</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>