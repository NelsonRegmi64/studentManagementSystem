<?php
require_once 'includes/auth_check.php';
$db = getDB();

// Stats
$total_students = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_events = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();
$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Today's attendance (students marked present today)
$today = date('Y-m-d');
$stmt = $db->prepare("SELECT COUNT(DISTINCT student_id) FROM attendance a 
                      JOIN events e ON a.event_id = e.id 
                      WHERE a.status = 'Present' AND DATE(a.marked_at) = ?");
$stmt->execute([$today]);
$today_attendance = $stmt->fetchColumn();

// Recent students
$recent = $db->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5")->fetchAll();
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
      <header class="topbar">
        <h1>Dashboard</h1>
        <div class="user-info">
          <span>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
          <div class="avatar">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
        </div>
      </header>

      <div class="content">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#5b6b7c" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-label">Total Students</div>
            <div class="stat-value"><?= $total_students ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#5b6b7c" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="stat-label">Total Events</div>
            <div class="stat-value"><?= $total_events ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#5b6b7c" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-label">Registered Users</div>
            <div class="stat-value"><?= $total_users ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#5b6b7c" stroke-width="1.5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M9 14l2 2 4-4"/></svg>
            </div>
            <div class="stat-label">Today's Attendance</div>
            <div class="stat-value"><?= $today_attendance ?></div>
          </div>
        </div>

        <div class="table-section">
          <h2>Recent Students</h2>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Program</th>
                  <th>Year</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recent)): ?>
                  <tr><td colspan="6" style="text-align:center;color:#999;">No students yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($recent as $s): ?>
                  <tr>
                    <td><?= htmlspecialchars($s['student_id']) ?></td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><?= htmlspecialchars($s['program']) ?></td>
                    <td><?= htmlspecialchars($s['year']) ?></td>
                    <td><span class="badge <?= strtolower($s['status']) ?>"><?= htmlspecialchars($s['status']) ?></span></td>
                    <td><a href="edit-student.php?id=<?= $s['id'] ?>" class="btn-view">View</a></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
