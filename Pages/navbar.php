<?php
include('../functions/session.php');

//Admin
if (($_SESSION['role_id']== "2")) {
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
            <a href="manage-user.php">Manage User</a>
            <a href="contact.php">Contact</a>
            <a href="admin-register.php">Register an Admin</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>
';
}

//User
else{
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
                <a href="contact.php">Contact</a>';
                if (($_SESSION['role_id']!= "1")) {
                    echo '<a href="login.php">Login</a>';
                }
                echo '<a href="logout.php">Logout</a>';
            echo '</div>
        </div>
    </header>
    ';
    }
?>