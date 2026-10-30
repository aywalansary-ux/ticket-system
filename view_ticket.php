<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT t.*, u.name FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?');
$stmt->execute([$id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

$isAgent = $_SESSION['role'] === 'agent';
if (!$ticket || (!$isAgent && $ticket['user_id'] != $_SESSION['user_id'])) {
    die('التذكرة غير موجودة');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['status']) && $isAgent) {
        if (in_array($_POST['status'], ['open', 'in_progress', 'closed'])) {
            $stmt = $pdo->prepare('UPDATE tickets SET status = ? WHERE id = ?');
            $stmt->execute([$_POST['status'], $id]);
        }
    }
    if (isset($_POST['message'])) {
        $message = trim($_POST['message']);
        if ($message !== '') {
            $stmt = $pdo->prepare('INSERT INTO comments (ticket_id, user_id, message) VALUES (?, ?, ?)');
            $stmt->execute([$id, $_SESSION['user_id'], $message]);
        }
    }
    header("Location: view_ticket.php?id=$id");
    exit;
}

$stmt = $pdo->prepare('SELECT c.*, u.name, u.role FROM comments c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = ? ORDER BY c.created_at ASC');
$stmt->execute([$id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusAr = ['open' => 'مفتوحة', 'in_progress' => 'قيد المعالجة', 'closed' => 'مغلقة'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><title>تذكرة #<?= $ticket['id'] ?></title></head>
<link rel="stylesheet" href="style.css">
<body>
  <h1><?= htmlspecialchars($ticket['title']) ?></h1>
  <p>صاحبها: <?= htmlspecialchars($ticket['name']) ?></p>
  <p>الحالة: <?= $statusAr[$ticket['status']] ?></p>
  <p><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>

  <?php if ($isAgent): ?>
    <form method="post">
      <select name="status">
        <?php foreach ($statusAr as $value => $label): ?>
          <option value="<?= $value ?>" <?= $ticket['status'] === $value ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">تحديث الحالة</button>
    </form>
  <?php endif; ?>

  <h2>الردود</h2>
  <?php if (!$comments): ?>
    <p>ما فيه ردود بعد.</p>
  <?php endif; ?>
  <?php foreach ($comments as $c): ?>
    <div style="border:1px solid #ccc; padding:8px; margin-bottom:8px">
      <strong><?= htmlspecialchars($c['name']) ?></strong>
      <?= $c['role'] === 'agent' ? '(دعم فني)' : '' ?>
      <small><?= $c['created_at'] ?></small>
      <p><?= nl2br(htmlspecialchars($c['message'])) ?></p>
    </div>
  <?php endforeach; ?>

  <form method="post">
    <textarea name="message" placeholder="اكتبي ردك" required></textarea>
    <button type="submit">إرسال الرد</button>
  </form>

  <a href="dashboard.php">رجوع</a>
</body>
</html>