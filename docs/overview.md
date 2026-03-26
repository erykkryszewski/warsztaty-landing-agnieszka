# Ercoding CMS Overview

## What this project is

A lightweight PHP CMS starter for brochure-style business websites. Includes an admin panel, editable page templates, a blog, contact form, plugin system, and theme customization (fonts + colors).

Designed for repeated use: reset, customize content, deploy.

## What this project is not

- Not a page builder.
- Not a WordPress replacement.
- Not a general-purpose CMS for arbitrary content structures.
- Not a large framework application.

## Product philosophy

- Page structure lives in code.
- Editable fields are declared explicitly.
- Content values live in the database.
- Templates render known fields.
- Plugins extend only defined extension points.
- Theme (fonts, colors) is manageable from the admin panel.
- Frontend structure stays predictable for AI-generated work.

## Intended use case

Use this starter for: company brochure websites, local service business sites, simple marketing pages, websites with a small blog and contact form.

Do not use it for: complex editorial workflows, visual layout builders, ecommerce, public user accounts, API-first architectures.

## Key commands

```bash
composer install              # PHP dependencies
npm install                   # Frontend dependencies
php database/migrate.php      # Create tables
php database/seed.php         # Seed demo data
npm run build                 # Build assets
npm run dev                   # Dev mode with file watching
php reset.php --confirm       # Reset to blank slate for new project
```
