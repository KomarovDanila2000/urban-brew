document.querySelectorAll('.add-item').forEach((button) => {
  button.addEventListener('click', () => {
    const templateId = button.dataset.template;
    const template = document.getElementById(templateId);
    const wrapper = button.closest('.repeater')?.querySelector('.repeater-items');

    if (!template || !wrapper) return;

    wrapper.insertAdjacentHTML('beforeend', template.innerHTML);
  });
});

document.addEventListener('click', (event) => {
  const removeButton = event.target.closest('.remove-item');
  if (removeButton) {
    const item = removeButton.closest('.repeater-item');
    if (item) item.remove();
  }

  const copyButton = event.target.closest('.copy-btn');
  if (copyButton) {
    const value = copyButton.dataset.copy || '';
    navigator.clipboard.writeText(value).then(() => {
      const original = copyButton.textContent;
      copyButton.textContent = 'Скопировано';
      setTimeout(() => {
        copyButton.textContent = original;
      }, 1300);
    });
  }
});
