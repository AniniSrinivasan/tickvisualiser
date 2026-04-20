<?php
include("../functions/session.php");
include("../functions/manage-functions.php");
include_once('../functions/db_connect.php'); ?>
<?php require_once '../functions/upload-functions.php'; ?>
<?php require_once '../functions/error.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Total-Sightings • Tick Visualiser</title>
    <script src="../script/script.js" defer></script>
</head>

<?php
require_once '../functions/db_connect.php';
require_once '../functions/manage-functions.php';

$action = trim((string) ($_POST['ajax_action'] ?? ''));

if ($action) {
    handleUserAjaxRequest($conn, ''); // No upload directory for users
    exit;
}

if (isset($_POST['delete_user'])) {
    $user_email = trim((string) ($_POST['user_email'] ?? ''));

    if ($user_email !== '') {
        $deleted = deleteUser($conn, $user_email);
    }
}
?>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div>

    <!-- banner -->
    <section class="banner-section">
        <div class="content-container">
            <h1>Manage User</h1>
            <p> Manage all registered accounts</p>
        </div>
    </section>
    <main class="dashboard-container" role="main">
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Browse Users</h2>
                <a type="button" class="btn-primary" name="Create" href="register.php"> Create User </a>
            </div>
            <br />
            <div>
                <input type="search" id="browse-user-search" name="browse-user-search" class="manage-toolbar-input"
                    placeholder="Search by ID, Name or Email..." onkeyup="searchBrowswData(this)">
            </div>
            <div class="manage-table-wrapper">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $user = getUsers($conn); ?>
                        <?php foreach ($user as $row): ?>
                        <tr>
                            <td class="col-email" ><?php echo escape($row['user_email'] ?? ''); ?></td>
                            <td class="col-f_name" ><?php echo escape($row['f_name'] ?? ''); ?></td>
                            <td class="col-l_name" ><?php echo escape($row['l_name'] ?? ''); ?></td>
                            <td class="col-role_name"><?php echo escape($row['role_name'] ?? ''); ?></td>
                            <td class="col-action">
                                <form method="POST" action="" style="display:inline;">
                                    <button type="button" name = "update_user" class="approve-button-in-list" onclick="enableUserInlineEdit(this)">Edit</button>
                                    <input type="hidden" name="user_email" value="<?php echo escape($row['user_email'] ?? ''); ?>">
                                    <button type="submit" name="delete_user" class="reject-button-in-list" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="popup-confirmation" class="popup-overlay" style="display: none;">
            <div class="popup-box">
                <h3>Delete</h3>
                <p>Are you sure you want to delete this?</p>
                <div class="popup-actions">
                    <button id="confirm" type="submit" class="confirm" name="delete">Yes, Delete</button>
                    <button id="cancel" class="cancel">Cancel</button>
                </div>
            </div>
        </div>
    </main>
</body>

</html>