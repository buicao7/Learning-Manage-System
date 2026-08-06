<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$name = $_SESSION['name'] ?? 'Student';
?>

<div class="topbar">

    <div class="page-title">
        <h2>Student Dashboard</h2>
    </div>

    <div class="topbar-right">

        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search...">
        </div>

        <div class="notification">
            <i class="fa-regular fa-bell"></i>
            <span class="badge">3</span>
        </div>

        <div class="user-box">

            <img
                src="../../../Public/images/avatar.png"
                onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($name) ?>&background=2563eb&color=fff'"
                alt="Avatar">

            <div>
                <h5><?= htmlspecialchars($name) ?></h5>
                <span>Student</span>
            </div>

        </div>

    </div>

</div>