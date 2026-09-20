<?php
require_once 'includes/auth_check.php';
$db = getDB();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($event_date) || empty($location)) {
        $error = 'Please fill in all required fields.';
    } else {
        $last = $db->query("SELECT event_id FROM events ORDER BY id DESC LIMIT 1")->fetch();
        if ($last) {
            $num = (int)substr($last['event_id'], 3) + 1;
        } else {
            $num = 1;
        }
        $event_id = 'EVT' . str_pad($num, 3, '0', STR_PAD_LEFT);

        $stmt = $db->prepare("INSERT INTO events (event_id, title, event_date, location, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$event_id, $title, $event_date, $location, $description]);
        header('Location: events.php?msg=added');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Event - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main">
      <div class="content">
        <div class="form-card" style="max-width:560px;">
          <h1>Add Event</h1>

          <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-group">
              <label>Event Title *</label>
              <input type="text" name="title" placeholder="Enter event title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label>Date *</label>
              <input type="date" name="event_date" value="<?= htmlspecialchars($_POST['event_date'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label>Location *</label>
              <input type="text" name="location" placeholder="Enter location" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label>Description</label>
              <textarea name="description" rows="4" placeholder="Enter event description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn-primary btn-sm">Save Event</button>
              <a href="events.php" class="btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
