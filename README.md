# Ercoding CMS

Lightweight PHP starter for brochure-style business websites. Admin panel, editable page templates, theme customization, blog, contact form, and a controlled plugin system.

## Stack

- PHP 8.2+, MySQL/MariaDB, Apache `.htaccess`
- Vite, SCSS, PostCSS + Autoprefixer, ES modules

## Installation

```bash
composer install
npm install
cp .env.example .env        # then edit database & URL
php database/migrate.php
php database/seed.php
npm run build
```

For development: `npm run dev`

## Admin panel

URL: `/admin/` — login with credentials from `.env` (default: `admin@example.pl` / `admin123`).

Features: dashboard, page editor, global settings (business info, theme, SEO), blog CRUD, plugin manager, user management.

### Theme settings

Admin panel → Ustawienia strony → Motyw strony:
- **Font główny** — Google Fonts name for body text
- **Font dekoracyjny** — Google Fonts name for headings
- **Kolor główny** — brand color (HEX)
- **Kolor akcentowy** — button/accent color (HEX)
- **Kolor tekstu** — main text color (HEX)

Changes apply immediately to the public site via CSS custom properties.

## Reset for new project

```bash
php reset.php --confirm
```

Clears all content, blog posts, contact messages, and uploads. Keeps admin users, plugin states, and database structure. After reset, update `config/pages.php` with new site content.

## Documentation

See `/docs` for full docs:
- `llm-site-builder-prompt.md` — two-prompt flow for LLMs: environment first, project second
- `llm-prompt-01-environment.md` — XAMPP, database, reset, cleanup, starter state
- `llm-prompt-02-project.md` — coding rules and full project build prompt
- `llm-instructions.md` — core implementation rules for LLMs
- `architecture.md` — technical architecture
- `content-model.md` — data model, field types, repeaters
- `frontend-conventions.md` — SCSS/JS conventions
- `plugin-api.md` — plugin system
- `how-to-create-page.md` — page creation walkthrough

## Core commands

```bash
composer install && composer dump-autoload
npm install && npm run build
php database/migrate.php
php database/seed.php
php reset.php --confirm
```
