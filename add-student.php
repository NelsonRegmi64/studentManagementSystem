<?php
require_once 'includes/auth_check.php';
require_once 'includes/schema_upgrade.php';
$db = getDB();

$errors = [];
$values = $_POST ?: [];
$student_id_display = nextStudentId($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $dup = $db->prepare("SELECT id FROM students WHERE email = ? LIMIT 1");
        $dup->execute([$email]);
        if ($dup->fetch()) $errors['email'] = 'This email is already registered.';
    }
    if ($phone !== '' && !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
        $errors['phone'] = 'Enter a valid phone number.';
    }
    if ($dob !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
        $errors['date_of_birth'] = 'Enter a valid date.';
        $dob = '';
    }

    if (!$errors) {
        $student_id = nextStudentId($db);
        $stmt = $db->prepare("INSERT INTO students
            (student_id, full_name, email, phone, address, program, year, status, gender, date_of_birth, section, guardian_name, guardian_phone, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $student_id, $full_name, $email, $phone ?: null, $address ?: null,
            $program, $year, $status, $gender ?: null, $dob ?: null,
            $section ?: null, $guardian_name ?: null, $guardian_phone ?: null, $notes ?: null
        ]);
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
  <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    <main class="main">
      <?php $page_title = 'Add Student'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="form-card form-full">
          <?php
            $form_title = 'Add Student';
            $submit_label = 'Save Student';
            include 'includes/student_form.php';
          ?>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/js/app.js?v=5"></script>
</body>
</html>
