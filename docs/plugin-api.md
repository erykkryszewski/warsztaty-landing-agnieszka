# Plugin API

## Philosophy

Plugins are controlled extensions. They use only supported extension points: routes, admin menu items, settings sections, page content groups, and page slot partials.

## Registration

Listed in `config/plugins.php`. Each plugin implements `App\Plugins\PluginInterface`:

- `definition(): array` — key, name, description, required, enabled_by_default
- `register(PluginApi $api): void` — registers extensions

## API methods

```php
// Routes
$api->get($path, $handler, $middleware = []);
$api->post($path, $handler, $middleware = []);

// Admin menu
$api->adminMenu($label, $url, $order = 50);

// Settings section
$api->settingsSection($sectionDefinition);

// Page extension (adds editable group + optional template slot)
$api->extendPage($pageKey, $groupDefinition, $slot = null, $view = null);
```

## Page extension example

```php
$api->extendPage('home', [
    'key' => 'testimonials',
    'label' => 'Opinie klientów',
    'fields' => [
        ['name' => 'section_title', 'type' => 'text', 'label' => 'Tytuł sekcji', 'default' => 'Co mówią klienci'],
        [
            'name' => 'items',
            'type' => 'repeater',
            'label' => 'Opinie',
            'button_label' => 'Dodaj opinię',
            'fields' => [
                ['name' => 'author', 'type' => 'text', 'label' => 'Autor'],
                ['name' => 'quote', 'type' => 'textarea', 'label' => 'Treść'],
            ],
            'default' => [],
        ],
    ],
], 'after-intro', 'plugins/testimonials/section');
```

Template slot in page view:
```php
<?= plugin_slot('home', 'after-intro', ['page' => $page]) ?>
```

## File layout

- PHP: `app/Plugins/<PluginName>/Plugin.php`
- Views: `resources/views/plugins/<plugin-name>/`
- Registration: `config/plugins.php`

## Rules

- Do not invent new storage systems.
- Do not bypass page groups for editable content.
- Use repeaters for lists the admin should manage.
- Keep plugin code small and predictable.
