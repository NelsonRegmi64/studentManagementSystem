<?php
require_once 'includes/auth_check.php';
$db = getDB();

$pass_msg = '';
$pass_error = '';
$profile_msg = '';
$profile_error = '';

// Change Password
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!password_verify($current, $user['password'])) {
        $pass_error = 'Current password is incorrect.';
    } elseif (strlen($new) < 6) {
        $pass_error = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $pass_error = 'New passwords do not match.';
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $_SESSION['user_id']]);
        $pass_msg = 'Password updated successfully!';
    }
}

// Update Profile
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $profile_error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profile_error = 'Invalid email address.';
    } else {
        // Check email uniqueness
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $profile_error = 'Email already in use by another account.';
        } else {
            $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $_SESSION['user_id']]);
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $profile_msg = 'Profile updated successfully!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main">
      <?php $page_title = 'Settings'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="settings-wrapper">
          <!-- Change Password -->
          <div class="form-card">
            <h2>Change Password</h2>
            <?php if ($pass_error): ?>
              <div class="alert alert-error"><?= htmlspecialchars($pass_error) ?></div>
            <?php endif; ?>
            <?php if ($pass_msg): ?>
              <div class="alert alert-success"><?= htmlspecialchars($pass_msg) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
              <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" placeholder="Enter current password" required>
              </div>
              <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password" required>
              </div>
              <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm new password" required>
              </div>
              <button type="submit" name="change_password" class="btn-primary btn-sm">Update Password</button>
            </form>
          </div>

          <!-- Profile Information -->
          <div class="form-card">
            <h2>Profile Information</h2>
            <?php if ($profile_error): ?>
              <div class="alert alert-error"><?= htmlspecialchars($profile_error) ?></div>
            <?php endif; ?>
            <?php if ($profile_msg): ?>
              <div class="alert alert-success"><?= htmlspecialchars($profile_msg) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
              <div class="form-group">
                <label>Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" required>
              </div>
              <button type="submit" name="update_profile" class="btn-primary btn-sm">Update Profile</button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
<script src="assets/js/app.js?v=5"></script>
</body>
</html>
