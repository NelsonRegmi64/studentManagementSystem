<?php
require_once 'includes/auth_check.php';
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: students.php'); exit; }

$stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) { header('Location: students.php'); exit; }

$att = $db->prepare("SELECT e.title, e.event_date, a.status, a.marked_at
                     FROM attendance a JOIN events e ON a.event_id = e.id
                     WHERE a.student_id = ? ORDER BY e.event_date DESC LIMIT 10");
$att->execute([$id]);
$attendance = $att->fetchAll();

function dval($row, $key, $fallback = '—') {
    $v = $row[$key] ?? '';
    if ($v === null || $v === '') return $fallback;
    return htmlspecialchars((string)$v);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($student['full_name']) ?> - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    <main class="main">
      <?php $page_title = 'Student Details'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="form-card form-full">
          <div class="form-banner">
            <div>
              <h1><?= htmlspecialchars($student['full_name']) ?></h1>
              <p><?= htmlspecialchars($student['program']) ?> · <?= htmlspecialchars($student['year']) ?> year<?= !empty($student['section']) ? ' · Section '.$student['section'] : '' ?></p>
            </div>
            <div class="actions" style="display:flex;gap:8px;flex-wrap:wrap;">
              <span class="badge <?= strtolower($student['status']) ?>"><?= htmlspecialchars($student['status']) ?></span>
              <a href="edit-student.php?id=<?= (int)$student['id'] ?>" class="btn-edit">Edit</a>
              <a href="students.php" class="btn-secondary" style="padding:7px 14px;font-size:.78rem;">Back</a>
            </div>
          </div>

          <section class="form-section">
            <h3>Personal Information</h3>
            <div class="detail-grid">
              <div class="detail-item"><label>Student ID</label><span><?= dval($student,'student_id') ?></span></div>
              <div class="detail-item"><label>Gender</label><span><?= dval($student,'gender') ?></span></div>
              <div class="detail-item"><label>Date of Birth</label><span><?= !empty($student['date_of_birth']) ? htmlspecialchars(date('M d, Y', strtotime($student['date_of_birth']))) : '—' ?></span></div>
            </div>
          </section>

          <section class="form-section">
            <h3>Academic Information</h3>
            <div class="detail-grid">
              <div class="detail-item"><label>Program</label><span><?= dval($student,'program') ?></span></div>
              <div class="detail-item"><label>Year</label><span><?= dval($student,'year') ?></span></div>
              <div class="detail-item"><label>Section</label><span><?= dval($student,'section') ?></span></div>
              <div class="detail-item"><label>Status</label><span><span class="badge <?= strtolower($student['status']) ?>"><?= htmlspecialchars($student['status']) ?></span></span></div>
            </div>
          </section>

          <section class="form-section">
            <h3>Contact Information</h3>
            <div class="detail-grid">
              <div class="detail-item"><label>Email</label><span><?= dval($student,'email') ?></span></div>
              <div class="detail-item"><label>Phone</label><span><?= dval($student,'phone') ?></span></div>
              <div class="detail-item"><label>Guardian Name</label><span><?= dval($student,'guardian_name') ?></span></div>
              <div class="detail-item"><label>Guardian Phone</label><span><?= dval($student,'guardian_phone') ?></span></div>
              <div class="detail-item"><label>Address</label><span><?= dval($student,'address') ?></span></div>
              <div class="detail-item"><label>Registered</label><span><?= date('M d, Y', strtotime($student['created_at'])) ?></span></div>
            </div>
            <?php if (!empty($student['notes'])): ?>
              <div class="detail-item" style="border-bottom:none;">
                <label>Notes</label>
                <span><?= nl2br(htmlspecialchars($student['notes'])) ?></span>
              </div>
            <?php endif; ?>
          </section>
        </div>

        <div class="table-section" style="margin-top:20px;">
          <h2>Attendance History</h2>
          <div class="table-wrapper">
            <table class="stack-table">
              <thead>
                <tr><th>Event</th><th>Date</th><th>Status</th><th>Marked At</th></tr>
              </thead>
              <tbody>
                <?php if (empty($attendance)): ?>
                  <tr><td colspan="4" style="text-align:center;color:#999;padding:24px;">No attendance records yet.</td></tr>
                <?php else: foreach ($attendance as $a): ?>
                  <tr>
                    <td data-label="Event"><?= htmlspecialchars($a['title']) ?></td>
                    <td data-label="Date"><?= htmlspecialchars($a['event_date']) ?></td>
                    <td data-label="Status"><span class="badge <?= $a['status'] === 'Present' ? 'active' : 'inactive' ?>"><?= htmlspecialchars($a['status']) ?></span></td>
                    <td data-label="Marked"><?= date('M d, Y H:i', strtotime($a['marked_at'])) ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/js/app.js?v=5"></script>
</body>
</html>
