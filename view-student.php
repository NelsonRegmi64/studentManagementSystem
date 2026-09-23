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

$att = $db->prepare("SELECT e.title, e.event_date, a.status, a.marked_at
                     FROM attendance a
                     JOIN events e ON a.event_id = e.id
                     WHERE a.student_id = ?
                     ORDER BY e.event_date DESC LIMIT 10");
$att->execute([$id]);
$attendance = $att->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($student['full_name']) ?> - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    <main class="main">
      <?php $page_title = 'Student Details'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="form-card">
          <div class="page-header">
            <h1><?= htmlspecialchars($student['full_name']) ?></h1>
            <div class="actions">
              <a href="edit-student.php?id=<?= $student['id'] ?>" class="btn-edit">Edit</a>
              <a href="students.php" class="btn-secondary" style="padding:5px 14px;font-size:0.75rem;">Back</a>
            </div>
          </div>
          <div class="detail-grid">
            <div class="detail-item"><label>Student ID</label><span><?= htmlspecialchars($student['student_id']) ?></span></div>
            <div class="detail-item"><label>Status</label><span><span class="badge <?= strtolower($student['status']) ?>"><?= htmlspecialchars($student['status']) ?></span></span></div>
            <div class="detail-item"><label>Email</label><span><?= htmlspecialchars($student['email']) ?></span></div>
            <div class="detail-item"><label>Phone</label><span><?= htmlspecialchars($student['phone'] ?: '—') ?></span></div>
            <div class="detail-item"><label>Program</label><span><?= htmlspecialchars($student['program']) ?></span></div>
            <div class="detail-item"><label>Year</label><span><?= htmlspecialchars($student['year']) ?></span></div>
            <div class="detail-item"><label>Address</label><span><?= htmlspecialchars($student['address'] ?: '—') ?></span></div>
            <div class="detail-item"><label>Registered</label><span><?= date('M d, Y', strtotime($student['created_at'])) ?></span></div>
          </div>
        </div>
        <div class="table-section" style="margin-top:20px;">
          <h2>Attendance History</h2>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr><th>Event</th><th>Date</th><th>Status</th><th>Marked At</th></tr>
              </thead>
              <tbody>
                <?php if (empty($attendance)): ?>
                  <tr><td colspan="4" style="text-align:center;color:#999;padding:24px;">No attendance records yet.</td></tr>
                <?php else: foreach ($attendance as $a): ?>
                  <tr>
                    <td><?= htmlspecialchars($a['title']) ?></td>
                    <td><?= htmlspecialchars($a['event_date']) ?></td>
                    <td><span class="badge <?= $a['status'] === 'Present' ? 'active' : 'inactive' ?>"><?= htmlspecialchars($a['status']) ?></span></td>
                    <td><?= date('M d, Y H:i', strtotime($a['marked_at'])) ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/js/app.js"></script>
</body>
</html>
