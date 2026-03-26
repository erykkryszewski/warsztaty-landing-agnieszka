<section class="admin-page-header">
    <h1><i class="fa-solid fa-puzzle-piece"></i> Wtyczki</h1>
    <p>Lista modułów rozszerzających starter.</p>
</section>

<section class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Opis</th>
                    <th>Status</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plugins as $plugin): ?>
                    <tr>
                        <td><strong><?= e($plugin['name']) ?></strong></td>
                        <td><?= e($plugin['description']) ?></td>
                        <td>
                            <?php if ($plugin['enabled']): ?>
                                <span class="admin-badge admin-badge--success"><i class="fa-solid fa-circle-check"></i> Aktywna</span>
                            <?php else: ?>
                                <span class="admin-badge admin-badge--muted"><i class="fa-solid fa-circle-xmark"></i> Wyłączona</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($plugin['required'])): ?>
                                <span class="admin-badge"><i class="fa-solid fa-lock"></i> Wymagana</span>
                            <?php else: ?>
                                <form method="post" action="<?= e(url('admin/plugins/' . $plugin['key'] . '/toggle/')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="enabled" value="<?= $plugin['enabled'] ? '0' : '1' ?>">
                                    <?php if ($plugin['enabled']): ?>
                                        <button class="button button--ghost button--small" type="submit">
                                            <i class="fa-solid fa-toggle-off"></i> Wyłącz
                                        </button>
                                    <?php else: ?>
                                        <button class="button button--small" type="submit">
                                            <i class="fa-solid fa-toggle-on"></i> Włącz
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
