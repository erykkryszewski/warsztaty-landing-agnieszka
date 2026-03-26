# How To Create A Page

## Steps

### 1. Add page definition in `config/pages.php`

```php
[
    'key' => 'team',
    'slug' => '/zespol/',
    'title' => 'Team',
    'admin_label' => 'Zespół',
    'navigation_label' => 'Zespół',
    'view' => 'pages/team',
    'show_in_navigation' => true,
    'groups' => [
        [
            'key' => 'main',
            'label' => 'Treść strony',
            'fields' => [
                ['name' => 'hero_title', 'type' => 'text', 'label' => 'Nagłówek', 'default' => 'Nasz zespół'],
                ['name' => 'hero_subtitle', 'type' => 'textarea', 'label' => 'Podtytuł', 'default' => ''],
                [
                    'name' => 'members',
                    'type' => 'repeater',
                    'label' => 'Członkowie',
                    'button_label' => 'Dodaj osobę',
                    'fields' => [
                        ['name' => 'name', 'type' => 'text', 'label' => 'Imię'],
                        ['name' => 'role', 'type' => 'text', 'label' => 'Stanowisko'],
                        ['name' => 'photo', 'type' => 'image', 'label' => 'Zdjęcie'],
                    ],
                    'default' => [],
                ],
            ],
        ],
    ],
]
```

### 2. Create template `resources/views/pages/team.php`

```php
<?php $main = page_group($page, 'main'); ?>
<main>
    <section class="hero hero--inner">
        <div class="container">
            <h1><?= e($main['hero_title'] ?? '') ?></h1>
            <p class="hero__lead"><?= e($main['hero_subtitle'] ?? '') ?></p>
        </div>
    </section>
    <section class="section">
        <div class="container card-grid">
            <?php foreach (($main['members'] ?? []) as $member): ?>
                <article class="card">
                    <?php if (!empty($member['photo'])): ?>
                        <img src="<?= e(asset(ltrim($member['photo'], '/'))) ?>" alt="<?= e($member['name'] ?? '') ?>">
                    <?php endif; ?>
                    <h2><?= e($member['name'] ?? '') ?></h2>
                    <p><?= e($member['role'] ?? '') ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
```

### 3. Routing — automatic

Standard pages auto-register from config. No extra route needed.

### 4. Admin — automatic

Page appears in admin page list and editor automatically.

### 5. Add styles if needed

Use existing shared classes (`.hero`, `.card-grid`, `.card`, `.container`, `.section`). Add page-specific styles only when necessary.

### 6. Seed defaults

Set useful `default` values in field definitions. The seeder uses these.

### 7. Re-seed

```bash
php database/seed.php
```

## Rules

- Field `name` in English, `label` in Polish.
- Use `repeater` for any list the admin should manage.
- Use `var(--color-*)` and `var(--font-*)` in styles.
- Do not create custom tables for brochure pages.
- Do not hardcode content in templates.
