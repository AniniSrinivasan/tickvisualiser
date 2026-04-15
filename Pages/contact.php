<?php
include('../functions/session.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Contact page">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/style.css">
    <script src="../script/script.js"></script>
</head>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div> <!-- Navbar will be loaded here -->

    <section class="banner-section">
        <div class="content-container">
            <h1>Contact our team</h1>
            <p>For general enquiries or dataset questions, get in touch with the platform team using the contact
                details</p>
        </div>
    </section>

        <main class="contact-container" role="main">
            <div class="contact-grid">
                <div class="contact-card"> 
                    <span class="larger-font">For general enquiries contact:</span><br /><br />
                    <p class="contact-email-box">example1@email.com</p>
                    <p class="contact-email-box">example2@email.com</p>
                    <p class="contact-email-box">example3@email.com</p>
                </div>
                
                <div class="contact-card"> 
                    <span class="larger-font">For more info about us: </span><br />
                    <span>To learn more about us visit: </span>
                    <a href="https://my.elanco.com/en_gb/about/about-elanco"
                        class="contact-link">https://www.elanco.com/us</a>
                    <a href="dashboard.php" class="contact-btn">Return to our dashboard</a>
                    <a href="login.php" class="contact-btn">Login to your account</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>