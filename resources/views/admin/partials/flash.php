<?php if ($success = flash('success')): ?>
    <div class="admin-alert admin-alert--success">
        <i class="fa-solid fa-check-circle"></i>
        <?= e($success) ?>
    </div>
<?php endif; ?>
<?php if ($error = flash('error')): ?>
    <div class="admin-alert admin-alert--error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <?= e($error) ?>
    </div>
<?php endif; ?>
