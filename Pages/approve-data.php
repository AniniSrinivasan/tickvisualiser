<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Manage uploaded tick data">
    <title>Approve Data • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/style.css">
    <script src="../script/script.js"></script>
</head>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div>

    <!-- banner -->
    <section class="banner-section">
        <div class="content-container">
            <h1>Approve Data</h1>
            <p>Approve files uploaded by the users</p>
        </div>
    </section>

    <!-- approve data -->
    <main class="dashboard-container" role="main">

        <!-- approve data -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Approve Data</h2>
            </div>
            <div class="manage-table-wrapper">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>File Name</th>
                            <th>Uploaded By</th>
                            <th>Uploaded On</th>
                            <th>Review</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>tick_info_jan2026</td>
                            <td>James Thomson</td>
                            <td>10/02/2026</td>
                            <td>
                                <form>
                                    <input type="submit" class="pending-button-in-list" name="pending"
                                        value="Pending Review">
                                    <input type="submit" class="complete-button-in-list" name="pending"
                                        value="Review Complete">
                                </form>
                            </td>
                            <td>
                                <form>
                                    <input type="submit" class="approve-button-in-list" name="approve" value="Approve">
                                    <input type="button" class="reject-button-in-list reject-btn" name="reject"
                                        value="Reject">
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- reject confirmation popup -->
        <div id="popup-confirmation" class="popup-overlay" style="display: none;">
            <div class="popup-box">
                <h3>Reject</h3>
                <p>Are you sure you want to reject this request?</p>
                <div class="popup-actions">
                    <button id="confirm" class="confirm">Yes, Reject</button>
                    <button id="cancel" class="cancel">Cancel</button>
                </div>
            </div>
        </div>
    </main>

</body>

</html>