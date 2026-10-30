<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] === 'agent') {
    $stmt = $pdo->query('SELECT t.*, u.name FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC');
} else {
    $stmt = $pdo->prepare('SELECT t.*, u.name FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.user_id = ? ORDER BY t.created_at DESC');
    $stmt->execute([$_SESSION['user_id']]);
}
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusAr = ['open' => 'مفتوحة', 'in_progress' => 'قيد المعالجة', 'closed' => 'مغلقة'];
$priorityAr = ['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><title>لوحة التحكم</title></head>
<link rel="stylesheet" href="style.css">
<body>
  <h1>أهلًا <?= htmlspecialchars($_SESSION['name']) ?></h1>
  <a href="create_ticket.php">تذكرة جديدة</a>
  <a href="logout.php">تسجيل خروج</a>

  <h2>التذاكر</h2>
  <?php if (!$tickets): ?>
    <p>ما فيه تذاكر بعد.</p>
  <?php else: ?>
    <table border="1" cellpadding="8">
      <tr><th>#</th><th>العنوان</th><th>صاحبها</th><th>الأولوية</th><th>الحالة</th><th>التاريخ</th></tr>
      <?php foreach ($tickets as $t): ?>
        <tr>
  <td><?= $t['id'] ?></td>
  <td><a href="view_ticket.php?id=<?= $t['id'] ?>"><?= htmlspecialchars($t['title']) ?></a></td>
  <td><?= htmlspecialchars($t['name']) ?></td>
  <td><?= $priorityAr[$t['priority']] ?></td>
  <td><?= $statusAr[$t['status']] ?></td>
  <td><?= $t['created_at'] ?></td>
</tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</body>
</html>