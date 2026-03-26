<section class="admin-auth-header">
    <div class="admin-auth-logo">
        <i class="fa-solid fa-cube"></i>
    </div>
    <h1>Ercoding CMS</h1>
    <p>Zaloguj się do panelu administracyjnego</p>
</section>
<form class="admin-form admin-form--stacked" method="post" action="<?= e(url('admin/login/')) ?>">
    <?= csrf_field() ?>
    <div class="admin-field">
        <label class="admin-field__label" for="login-email">
            <i class="fa-solid fa-envelope"></i> E-mail
        </label>
        <input id="login-email" type="email" name="email" value="<?= e(old('email', '')) ?>" placeholder="admin@example.pl" autofocus>
    </div>
    <div class="admin-field">
        <label class="admin-field__label" for="login-password">
            <i class="fa-solid fa-lock"></i> Hasło
        </label>
        <input id="login-password" type="password" name="password" placeholder="••••••••">
    </div>
    <button class="button" type="submit">
        <i class="fa-solid fa-right-to-bracket"></i>
        Zaloguj się
    </button>
</form>
