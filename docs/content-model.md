# Content Model

## Core rule

This starter uses an editable template model:
- Structure defined in code.
- Values stored in database.
- Admin forms auto-generated from field definitions.
- Frontend templates render known values.

## Page definitions

Pages defined in `config/pages.php`. Each page has:
- `key`, `slug`, `view`, `navigation_label`, `admin_label`
- `groups` → each group has `key`, `label`, `fields`

## Field types

| Type | Description | Storage |
|------|-------------|---------|
| `text` | Single line input | string |
| `textarea` | Multi-line text | string |
| `richtext` | HTML content | string (HTML) |
| `image` | File upload | string (path) |
| `url` | URL input | string |
| `email` | Email input | string |
| `phone` | Phone input | string |
| `repeater` | Array of sub-fields | array of objects |

## Repeaters

Repeater fields store arrays. Used for lists (services, testimonials, team members, FAQ items). Each repeater has `fields` array and optional `button_label`.

```php
[
    'name' => 'items',
    'type' => 'repeater',
    'label' => 'Elementy',
    'button_label' => 'Dodaj element',
    'fields' => [
        ['name' => 'title', 'type' => 'text', 'label' => 'Tytuł'],
        ['name' => 'text', 'type' => 'textarea', 'label' => 'Opis'],
    ],
]
```

Admin renders add/remove buttons automatically.

## Group-based storage

Each page stores one JSON payload:
```json
{
  "main": { "hero_title": "...", "services_list": [...] },
  "testimonials": { "section_title": "...", "items": [...] }
}
```

## Frontend rendering

```php
$main = page_group($page, 'main');
echo e($main['hero_title'] ?? '');

foreach (($main['services_list'] ?? []) as $item):
    echo e($item['title'] ?? '');
endforeach;
```

Plugin sections rendered in known slots:
```php
<?= plugin_slot('home', 'after-intro', ['page' => $page]) ?>
```

## Global settings

Same model as page groups. Defined in `config/settings.php`. Includes:
- `business` — company name, phone, email, address, logo
- `theme` — fonts (font_primary, font_decorated) and colors (color_main, color_accent, color_text)
- `seo_defaults` — default SEO title, description, OG image

Theme settings are wired to CSS custom properties. When the admin changes colors or fonts, the site updates immediately.

## Admin mapping

Admin reads field definitions → renders inputs → validates → stores JSON.
Use the shared field rendering path (`resources/views/admin/partials/field.php`).
