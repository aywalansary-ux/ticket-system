<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'الإيميل أو كلمة المرور غلط';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><title>تسجيل الدخول</title></head>
<link rel="stylesheet" href="style.css">
<body>
  <h1>تسجيل الدخول</h1>
  <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post">
    <input name="email" type="email" placeholder="الإيميل" required>
    <input name="password" type="password" placeholder="كلمة المرور" required>
    <button type="submit">دخول</button>
  </form>
</body>
</html>