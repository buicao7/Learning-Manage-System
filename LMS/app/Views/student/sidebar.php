<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['PHP_SELF']);

$name = $_SESSION['name'] ?? 'Student';
$role = 'Student';
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

        <li class="<?= $current == 'dashboard.php' ? 'active' : '' ?>">
            <a href="dashboard.php">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>
        </li>

        <li class="<?= $current == 'courses.php' ? 'active' : '' ?>">
            <a href="courses.php">
                <i class="fa-solid fa-book"></i>
                My Courses
            </a>
        </li>

        <li class="<?= $current == 'materials.php' ? 'active' : '' ?>">
            <a href="materials.php">
                <i class="fa-solid fa-folder-open"></i>
                Materials
            </a>
        </li>

        <li class="<?= $current == 'assignments.php' ? 'active' : '' ?>">
            <a href="assignments.php">
                <i class="fa-solid fa-file-lines"></i>
                Assignments
            </a>
        </li>

        <li class="<?= $current == 'grades.php' ? 'active' : '' ?>">
            <a href="grades.php">
                <i class="fa-solid fa-chart-column"></i>
                Grades
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