<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login • Tick Visualiser</title>
  <link rel="stylesheet" href="login.css" />
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
  <main class="auth-wrap">
    <section class="auth-card" aria-label="Login">
      <div class="auth-header">
        <h2>Log in</h2>
        <span class="badge">Secure</span>
      </div>

      <form class="auth-form" method="post" action="#">
        <label class="field">
          <span>Email</span>
          <input type="email" name="email" placeholder="you@example.com" autocomplete="email" required />
        </label>

        <label class="field">
          <span>Password</span>
          <input type="password" name="password" placeholder="••••••••" autocomplete="current-password" required />
        </label>

        <div class="form-row">
          <label class="checkbox">
            <input type="checkbox" name="remember" />
            <span>Remember me</span>
          </label>

          <a class="link" href="#">Forgot password?</a>
        </div>

        <button type="submit" class="btn-primary btn-full">Sign in</button>

        <p class="helper">
          Don’t have an account?
          <a class="link" href="#">Create one</a>
        </p>
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