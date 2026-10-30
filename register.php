<?php
require 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name === '' || $email === '' || strlen($password) < 6) {
        $error = 'عبّ كل الحقول، وكلمة المرور 6 أحرف على الأقل';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'هذا الإيميل مسجل من قبل';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);
            $success = 'تم إنشاء الحساب بنجاح';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><title>إنشاء حساب</title></head>
<link rel="stylesheet" href="style.css">
<body>
  <h1>إنشاء حساب</h1>
  <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <?php if ($success): ?><p style="color:green"><?= htmlspecialchars($success) ?></p><?php endif; ?>
  <form method="post">
    <input name="name" placeholder="الاسم" required>
    <input name="email" type="email" placeholder="الإيميل" required>
    <input name="password" type="password" placeholder="كلمة المرور" required>
    <button type="submit">سجّل</button>
  </form>
</body>
</html>