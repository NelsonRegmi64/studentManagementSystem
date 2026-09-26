<?php
require_once 'includes/auth_check.php';
require_once 'includes/schema_upgrade.php';
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: students.php'); exit; }

$stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) { header('Location: students.php'); exit; }

$errors = [];
$values = $student;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = array_merge($student, $_POST);
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $status = ($_POST['status'] ?? 'Active') === 'Inactive' ? 'Inactive' : 'Active';
    $gender = trim($_POST['gender'] ?? '');
    $dob = trim($_POST['date_of_birth'] ?? '');
    $section = trim($_POST['section'] ?? '');
    $guardian_name = trim($_POST['guardian_name'] ?? '');
    $guardian_phone = trim($_POST['guardian_phone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($full_name === '') $errors['full_name'] = 'Full name is required.';
    if ($program === '') $errors['program'] = 'Please select a program.';
    if ($year === '') $errors['year'] = 'Please select a year.';
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } else {
        $dup = $db->prepare("SELECT id FROM students WHERE email = ? AND id != ? LIMIT 1");
        $dup->execute([$email, $id]);
        if ($dup->fetch()) $errors['email'] = 'This email is already registered.';
    }
    if ($phone !== '' && !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
        $errors['phone'] = 'Enter a valid phone number.';
    }

    if (!$errors) {
        $stmt = $db->prepare("UPDATE students SET
            full_name=?, email=?, phone=?, address=?, program=?, year=?, status=?,
            gender=?, date_of_birth=?, section=?, guardian_name=?, guardian_phone=?, notes=?
            WHERE id=?");
        $stmt->execute([
            $full_name, $email, $phone ?: null, $address ?: null, $program, $year, $status,
            $gender ?: null, $dob ?: null, $section ?: null, $guardian_name ?: null,
            $guardian_phone ?: null, $notes ?: null, $id
        ]);
        header('Location: students.php?msg=updated');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Student - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    <main class="main">
      <?php $page_title = 'Edit Student'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="form-card form-full">
          <?php
            $form_title = 'Edit Student';
            $submit_label = 'Update Student';
            $student_id_display = $student['student_id'];
            $id_locked = true;
            include 'includes/student_form.php';
          ?>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/js/app.js?v=5"></script>
</body>
</html>
