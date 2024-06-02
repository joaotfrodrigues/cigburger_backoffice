<?= $this->extend('layouts/layout_auth') ?>
<?= $this->section('content') ?>

<div class="login-box">
    <div class="text-center mb-3">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
    </div>

    <div class="card p-3 m-3">
        <p class="mb-0">Bem vindo, <strong><?= session('new_user')['username'] ?></strong>.</p>
        <p class="mb-0">Nome: <strong><?= session('new_user')['name'] ?></strong></p>
        <p class="mb-0">E-mail: <strong><?= session('new_user')['email'] ?></strong></p>
        <p class="my-3 text-center px-3">Neste quadro deverá definir a sua senha para que conclua o registo no <strong><?= session('new_user')['restaurant_name'] ?></strong></p>
        <small>Deve conter entre 8 e 16 caracteres, pelo menos uma maiúscula, uma minúscula e um algarismo</small>
    </div>

    <?= form_open('/auth/define_password_submit') ?>
    <div class="mb-3">
        <input type="password" name="text_password" class="form-control" placeholder="Senha">
        <?= display_error('text_password', $validation_errors) ?>
    </div>
    <div class="mb-3">
        <input type="password" name="text_password_confirm" class="form-control" placeholder="Confirmar Senha">
        <?= display_error('text_password_confirm', $validation_errors) ?>
    </div>
    <div class="mb-3 text-center">
        <button type="submit" class="btn btn-login">Definir Senha</button>
    </div>
    <?= form_close() ?>
</div>

<?= $this->endSection() ?>