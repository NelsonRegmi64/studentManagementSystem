<?php
require_once 'includes/auth_check.php';
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: students.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: students.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $program = $_POST['program'] ?? '';
    $year = $_POST['year'] ?? '';
    $status = $_POST['status'] ?? 'Active';

    if (empty($full_name) || empty($email) || empty($program) || empty($year)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $db->prepare("UPDATE students SET full_name=?, email=?, phone=?, address=?, program=?, year=?, status=? WHERE id=?");
        $stmt->execute([$full_name, $email, $phone, $address, $program, $year, $status, $id]);
        header('Location: students.php?msg=updated');
        exit;
    }
    // Refresh data for form
    $student = array_merge($student, $_POST);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Student - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css?v=4">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main">
      <?php $page_title = 'Edit Student'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="form-card">
          <h1>Edit Student</h1>

          <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-row">
              <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>
              </div>
              <div class="form-group">
                <label>Program *</label>
                <select name="program" required>
                  <?php foreach (['BCA','BBA','BIT','CSIT'] as $p): ?>
                    <option value="<?= $p ?>" <?= $student['program'] == $p ? 'selected' : '' ?>><?= $p ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
              </div>
              <div class="form-group">
                <label>Year *</label>
                <select name="year" required>
                  <?php foreach (['1st','2nd','3rd','4th'] as $y): ?>
                    <option value="<?= $y ?>" <?= $student['year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($student['address'] ?? '') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Status</label>
                <select name="status">
                  <option value="Active" <?= $student['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                  <option value="Inactive" <?= $student['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
              <div class="form-group">
                <label>Student ID</label>
                <input type="text" value="<?= htmlspecialchars($student['student_id']) ?>" disabled>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary btn-sm">Update</button>
              <a href="students.php" class="btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
<script src="assets/js/app.js?v=4"></script>
</body>
</html>
