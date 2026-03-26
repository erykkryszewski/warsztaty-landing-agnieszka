<section class="admin-page-header">
    <h1><i class="fa-solid fa-file-lines"></i> Strony</h1>
    <p>Lista predefiniowanych stron z edytowalną treścią.</p>
</section>

<section class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Strona</th>
                    <th>Adres</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?= e($page['admin_label']) ?></td>
                        <td><a href="<?= e(url(ltrim($page['slug'], '/'))) ?>" target="_blank" rel="noreferrer"><?= e($page['slug']) ?></a></td>
                        <td>
                            <a class="button button--small" href="<?= e(url('admin/pages/' . $page['key'] . '/')) ?>">
                                <i class="fa-solid fa-pen-to-square"></i> Edytuj
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
