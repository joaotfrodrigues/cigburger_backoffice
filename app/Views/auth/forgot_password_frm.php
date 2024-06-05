<?= $this->extend('layouts/layout_auth') ?>
<?= $this->section('content') ?>

<div class="login-box">
    <div class="text-center mb-3">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
    </div>

    <?= form_open('/auth/forgot_password_submit') ?>
    <div class="mb-3">
        <div class="mb-2">Restaurante</div>
        <select name="select_restaurant" id="select_restaurant" class="form-select">
            <option selected disabled value="">--Selecione o restaurante--</option>
            <?php foreach ($restaurants as $restaurant) : ?>
                <option value="<?= Encrypt($restaurant->id) ?>"><?= $restaurant->name ?></option>
            <?php endforeach; ?>
        </select>
        <?= display_error('select_restaurant', $validation_errors) ?>
    </div>

    <hr>

    <div class="mb-3">
        <input type="email" placeholder="E-mail" name="text_email" id="text-email" class="form-control" required value="<?= old('text_email') ?>">
        <?= display_error('text_email', $validation_errors) ?>
    </div>

    <div class="mb-3 text-end">
        <button type="submit" class="btn-login px-4">Recuperar Senha</button>
    </div>

    <?= form_close() ?>

    <div class="my-3">
        <p class="text-center">Sabe a senha? <a href="<?= site_url('/auth/login') ?>" class="login-link">Entrar</a></p>
    </div>
</div>

<?= $this->endSection() ?>