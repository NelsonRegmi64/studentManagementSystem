<?php
require_once 'includes/auth_check.php';
$db = getDB();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $program = $_POST['program'] ?? '';
    $year = $_POST['year'] ?? '';

    if (empty($full_name) || empty($email) || empty($program) || empty($year)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email.';
    } else {
        // Generate student ID
        $last = $db->query("SELECT student_id FROM students ORDER BY id DESC LIMIT 1")->fetch();
        if ($last) {
            $num = (int)substr($last['student_id'], 2) + 1;
        } else {
            $num = 1;
        }
        $student_id = 'ST' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $stmt = $db->prepare("INSERT INTO students (student_id, full_name, email, phone, address, program, year) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $full_name, $email, $phone, $address, $program, $year]);
        header('Location: students.php?msg=added');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Student - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main">
      <?php $page_title = 'Add Student'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="form-card">
          <h1>Add Student</h1>

          <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-row">
              <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" placeholder="Enter full name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
              </div>
              <div class="form-group">
                <label>Program *</label>
                <select name="program" required>
                  <option value="" disabled <?= empty($_POST['program']) ? 'selected' : '' ?>>Select program</option>
                  <option value="BCA" <?= ($_POST['program'] ?? '') == 'BCA' ? 'selected' : '' ?>>BCA</option>
                  <option value="BBA" <?= ($_POST['program'] ?? '') == 'BBA' ? 'selected' : '' ?>>BBA</option>
                  <option value="BIT" <?= ($_POST['program'] ?? '') == 'BIT' ? 'selected' : '' ?>>BIT</option>
                  <option value="CSIT" <?= ($_POST['program'] ?? '') == 'CSIT' ? 'selected' : '' ?>>CSIT</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" placeholder="Enter email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
              </div>
              <div class="form-group">
                <label>Year *</label>
                <select name="year" required>
                  <option value="" disabled <?= empty($_POST['year']) ? 'selected' : '' ?>>Select year</option>
                  <option value="1st" <?= ($_POST['year'] ?? '') == '1st' ? 'selected' : '' ?>>1st</option>
                  <option value="2nd" <?= ($_POST['year'] ?? '') == '2nd' ? 'selected' : '' ?>>2nd</option>
                  <option value="3rd" <?= ($_POST['year'] ?? '') == '3rd' ? 'selected' : '' ?>>3rd</option>
                  <option value="4th" <?= ($_POST['year'] ?? '') == '4th' ? 'selected' : '' ?>>4th</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" placeholder="Enter phone number" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" placeholder="Enter address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary btn-sm">Save Student</button>
              <a href="students.php" class="btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
<script src="assets/js/app.js"></script>
</body>
</html>
