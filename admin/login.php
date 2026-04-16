<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

if (admin_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Сессия истекла. Обнови страницу и попробуй ещё раз.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($username === ADMIN_USER && password_verify($password, ADMIN_PASSWORD_HASH)) {
            $_SESSION['admin_logged_in'] = true;
            redirect('index.php');
        }

        $error = 'Неверный логин или пароль.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Вход в админку</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-login-body">
  <main class="login-shell">
    <section class="login-card">
      <p class="admin-kicker">Urban Brew Premium CMS</p>
      <h1>Вход в админку</h1>
      <p class="admin-help">Стандартный логин: <strong>admin</strong><br>Стандартный пароль: <strong>Admin123!</strong></p>

      <?php if ($error !== ''): ?>
        <div class="alert error"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" class="login-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label>
          <span>Логин</span>
          <input type="text" name="username" value="admin" required>
        </label>

        <label>
          <span>Пароль</span>
          <input type="password" name="password" placeholder="Введите пароль" required>
        </label>

        <button type="submit" class="admin-btn admin-btn-gold">Войти</button>
      </form>

      <a class="back-link" href="../index.php">← Вернуться на сайт</a>
    </section>
  </main>
</body>
</html>
