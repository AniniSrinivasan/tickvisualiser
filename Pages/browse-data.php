<!DOCTYPE html>
<html lang="en">

<?php require_once '../functions/upload-functions.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Manage uploaded tick data">
    <title>Browse Data • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/style.css">
    <script src="../script/script.js" defer></script>
</head>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div>

    <section class="banner-section">
        <div class="content-container">
            <h1>Upload Data</h1>
            <p>Upload CSV files, view previous uploads, and browse tick records.</p>
        </div>
    </section>

    <main class="dashboard-container" role="main">
        <div class="dashboard-grid manage-data-grid">
            <div class="dashboard-card manage-upload-card">
                <h2>Upload CSV Files</h2>

                <?php if ($uploadErrorMessage !== ''): ?>
                    <div id="uploadError"><?php echo escape($uploadErrorMessage); ?></div>
                <?php else: ?>
                    <div id="uploadError" style="display:none;"></div>
                <?php endif; ?>

                <?php if ($uploadSuccessMessage !== ''): ?>
                    <div class="upload-success"><?php echo escape($uploadSuccessMessage); ?></div>
                <?php endif; ?>

                <form id="csvUploadForm" class="manage-upload-form" method="post" enctype="multipart/form-data">
                    <div class="upload-box" id="uploadDropCard">
                        <p>
                            <label for="fileUpload">
                                Drag files here or choose a CSV file to upload
                            </label>
                        </p>
                        <input type="file" id="fileUpload" name="csv_files" accept=".csv">
                    </div>
                </form>
            </div>

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

        <div class="dashboard-card manage-table-card">
            <div class="card-header">
                <h2>Browse Data</h2>
                <span class="year-badge">Live View</span>
            </div>

            <form class="manage-toolbar">
                <input type="search" id="browse-data-search" name="browse-data-search" class="manage-toolbar-input"
                    placeholder="Search by ID, location, species or latin name" onkeyup="searchBrowswData(this)">
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
                        <?php if (!empty($csvRows)): ?>
                            <?php foreach ($csvRows as $row): ?>
                                <tr>
                                    <td><?php echo escape($row['id']); ?></td>
                                    <td><?php echo escape($row['date']); ?></td>
                                    <td><?php echo escape($row['location']); ?></td>
                                    <td><?php echo escape($row['species']); ?></td>
                                    <td><?php echo escape($row['latinName']); ?></td>
                                    <td>
                                        <form>
                                            <input type="submit" class="approve-button-in-list" name="approve" value="Edit">
                                            <input type="button" class="reject-button-in-list reject-btn" name="reject"
                                                value="Delete">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Upload a CSV file to view it.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- delete confirmation popup -->
        <div id="popup-confirmation" class="popup-overlay" style="display: none;">
            <div class="popup-box">
                <h3>Delete</h3>
                <p>Are you sure you want to delete this?</p>
                <div class="popup-actions">
                    <button id="confirm" class="confirm">Yes, Delete</button>
                    <button id="cancel" class="cancel">Cancel</button>
                </div>
            </div>
        </div>
    </main>

</body>

</html>