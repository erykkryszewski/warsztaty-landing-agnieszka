const repeaterItemsSelector = '[data-repeater-item]';

const fieldId = (value) => value.replaceAll('[', '_').replaceAll(']', '_');

const updateAttributePrefix = (element, attribute, oldPrefix, newPrefix) => {
  const currentValue = element.getAttribute(attribute);

  if (!currentValue) {
    return;
  }

  const nextValue = currentValue.replaceAll(oldPrefix, newPrefix);

  if (nextValue !== currentValue) {
    element.setAttribute(attribute, nextValue);
  }
};

const updateRepeaterItemTitle = (item, index) => {
  const title = item.querySelector('[data-repeater-item-title]');

  if (!(title instanceof HTMLElement)) {
    return;
  }

  const label = title.dataset.repeaterItemLabel ?? title.textContent ?? '';
  title.textContent = `${label} #${index + 1}`;
};

const updateRepeaterItemAttributes = (repeater, item, nextIndex) => {
  const repeaterPath = repeater.dataset.repeaterPath ?? '';
  const currentIndex = item.dataset.repeaterIndex ?? String(nextIndex);

  if (repeaterPath === '') {
    item.dataset.repeaterIndex = String(nextIndex);
    updateRepeaterItemTitle(item, nextIndex);
    return;
  }

  const oldPrefix = `${repeaterPath}[${currentIndex}]`;
  const newPrefix = `${repeaterPath}[${nextIndex}]`;
  const oldIdPrefix = fieldId(oldPrefix);
  const newIdPrefix = fieldId(newPrefix);
  const elements = [item, ...item.querySelectorAll('*')];

  elements.forEach((element) => {
    if (!(element instanceof HTMLElement)) {
      return;
    }

    updateAttributePrefix(element, 'name', oldPrefix, newPrefix);
    updateAttributePrefix(element, 'id', oldIdPrefix, newIdPrefix);
    updateAttributePrefix(element, 'for', oldIdPrefix, newIdPrefix);
    updateAttributePrefix(element, 'data-repeater-path', oldPrefix, newPrefix);
  });

  item.dataset.repeaterIndex = String(nextIndex);
  updateRepeaterItemTitle(item, nextIndex);
};

const reindexRepeater = (repeater) => {
  const items = repeater.querySelector('[data-repeater-items]');

  if (!(items instanceof HTMLElement)) {
    return;
  }

  items.querySelectorAll(repeaterItemsSelector).forEach((item, index) => {
    if (item instanceof HTMLElement) {
      updateRepeaterItemAttributes(repeater, item, index);
    }
  });
};

const appendRepeaterItem = (repeater) => {
  const items = repeater.querySelector('[data-repeater-items]');
  const template = repeater.querySelector('[data-repeater-template]');

  if (!(items instanceof HTMLElement) || !(template instanceof HTMLTemplateElement)) {
    return null;
  }

  const index = items.querySelectorAll(repeaterItemsSelector).length;
  const html = template.innerHTML.replaceAll('__INDEX__', String(index));
  items.insertAdjacentHTML('beforeend', html);

  const appendedItems = items.querySelectorAll(repeaterItemsSelector);
  const item = appendedItems[appendedItems.length - 1];

  if (!(item instanceof HTMLElement)) {
    return null;
  }

  reindexRepeater(repeater);

  return item;
};

const attachFileToInput = (input, file) => {
  if (!(input instanceof HTMLInputElement) || typeof DataTransfer === 'undefined') {
    return;
  }

  const transfer = new DataTransfer();
  transfer.items.add(file);
  input.files = transfer.files;
  input.dispatchEvent(new Event('change', { bubbles: true }));
};

const bindBulkUpload = (repeater) => {
  const bulkInput = repeater.querySelector('[data-repeater-bulk-upload]');

  if (!(bulkInput instanceof HTMLInputElement)) {
    return;
  }

  bulkInput.addEventListener('change', () => {
    const files = Array.from(bulkInput.files ?? []);

    files.forEach((file) => {
      const item = appendRepeaterItem(repeater);

      if (!(item instanceof HTMLElement)) {
        return;
      }

      const mediaInput = item.querySelector('[data-media-file]');

      if (mediaInput instanceof HTMLInputElement) {
        attachFileToInput(mediaInput, file);
      }
    });

    bulkInput.value = '';
  });
};

document.querySelectorAll('[data-repeater]').forEach((repeater) => {
  if (!(repeater instanceof HTMLElement)) {
    return;
  }

  const addButton = repeater.querySelector('[data-repeater-add]');

  if (addButton instanceof HTMLElement) {
    addButton.addEventListener('click', () => {
      appendRepeaterItem(repeater);
    });
  }

  repeater.addEventListener('click', (event) => {
    const target = event.target;

    if (!(target instanceof HTMLElement)) {
      return;
    }

    const removeButton = target.closest('[data-repeater-remove]');

    if (!(removeButton instanceof HTMLElement)) {
      return;
    }

    const item = removeButton.closest(repeaterItemsSelector);

    if (!(item instanceof HTMLElement)) {
      return;
    }

    item.remove();
    reindexRepeater(repeater);
  });

  bindBulkUpload(repeater);
  reindexRepeater(repeater);
});
