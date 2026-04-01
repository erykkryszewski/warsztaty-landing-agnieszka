<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel administracyjny</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= e(asset('assets/build/css/admin.css')) ?>">
</head>
<body class="admin-shell">
    <?php
    $deployState = $deployStatus['status'] ?? '';
    $deployTimestamp = trim((string) ($deployStatus['build_finished_at'] ?? $deployStatus['generated_at'] ?? ''));
    $deployDate = $deployTimestamp !== '' ? date('d.m.Y H:i', strtotime($deployTimestamp)) : '';
    ?>
    <aside class="admin-sidebar">
        <a class="admin-sidebar__brand" href="<?= e(url('admin/')) ?>">
            <i class="fa-solid fa-cube"></i>
            <span>Ercoding CMS</span>
        </a>
        <nav>
            <ul class="admin-nav">
                <?php foreach ($adminNavigation as $item): ?>
                    <li>
                        <?php if (($item['method'] ?? 'GET') === 'POST'): ?>
                            <form method="post" action="<?= e(url(ltrim($item['url'], '/'))) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="admin-nav__link admin-nav__link--logout">
                                    <i class="<?= e($item['icon'] ?? 'fa-solid fa-circle') ?>"></i>
                                    <span><?= e($item['label']) ?></span>
                                </button>
                            </form>
                        <?php else: ?>
                            <a class="admin-nav__link <?= is_active_path($item['url']) ? 'is-active' : '' ?>" href="<?= e(url(ltrim($item['url'], '/'))) ?>">
                                <i class="<?= e($item['icon'] ?? 'fa-solid fa-circle') ?>"></i>
                                <span><?= e($item['label']) ?></span>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar__group">
                <div class="admin-topbar__user">
                    <div class="admin-topbar__avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <strong><?= e($currentUser['name'] ?? 'Administrator') ?></strong>
                        <span><?= e($currentUser['email'] ?? '') ?></span>
                    </div>
                </div>

                <div class="admin-topbar__deploy <?= $deployState === 'success' ? 'is-success' : ($deployState === 'error' ? 'is-error' : 'is-idle') ?>">
                    <i class="fa-solid <?= $deployState === 'success' ? 'fa-box-archive' : ($deployState === 'error' ? 'fa-triangle-exclamation' : 'fa-clock-rotate-left') ?>"></i>
                    <div>
                        <strong>
                            <?php if ($deployState === 'success'): ?>
                                Paczka deploy gotowa
                            <?php elseif ($deployState === 'error'): ?>
                                Ostatni build deploy nie powiodl sie
                            <?php else: ?>
                                Brak historii builda deploy
                            <?php endif; ?>
                        </strong>
                        <span>
                            <?php if ($deployState === 'error' && !empty($deployStatus['error'])): ?>
                                <?= e($deployStatus['error']) ?>
                            <?php elseif ($deployDate !== ''): ?>
                                <?= e($deployDate) ?>
                            <?php else: ?>
                                Paczka pojawi sie po pierwszym zapisie danych.
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            <a class="admin-topbar__site-link" href="<?= e(url('/')) ?>" target="_blank" rel="noreferrer">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Zobacz stronę</span>
            </a>
        </header>

        <main class="admin-content">
            <?php include __DIR__ . '/../admin/partials/flash.php'; ?>
            <?= $content ?>
        </main>
    </div>

    <script type="module" src="<?= e(asset('assets/build/js/admin.js')) ?>"></script>
</body>
</html>
