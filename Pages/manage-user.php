<?php
include("../functions/session.php");
include_once('../functions/db_connect.php');?>
<?php require_once '../functions/upload-functions.php'; ?>
<?php require_once '../functions/error.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Total-Sightings • Tick Visualiser</title>
    <script src="../script/script.js"></script>
</head>

<?php
function getUsers($conn)
{
    $query = "SELECT 
        u.user_email, u.f_name, u.l_name, r.role_name
        FROM users u
        INNER JOIN roles r ON u.role_id = r.role_id;
        ";
    $result = $conn->query($query);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}
function deleteUser(mysqli $conn, string $user_email): bool
{
    try {
        $sql = "DELETE FROM users
                WHERE user_email = $user_email
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $deleted = $stmt->affected_rows > 0;
        $stmt->close();

        return $deleted;
    } catch (Throwable $e) {
        return false;
    }
}

    if (isset($_POST['delete'])) {
        $user = getUsers($conn);
        foreach ($user as $row) {
        $user_email = $_POST['user_email'] ?? '';
        if ($user_email) {
            $success = deleteuser($user_email, $conn);
        }
        }
        exit;
}

function updateUser(mysqli $conn,int $rowNum,string $user_email,string $f_name,string $l_name,string $role_id
): bool {
    try {
        $user_email = getOrCreateSpecies($conn, $user_email, $user_email);
        $f_name = getLocationId($conn, $f_name);
        $l_name = normaliseDateTime($l_name);

        if ($user_email === '') {
            throw new RuntimeException('User email is required.');
        }

        $sql = "UPDATE users
                SET user_email = ?, f_name = ?, l_name = ?, role_id = ?
                WHERE user_email = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $user_email, $f_name, $l_name, $role_id, $user_email);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected >= 0;
    } catch (Throwable $e) {
        return false;
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
                        <tr>          
                            <?php $user = getUsers($conn); ?>
                            <?php foreach ($user as $row): ?>
                            <tr data-row-num="<?php echo escape($row['row_num'] ?? ''); ?>">
                                <td class="col-email"><?php echo escape($row['user_email'] ?? ''); ?></td>
                                <td class="col-f_name"><?php echo escape($row['f_name'] ?? ''); ?></td>
                                <td class="col-l_name"><?php echo escape($row['l_name'] ?? ''); ?></td>
                                <td class="col-role_name"><?php echo escape($row['role_name'] ?? ''); ?></td>
                                <td class="col-action">
                                    <button type="button" class="approve-button-in-list"
                                        onclick="enableInlineEdit(this)">Edit</button>
                                    <button type="button" class="reject-button-in-list"
                                        onclick="openDeletePopup(this)">Delete</button>
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
                    <button id="confirm" type="submit" class="confirm" name = "delete">Yes, Delete</button>
                    <button id="cancel" class="cancel">Cancel</button>
                </div>
            </div>
        </div>
    </main>
</body>

</html>