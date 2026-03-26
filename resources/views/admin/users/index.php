<section class="admin-page-header">
    <h1><i class="fa-solid fa-users"></i> Użytkownicy</h1>
    <a class="button button--small" href="<?= e(url('admin/users/create/')) ?>">
        <i class="fa-solid fa-user-plus"></i> Dodaj użytkownika
    </a>
</section>

<section class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>E-mail</th>
                    <th>Rola</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><strong><?= e($user['name']) ?></strong></td>
                        <td><?= e($user['email']) ?></td>
                        <td>
                            <span class="admin-badge"><?= e($user['role']) ?></span>
                        </td>
                        <td>
                            <a class="button button--small" href="<?= e(url('admin/users/' . $user['id'] . '/edit/')) ?>">
                                <i class="fa-solid fa-pen-to-square"></i> Edytuj
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
