const repeaters = document.querySelectorAll('[data-repeater]');

repeaters.forEach((repeater) => {
  const items = repeater.querySelector('[data-repeater-items]');
  const template = repeater.querySelector('[data-repeater-template]');
  const addButton = repeater.querySelector('[data-repeater-add]');

  if (!items || !template || !addButton) {
    return;
  }

  addButton.addEventListener('click', () => {
    const index = items.querySelectorAll('[data-repeater-item]').length;
    const html = template.innerHTML.replaceAll('__INDEX__', String(index));
    items.insertAdjacentHTML('beforeend', html);
  });

  repeater.addEventListener('click', (event) => {
    const target = event.target;

    if (!(target instanceof HTMLElement)) {
      return;
    }

    if (!target.matches('[data-repeater-remove]')) {
      return;
    }

    const item = target.closest('[data-repeater-item]');

    if (item) {
      item.remove();
    }
  });
});
