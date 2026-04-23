<?php
include('../functions/session.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Register page">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/login.css">
    <link rel="stylesheet" href="../style/style.css">
    <script src="../script/script.js"></script>
</head>

<?php
require_once '../functions/error.php'; 

$error="";

function addUser($user_email, $user_hash_password, $f_name, $l_name, $role_id)
{
    include_once('../functions/db_connect.php');

    $stmt=$conn->prepare("INSERT INTO users(user_email, user_hash_password, f_name, l_name, role_id)
    VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssi', $user_email, $user_hash_password, $f_name, $l_name, $role_id);
    // echo $user__hash_password;
    echo $_POST["user_hash_password"];
    $success=$stmt->execute();

    $stmt->close();
    $conn->close();

    return $success;
}

if (isset($_POST['createUser'])){

    //if time do special chars, capital and number needed for password

    if (strlen($_POST["user_hash_password"])<8 || strlen($_POST["user_hash_password"])>20){   
        $error= "Password must be between 8 and 20 characters!";
    }

    elseif ($_POST["user_hash_password"]!==$_POST["confirmPassword"]){
        $error= "Passwords must be identical, \n They must match!";
    }
    else{ 
        $password=$_POST['user_hash_password'];
        $hash=password_hash($password, PASSWORD_DEFAULT);

        //users role id 
        $role_id= 1;

        addUser($_POST['user_email'],
            $hash,
            $_POST['f_name'],
            $_POST['l_name'], 
            $role_id 
        );

        header('Location: login.php');
    }
}
try{
if ($_SESSION['role_id'] == 2) {
    echo '<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div> <!-- Navbar will be loaded here -->';

}else{
    echo '<body>';
}
} catch (Exception $e) {
    echo '<body>';
}
?>
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
                    <input type="text" name="f_name" placeholder="Forename" required  />
                </label>

                <label class="field">
                    <span>Last Name</span>
                    <input type="text" name="l_name" placeholder="Surname" required />
                </label>

                <label class="field">
                    <span>Email</span>
                    <input type="email" name="user_email" placeholder="you@example.com" required />
                </label>

                <label class="field">
                    <span>Password</span>
                    <input type="password" name="user_hash_password" placeholder="••••••••" required />
                </label>

                <label class="field">
                    <span>Confirm Password</span>
                    <input type="password" name="confirmPassword" placeholder="••••••••" required />
                </label>

                <?php
                if (!empty($error)): ?>
                    <p style="color: red; margin-bottom:10px;"> 
                        <?php echo $error; ?> 
                    </p>
                <?php endif; ?>

                <button type="submit" name="createUser" class="btn-primary btn-full">Create A User Account</button>
        
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

