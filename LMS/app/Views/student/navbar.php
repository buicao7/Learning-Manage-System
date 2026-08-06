<?php
$name = $_SESSION['name'] ?? 'Student';
$role = 'Student';
$initial = strtoupper(substr($name, 0, 1));
?>

<div class="user-profile">

    <div class="avatar-circle">
        <?= htmlspecialchars($initial) ?>
    </div>

    <div class="user-info">
        <h5><?= htmlspecialchars($name) ?></h5>
        <span><?= $role ?></span>
    </div>

</div>

<div class="top-navbar">

    <div class="left">

        <h3>

            Student Dashboard

        </h3>

    </div>

    <div class="right">

        <div class="search-box">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                placeholder="Search...">

        </div>

        <div class="notification">

            <i class="fa-regular fa-bell"></i>

            <span class="badge bg-danger">

                3

            </span>

        </div>

        <div class="user-profile">

            <div class="avatar-circle">

                <?= htmlspecialchars($initial) ?>

            </div>

            <div class="user-info">

                <h5>

                    <?= htmlspecialchars($name) ?>

                </h5>

                <span>

                    <?= $role ?>

                </span>

            </div>

        </div>

    </div>

</div>