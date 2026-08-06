<?php
session_start();

require_once "../../../config/database.php";

$db = new Database();
$conn = $db->connect();

$error = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {

                case 'admin':
                    header("Location: ../admin/dashboard.php");
                    exit();

                case 'lecturer':
                    header("Location: ../lecturer/dashboard.php");
                    exit();

                case 'student':
                    header("Location: ../student/dashboard.php");
                    exit();

                default:
                    $error = "Invalid account role.";
            }

        } else {

            $error = "Incorrect password.";

        }

    } else {

        $error = "Email does not exist.";

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Login - LMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<link rel="stylesheet"
href="../../../Public/css/style.css">

<style>

body{
background:#F4F7FB;
}

.login-section{
min-height:100vh;
display:flex;
align-items:center;
}

.login-image{
background:linear-gradient(135deg,#4F46E5,#6366F1);
border-radius:25px;
color:white;
padding:60px;
height:100%;
}

.login-image h1{
font-weight:700;
margin-bottom:20px;
}

.login-image p{
opacity:.9;
line-height:1.8;
}

.login-card{
background:white;
border-radius:25px;
padding:45px;
box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.logo{
font-size:30px;
font-weight:bold;
color:#4F46E5;
}

.form-control{
height:50px;
border-radius:12px;
}

.btn-login{
height:50px;
border-radius:12px;
background:#4F46E5;
color:white;
font-weight:600;
}

.btn-login:hover{
background:#4338CA;
color:white;
}

a{
text-decoration:none;
}

</style>

</head>

<body>

<section class="login-section">

<div class="container">

<div class="row g-5 align-items-center">

<div class="col-lg-6">

<div class="login-image">

<h1>🎓 LMS</h1>

<h3>Welcome Back</h3>

<p>

Login to access your courses,
assignments, grades and learning resources.

</p>

<hr>

<p>✔ Course Management</p>

<p>✔ Assignment Submission</p>

<p>✔ Grade Tracking</p>

<p>✔ Notifications</p>

</div>

</div>

<div class="col-lg-6">

<div class="login-card">

<div class="text-center mb-4">

<div class="logo">LMS</div>

<h2>Sign In</h2>

<p>Enter your account information</p>

</div>
<?php if($error!=""){ ?>

<div class="alert alert-danger">

<i class="fa-solid fa-circle-exclamation me-2"></i>

<?= $error; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Email

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fa-solid fa-envelope"></i>

</span>

<input
type="email"
name="email"
class="form-control"
placeholder="Enter your email"
required>

</div>

</div>

<div class="mb-4">

<label class="form-label">

Password

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fa-solid fa-lock"></i>

</span>

<input
type="password"
name="password"
class="form-control"
placeholder="Enter your password"
required>

</div>

</div>

<button
type="submit"
name="login"
class="btn btn-login w-100">

<i class="fa-solid fa-right-to-bracket me-2"></i>

Login

</button>

</form>

<div class="text-center mt-4">

Don't have an account?

<a href="register.php">

Register

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