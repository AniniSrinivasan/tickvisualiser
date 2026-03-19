<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Register page">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/login.css">
</head>

<?php

include_once('../functions/db_connect.php');

function addUser($user_email, $user_password, $f_name, $l_name, $role_id)
{
    $created=false;

    $stmt=$conn->prepare("INSERT INTO users(user_email, user_password, f_name, l_name, role_id)
    VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssi', $user_email, $user_password, $f_name, $l_name, $role_id);

    $user_email = $_POST['user_email'];
    $user_password = $_POST['user_password'];
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $role_id = $_POST['role_id'];
    $stmt->execute();

    if ($stmt){
        $created=true;
    }
    $stmt->close();
    $conn->close();
}

if (isset($_POST[]))

?>

<body>
    <section class="banner-section">
        <div class="content-container">
            <h1>Welcome!</h1>
            <p><b>Create an account to access your dashboard and uploads</b></p>
        </div>
    </section>

    <main class="auth-wrap-two" role="main">
        <section class="auth-card" aria-label="Register">
            <div class="auth-header">
                <h2>Register</h2>
                <span class="badge">Secure</span>
            </div>

            <form class="auth-form" method="post" action="#">
                <label class="field">
                    <span>First Name</span>
                    <input type="text" name="FName" placeholder="Forename" required  autocomplete="off"/>
                </label>

                <label class="field">
                    <span>Last Name</span>
                    <input type="text" name="LName" placeholder="Surname" required />
                </label>

                <label class="field">
                    <span>Email</span>
                    <input type="email" name="email" placeholder="you@example.com" required />
                </label>

                <label class="field">
                    <span>Password</span>
                    <input type="password" name="password" placeholder="••••••••" required />
                </label>

                <label class="field">
                    <span>Confirm Password</span>
                    <input type="password" name="confirmPassword" placeholder="••••••••" required />
                </label>

                <button type="submit" class="btn-primary btn-full">Create Account</button>
        
                <p class="helper"> Want to Login instead? <a class="link" href="login.php">Login</a></p>
            </form>
        </section>
    </main><br />

    <script>
        // mobile toggle
        const toggle = document.querySelector(".menu-toggle");
        const menu = document.querySelector(".top-menu");
        if (toggle && menu) {
            toggle.addEventListener("click", () => menu.classList.toggle("open"));
        }
    </script>
</body>

</html>

