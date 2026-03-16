<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/login.css">
</head>

<!-- this register page is only for users -->

<body>
  
    <!-- top navigation -->
    <!-- <header class="top-nav">
        <div class="nav-container">
            <div class="logo-title">Tick Visualiser</div> -->

            <!-- default hidden on large screens, only appears on mobile screens -->
            <!-- <button class="menu-toggle" aria-label="Toggle Menu">
                ☰
            </button>

            <div class="top-menu">
                <a href="dashboard.php">Home</a>
                <a href="#">Browse Data</a>
                <a href="#">Insights</a>
                <a href="#">Upload</a>
                <a href="contact.php">Contact</a>
            </div>
        </div>
    </header> -->

    <section class="banner-section">
        <div class="content-container">
            <h1>Welcome!</h1>
            <h2>Create an account to access your dashboard and uploads</h2>
        </div>
    </section>

    <main class="auth-wrap-two" role="Register Page">
        <section class="auth-card" aria-label="Register">
            <div class="auth-header">
                <h2>Register</h2>
                <span class="badge">Secure</span>
            </div>

            <form class="auth-form" method="post" action="#">
                <label class="field">
                    <span>First Name</span>
                    <input type="text" name="FName" placeholder="Forename" required />
                </label>

                <label class="field">
                    <span>Last Name</span>
                    <input type="text" name="LName" placeholder="Surname" required />
                </label>

                <label class="field">
                    <span>Username/Email</span>
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
        
                <p class="helper"> Want to Login instead? <a class="link" href="register.php">Login</a></p>
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

