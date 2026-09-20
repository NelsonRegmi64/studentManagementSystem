<?php
require_once 'includes/auth_check.php';
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $db->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: students.php?msg=deleted');
exit;
?>
