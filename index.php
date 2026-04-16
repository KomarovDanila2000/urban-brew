<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$content = load_content();
$site = $content['site'] ?? [];
$brand = $site['brand'] ?? 'Urban Brew';
$ticker = $content['ticker'] ?? [];
$features = $content['features'] ?? [];
$menu = $content['menu'] ?? [];
$promotions = $content['promotions'] ?? [];
$gallery = $content['gallery'] ?? [];
$reviews = $content['reviews'] ?? [];

$marqueeItems = array_merge($ticker, $ticker);
$marqueeDuration = max(16, (int) ($site['marquee_speed'] ?? 28));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($brand) ?> — премиальная кофейня</title>
  <meta name="description" content="<?= e($site['hero_text'] ?? 'Премиальная кофейня с авторскими напитками и красивой атмосферой.') ?>">
  <meta name="theme-color" content="#0d0b0a">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-row">
      <a class="brand" href="#top"><?= e($brand) ?></a>

      <button class="nav-toggle" aria-label="Открыть меню" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>

      <nav class="site-nav">
        <a href="#about">О нас</a>
        <a href="#menu">Меню</a>
        <a href="#promotions">Акции</a>
        <a href="#gallery">Галерея</a>
        <a href="#contacts">Контакты</a>
        <a class="admin-link" href="admin/login.php">Админка</a>
      </nav>
    </div>
  </header>

  <section class="marquee-section">
    <div class="marquee-shell">
      <div class="marquee-track" style="--marquee-duration: <?= e((string) $marqueeDuration) ?>s;">
        <?php foreach ($marqueeItems as $item): ?>
          <article class="marquee-card">
            <div class="marquee-image">
              <img src="<?= e(image_url($item['image'] ?? '')) ?>" alt="<?= e($item['title'] ?? '') ?>">
            </div>
            <div class="marquee-caption"><?= e($item['title'] ?? '') ?></div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <main id="top">
    <section class="hero-section">
      <div class="container hero-grid">
        <div class="hero-copy reveal">
          <p class="eyebrow"><?= e($site['hero_badge'] ?? 'Signature Coffee House') ?></p>
          <h1><?= e($site['hero_title'] ?? 'Темп города. Вкус тишины. Кофе с характером.') ?></h1>
          <p class="hero-description"><?= e($site['hero_text'] ?? '') ?></p>

          <div class="hero-actions">
            <a class="btn btn-gold" href="<?= e($site['cta_primary_link'] ?? '#menu') ?>"><?= e($site['cta_primary_text'] ?? 'Посмотреть меню') ?></a>
            <a class="btn btn-outline" href="<?= e($site['cta_secondary_link'] ?? '#contacts') ?>"><?= e($site['cta_secondary_text'] ?? 'Забронировать столик') ?></a>
          </div>

          <div class="hero-stats">
            <article class="stat-card">
              <span>График</span>
              <strong><?= e($site['hours'] ?? 'Ежедневно') ?></strong>
            </article>
            <article class="stat-card">
              <span>Меню</span>
              <strong><?= e((string) count($menu)) ?> позиций</strong>
            </article>
            <article class="stat-card">
              <span>Формат</span>
              <strong>Luxury Coffee</strong>
            </article>
          </div>
        </div>

        <div class="hero-media reveal delay-1">
          <div class="image-frame hero-frame">
            <img src="<?= e(image_url($site['hero_image'] ?? '')) ?>" alt="<?= e($brand) ?>">
          </div>
        </div>
      </div>
    </section>

    <section class="section about-section" id="about">
      <div class="container section-heading reveal">
        <p class="eyebrow">О кофейне</p>
        <h2>Атмосфера, подача и вкус — как единый премиальный бренд</h2>
        <p class="section-text narrow-text">
          Этот сайт уже готов под реальные фотографии, цены, товары, сезонные акции и обновления через админку.
          Замени текст и изображения — и у тебя будет сильный кейс для портфолио или рабочий сайт кофейни.
        </p>
      </div>

      <div class="container feature-grid">
        <?php foreach ($features as $index => $item): ?>
          <article class="feature-card reveal delay-<?= e((string) min($index, 2)) ?>">
            <span class="feature-number"><?= e($item['number'] ?? sprintf('%02d', $index + 1)) ?></span>
            <h3><?= e($item['title'] ?? '') ?></h3>
            <p><?= e($item['text'] ?? '') ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section menu-section" id="menu">
      <div class="container section-heading reveal">
        <p class="eyebrow">Меню</p>
        <h2>Редактируемые товары, цены и описания</h2>
        <p class="section-text narrow-text">
          Все карточки ниже можно менять через админку: названия, цены, описания, категории, бейджи и изображения.
        </p>
      </div>

      <div class="container menu-grid">
        <?php foreach ($menu as $index => $item): ?>
          <article class="product-card reveal delay-<?= e((string) min($index % 3, 2)) ?>">
            <div class="product-image">
              <img src="<?= e(image_url($item['image'] ?? '')) ?>" alt="<?= e($item['title'] ?? '') ?>">
            </div>
            <div class="product-body">
              <div class="product-topline">
                <span class="pill"><?= e($item['category'] ?? 'Coffee') ?></span>
                <?php if (!empty($item['label'])): ?>
                  <span class="product-label"><?= e($item['label']) ?></span>
                <?php endif; ?>
              </div>
              <div class="product-head">
                <h3><?= e($item['title'] ?? '') ?></h3>
                <strong class="price"><?= e($item['price'] ?? '') ?></strong>
              </div>
              <p><?= e($item['description'] ?? '') ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section promo-section" id="promotions">
      <div class="container promo-shell reveal">
        <div class="section-heading compact">
          <p class="eyebrow">Акции и сезонные предложения</p>
          <h2>Блок спецпредложений с редактированием из админки</h2>
        </div>

        <div class="promo-grid">
          <?php foreach ($promotions as $index => $item): ?>
            <article class="promo-card">
              <?php if (!empty($item['badge'])): ?>
                <span class="tag"><?= e($item['badge']) ?></span>
              <?php endif; ?>
              <h3><?= e($item['title'] ?? '') ?></h3>
              <p><?= e($item['text'] ?? '') ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section gallery-section" id="gallery">
      <div class="container section-heading reveal">
        <p class="eyebrow">Галерея</p>
        <h2>Загружай свои фото кофе, интерьера и работ</h2>
        <p class="section-text narrow-text">
          Сразу после замены плейсхолдеров на реальные тёмные фото сайт будет выглядеть заметно дороже.
        </p>
      </div>

      <div class="container gallery-grid">
        <?php foreach ($gallery as $index => $item): ?>
          <figure class="gallery-item <?= $index === 0 ? 'gallery-tall' : '' ?> <?= $index === 4 ? 'gallery-wide' : '' ?> reveal delay-<?= e((string) min($index % 3, 2)) ?>">
            <img src="<?= e(image_url($item['image'] ?? '')) ?>" alt="<?= e($item['title'] ?? '') ?>">
            <figcaption><?= e($item['title'] ?? '') ?></figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section reviews-section" id="reviews">
      <div class="container section-heading reveal">
        <p class="eyebrow">Отзывы</p>
        <h2>Тоже редактируются из админки</h2>
      </div>

      <div class="container reviews-grid">
        <?php foreach ($reviews as $index => $item): ?>
          <article class="review-card reveal delay-<?= e((string) min($index, 2)) ?>">
            <p>“<?= e($item['text'] ?? '') ?>”</p>
            <strong><?= e($item['name'] ?? '') ?></strong>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section contacts-section" id="contacts">
      <div class="container contacts-grid">
        <div class="reveal">
          <p class="eyebrow">Контакты</p>
          <h2>Удобно смотреть и на десктопе, и на смартфонах</h2>

          <div class="contact-list">
            <p><span>Адрес</span><strong><?= e($site['address'] ?? '') ?></strong></p>
            <p><span>Телефон</span><strong><a href="tel:<?= e(preg_replace('/\s+/', '', (string) ($site['phone'] ?? ''))) ?>"><?= e($site['phone'] ?? '') ?></a></strong></p>
            <p><span>Email</span><strong><a href="mailto:<?= e($site['email'] ?? '') ?>"><?= e($site['email'] ?? '') ?></a></strong></p>
            <p><span>Часы работы</span><strong><?= e($site['hours'] ?? '') ?></strong></p>
          </div>

          <div class="hero-actions">
            <a class="btn btn-gold" href="tel:<?= e(preg_replace('/\s+/', '', (string) ($site['phone'] ?? ''))) ?>">Позвонить</a>
            <a class="btn btn-outline" href="<?= e($site['instagram'] ?? '#') ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
          </div>
        </div>

        <div class="reveal delay-1">
          <div class="map-shell">
            <iframe src="<?= e($site['map_embed'] ?? '') ?>" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container footer-row">
      <div>
        <a class="brand footer-brand" href="#top"><?= e($brand) ?></a>
        <p>Премиальный одностраничный сайт с собственной админкой для редактирования контента.</p>
      </div>

      <div class="footer-links">
        <a href="#menu">Меню</a>
        <a href="#promotions">Акции</a>
        <a href="#gallery">Галерея</a>
        <a href="<?= e($site['telegram'] ?? '#') ?>" target="_blank" rel="noopener noreferrer">Telegram</a>
      </div>
    </div>
  </footer>

  <script src="assets/js/main.js"></script>
</body>
</html>
