<?php $main = page_group($page, 'main'); ?>
<main>
    <section class="hero hero--inner">
        <div class="container">
            <p class="eyebrow"><?= e($main['page_title'] ?? '') ?></p>
            <h1><?= e($main['hero_title'] ?? '') ?></h1>
            <p class="hero__lead"><?= e($main['hero_subtitle'] ?? '') ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container contact-layout">
            <form class="contact-form" method="post" action="<?= e(url('kontakt/wyslij/')) ?>">
                <?= csrf_field() ?>
                <input type="text" name="website" value="" class="hp-field" autocomplete="off" tabindex="-1">

                <?php if ($success = flash('success')): ?>
                    <div class="alert alert--success"><?= e($success) ?></div>
                <?php endif; ?>
                <?php if ($error = flash('error')): ?>
                    <div class="alert alert--error"><?= e($error) ?></div>
                <?php endif; ?>

                <div class="contact-form__intro prose">
                    <?= $main['form_intro'] ?? '' ?>
                </div>

                <div class="contact-form__fields">
                    <label>
                        <span>Imię i nazwisko</span>
                        <input type="text" name="name" value="<?= e(old('name', '')) ?>" placeholder="Jan Kowalski">
                        <?php if (has_error('name')): ?><small><?= e(error_message('name')) ?></small><?php endif; ?>
                    </label>
                    <label>
                        <span>Adres e-mail</span>
                        <input type="email" name="email" value="<?= e(old('email', '')) ?>" placeholder="jan@firma.pl">
                        <?php if (has_error('email')): ?><small><?= e(error_message('email')) ?></small><?php endif; ?>
                    </label>
                    <label>
                        <span>Numer telefonu</span>
                        <input type="text" name="phone" value="<?= e(old('phone', '')) ?>" placeholder="+48 000 000 000">
                    </label>
                    <label>
                        <span>Temat</span>
                        <input type="text" name="subject" value="<?= e(old('subject', '')) ?>" placeholder="Zapytanie o stronę internetową">
                    </label>
                </div>

                <label>
                    <span>Wiadomość</span>
                    <textarea name="message" rows="6" placeholder="Opisz swój projekt..."><?= e(old('message', '')) ?></textarea>
                    <?php if (has_error('message')): ?><small><?= e(error_message('message')) ?></small><?php endif; ?>
                </label>

                <div class="contact-form__footer">
                    <button class="button" type="submit">Wyślij wiadomość</button>
                    <p class="contact-form__privacy">Wysyłając formularz akceptujesz <a href="<?= e(url('polityka-prywatnosci/')) ?>">politykę prywatności</a>.</p>
                </div>
            </form>
        </div>
    </section>
</main>
