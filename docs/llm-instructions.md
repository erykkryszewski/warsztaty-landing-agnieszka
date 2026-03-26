# LLM Instructions

## What is this

Ercoding CMS is a lightweight PHP starter for brochure-style business websites. You will use it as a foundation to build client websites. The CMS provides an admin panel, editable page templates, a plugin system, and theme customization.

## Recommended prompt flow

If you are starting a new project from scratch, use these two prompts in order:

1. `docs/llm-prompt-01-environment.md`
2. `docs/llm-prompt-02-project.md`

The first prompt is for XAMPP environment setup, database creation, reset, cleanup, and leaving a clean starter state.
The second prompt is for actual project implementation.

## Your workflow

1. Read this file and `docs/content-model.md` first.
2. User gives you content (texts, images, brand info).
3. You modify page definitions, templates, styles, and seed data.
4. The client manages content through the admin panel after launch.

## Non-negotiable rules

- Keep the project small. No heavy frameworks.
- Keep developer-facing code and docs in English.
- Keep admin-facing labels in Polish.
- Keep public brochure pages in the editable template model.
- Do not add a page builder, ORM, or frontend framework.
- Do not change the core architecture.

## Theme system

The admin panel has a "Motyw strony" (Theme) settings section. It stores:

- `font_primary` — body font (Google Fonts name, e.g. "Manrope")
- `font_decorated` — heading font (Google Fonts name, e.g. "Fraunces")
- `color_main` — brand color HEX (e.g. "#231939") → maps to `--color-brand`
- `color_accent` — accent color HEX (e.g. "#db2d7e") → maps to `--color-accent`
- `color_text` — text color HEX (e.g. "#1a1a2e") → maps to `--color-text`

These values are injected as CSS custom properties in `resources/views/layout/app.php`. Google Fonts are loaded dynamically based on font names.

When building a new site:
1. Set the fonts and colors via `config/settings.php` defaults.
2. The admin can later change them in the panel without touching code.
3. All SCSS must use `var(--color-brand)`, `var(--color-accent)`, `var(--color-text)`, `var(--font-body)`, `var(--font-heading)` — never hardcode colors or font names in stylesheets.

## Where things go

### New public page templates

- definition: `config/pages.php`
- template: `resources/views/pages/`
- page-specific styles only if needed: `resources/scss/pages/`
- shared section styles: `resources/scss/sections/`

### New editable page content

- declare fields in the page definition in `config/pages.php`
- use groups, not ad hoc JSON structures
- keep field names in English
- keep field labels in Polish
- supported field types: `text`, `textarea`, `richtext`, `image`, `url`, `email`, `phone`, `repeater`

### Repeater fields (ACF-like)

When you create a section with repeatable items (e.g. testimonials, team members, features), use a `repeater` field type. This gives the admin an "add/remove" interface in the panel.

```php
[
    'name' => 'team_members',
    'type' => 'repeater',
    'label' => 'Członkowie zespołu',
    'button_label' => 'Dodaj osobę',
    'fields' => [
        ['name' => 'name', 'type' => 'text', 'label' => 'Imię i nazwisko'],
        ['name' => 'role', 'type' => 'text', 'label' => 'Stanowisko'],
        ['name' => 'bio', 'type' => 'textarea', 'label' => 'Opis'],
        ['name' => 'photo', 'type' => 'image', 'label' => 'Zdjęcie'],
    ],
    'default' => [
        ['name' => 'Jan Kowalski', 'role' => 'CEO', 'bio' => '...', 'photo' => ''],
    ],
]
```

In the template:
```php
$main = page_group($page, 'main');
foreach (($main['team_members'] ?? []) as $member):
    // render $member['name'], $member['role'], etc.
endforeach;
```

### Global settings

- core settings: `config/settings.php`
- plugin settings: plugin registration through `settingsSection()`

### New plugins

- PHP code: `app/Plugins/<PluginName>/`
- optional views: `resources/views/plugins/<plugin-name>/`
- registration list: `config/plugins.php`
- plugin can extend pages with editable groups and render in template slots

### Contact form

The contact form is already implemented with:
- honeypot spam protection
- CSRF token
- server-side validation
- database storage
- mail/log fallback

Do not reimplement the form. Just customize the fields and labels if needed.

## How to extend pages

1. Define or extend the page schema in `config/pages.php`.
2. Render the values in the template file.
3. Add only the minimal styles needed (use CSS variables).
4. Add JS only if the UI truly needs behavior.

## SEO requirements

Every page must have:
- unique `<title>` tag (admin-editable via SEO fields)
- `<meta name="description">` (admin-editable)
- `<link rel="canonical">` (auto-generated)
- Open Graph tags (auto-generated)
- semantic HTML (h1, h2, article, nav, main, footer)
- valid HTML structure
- lazy-loaded images with width/height attributes
- no render-blocking JS (use `type="module"` or `defer`)

The CMS auto-generates `robots.txt` and `sitemap.xml` from defined pages.

## SCSS conventions

- Use CSS custom properties from `resources/scss/global/_variables.scss`.
- **Always use variables**: `var(--color-brand)`, `var(--color-accent)`, `var(--color-text)`, `var(--font-body)`, `var(--font-heading)`.
- Never hardcode colors or font names.
- Entry points: `resources/scss/site.scss` and `resources/scss/admin.scss`.
- Folder structure: `global/` → `components/` → `sections/` → `pages/`.
- Class naming: kebab-case, BEM for parts (`block__element`, `block--modifier`).
- Keep selectors shallow.

## JavaScript conventions

- ES modules only.
- Entry points: `resources/js/site.js` and `resources/js/admin.js`.
- Use `data-` attributes for DOM hooks.
- No frontend framework unless there is a real product reason.

## Performance targets

- Google PageSpeed Insights score: 90+ on all metrics
- Use semantic HTML, minimal CSS, no heavy JS
- Preconnect to Google Fonts
- Use modern image formats (WebP) when possible

## Reset for new project

Run `php reset.php` (interactive) or `php reset.php --confirm` (non-interactive) to return the CMS to a clean starter state. This:
- Resets all page content to `config/pages.php` defaults (lorem/demo text)
- Resets all settings to `config/settings.php` defaults
- Deletes all blog posts and re-seeds sample ones
- Deletes all contact messages
- Cleans all uploaded files
- Resets admin user to `admin@example.pl` / `pass`
- Re-initializes all plugin states from their definitions

After reset the site is fully functional with default content. Start customizing `config/pages.php` defaults, templates, and styles for the new project.

## Decision rule

When uncertain, choose the solution that:
1. preserves the current content model
2. is easiest for the next developer or model to understand
3. adds the fewest new concepts
4. keeps the project maintainable for small brochure sites
