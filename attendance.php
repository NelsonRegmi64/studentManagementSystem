<?php
require_once 'includes/auth_check.php';
$db = getDB();

$events = $db->query("SELECT id, event_id, title FROM events ORDER BY event_date DESC")->fetchAll();
$students = $db->query("SELECT id, student_id, full_name FROM students WHERE status = 'Active' ORDER BY full_name")->fetchAll();

$selected_event = (int)($_GET['event_id'] ?? ($events[0]['id'] ?? 0));
$msg = '';

// Save attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int)($_POST['event_id'] ?? 0);
    $statuses = $_POST['status'] ?? [];

    if ($event_id && !empty($statuses)) {
        foreach ($statuses as $student_id => $status) {
            $student_id = (int)$student_id;
            $status = ($status === 'Present') ? 'Present' : 'Absent';

            // Upsert
            $stmt = $db->prepare("INSERT INTO attendance (event_id, student_id, status) VALUES (?, ?, ?)
                                  ON DUPLICATE KEY UPDATE status = VALUES(status), marked_at = CURRENT_TIMESTAMP");
            $stmt->execute([$event_id, $student_id, $status]);
        }
        $msg = 'Attendance saved successfully!';
        $selected_event = $event_id;
    }
}

// Load existing attendance for selected event
$existing = [];
if ($selected_event) {
    $stmt = $db->prepare("SELECT student_id, status FROM attendance WHERE event_id = ?");
    $stmt->execute([$selected_event]);
    foreach ($stmt->fetchAll() as $row) {
        $existing[$row['student_id']] = $row['status'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main">
      <?php $page_title = 'Attendance'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="page-card" style="max-width:700px;">
          <h1>Mark Attendance</h1>

          <?php if ($msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
          <?php endif; ?>

          <?php if (empty($events)): ?>
            <p style="color:#999;">No events available. Please <a href="add-event.php">add an event</a> first.</p>
          <?php elseif (empty($students)): ?>
            <p style="color:#999;">No active students. Please <a href="add-student.php">add students</a> first.</p>
          <?php else: ?>

          <form method="GET" class="select-event">
            <label>Select Event</label>
            <select name="event_id" onchange="this.form.submit()">
              <?php foreach ($events as $e): ?>
                <option value="<?= $e['id'] ?>" <?= $selected_event == $e['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($e['title']) ?> (<?= htmlspecialchars($e['event_id']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </form>

          <form method="POST" action="">
            <input type="hidden" name="event_id" value="<?= $selected_event ?>">

            <div class="table-wrapper">
              <table class="stack-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($students as $s): ?>
                  <tr>
                    <td data-label="ID"><?= htmlspecialchars($s['student_id']) ?></td>
                    <td data-label="Name"><?= htmlspecialchars($s['full_name']) ?></td>
                    <td data-label="Status">
                      <select name="status[<?= $s['id'] ?>]" class="status-select">
                        <option value="Present" <?= ($existing[$s['id']] ?? 'Present') === 'Present' ? 'selected' : '' ?>>Present</option>
                        <option value="Absent" <?= ($existing[$s['id']] ?? '') === 'Absent' ? 'selected' : '' ?>>Absent</option>
                      </select>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary btn-sm">Save Attendance</button>
            </div>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
<script src="assets/js/app.js?v=5"></script>
</body>
</html>
