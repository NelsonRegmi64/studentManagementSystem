<?php
require_once 'includes/auth_check.php';
$db = getDB();

$total_students = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_events = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();
$active_students = $db->query("SELECT COUNT(*) FROM students WHERE status = 'Active'")->fetchColumn();

$today = date('Y-m-d');
$stmt = $db->prepare("SELECT COUNT(DISTINCT student_id) FROM attendance a JOIN events e ON a.event_id = e.id WHERE a.status = 'Present' AND DATE(a.marked_at) = ?");
$stmt->execute([$today]);
$today_attendance = $stmt->fetchColumn();

$recent = $db->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5")->fetchAll();
$upcoming = $db->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 4")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Dashboard - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    <main class="main">
      <?php $page_title = 'Dashboard'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-label">Total Students</div>
            <div class="stat-value"><?= $total_students ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Active Students</div>
            <div class="stat-value"><?= $active_students ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Total Events</div>
            <div class="stat-value"><?= $total_events ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Today's Attendance</div>
            <div class="stat-value"><?= $today_attendance ?></div>
          </div>
        </div>

        <div class="quick-actions">
          <a class="qa-btn" href="add-student.php">+ Student</a>
          <a class="qa-btn" href="add-event.php">+ Event</a>
          <a class="qa-btn" href="attendance.php">Attendance</a>
          <a class="qa-btn" href="reports.php">Reports</a>
        </div>

        <div class="dash-grid">
          <div class="table-section">
            <h2>Recent Students</h2>
            <?php if (empty($recent)): ?>
              <div class="empty-state" style="padding:24px;"><p>No students yet.</p><a href="add-student.php" class="btn-add">+ Add Student</a></div>
            <?php else: ?>
              <div class="card-list">
                <?php foreach ($recent as $s): ?>
                  <a class="list-card" href="view-student.php?id=<?= (int)$s['id'] ?>">
                    <div class="list-card-main">
                      <strong><?= htmlspecialchars($s['full_name']) ?></strong>
                      <span class="muted"><?= htmlspecialchars($s['student_id']) ?> · <?= htmlspecialchars($s['program']) ?></span>
                    </div>
                    <span class="badge <?= strtolower($s['status']) ?>"><?= htmlspecialchars($s['status']) ?></span>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="table-section">
            <h2>Upcoming Events</h2>
            <?php if (empty($upcoming)): ?>
              <div class="empty-state" style="padding:24px;"><p>No upcoming events.</p><a href="add-event.php" class="btn-add">+ Add Event</a></div>
            <?php else: ?>
              <div class="card-list">
                <?php foreach ($upcoming as $e): ?>
                  <div class="list-card">
                    <div class="list-card-main">
                      <strong><?= htmlspecialchars($e['title']) ?></strong>
                      <span class="muted"><?= htmlspecialchars($e['event_date']) ?> · <?= htmlspecialchars($e['location'] ?: 'No location') ?></span>
                    </div>
                    <a href="edit-event.php?id=<?= (int)$e['id'] ?>" class="btn-view">View</a>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/js/app.js?v=5"></script>
</body>
</html>
