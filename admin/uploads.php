<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Сессия истекла. Обнови страницу и попробуй ещё раз.';
    } else {
        $result = save_uploaded_files($_FILES['files'] ?? []);
        if (!empty($result['uploaded'])) {
            $success = 'Файлы загружены. Скопируй путь и вставь его в нужное поле на странице редактирования.';
        }
        if (!empty($result['errors'])) {
            $error = implode(' ', $result['errors']);
        }
    }
}

$files = list_uploaded_files();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Медиа — Urban Brew</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <a class="sidebar-brand" href="../index.php" target="_blank">Urban Brew</a>
      <nav class="sidebar-nav">
        <a href="index.php#general">Общее</a>
        <a href="index.php#ticker">Лента фото</a>
        <a href="index.php#menu">Товары и цены</a>
        <a href="index.php#promotions">Акции</a>
        <a href="index.php#gallery">Галерея</a>
        <a href="index.php#reviews">Отзывы</a>
        <a href="uploads.php">Медиа</a>
      </nav>
      <div class="sidebar-actions">
        <a class="admin-btn admin-btn-dark" href="../index.php" target="_blank">Открыть сайт</a>
        <a class="admin-btn admin-btn-outline" href="logout.php">Выйти</a>
      </div>
    </aside>

    <main class="admin-main">
      <header class="admin-topbar">
        <div>
          <p class="admin-kicker">Медиатека</p>
          <h1>Загрузка фото</h1>
        </div>
        <div class="topbar-links">
          <a href="index.php">Назад к редактированию</a>
        </div>
      </header>

      <?php if ($success !== ''): ?>
        <div class="alert success"><?= e($success) ?></div>
      <?php endif; ?>

      <?php if ($error !== ''): ?>
        <div class="alert error"><?= e($error) ?></div>
      <?php endif; ?>

      <section class="admin-card">
        <div class="section-head-admin">
          <div>
            <p class="admin-kicker">Upload Center</p>
            <h2>Загрузи фото и используй пути в админке</h2>
          </div>
        </div>

        <form method="post" enctype="multipart/form-data" class="upload-form">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <label class="upload-drop">
            <span>Выбери JPG, PNG, WEBP или GIF</span>
            <input type="file" name="files[]" multiple accept=".jpg,.jpeg,.png,.webp,.gif">
          </label>
          <button type="submit" class="admin-btn admin-btn-gold">Загрузить файлы</button>
        </form>
      </section>

      <section class="admin-card">
        <div class="section-head-admin">
          <div>
            <p class="admin-kicker">Файлы</p>
            <h2>Скопируй нужный путь</h2>
          </div>
        </div>

        <div class="media-grid">
          <?php foreach ($files as $file): ?>
            <article class="media-card">
              <div class="media-preview">
                <img src="../<?= e($file['path']) ?>" alt="<?= e($file['name']) ?>">
              </div>
              <div class="media-meta">
                <strong><?= e($file['name']) ?></strong>
                <small><?= e($file['modified']) ?> · <?= e(format_file_size((int) $file['size'])) ?></small>
                <div class="copy-row">
                  <input type="text" readonly value="<?= e($file['path']) ?>">
                  <button type="button" class="copy-btn" data-copy="<?= e($file['path']) ?>">Копировать</button>
                </div>
              </div>
            </article>
          <?php endforeach; ?>

          <?php if (!$files): ?>
            <p class="empty-state">Пока нет загруженных файлов.</p>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>

  <script src="../assets/js/admin.js"></script>
</body>
</html>
