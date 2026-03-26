# Frontend Conventions

## SCSS structure

Entry points:
- `resources/scss/site.scss` (public site)
- `resources/scss/admin.scss` (admin panel)

Folder layers:
- `global/` — variables, reset, base rules
- `components/` — buttons, cards, forms
- `sections/` — header, hero, footer, content
- `pages/` — page-specific adjustments only
- `admin/` — admin layout and forms

## CSS variables (theme-driven)

All colors and fonts come from CSS custom properties set in the layout. Never hardcode.

```scss
// ✅ Correct
color: var(--color-brand);
font-family: var(--font-heading);
background: var(--color-accent);

// ❌ Wrong
color: #231939;
font-family: 'Fraunces';
```

Available variables:
- `--color-brand` — main brand color
- `--color-brand-strong` — darker brand shade
- `--color-accent` — accent/button color
- `--color-text` — main text color
- `--color-muted` — secondary text
- `--color-bg` — page background
- `--color-surface` — card/surface background
- `--color-line` — borders
- `--color-danger` — error states
- `--color-success` — success states
- `--font-body` — body font
- `--font-heading` — heading font
- `--radius-sm`, `--radius-md`, `--radius-lg` — border radii
- `--shadow-soft` — default shadow
- `--container` — container width

## SCSS rules

- Prefer shared styles before page-specific styles.
- Keep selectors shallow and readable.
- Use BEM naming: `block__element`, `block--modifier`.
- Use kebab-case for class names.

## JavaScript

- ES modules only. Entry: `resources/js/site.js` and `resources/js/admin.js`.
- Use `data-` attributes for DOM hooks.
- No frontend framework.
- Keep behavior small, explicit, discoverable.

## Adding new sections

1. Add markup in the view.
2. Add section styles in `resources/scss/sections/`.
3. Import the SCSS partial from `site.scss`.
4. Add JS only if the section truly needs behavior.
