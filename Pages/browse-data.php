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
                <form class="manage-select-form" method="get">
                    <label for="uploaded-file-select">Select a previously uploaded file</label>
                    <select id="uploaded-file-select" name="uploaded-file-select" class="manage-select-input"
                        onchange="this.form.submit()">
                        <option value="">Select a file to view</option>
                        <?php foreach ($storedFiles as $file): ?>
                            <option value="<?php echo escape($file['upload_id']); ?>"
                                <?php echo (isset($_GET['uploaded-file-select']) && (int) $_GET['uploaded-file-select'] === (int) $file['upload_id']) ? 'selected' : ''; ?>>
                                <?php echo escape($file['upload_id']); ?> . 
                                <?php echo escape(getOriginalFileName($file['display_name'])); ?> - uploaded 
                                <?php echo escape($file['uploaded_at']); ?>
                         </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <br />

        <div class="dashboard-card manage-table-card">
            <div class="card-header">
                <h2>Browse Data</h2>
            </div>

            <form class="manage-toolbar">
                <input type="search" id="browse-data-search" name="browse-data-search" class="manage-toolbar-input"
                    placeholder="Search by ID, location, species or latin name" onkeyup="searchBrowswData(this)">
                <label class="manage-checkbox" for="show-inaccurate-only">
                    <input type="checkbox" id="show-inaccurate-only" name="show-inaccurate-only">
                    Show only inaccurate data
                </label>
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
            <?php $isEditing = ($editingRowId !== null && (string) $editingRowId === (string) ($row['ID'] ?? '')); ?>

            <?php if ($isEditing): ?>
                <tr>
                    <td>
                        <form method="post">
                            <?php echo escape($row['ID'] ?? ''); ?>
                            <input type="hidden" name="row-id" value="<?php echo escape($row['ID'] ?? ''); ?>">
                            <input type="hidden" name="uploaded-file-select" value="<?php echo escape($selectedUploadId); ?>">
                    </td>
                    <td>
                            <input type="text" name="date_time" value="<?php echo escape($row['DATE_TIME'] ?? ''); ?>" class="manage-toolbar-input">
                    </td>
                    <td>
                            <input type="text" name="location" value="<?php echo escape($row['LOCATION'] ?? ''); ?>" class="manage-toolbar-input">
                    </td>
                    <td>
                            <input type="text" name="species" value="<?php echo escape($row['SPECIES'] ?? ''); ?>" class="manage-toolbar-input">
                    </td>
                    <td>
                            <input type="text" name="latin_name" value="<?php echo escape($row['LATINNAME'] ?? ''); ?>" class="manage-toolbar-input">
                    </td>
                    <td>
                            <input type="submit" class="approve-button-in-list" name="save-row" value="Save">
                            <input type="submit" class="reject-button-in-list" name="cancel-row" value="Cancel">
                        </form>
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td><?php echo escape($row['ID'] ?? ''); ?></td>
                    <td><?php echo escape($row['DATE_TIME'] ?? ''); ?></td>
                    <td><?php echo escape($row['LOCATION'] ?? ''); ?></td>
                    <td><?php echo escape($row['SPECIES'] ?? ''); ?></td>
                    <td><?php echo escape($row['LATINNAME'] ?? ''); ?></td>
                    <td>
                    <form method="post">
                        <input type="hidden" name="row-id" value="<?php echo escape($row['ID'] ?? ''); ?>">
                        <input type="hidden" name="uploaded-file-select" value="<?php echo escape($selectedUploadId); ?>">
                        <input type="submit" class="approve-button-in-list" name="edit-row" value="Edit">
                        <input type="button" class="reject-button-in-list" name="reject" value="Delete">
                    </form>
                    </td>
                </tr>
            <?php endif; ?>
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