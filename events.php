<?php
require_once 'includes/auth_check.php';
$db = getDB();

$events = $db->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main">
      <div class="content">
        <div class="page-card">
          <div class="page-header">
            <h1>Events</h1>
            <a href="add-event.php" class="btn-add">+ Add Event</a>
          </div>

          <?php if ($msg === 'deleted'): ?>
            <div class="alert alert-success">Event deleted successfully.</div>
          <?php elseif ($msg === 'added'): ?>
            <div class="alert alert-success">Event added successfully.</div>
          <?php elseif ($msg === 'updated'): ?>
            <div class="alert alert-success">Event updated successfully.</div>
          <?php endif; ?>

          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Date</th>
                  <th>Location</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($events)): ?>
                  <tr><td colspan="5" style="text-align:center;color:#999;">No events yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($events as $e): ?>
                  <tr>
                    <td><?= htmlspecialchars($e['event_id']) ?></td>
                    <td><?= htmlspecialchars($e['title']) ?></td>
                    <td><?= htmlspecialchars($e['event_date']) ?></td>
                    <td><?= htmlspecialchars($e['location']) ?></td>
                    <td class="actions">
                      <a href="edit-event.php?id=<?= $e['id'] ?>" class="btn-edit">Edit</a>
                      <a href="delete-event.php?id=<?= $e['id'] ?>" class="btn-delete" onclick="return confirm('Delete this event?')">Delete</a>
                    </td>
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
