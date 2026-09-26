<?php
require_once 'includes/auth_check.php';
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: events.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: events.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($event_date) || empty($location)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $db->prepare("UPDATE events SET title=?, event_date=?, location=?, description=? WHERE id=?");
        $stmt->execute([$title, $event_date, $location, $description, $id]);
        header('Location: events.php?msg=updated');
        exit;
    }
    $event = array_merge($event, $_POST);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Event - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main">
      <?php $page_title = 'Edit Event'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="form-card" style="max-width:560px;">
          <h1>Edit Event</h1>

          <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-group">
              <label>Event Title *</label>
              <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
            </div>

            <div class="form-group">
              <label>Date *</label>
              <input type="date" name="event_date" value="<?= htmlspecialchars($event['event_date']) ?>" required>
            </div>

            <div class="form-group">
              <label>Location *</label>
              <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
            </div>

            <div class="form-group">
              <label>Description</label>
              <textarea name="description" rows="4"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary btn-sm">Update Event</button>
              <a href="events.php" class="btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
<script src="assets/js/app.js?v=5"></script>
</body>
</html>
