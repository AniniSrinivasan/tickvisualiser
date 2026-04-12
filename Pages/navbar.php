<?php
include('../functions/admin-check.php');

if (($_SESSION['role']== "2")) {
// <!-- top navigation -->
echo'
<header class="top-nav">
    <div class="nav-container">
        <div class="logo-title">Tick Visualiser</div>

        <!-- default hidden on large screens, only appears on mobile screens -->
        <button class="menu-toggle" aria-label="Toggle Menu">
            ☰
        </button>

        <div class="top-menu">
            <a href="dashboard.php">Home</a>
            <a href="insights.php">Insights</a>
            <a href="browse-data.php">Browse Data</a>
            <a href="approve-data.php">Approve Data</a>
            <a href="manage-user.php">Manage User</a>
            <a href="contact.php">Contact</a>
            <a href="login.php">Login</a>
        </div>
    </div>
</header>
';
}
elseif (($_SESSION['role']== "1")) {
    echo'
    <header class="top-nav">
        <div class="nav-container">
            <div class="logo-title">Tick Visualiser</div>
            <button class="menu-toggle" aria-label="Toggle Menu">
                ☰
            </button>
            <div class="top-menu">
                <a href="dashboard.php">Home</a>
                <a href="insights.php">Insights</a>
                <a href="contact.php">Contact</a>
                <a href="login.php">Login</a>
            </div>
        </div>
    </header>
    ';
    }
?>