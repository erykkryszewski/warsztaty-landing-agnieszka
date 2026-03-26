# Architecture

## Core principle

The codebase uses an editable template model:

1. Page defined in code (`config/pages.php`).
2. Editable fields declared in code (groups → fields).
3. Admin panel reads definitions and renders form inputs.
4. Database stores only the values (JSON).
5. Public template renders those values.

Same model applies to global settings and plugin-provided sections.

## Folder structure

```
/app                    # Application code
  /Core                 # Framework foundation (Router, Database, View, Session, etc.)
  /Helpers              # Global helper functions
  /Http/Controllers     # Request handlers (Admin/ and public)
  /Http/Middleware       # Auth, CSRF, guest checks
  /Models               # Database models (thin wrappers)
  /Services             # Business logic
  /Plugins              # Plugin implementations
/bootstrap              # App bootstrapping
/config                 # Page, settings, plugin definitions
/database               # Migrations and seeders
/docs                   # Documentation (this folder)
/public                 # Front controller, built assets, uploads
/resources
  /scss                 # SCSS source (global, components, sections, pages, admin)
  /js                   # JS source (ES modules)
  /views                # PHP templates (layout, pages, admin, partials, plugins)
/routes                 # Route definitions (web.php, admin.php)
/storage                # Cache
```

## Request flow

1. Apache rewrites to `public/index.php`.
2. Bootstrap loads env, config, helpers, services.
3. Router matches request to route.
4. Middleware runs (auth, CSRF).
5. Controller fetches data via services.
6. View renders template with shared site data.

## Theme system

Admin panel "Motyw strony" section stores font names and 3 colors. These are injected as CSS custom properties in the public layout (`resources/views/layout/app.php`). Google Fonts loaded dynamically.

SCSS files use `var(--color-brand)`, `var(--color-accent)`, `var(--color-text)`, `var(--font-body)`, `var(--font-heading)`.

## Plugin system

Plugins are explicit modules in `config/plugins.php`. Extension points:
- Public/admin routes
- Admin menu items
- Settings sections
- Page content groups (with template slot rendering)

## Content storage

- **Pages**: JSON payload keyed by content group in `pages` table.
- **Settings**: JSON payload by section key in `settings` table.
- **Blog posts**: Dedicated `blog_posts` table.
- **Contact messages**: Dedicated `contact_messages` table.
- **Plugin states**: `plugin_states` table.
