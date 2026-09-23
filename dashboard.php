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
$upcoming = $db->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
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

        <div class="dash-grid">
          <div class="table-section">
            <h2>Recent Students</h2>
            <div class="table-wrapper">
              <table>
                <thead><tr><th>ID</th><th>Name</th><th>Program</th><th>Status</th><th></th></tr></thead>
                <tbody>
                  <?php if (empty($recent)): ?>
                    <tr><td colspan="5"><div class="empty-state" style="padding:24px;"><p>No students yet.</p><a href="add-student.php" class="btn-add">+ Add Student</a></div></td></tr>
                  <?php else: foreach ($recent as $s): ?>
                    <tr>
                      <td><?= htmlspecialchars($s['student_id']) ?></td>
                      <td><?= htmlspecialchars($s['full_name']) ?></td>
                      <td><?= htmlspecialchars($s['program']) ?></td>
                      <td><span class="badge <?= strtolower($s['status']) ?>"><?= htmlspecialchars($s['status']) ?></span></td>
                      <td><a href="view-student.php?id=<?= $s['id'] ?>" class="btn-view">View</a></td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="table-section">
            <h2>Upcoming Events</h2>
            <div class="table-wrapper">
              <table>
                <thead><tr><th>Title</th><th>Date</th><th>Location</th></tr></thead>
                <tbody>
                  <?php if (empty($upcoming)): ?>
                    <tr><td colspan="3"><div class="empty-state" style="padding:24px;"><p>No upcoming events.</p><a href="add-event.php" class="btn-add">+ Add Event</a></div></td></tr>
                  <?php else: foreach ($upcoming as $e): ?>
                    <tr>
                      <td><?= htmlspecialchars($e['title']) ?></td>
                      <td><?= htmlspecialchars($e['event_date']) ?></td>
                      <td><?= htmlspecialchars($e['location']) ?></td>
                    </tr>
                  <?php endforeach; endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/js/app.js"></script>
</body>
</html>
