async function loadJson() {
  const response = await fetch('content/site.json');
  if (!response.ok) throw new Error('Не удалось загрузить content/site.json');
  return response.json();
}

function setValueByPath(obj, path, value) {
  const keys = path.split('.');
  const lastKey = keys.pop();
  const target = keys.reduce((acc, key) => {
    if (!(key in acc)) acc[key] = {};
    return acc[key];
  }, obj);
  target[lastKey] = value;
}

function getValueByPath(obj, path) {
  return path.split('.').reduce((acc, key) => acc?.[key], obj);
}

function downloadFile(filename, content) {
  const blob = new Blob([content], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  a.click();
  URL.revokeObjectURL(url);
}

let originalData = null;
let currentData = null;

function populateForm(data) {
  const form = document.getElementById('adminForm');
  form.querySelectorAll('input, textarea').forEach((field) => {
    const path = field.name;
    if (['hero.stats', 'businessCoffeeSlides', 'about', 'menu', 'promo', 'specials', 'gallery', 'reviews'].includes(path)) {
      const value = getValueByPath(data, path);
      field.value = JSON.stringify(value, null, 2);
    } else {
      field.value = getValueByPath(data, path) ?? '';
    }
  });
}

function readForm() {
  const form = document.getElementById('adminForm');
  const next = structuredClone(originalData);
  form.querySelectorAll('input, textarea').forEach((field) => {
    const path = field.name;
    let value = field.value;
    if (['hero.stats', 'businessCoffeeSlides', 'about', 'menu', 'promo', 'specials', 'gallery', 'reviews'].includes(path)) {
      try {
        value = JSON.parse(value);
      } catch (error) {
        throw new Error(`Поле ${path} содержит невалидный JSON`);
      }
    }
    setValueByPath(next, path, value);
  });
  return next;
}

(async () => {
  try {
    originalData = await loadJson();
    currentData = structuredClone(originalData);
    populateForm(currentData);

    document.getElementById('downloadJson').addEventListener('click', () => {
      try {
        currentData = readForm();
        downloadFile('site.json', JSON.stringify(currentData, null, 2));
      } catch (error) {
        alert(error.message);
      }
    });

    document.getElementById('resetForm').addEventListener('click', () => {
      populateForm(originalData);
    });
  } catch (error) {
    console.error(error);
    document.body.innerHTML = `<div style="padding:40px;color:#fff;font-family:Inter,sans-serif">Ошибка загрузки редактора. Проверь файл <strong>content/site.json</strong>.</div>`;
  }
})();
