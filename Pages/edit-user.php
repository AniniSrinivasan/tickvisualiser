<?php
include('../functions/session.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Edit User page">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User • Tick Visualiser</title>
    <link rel="stylesheet" href="../style/login.css">
    <link rel="stylesheet" href="../style/style.css">
    <script src="../script/script.js"></script>
</head>

<?php
require_once '../functions/error.php';
require_once '../functions/manage-functions.php';

adminCheck();

$error = '';
$user_email = '';
$f_name = '';
$l_name = '';
$role_id = '';

$rolesResult = getRoles($conn);
if ($rolesResult === false) {
    $error = 'Unable to load roles. Please try again later.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_email = trim((string) ($_POST['user_email'] ?? ''));
    $f_name = trim((string) ($_POST['f_name'] ?? ''));
    $l_name = trim((string) ($_POST['l_name'] ?? ''));
    $role_id = trim((string) ($_POST['role_id'] ?? ''));

    if ($user_email === '' || $f_name === '' || $l_name === '' || $role_id === '') {
        $error = 'All fields are required.';
    } else {
        $updated = updateUser($conn, $user_email, $f_name, $l_name, (int) $role_id);
        if ($updated) {
            header('Location: manage-user.php');
            exit();
        }

        $error = 'Unable to update user. Please try again.';
    }
} elseif (isset($_GET['email'])) {
    $selectedEmail = trim((string) ($_GET['email'] ?? ''));
    if ($selectedEmail !== '') {
        $userData = getUserByEmail($conn, $selectedEmail);
        if ($userData) {
            $user_email = $userData['user_email'] ?? '';
            $f_name = $userData['f_name'] ?? '';
            $l_name = $userData['l_name'] ?? '';
            $role_id = $userData['role_id'] ?? '';
        } else {
            $error = 'Selected user not found.';
        }
    }
} else {
    header('Location: manage-user.php');
    exit();
}

?>
    <section class="banner-section">
        <div class="content-container">
            <?php
            if (($_SESSION['role_id']) == 2) {
                echo "<h1>Update Account</h1>";
            } else {
                echo "<h1>Welcome!</h1>";
            }
            ?>
            <p><b>Edit Selected User</b></p><br />
        </div>
    </section>

    <main class="auth-wrap-two" role="main">
        <section class="auth-card" aria-label="Register">
            <div class="auth-header">
                <h2>Update User</h2>
                <span class="badge">Secure</span>
            </div>

            <form class="auth-form" method="post" action="">
                <label class="field">
                    <span>First Name</span>
                    <input type="text" name="f_name" placeholder="Forename" required value="<?php echo htmlspecialchars($f_name, ENT_QUOTES, 'UTF-8'); ?>" />
                </label>

                <label class="field">
                    <span>Last Name</span>
                    <input type="text" name="l_name" placeholder="Surname" required value="<?php echo htmlspecialchars($l_name, ENT_QUOTES, 'UTF-8'); ?>" />
                </label>

                <label class="field">
                    <span>Email</span>
                    <input type="email" value="<?php echo htmlspecialchars($user_email, ENT_QUOTES, 'UTF-8'); ?>" readonly />
                    <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($user_email, ENT_QUOTES, 'UTF-8'); ?>" />
                </label>

                <label class="field">
                    <span>Role</span>
                    <select name="role_id" required>
                        <option value="">Select a role</option>
                        <?php if ($rolesResult !== false): ?>
                            <?php while ($row = $rolesResult->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($row['role_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string) $role_id === (string) $row['role_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['role_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </label>

                <?php if (!empty($error)): ?>
                    <p style="color: red; margin-bottom:10px;">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php endif; ?>

                <button type="submit" name="update_user" class="btn-primary btn-full">Update Account</button>
            </form>
        </section>
    </main><br />

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

