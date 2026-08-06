<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['PHP_SELF']);

$name = $_SESSION['name'] ?? 'Lecturer';
$role = 'Lecturer';
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

        <div class="profile-info">

            <h4><?= htmlspecialchars($name) ?></h4>

            <span><?= $role ?></span>

        </div>

    </div>

    <ul class="sidebar-menu">

        <li class="<?= $current == 'dashboard.php' ? 'active' : '' ?>">
            <a href="dashboard.php">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>
        </li>

        <li class="<?= $current == 'materials.php' ? 'active' : '' ?>">
            <a href="materials.php">
                <i class="fa-solid fa-folder-open"></i>
                Manage Materials
            </a>
        </li>

        <li class="<?= $current == 'assignments.php' ? 'active' : '' ?>">
            <a href="assignments.php">
                <i class="fa-solid fa-file-circle-plus"></i>
                Create Assignment
            </a>
        </li>

        <li class="<?= $current == 'submissions.php' ? 'active' : '' ?>">
            <a href="submissions.php">
                <i class="fa-solid fa-upload"></i>
                View Submissions
            </a>
        </li>

        <li class="<?= $current == 'grades.php' ? 'active' : '' ?>">
            <a href="grades.php">
                <i class="fa-solid fa-marker"></i>
                Grade Submission
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