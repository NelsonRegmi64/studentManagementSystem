<?php
require_once 'includes/auth_check.php';
$db = getDB();

$search = trim($_GET['search'] ?? '');
$filter_program = $_GET['program'] ?? '';
$filter_year = $_GET['year'] ?? '';
$filter_status = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$where = [];
$params = [];

if ($search) {
    $where[] = "(full_name LIKE ? OR student_id LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like, $like]);
}
if ($filter_program) { $where[] = "program = ?"; $params[] = $filter_program; }
if ($filter_year) { $where[] = "year = ?"; $params[] = $filter_year; }
if ($filter_status) { $where[] = "status = ?"; $params[] = $filter_status; }

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$count_stmt = $db->prepare("SELECT COUNT(*) FROM students $where_sql");
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();

$stmt = $db->prepare("SELECT * FROM students $where_sql ORDER BY id DESC LIMIT $per_page OFFSET $offset");
$stmt->execute($params);
$students = $stmt->fetchAll();
$total_pages = max(1, ceil($total / $per_page));

$msg = $_GET['msg'] ?? '';
$query_string = http_build_query(array_filter([
    'search' => $search, 'program' => $filter_program, 'year' => $filter_year, 'status' => $filter_status,
]));
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
      <?php $page_title = 'Students'; include 'includes/header_bar.php'; ?>
      <div class="content">
        <div class="page-card">
          <div class="page-header">
            <h1>Students <span style="font-size:0.85rem;color:#6b7280;font-weight:400;">(<?= $total ?>)</span></h1>
            <a href="add-student.php" class="btn-add">+ Add Student</a>
          </div>
          <?php if ($msg === 'deleted'): ?><div class="alert alert-success">Student deleted successfully.</div>
          <?php elseif ($msg === 'added'): ?><div class="alert alert-success">Student added successfully.</div>
          <?php elseif ($msg === 'updated'): ?><div class="alert alert-success">Student updated successfully.</div>
          <?php endif; ?>
          <form method="GET" class="filter-bar">
            <input type="text" name="search" class="filter-select" placeholder="Search name, ID, email..." value="<?= htmlspecialchars($search) ?>">
            <select name="program" class="filter-select">
              <option value="">All Programs</option>
              <?php foreach (['BCA','BBA','BIT','CSIT'] as $p): ?>
                <option value="<?= $p ?>" <?= $filter_program === $p ? 'selected' : '' ?>><?= $p ?></option>
              <?php endforeach; ?>
            </select>
            <select name="year" class="filter-select">
              <option value="">All Years</option>
              <?php foreach (['1st','2nd','3rd','4th'] as $y): ?>
                <option value="<?= $y ?>" <?= $filter_year === $y ? 'selected' : '' ?>><?= $y ?></option>
              <?php endforeach; ?>
            </select>
            <select name="status" class="filter-select">
              <option value="">All Status</option>
              <option value="Active" <?= $filter_status === 'Active' ? 'selected' : '' ?>>Active</option>
              <option value="Inactive" <?= $filter_status === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button type="submit" class="btn-filter">Filter</button>
            <?php if ($search || $filter_program || $filter_year || $filter_status): ?>
              <a href="students.php" class="btn-secondary" style="padding:9px 14px;font-size:0.85rem;">Clear</a>
            <?php endif; ?>
          </form>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Program</th><th>Year</th><th>Status</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php if (empty($students)): ?>
                  <tr><td colspan="7"><div class="empty-state"><p>No students found.</p><a href="add-student.php" class="btn-add">+ Add Student</a></div></td></tr>
                <?php else: foreach ($students as $s): ?>
                  <tr>
                    <td><?= htmlspecialchars($s['student_id']) ?></td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars($s['program']) ?></td>
                    <td><?= htmlspecialchars($s['year']) ?></td>
                    <td><span class="badge <?= strtolower($s['status']) ?>"><?= htmlspecialchars($s['status']) ?></span></td>
                    <td class="actions">
                      <a href="view-student.php?id=<?= $s['id'] ?>" class="btn-view">View</a>
                      <a href="edit-student.php?id=<?= $s['id'] ?>" class="btn-edit">Edit</a>
                      <a href="delete-student.php?id=<?= $s['id'] ?>" class="btn-delete" data-confirm="Delete this student? This cannot be undone.">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
          <?php if ($total_pages > 1): ?>
          <div class="pagination">
            <?php if ($page > 1): ?><a href="?page=<?= $page - 1 ?>&<?= $query_string ?>" class="page-btn">&lt;</a><?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <a href="?page=<?= $i ?>&<?= $query_string ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?><a href="?page=<?= $page + 1 ?>&<?= $query_string ?>" class="page-btn">&gt;</a><?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/js/app.js"></script>
</body>
</html>
