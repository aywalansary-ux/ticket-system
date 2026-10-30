<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];

    if ($title === '' || $description === '') {
        $error = 'عبّ العنوان والوصف';
    } elseif (!in_array($priority, ['low', 'medium', 'high'])) {
        $error = 'أولوية غير صحيحة';
    } else {
        $stmt = $pdo->prepare('INSERT INTO tickets (user_id, title, description, priority) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $title, $description, $priority]);
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><title>تذكرة جديدة</title></head>
<link rel="stylesheet" href="style.css">
<body>
  <h1>تذكرة جديدة</h1>
  <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post">
    <input name="title" placeholder="عنوان المشكلة" required>
    <textarea name="description" placeholder="اشرحي المشكلة" required></textarea>
    <select name="priority">
      <option value="low">منخفضة</option>
      <option value="medium" selected>متوسطة</option>
      <option value="high">عالية</option>
    </select>
    <button type="submit">إرسال</button>
  </form>
  <a href="dashboard.php">رجوع</a>
</body>
</html>