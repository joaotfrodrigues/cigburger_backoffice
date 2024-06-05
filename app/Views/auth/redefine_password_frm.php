<?= $this->extend('layouts/layout_auth') ?>
<?= $this->section('content') ?>

<div class="login-box">
    <div class="text-center mb-3">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
    </div>

    <div class="card p-3 mb-3">
        <h5 class="text-center">Redefinição de senha</h5>
        <p class="text-center">
            <small>Deve conter entre 8 e 16 caracteres, pelo menos uma maiúscula, uma minúscula e um algarismo.</small>
        </p>
    </div>

    <?= form_open('/auth/redefine_password_submit') ?>
    <input type="hidden" name="purl_code" value="<?= $purl_code ?>">
    <div class="mb-3">
        <input type="password" name="text_password" class="form-control" placeholder="Senha" required>
        <?= display_error('text_password', $validation_errors) ?>
    </div>
    <div class="mb-3">
        <input type="password" name="text_password_confirm" class="form-control" placeholder="Confirmar Senha" required>
        <?= display_error('text_password_confirm', $validation_errors) ?>
    </div>
    <div class="mb-3 text-center">
        <button type="submit" class="btn btn-login">Redefinir Senha</button>
    </div>
    <?= display_error('purl_code', $validation_errors) ?>
    <?= form_close() ?>
</div>

<?= $this->endSection() ?>