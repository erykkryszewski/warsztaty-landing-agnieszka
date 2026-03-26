<section class="admin-page-header">
    <h1><i class="fa-solid fa-pen-to-square"></i> Blog</h1>
    <a class="button button--small" href="<?= e(url('admin/blog/create/')) ?>">
        <i class="fa-solid fa-plus"></i> Dodaj wpis
    </a>
</section>

<section class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Tytuł</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Publikacja</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><strong><?= e($post['title']) ?></strong></td>
                        <td><?= e($post['slug']) ?></td>
                        <td>
                            <?php if ($post['status'] === 'published'): ?>
                                <span class="admin-badge admin-badge--success"><i class="fa-solid fa-circle-check"></i> Opublikowany</span>
                            <?php else: ?>
                                <span class="admin-badge admin-badge--muted"><i class="fa-solid fa-pencil"></i> Szkic</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($post['published_at'] ? date('d.m.Y H:i', strtotime((string) $post['published_at'])) : '-') ?></td>
                        <td class="admin-actions">
                            <a class="button button--small" href="<?= e(url('admin/blog/' . $post['id'] . '/edit/')) ?>">
                                <i class="fa-solid fa-pen-to-square"></i> Edytuj
                            </a>
                            <form method="post" action="<?= e(url('admin/blog/' . $post['id'] . '/delete/')) ?>" onsubmit="return confirm('Na pewno usunąć ten wpis?')">
                                <?= csrf_field() ?>
                                <button class="button button--danger button--small" type="submit">
                                    <i class="fa-solid fa-trash"></i> Usuń
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
