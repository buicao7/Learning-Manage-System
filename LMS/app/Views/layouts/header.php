<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>LMS Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="/Public/css/style.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">

    <div class="container-fluid">

        <a class="navbar-brand"
           href="index.php">

            LMS

        </a>

        <button class="navbar-toggler"
                data-bs-toggle="collapse"
                data-bs-target="#navbar">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse"
             id="navbar">

            <ul class="navbar-nav ms-auto">

                <?php if(isset($_SESSION['user'])): ?>

                <li class="nav-item">

                    <span class="nav-link">

                        <i class="bi bi-person-circle"></i>

                        <?= $_SESSION['user']['full_name']; ?>

                    </span>

                </li>

                <li class="nav-item">

                    <a class="nav-link"
                       href="index.php?controller=auth&action=logout">

                        Logout

                    </a>

                </li>

                <?php endif; ?>

            </ul>

        </div>

    </div>

</nav>

<div class="container mt-4">