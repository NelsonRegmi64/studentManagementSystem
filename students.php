<?php
require_once 'includes/auth_check.php';
$db = getDB();

$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

if ($search) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE full_name LIKE ? OR student_id LIKE ? OR email LIKE ?");
    $like = "%$search%";
    $stmt->execute([$like, $like, $like]);
    $total = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT * FROM students WHERE full_name LIKE ? OR student_id LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT $per_page OFFSET $offset");
    $stmt->execute([$like, $like, $like]);
} else {
    $total = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $stmt = $db->query("SELECT * FROM students ORDER BY id DESC LIMIT $per_page OFFSET $offset");
}
$students = $stmt->fetchAll();
$total_pages = max(1, ceil($total / $per_page));

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Students - Student Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="main">
      <div class="content">
        <div class="page-card">
          <div class="page-header">
            <h1>Students</h1>
            <form class="search-bar" method="GET">
              <input type="text" name="search" placeholder="Search by name or ID..." value="<?= htmlspecialchars($search) ?>">
              <button type="submit" class="btn-search">Search</button>
            </form>
          </div>

          <?php if ($msg === 'deleted'): ?>
            <div class="alert alert-success">Student deleted successfully.</div>
          <?php elseif ($msg === 'added'): ?>
            <div class="alert alert-success">Student added successfully.</div>
          <?php elseif ($msg === 'updated'): ?>
            <div class="alert alert-success">Student updated successfully.</div>
          <?php endif; ?>

          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Program</th>
                  <th>Year</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($students)): ?>
                  <tr><td colspan="7" style="text-align:center;color:#999;">No students found.</td></tr>
                <?php else: ?>
                  <?php foreach ($students as $s): ?>
                  <tr>
                    <td><?= htmlspecialchars($s['student_id']) ?></td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars($s['program']) ?></td>
                    <td><?= htmlspecialchars($s['year']) ?></td>
                    <td><span class="badge <?= strtolower($s['status']) ?>"><?= htmlspecialchars($s['status']) ?></span></td>
                    <td class="actions">
                      <a href="edit-student.php?id=<?= $s['id'] ?>" class="btn-edit">Edit</a>
                      <a href="delete-student.php?id=<?= $s['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <?php if ($total_pages > 1): ?>
          <div class="pagination">
            <?php if ($page > 1): ?>
              <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">&lt;</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
              <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">&gt;</a>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
