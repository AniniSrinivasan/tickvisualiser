<?php
session_start();
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

<body class="dashboard-body contact-page" onload="loadNavbar()">
    <div id="navbar-container"></div> <!-- Navbar will be loaded here -->

    <section class="banner-section">
        <div class="content-container">
            <h1>Contact our team</h1>
            <p>For general enquiries, data questions, or platform support, reach the team using the details below.</p>
        </div>
    </section>

    <main class="contact-container" role="main">
        <section class="contact-grid">
            <article class="contact-card contact-card-primary">
                <div class="contact-card-header">
                    <span class="contact-badge">Support channels</span>
                    <h2>Reach the Tick Visualiser team</h2>
                    <p>Select the most relevant contact and we will route your enquiry quickly.</p>
                </div>

                <div class="contact-method-list">
                    <div class="contact-method">
                        <span class="contact-method-label">General enquiries</span>
                        <a href="mailto:example1@email.com" class="contact-pill">example1@email.com</a>
                    </div>

                    <div class="contact-method">
                        <span class="contact-method-label">Dataset support</span>
                        <a href="mailto:example2@email.com" class="contact-pill">example2@email.com</a>
                    </div>

                    <div class="contact-method">
                        <span class="contact-method-label">Technical issues</span>
                        <a href="mailto:example3@email.com" class="contact-pill">example3@email.com</a>
                    </div>
                </div>
            </article>

            <aside class="contact-card contact-card-secondary">
                <div class="contact-card-header">
                    <span class="contact-badge">Company details</span>
                    <h2>Learn more and continue exploring</h2>
                    <p>Use the quick links below to read about Elanco or return to the application.</p>
                </div>

                <div class="contact-action-list">
                    <a href="https://my.elanco.com/en_gb/about/about-elanco" class="contact-action contact-action-link">
                        Visit Elanco
                    </a>
                    <a href="dashboard.php" class="contact-action">
                        Return to dashboard
                    </a>
                    <a href="login.php" class="contact-action contact-action-muted">
                        Login to your account
                    </a>
                </div>


            </aside>
        </section>
    </main>
</body>

</html>