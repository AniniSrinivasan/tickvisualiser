<?php 
include('../functions/session.php');
$_SESSION['role_id'] = 0; // Set role_id to 0 for guests
$error="";

if ($_SERVER['REQUEST_METHOD']==='POST'){
  include_once('../functions/db_connect.php');

  $email=$_POST['user_email'];
  $password=$_POST['password'];

  $stmt=$conn->prepare("SELECT * FROM users WHERE user_email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();

  $result= $stmt->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['user_hash_password'])){

    $_SESSION['email'] =$user['user_email'];
    $_SESSION['role_id'] =$user['role_id'];
    

    //change when navbar has been ammended for user and admin
    //role_id=1 when user and 2 when admin
    if ($user['role_id']==1){
      header("Location: dashboard.php");
    }
    else if($user['role_id']==2){
      header("Location: dashboard.php");
    }
    exit();
  } else{
    //do css for error message
    $error= "Invalid login credentials";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="description" content="Login Page" />
  <title>Login • Tick Visualiser</title>
  <link rel="stylesheet" href="../style/login.css" />
</head>

<body>

  <!-- banner -->
  <section class="banner-section">
    <div class="content-container">
      <h1>Welcome!</h1>
      <p class="banner-subtitle">Sign in to access your dashboard and uploads.</p>
    </div>
  </section>

  <!-- login card -->
  <main class="auth-wrap" role="main">
    <section class="auth-card" aria-label="Login">
      <div class="auth-header">
        <h2>Log in</h2>
        <span class="badge">Secure</span>
      </div>

      <form class="auth-form" method="post" action="">

        <label class="field">
          <span>Email</span>
          <input type="email" name="user_email" placeholder="you@example.com" autocomplete="email" required />
        </label>

        <label class="field">
          <span>Password</span>
          <input type="password" name="password" placeholder="••••••••" autocomplete="current-password" required />
        </label>

        <?php if(!empty($error)): ?>
          <p style="color: red; margin-bottom:10px;">
            <?php echo $error; ?>
          </p>
        <?php endif; ?>

        <button type="submit" name="signIn" value="1" class="btn-primary btn-full">Sign in</button>

        <p class="helper"> Don’t have an account? <a class="link" href="register.php">Create one</a></p>
      </form>
    </section>
  </main>

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