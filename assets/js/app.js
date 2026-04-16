const getByPath = (obj, path) => path.split('.').reduce((acc, key) => acc?.[key], obj);

async function loadContent() {
  const response = await fetch('content/site.json');
  if (!response.ok) throw new Error('Не удалось загрузить content/site.json');
  return response.json();
}

function bindText(content) {
  document.querySelectorAll('[data-bind]').forEach((el) => {
    const value = getByPath(content, el.dataset.bind);
    if (typeof value === 'string') el.textContent = value;
  });
  if (content.site?.seoTitle) document.title = content.site.seoTitle;
  const desc = document.querySelector('meta[name="description"]');
  if (desc && content.site?.seoDescription) desc.setAttribute('content', content.site.seoDescription);
}

function renderBusinessSlider(slides = []) {
  const slider = document.getElementById('businessSlider');
  const dots = document.getElementById('businessSliderDots');
  const metaKicker = document.getElementById('businessSlideMetaKicker');
  const metaTitle = document.getElementById('businessSlideMetaTitle');
  const metaText = document.getElementById('businessSlideMetaText');
  const current = document.getElementById('businessSlideCurrent');
  const total = document.getElementById('businessSlideTotal');
  const progress = document.getElementById('businessSliderProgress');
  const prevButton = document.getElementById('businessSliderPrev');
  const nextButton = document.getElementById('businessSliderNext');
  const shell = document.querySelector('.business-slider-shell');

  if (!slider || !dots || !slides.length) return;

  let activeIndex = 0;
  let timerId = null;
  const autoplayMs = 4200;

  const getPositionClass = (index) => {
    const totalSlides = slides.length;
    const prevIndex = (activeIndex - 1 + totalSlides) % totalSlides;
    const nextIndex = (activeIndex + 1) % totalSlides;

    if (index === activeIndex) return 'is-active';
    if (index === prevIndex) return 'is-prev';
    if (index === nextIndex) return 'is-next';

    const offset = (index - activeIndex + totalSlides) % totalSlides;
    return offset < totalSlides / 2 ? 'is-hidden-right' : 'is-hidden-left';
  };

  const animateProgress = () => {
    if (!progress) return;
    progress.style.transition = 'none';
    progress.style.width = '0%';
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        progress.style.transition = `width ${autoplayMs - 120}ms linear`;
        progress.style.width = '100%';
      });
    });
  };

  const render = () => {
    slider.innerHTML = slides
      .map((slide, index) => `
        <article
          class="business-slide ${getPositionClass(index)}"
          data-slide="${index}"
          tabindex="0"
          aria-label="${slide.title}">
          <span class="business-slide-ring" aria-hidden="true"></span>
          <img src="${slide.image}" alt="${slide.title}" loading="lazy" />
          <div class="business-slide-overlay"></div>
          <div class="business-slide-shine" aria-hidden="true"></div>
          <div class="business-slide-content">
            <span class="business-slide-kicker">${slide.kicker || 'Urban Brew'}</span>
            <h3>${slide.title}</h3>
          </div>
        </article>`)
      .join('');

    dots.innerHTML = slides
      .map((_, index) => `
        <button
          class="business-dot ${index === activeIndex ? 'active' : ''}"
          type="button"
          aria-label="Перейти к слайду ${index + 1}"
          data-slide="${index}">
        </button>`)
      .join('');

    const activeSlide = slides[activeIndex];
    if (metaKicker) metaKicker.textContent = activeSlide.kicker || 'Urban Brew';
    if (metaTitle) metaTitle.textContent = activeSlide.title;
    if (metaText) metaText.textContent = activeSlide.text;
    if (current) current.textContent = String(activeIndex + 1).padStart(2, '0');
    if (total) total.textContent = String(slides.length).padStart(2, '0');

    animateProgress();
  };

  const stop = () => {
    if (timerId) clearInterval(timerId);
  };

  const goTo = (index) => {
    activeIndex = (index + slides.length) % slides.length;
    render();
    start();
  };

  const start = () => {
    stop();
    animateProgress();
    timerId = setInterval(() => {
      activeIndex = (activeIndex + 1) % slides.length;
      render();
    }, autoplayMs);
  };

  render();
  start();

  slider.addEventListener('click', (event) => {
    const slide = event.target.closest('[data-slide]');
    if (!slide) return;
    goTo(Number(slide.dataset.slide));
  });

  slider.addEventListener('keydown', (event) => {
    const slide = event.target.closest('[data-slide]');
    if (!slide) return;
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      goTo(Number(slide.dataset.slide));
    }
  });

  dots.addEventListener('click', (event) => {
    const button = event.target.closest('[data-slide]');
    if (!button) return;
    goTo(Number(button.dataset.slide));
  });

  prevButton?.addEventListener('click', () => goTo(activeIndex - 1));
  nextButton?.addEventListener('click', () => goTo(activeIndex + 1));

  shell?.addEventListener('mouseenter', stop);
  shell?.addEventListener('mouseleave', start);
}

function renderStats(stats = []) {
  const wrap = document.getElementById('heroStats');
  if (!wrap) return;
  wrap.innerHTML = stats
    .map((item) => `
      <article class="meta-card">
        <span>${item.label}</span>
        <strong>${item.value}</strong>
      </article>`)
    .join('');
}

function renderFeatures(features = []) {
  const wrap = document.getElementById('aboutFeatures');
  if (!wrap) return;
  wrap.innerHTML = features
    .map((item, index) => `
      <article class="feature-card reveal ${index ? `delay-${Math.min(index, 2)}` : ''}">
        <span class="feature-number">${item.number}</span>
        <h3>${item.title}</h3>
        <p>${item.text}</p>
      </article>`)
    .join('');
}

function renderMenu(categories = []) {
  const wrap = document.getElementById('menuGrid');
  if (!wrap) return;
  wrap.innerHTML = categories
    .map((category, index) => `
      <article class="menu-card reveal ${index ? `delay-${Math.min(index, 2)}` : ''}">
        <h3>${category.title}</h3>
        <ul>
          ${category.items
            .map((item) => `<li><span>${item.name}</span><strong>${item.price}</strong></li>`)
            .join('')}
        </ul>
      </article>`)
    .join('');
}

function renderSpecials(items = []) {
  const wrap = document.getElementById('specialsGrid');
  if (!wrap) return;
  wrap.innerHTML = items
    .map((item) => `
      <article class="signature-card">
        <span class="tag">${item.tag}</span>
        <h3>${item.title}</h3>
        <p>${item.text}</p>
      </article>`)
    .join('');
}

function renderGallery(images = []) {
  const wrap = document.getElementById('galleryGrid');
  if (!wrap) return;
  wrap.innerHTML = images
    .map((src, index) => {
      let extraClass = '';
      if (index === 0) extraClass = 'large';
      if (index === 4) extraClass = 'wide';
      return `
        <div class="gallery-item ${extraClass} reveal ${index % 3 === 1 ? 'delay-1' : index % 3 === 2 ? 'delay-2' : ''}">
          <img src="${src}" alt="Фотография кофейни ${index + 1}" loading="lazy" />
        </div>`;
    })
    .join('');
}

function renderReviews(items = []) {
  const wrap = document.getElementById('reviewsGrid');
  if (!wrap) return;
  wrap.innerHTML = items
    .map((item, index) => `
      <article class="review-card reveal ${index ? `delay-${Math.min(index, 2)}` : ''}">
        <p>${item.text}</p>
        <strong>${item.author}</strong>
      </article>`)
    .join('');
}

function bindLinks(content) {
  const heroImage = document.getElementById('heroImage');
  const aboutImage = document.getElementById('aboutImage');
  const primaryCta = document.getElementById('primaryCta');
  const secondaryCta = document.getElementById('secondaryCta');
  const phoneLink = document.getElementById('phoneLink');
  const emailLink = document.getElementById('emailLink');
  const mapButton = document.getElementById('mapButton');
  const whatsButton = document.getElementById('whatsButton');
  const instagramLink = document.getElementById('instagramLink');
  const telegramLink = document.getElementById('telegramLink');

  if (heroImage) heroImage.src = content.hero.heroImage;
  if (aboutImage) aboutImage.src = content.about.image;
  if (primaryCta) {
    primaryCta.textContent = content.hero.primaryCtaText;
    primaryCta.href = content.hero.primaryCtaLink;
  }
  if (secondaryCta) {
    secondaryCta.textContent = content.hero.secondaryCtaText;
    secondaryCta.href = content.hero.secondaryCtaLink;
  }
  if (phoneLink) phoneLink.href = `tel:${content.site.phone.replace(/\s+/g, '')}`;
  if (emailLink) emailLink.href = `mailto:${content.site.email}`;
  if (mapButton) mapButton.href = content.site.mapUrl;
  if (whatsButton) whatsButton.href = content.site.whatsapp;
  if (instagramLink) instagramLink.href = content.site.instagram;
  if (telegramLink) telegramLink.href = content.site.telegram;
}

function initMenu() {
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.site-nav');
  const navLinks = document.querySelectorAll('.site-nav a');
  if (!toggle || !nav) return;
  toggle.addEventListener('click', () => {
    const isOpen = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(isOpen));
  });
  navLinks.forEach((link) => {
    link.addEventListener('click', () => {
      nav.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    });
  });
}

function initReveal() {
  const items = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) entry.target.classList.add('in-view');
      });
    },
    { threshold: 0.12 }
  );
  items.forEach((item) => observer.observe(item));
}

(async () => {
  try {
    const content = await loadContent();
    bindText(content);
    bindLinks(content);
    renderBusinessSlider(content.businessCoffeeSlides);
    renderStats(content.hero.stats);
    renderFeatures(content.about.features);
    renderMenu(content.menu.categories);
    renderSpecials(content.specials.items);
    renderGallery(content.gallery.images);
    renderReviews(content.reviews.items);
    initMenu();
    initReveal();
  } catch (error) {
    console.error(error);
    document.body.innerHTML = `<div style="padding:40px;color:#fff;font-family:Inter,sans-serif">Ошибка загрузки сайта. Проверь файл <strong>content/site.json</strong>.</div>`;
  }
})();
