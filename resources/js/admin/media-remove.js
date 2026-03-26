const mediaFields = document.querySelectorAll('[data-media-field]');

mediaFields.forEach((field) => {
  const removeInput = field.querySelector('[data-media-remove-input]');
  const mediaCard = field.querySelector('[data-media-card]');
  const removeButton = field.querySelector('[data-media-remove]');
  const fileInput = field.querySelector('[data-media-file]');

  if (!removeInput || !mediaCard || !removeButton) {
    return;
  }

  removeButton.addEventListener('click', () => {
    const nextValue = removeInput.value === '1' ? '0' : '1';
    removeInput.value = nextValue;
    mediaCard.classList.toggle('is-pending-remove', nextValue === '1');
  });

  if (fileInput instanceof HTMLInputElement) {
    fileInput.addEventListener('change', () => {
      removeInput.value = '0';
      mediaCard.classList.remove('is-pending-remove');
    });
  }
});
