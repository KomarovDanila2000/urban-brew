<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$content = load_content();
$site = $content['site'] ?? [];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Сессия истекла. Обнови страницу и попробуй ещё раз.';
    } else {
        $updated = [
            'site' => [
                'brand' => post_string('brand'),
                'hero_badge' => post_string('hero_badge'),
                'hero_title' => post_string('hero_title'),
                'hero_text' => post_string('hero_text'),
                'hero_image' => post_string('hero_image'),
                'cta_primary_text' => post_string('cta_primary_text'),
                'cta_primary_link' => post_string('cta_primary_link'),
                'cta_secondary_text' => post_string('cta_secondary_text'),
                'cta_secondary_link' => post_string('cta_secondary_link'),
                'address' => post_string('address'),
                'phone' => post_string('phone'),
                'email' => post_string('email'),
                'hours' => post_string('hours'),
                'instagram' => post_string('instagram'),
                'telegram' => post_string('telegram'),
                'map_embed' => post_string('map_embed'),
                'marquee_speed' => post_string('marquee_speed', '28'),
            ],
            'features' => build_repeater('features', ['number', 'title', 'text']),
            'ticker' => build_repeater('ticker', ['title', 'image']),
            'menu' => build_repeater('menu', ['category', 'title', 'price', 'label', 'description', 'image']),
            'promotions' => build_repeater('promotions', ['badge', 'title', 'text']),
            'gallery' => build_repeater('gallery', ['title', 'image']),
            'reviews' => build_repeater('reviews', ['name', 'text']),
        ];

        if (save_content($updated)) {
            $success = 'Изменения сохранены.';
            $content = $updated;
            $site = $content['site'];
        } else {
            $error = 'Не удалось сохранить изменения. Проверь права записи для папки content.';
        }
    }
}

$features = $content['features'] ?? [];
$ticker = $content['ticker'] ?? [];
$menu = $content['menu'] ?? [];
$promotions = $content['promotions'] ?? [];
$gallery = $content['gallery'] ?? [];
$reviews = $content['reviews'] ?? [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Админка — Urban Brew</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <a class="sidebar-brand" href="../index.php" target="_blank"><?= e($site['brand'] ?? 'Urban Brew') ?></a>
      <nav class="sidebar-nav">
        <a href="#general">Общее</a>
        <a href="#ticker">Лента фото</a>
        <a href="#menu">Товары и цены</a>
        <a href="#promotions">Акции</a>
        <a href="#gallery">Галерея</a>
        <a href="#reviews">Отзывы</a>
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
          <p class="admin-kicker">Панель управления</p>
          <h1>Редактирование сайта</h1>
        </div>
        <div class="topbar-links">
          <a href="uploads.php">Загрузить фото</a>
          <a href="../index.php" target="_blank">Посмотреть сайт</a>
        </div>
      </header>

      <?php if ($success !== ''): ?>
        <div class="alert success"><?= e($success) ?></div>
      <?php endif; ?>

      <?php if ($error !== ''): ?>
        <div class="alert error"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <section class="admin-card" id="general">
          <div class="section-head-admin">
            <div>
              <p class="admin-kicker">Общие настройки</p>
              <h2>Бренд, hero и контакты</h2>
            </div>
          </div>

          <div class="form-grid two-cols">
            <label><span>Название бренда</span><input type="text" name="brand" value="<?= e($site['brand'] ?? '') ?>"></label>
            <label><span>Скорость верхней ленты, сек</span><input type="number" min="16" max="60" name="marquee_speed" value="<?= e($site['marquee_speed'] ?? '28') ?>"></label>
            <label class="full"><span>Hero badge</span><input type="text" name="hero_badge" value="<?= e($site['hero_badge'] ?? '') ?>"></label>
            <label class="full"><span>Hero заголовок</span><input type="text" name="hero_title" value="<?= e($site['hero_title'] ?? '') ?>"></label>
            <label class="full"><span>Hero описание</span><textarea name="hero_text" rows="4"><?= e($site['hero_text'] ?? '') ?></textarea></label>
            <label class="full"><span>Hero фото / путь</span><input type="text" name="hero_image" value="<?= e($site['hero_image'] ?? '') ?>" placeholder="uploads/202504/photo.jpg"></label>
            <label><span>Текст кнопки 1</span><input type="text" name="cta_primary_text" value="<?= e($site['cta_primary_text'] ?? '') ?>"></label>
            <label><span>Ссылка кнопки 1</span><input type="text" name="cta_primary_link" value="<?= e($site['cta_primary_link'] ?? '') ?>"></label>
            <label><span>Текст кнопки 2</span><input type="text" name="cta_secondary_text" value="<?= e($site['cta_secondary_text'] ?? '') ?>"></label>
            <label><span>Ссылка кнопки 2</span><input type="text" name="cta_secondary_link" value="<?= e($site['cta_secondary_link'] ?? '') ?>"></label>
            <label><span>Адрес</span><input type="text" name="address" value="<?= e($site['address'] ?? '') ?>"></label>
            <label><span>Телефон</span><input type="text" name="phone" value="<?= e($site['phone'] ?? '') ?>"></label>
            <label><span>Email</span><input type="email" name="email" value="<?= e($site['email'] ?? '') ?>"></label>
            <label><span>Часы работы</span><input type="text" name="hours" value="<?= e($site['hours'] ?? '') ?>"></label>
            <label><span>Instagram URL</span><input type="text" name="instagram" value="<?= e($site['instagram'] ?? '') ?>"></label>
            <label><span>Telegram URL</span><input type="text" name="telegram" value="<?= e($site['telegram'] ?? '') ?>"></label>
            <label class="full"><span>Google Maps embed URL</span><input type="text" name="map_embed" value="<?= e($site['map_embed'] ?? '') ?>"></label>
          </div>
        </section>

        <section class="admin-card">
          <div class="section-head-admin">
            <div>
              <p class="admin-kicker">Преимущества</p>
              <h2>Карточки под hero</h2>
            </div>
          </div>

          <div class="repeater" data-repeater="features">
            <div class="repeater-items">
              <?php foreach ($features as $item): ?>
                <div class="repeater-item">
                  <div class="repeater-row repeater-row-compact">
                    <label><span>Номер</span><input type="text" name="features[number][]" value="<?= e($item['number'] ?? '') ?>"></label>
                    <label><span>Заголовок</span><input type="text" name="features[title][]" value="<?= e($item['title'] ?? '') ?>"></label>
                    <label class="full"><span>Текст</span><textarea name="features[text][]" rows="3"><?= e($item['text'] ?? '') ?></textarea></label>
                  </div>
                  <button type="button" class="remove-item">Удалить</button>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="admin-btn admin-btn-outline add-item" data-template="features-template">Добавить карточку</button>
          </div>
        </section>

        <section class="admin-card" id="ticker">
          <div class="section-head-admin">
            <div>
              <p class="admin-kicker">Лента вверху</p>
              <h2>Автопрокрутка фотографий</h2>
            </div>
            <p class="section-tip">Сначала загрузи фото в разделе “Медиа”, потом вставь путь вида <code>uploads/202504/имя-файла.jpg</code>.</p>
          </div>

          <div class="repeater" data-repeater="ticker">
            <div class="repeater-items">
              <?php foreach ($ticker as $item): ?>
                <div class="repeater-item">
                  <div class="repeater-row">
                    <label><span>Название</span><input type="text" name="ticker[title][]" value="<?= e($item['title'] ?? '') ?>"></label>
                    <label class="full"><span>Путь к фото</span><input type="text" name="ticker[image][]" value="<?= e($item['image'] ?? '') ?>"></label>
                  </div>
                  <button type="button" class="remove-item">Удалить</button>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="admin-btn admin-btn-outline add-item" data-template="ticker-template">Добавить слайд</button>
          </div>
        </section>

        <section class="admin-card" id="menu">
          <div class="section-head-admin">
            <div>
              <p class="admin-kicker">Товары</p>
              <h2>Цены, категории, фото и описания</h2>
            </div>
          </div>

          <div class="repeater" data-repeater="menu">
            <div class="repeater-items">
              <?php foreach ($menu as $item): ?>
                <div class="repeater-item">
                  <div class="repeater-row">
                    <label><span>Категория</span><input type="text" name="menu[category][]" value="<?= e($item['category'] ?? '') ?>"></label>
                    <label><span>Название</span><input type="text" name="menu[title][]" value="<?= e($item['title'] ?? '') ?>"></label>
                    <label><span>Цена</span><input type="text" name="menu[price][]" value="<?= e($item['price'] ?? '') ?>"></label>
                    <label><span>Бейдж</span><input type="text" name="menu[label][]" value="<?= e($item['label'] ?? '') ?>"></label>
                    <label class="full"><span>Описание</span><textarea name="menu[description][]" rows="3"><?= e($item['description'] ?? '') ?></textarea></label>
                    <label class="full"><span>Путь к фото</span><input type="text" name="menu[image][]" value="<?= e($item['image'] ?? '') ?>"></label>
                  </div>
                  <button type="button" class="remove-item">Удалить</button>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="admin-btn admin-btn-outline add-item" data-template="menu-template">Добавить товар</button>
          </div>
        </section>

        <section class="admin-card" id="promotions">
          <div class="section-head-admin">
            <div>
              <p class="admin-kicker">Акции</p>
              <h2>Сезонные предложения</h2>
            </div>
          </div>

          <div class="repeater" data-repeater="promotions">
            <div class="repeater-items">
              <?php foreach ($promotions as $item): ?>
                <div class="repeater-item">
                  <div class="repeater-row repeater-row-compact">
                    <label><span>Бейдж</span><input type="text" name="promotions[badge][]" value="<?= e($item['badge'] ?? '') ?>"></label>
                    <label><span>Название</span><input type="text" name="promotions[title][]" value="<?= e($item['title'] ?? '') ?>"></label>
                    <label class="full"><span>Описание</span><textarea name="promotions[text][]" rows="3"><?= e($item['text'] ?? '') ?></textarea></label>
                  </div>
                  <button type="button" class="remove-item">Удалить</button>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="admin-btn admin-btn-outline add-item" data-template="promotions-template">Добавить акцию</button>
          </div>
        </section>

        <section class="admin-card" id="gallery">
          <div class="section-head-admin">
            <div>
              <p class="admin-kicker">Галерея</p>
              <h2>Фото кофейни и работ</h2>
            </div>
          </div>

          <div class="repeater" data-repeater="gallery">
            <div class="repeater-items">
              <?php foreach ($gallery as $item): ?>
                <div class="repeater-item">
                  <div class="repeater-row">
                    <label><span>Подпись</span><input type="text" name="gallery[title][]" value="<?= e($item['title'] ?? '') ?>"></label>
                    <label class="full"><span>Путь к фото</span><input type="text" name="gallery[image][]" value="<?= e($item['image'] ?? '') ?>"></label>
                  </div>
                  <button type="button" class="remove-item">Удалить</button>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="admin-btn admin-btn-outline add-item" data-template="gallery-template">Добавить фото</button>
          </div>
        </section>

        <section class="admin-card" id="reviews">
          <div class="section-head-admin">
            <div>
              <p class="admin-kicker">Отзывы</p>
              <h2>Отзывы гостей</h2>
            </div>
          </div>

          <div class="repeater" data-repeater="reviews">
            <div class="repeater-items">
              <?php foreach ($reviews as $item): ?>
                <div class="repeater-item">
                  <div class="repeater-row repeater-row-compact">
                    <label><span>Имя</span><input type="text" name="reviews[name][]" value="<?= e($item['name'] ?? '') ?>"></label>
                    <label class="full"><span>Текст</span><textarea name="reviews[text][]" rows="3"><?= e($item['text'] ?? '') ?></textarea></label>
                  </div>
                  <button type="button" class="remove-item">Удалить</button>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="admin-btn admin-btn-outline add-item" data-template="reviews-template">Добавить отзыв</button>
          </div>
        </section>

        <div class="submit-row">
          <button type="submit" class="admin-btn admin-btn-gold">Сохранить все изменения</button>
        </div>
      </form>
    </main>
  </div>

  <template id="features-template">
    <div class="repeater-item">
      <div class="repeater-row repeater-row-compact">
        <label><span>Номер</span><input type="text" name="features[number][]" value=""></label>
        <label><span>Заголовок</span><input type="text" name="features[title][]" value=""></label>
        <label class="full"><span>Текст</span><textarea name="features[text][]" rows="3"></textarea></label>
      </div>
      <button type="button" class="remove-item">Удалить</button>
    </div>
  </template>

  <template id="ticker-template">
    <div class="repeater-item">
      <div class="repeater-row">
        <label><span>Название</span><input type="text" name="ticker[title][]" value=""></label>
        <label class="full"><span>Путь к фото</span><input type="text" name="ticker[image][]" value=""></label>
      </div>
      <button type="button" class="remove-item">Удалить</button>
    </div>
  </template>

  <template id="menu-template">
    <div class="repeater-item">
      <div class="repeater-row">
        <label><span>Категория</span><input type="text" name="menu[category][]" value=""></label>
        <label><span>Название</span><input type="text" name="menu[title][]" value=""></label>
        <label><span>Цена</span><input type="text" name="menu[price][]" value=""></label>
        <label><span>Бейдж</span><input type="text" name="menu[label][]" value=""></label>
        <label class="full"><span>Описание</span><textarea name="menu[description][]" rows="3"></textarea></label>
        <label class="full"><span>Путь к фото</span><input type="text" name="menu[image][]" value=""></label>
      </div>
      <button type="button" class="remove-item">Удалить</button>
    </div>
  </template>

  <template id="promotions-template">
    <div class="repeater-item">
      <div class="repeater-row repeater-row-compact">
        <label><span>Бейдж</span><input type="text" name="promotions[badge][]" value=""></label>
        <label><span>Название</span><input type="text" name="promotions[title][]" value=""></label>
        <label class="full"><span>Описание</span><textarea name="promotions[text][]" rows="3"></textarea></label>
      </div>
      <button type="button" class="remove-item">Удалить</button>
    </div>
  </template>

  <template id="gallery-template">
    <div class="repeater-item">
      <div class="repeater-row">
        <label><span>Подпись</span><input type="text" name="gallery[title][]" value=""></label>
        <label class="full"><span>Путь к фото</span><input type="text" name="gallery[image][]" value=""></label>
      </div>
      <button type="button" class="remove-item">Удалить</button>
    </div>
  </template>

  <template id="reviews-template">
    <div class="repeater-item">
      <div class="repeater-row repeater-row-compact">
        <label><span>Имя</span><input type="text" name="reviews[name][]" value=""></label>
        <label class="full"><span>Текст</span><textarea name="reviews[text][]" rows="3"></textarea></label>
      </div>
      <button type="button" class="remove-item">Удалить</button>
    </div>
  </template>

  <script src="../assets/js/admin.js"></script>
</body>
</html>
