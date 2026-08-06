<?php
$current = $_GET['page'] ?? '';

$name = $_SESSION['name'] ?? 'Administrator';
$role = ucfirst($_SESSION['role'] ?? 'Admin');
?>

<div class="sidebar">

    <div class="logo">

        <i class="fa-solid fa-graduation-cap"></i>

        <h2>LMS</h2>

    </div>

    <div class="profile">

        <img
            src="../../../Public/images/avatar.png"
            onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($name) ?>&background=2563eb&color=fff'"
            alt="Avatar">

        <h4><?= htmlspecialchars($name) ?></h4>

        <span><?= $role ?></span>

    </div>

    <ul class="sidebar-menu">

    <li>
        <a href="../admin/dashboard.php">
            <i class="fa-solid fa-chart-line"></i>
            Dashboard
        </a>
    </li>

    <li>
        <a href="../admin/users.php">
            <i class="fa-solid fa-users"></i>
            Users
        </a>
    </li>

    <li>
        <a href="../admin/courses.php">
            <i class="fa-solid fa-book"></i>
            Courses
        </a>
    </li>

    <li>
        <a href="../admin/enrollments.php">
            <i class="fa-solid fa-user-graduate"></i>
            Enrollments
        </a>
    </li>

    <li>
        <a href="../admin/reports.php">
            <i class="fa-solid fa-chart-pie"></i>
            Reports
        </a>
    </li>

    <li>
        <a href="../auth/login.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>
    </li>

</ul>

</div>