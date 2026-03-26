<section class="admin-page-header">
    <h1><i class="fa-solid fa-user-pen"></i> <?= e($formTitle) ?></h1>
</section>

<form class="admin-form admin-form--stacked" method="post" action="<?= e(url(ltrim($formAction, '/'))) ?>">
    <?= csrf_field() ?>
    <section class="admin-panel">
        <div class="admin-panel__body">
            <div class="admin-field">
                <label class="admin-field__label" for="user-name">Nazwa użytkownika</label>
                <input id="user-name" type="text" name="name" value="<?= e($user['name']) ?>">
                <?php if (has_error('name')): ?><p class="admin-field__error"><?= e(error_message('name')) ?></p><?php endif; ?>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="user-email">E-mail</label>
                <input id="user-email" type="email" name="email" value="<?= e($user['email']) ?>">
                <?php if (has_error('email')): ?><p class="admin-field__error"><?= e(error_message('email')) ?></p><?php endif; ?>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="user-role">Rola</label>
                <select id="user-role" name="role">
                    <option value="editor" <?= selected($user['role'], 'editor') ?>>editor</option>
                    <option value="superadmin" <?= selected($user['role'], 'superadmin') ?>>superadmin</option>
                </select>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="user-password">Hasło</label>
                <input id="user-password" type="password" name="password">
                <?php if (has_error('password')): ?><p class="admin-field__error"><?= e(error_message('password')) ?></p><?php endif; ?>
            </div>
        </div>
    </section>
    <button class="button" type="submit">
        <i class="fa-solid fa-floppy-disk"></i> Zapisz
    </button>
</form>
