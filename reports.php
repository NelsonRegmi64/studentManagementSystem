<?php
require_once 'includes/auth_check.php';
$db = getDB();

// Students by program
$by_program = $db->query("SELECT program, COUNT(*) as cnt FROM students GROUP BY program ORDER BY cnt DESC")->fetchAll();

// Students by year
$by_year = $db->query("SELECT year, COUNT(*) as cnt FROM students GROUP BY year ORDER BY year")->fetchAll();

// Students by status
$by_status = $db->query("SELECT status, COUNT(*) as cnt FROM students GROUP BY status")->fetchAll();

// Attendance summary per event
$att_summary = $db->query("
    SELECT e.event_id, e.title, e.event_date,
           SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_count,
           SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as absent_count,
           COUNT(a.id) as total_marked
    FROM events e
    LEFT JOIN attendance a ON e.id = a.event_id
    GROUP BY e.id
    ORDER BY e.event_date DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main">
      <?php $page_title = 'Reports'; include 'includes/header_bar.php'; ?>

      <div class="content">
        <div class="stats-grid" style="margin-bottom:24px;">
          <?php foreach ($by_status as $s): ?>
          <div class="stat-card">
            <div class="stat-label"><?= htmlspecialchars($s['status']) ?> Students</div>
            <div class="stat-value"><?= $s['cnt'] ?></div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($by_status)): ?>
          <div class="stat-card">
            <div class="stat-label">No data</div>
            <div class="stat-value">0</div>
          </div>
          <?php endif; ?>
        </div>

        <div class="report-grid">
          <div class="table-section">
            <h2>Students by Program</h2>
            <div class="table-wrapper">
              <table>
                <thead><tr><th>Program</th><th>Count</th></tr></thead>
                <tbody>
                  <?php if (empty($by_program)): ?>
                    <tr><td colspan="2" style="text-align:center;color:#999;">No data</td></tr>
                  <?php else: ?>
                    <?php foreach ($by_program as $r): ?>
                    <tr>
                      <td><?= htmlspecialchars($r['program']) ?></td>
                      <td><strong><?= $r['cnt'] ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="table-section">
            <h2>Students by Year</h2>
            <div class="table-wrapper">
              <table>
                <thead><tr><th>Year</th><th>Count</th></tr></thead>
                <tbody>
                  <?php if (empty($by_year)): ?>
                    <tr><td colspan="2" style="text-align:center;color:#999;">No data</td></tr>
                  <?php else: ?>
                    <?php foreach ($by_year as $r): ?>
                    <tr>
                      <td><?= htmlspecialchars($r['year']) ?></td>
                      <td><strong><?= $r['cnt'] ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="table-section" style="margin-top:20px;">
          <h2>Attendance Summary by Event</h2>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Event ID</th>
                  <th>Title</th>
                  <th>Date</th>
                  <th>Present</th>
                  <th>Absent</th>
                  <th>Total Marked</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($att_summary)): ?>
                  <tr><td colspan="6" style="text-align:center;color:#999;padding:24px;">No events yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($att_summary as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['event_id']) ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['event_date']) ?></td>
                    <td><span class="badge active"><?= (int)$r['present_count'] ?></span></td>
                    <td><span class="badge inactive"><?= (int)$r['absent_count'] ?></span></td>
                    <td><?= (int)$r['total_marked'] ?></td>
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
  <script src="assets/js/app.js"></script>
</body>
</html>
