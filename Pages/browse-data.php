<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<?php require_once '../functions/upload-functions.php'; ?>
<?php require_once '../functions/error.php'; ?>

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

        <?php if ($uploadErrorMessage !== ''): ?>
            <div id="uploadError"><?php echo escape($uploadErrorMessage); ?></div>
        <?php else: ?>
            <div id="uploadError" style="display:none;"></div>
        <?php endif; ?>

        <?php if ($uploadSuccessMessage !== ''): ?>
            <div class="upload-success"><?php echo escape($uploadSuccessMessage); ?></div>
        <?php endif; ?>
    </section>

    <main class="dashboard-container" role="main">
        <div class="dashboard-grid manage-data-grid">
            <div class="dashboard-card manage-upload-card">
                <h2>Upload CSV Files</h2>

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
                    <select
                        id="uploaded-file-select"
                        name="uploaded-file-select"
                        class="manage-select-input"
                        onchange="this.form.submit()">
                        <option value="">Select a file to view</option>
                        <?php foreach ($storedFiles as $file): ?>
                            <option
                                value="<?php echo escape($file['upload_id']); ?>"
                                <?php echo ((int) $selectedUploadId === (int) $file['upload_id']) ? 'selected' : ''; ?>>
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
                <?php $totalRecords = count($csvRows); ?>
                <span class="year-badge">Total records: <?php echo $totalRecords; ?></span>
            </div>

            <form class="manage-toolbar" method="get">
                <!-- Keep the selected upload ID when using the search and filter options -->
            <input 
                type="hidden" 
                name="uploaded-file-select" 
                value="<?php echo escape($selectedUploadId); ?>">

                <input
                    type="search"
                    id="browse-data-search"
                    name="browse-data-search"
                    class="manage-toolbar-input"
                    placeholder="Search by ID, location, county, species or latin name"
                    onkeyup="searchBrowswData(this)">

                <label class="manage-checkbox" for="show-inaccurate-only">
                <!-- Hidden input to ensure a value is sent when the checkbox is unchecked     -->
                <input 
                    type="hidden" 
                    name="show-inaccurate-only" 
                    value="0">
                    <!-- inaccurate data based on id file uploaded -->
                <input 
                    type="checkbox" 
                    id="show-inaccurate-only" 
                    name="show-inaccurate-only"
                    value="1"
                    onchange="this.form.submit()"
                    <?php echo (isset($_GET['show-inaccurate-only']) && $_GET['show-inaccurate-only'] == '1') ? 'checked' : ''; ?>>
                    Show only inaccurate data
                </label>
            </form>
            <?php
            // Determine whether to show only inaccurate data based on the checkbox value
            $showInaccurateOnly = isset($_GET['show-inaccurate-only']) && $_GET['show-inaccurate-only'] == '1';
            ?>

            <div class="manage-table-wrapper">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>County</th>
                            <th>Species</th>
                            <th>Latin Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($csvRows)): ?>
                            <?php foreach ($csvRows as $row): ?>
                                <?php
                                $currentRowNum = (string) ($row['row_num'] ?? '');
                                $isEditing = ($editingRowId !== null && (string) $editingRowId === $currentRowNum);
                                ?>

                                <?php if ($isEditing): ?>
                                    <tr>
                                        <td>
                                            <form method="post">
                                                <?php echo escape($row['id'] ?? ''); ?>
                                                <input type="hidden" name="row-num" value="<?php echo escape($row['row_num'] ?? ''); ?>">
                                                <input type="hidden" name="row-id" value="<?php echo escape($row['id'] ?? ''); ?>">
                                                <input type="hidden" name="uploaded-file-select" value="<?php echo escape($selectedUploadId); ?>">
                                        </td>
                                        <td>
                                                <input
                                                    type="text"
                                                    name="date_time"
                                                    value="<?php echo escape($row['date_time'] ?? ''); ?>"
                                                    class="manage-toolbar-input">
                                        </td>
                                        <td>
                                                <input
                                                    type="text"
                                                    name="location_name"
                                                    value="<?php echo escape($row['location_name'] ?? ''); ?>"
                                                    class="manage-toolbar-input">
                                        </td>
                                        <td>
                                                <input
                                                    type="text"
                                                    name="county"
                                                    value="<?php echo escape($row['county'] ?? ''); ?>"
                                                    class="manage-toolbar-input">
                                        </td>
                                        <td>
                                                <input
                                                    type="text"
                                                    name="species_name"
                                                    value="<?php echo escape($row['species_name'] ?? ''); ?>"
                                                    class="manage-toolbar-input">
                                        </td>
                                        <td>
                                                <input
                                                    type="text"
                                                    name="species_latin_name"
                                                    value="<?php echo escape($row['species_latin_name'] ?? ''); ?>"
                                                    class="manage-toolbar-input">
                                        </td>
                                        <td>
                                                <input type="submit" class="approve-button-in-list" name="save-row" value="Save">
                                                <input type="submit" class="reject-button-in-list" name="cancel-row" value="Cancel">
                                            </form>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td><?php echo escape($row['id'] ?? ''); ?></td>
                                        <td><?php echo escape($row['date_time'] ?? ''); ?></td>
                                        <td><?php echo escape($row['location_name'] ?? ''); ?></td>
                                        <td><?php echo escape($row['county'] ?? ''); ?></td>
                                        <td><?php echo escape($row['species_name'] ?? ''); ?></td>
                                        <td><?php echo escape($row['species_latin_name'] ?? ''); ?></td>
                                        <td>
                                            <form method="post">
                                                <input type="hidden" name="row-num" value="<?php echo escape($row['row_num'] ?? ''); ?>">
                                                <input type="hidden" name="row-id" value="<?php echo escape($row['id'] ?? ''); ?>">
                                                <input type="hidden" name="uploaded-file-select" value="<?php echo escape($selectedUploadId); ?>">
                                                <input type="submit" class="approve-button-in-list" name="edit-row" value="Edit">
                                                <input type="submit" class="reject-button-in-list" name="reject" value="Delete">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">Upload a CSV file to view it.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

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