<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Manage uploaded tick data">
    <title>Browse Data • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/style.css">
    <script src="../script/app.js"></script>
</head>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div>

    <!-- banner -->
    <section class="banner-section">
        <div class="content-container">
            <h1>Upload Data</h1>
            <p>Upload CSV files, view previous uploads, and browse tick records.</p>
        </div>
    </section>

    <!-- main manage/browse data -->
    <main class="dashboard-container" role="main">
        <div class="dashboard-grid manage-data-grid">
            <!-- upload -->
            <div class="dashboard-card manage-upload-card">
                <h2>Upload CSV Files</h2>
                <form class="manage-upload-form">
                    <div class="upload-box" id="uploadBox">
                        <p><label for="csv-file-upload">Drag files here or choose a CSV file to upload</label></p>
                        <input type="file" id="csv-file-upload" name="csv-file-upload" accept=".csv" multiple>
                    </div>
                </form>
            </div>
            <!-- previously uploaded -->
            <div class="dashboard-card manage-upload-card">
                <h2>Previously Uploaded Files</h2>
                <form class="manage-select-form">
                    <label for="uploaded-file-select">Select a previously uploaded file</label>
                    <select id="uploaded-file-select" name="uploaded-file-select" class="manage-select-input">
                        <option value="">Select a file to view</option>
                        <option value="file1">file 1</option>
                        <option value="file2">file 2</option>
                    </select>
                </form>
            </div>
        </div>
        <br />

        <!-- browse data -->
        <div class="dashboard-card manage-table-card">
            <div class="card-header">
                <h2>Browse Data</h2>
                <span class="year-badge">Live View</span>
            </div>

            <form class="manage-toolbar">
                <input type="search" id="browse-data-search" name="browse-data-search" class="manage-toolbar-input"
                    placeholder="Search by ID, location or species">
                <label class="manage-checkbox" for="show-inaccurate-only">
                    <input type="checkbox" id="show-inaccurate-only" name="show-inaccurate-only">
                    Show only inaccurate data
                </label>
                <input type="button" class="btn-primary" value="Submit">
            </form>

            <div class="manage-table-wrapper">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Species</th>
                            <th>Latin Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>02WNholuSg6ndCk4c1dA</td>
                            <td>2026-03-14</td>
                            <td>London</td>
                            <td>Tick</td>
                            <td>Ixodes ricinus</td>
                            <td>
                                <form>
                                    <input type="submit" class="approve-button-in-list" name="approve" value="Edit">
                                    <input type="button" class="reject-button-in-list reject-btn" name="reject"
                                        value="Delete">
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- reject confirmation popup -->
        <div id="rejectPopup" class="popup-overlay" style="display: none;">
            <div class="popup-box">
                <h3>Delete</h3>
                <p>Are you sure you want to delete this?</p>
                <div class="popup-actions">
                    <button id="confirmReject" class="confirm-reject">Yes, Delete</button>
                    <button id="cancelReject" class="cancel-reject">Cancel</button>
                </div>
            </div>
        </div>
    </main>

</body>

</html>