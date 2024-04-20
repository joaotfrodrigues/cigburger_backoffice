<?= $this->extend('layouts/layout_auth') ?>

<?= $this->section('content') ?>
    <div class="login-box">
        <div class="text-center mb-3">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
        </div>

        <?= form_open('/auth/login_submit') ?>
            <div class="mb-3">
                <p class="mb-2">Restaurante</p>
                <select name="select_restaurant" id="select_restaurant" class="form-select">
                    <option value=""></option>
                    <?php foreach($restaurants as $restaurant): ?>
                        <option value="<?= Encrypt($restaurant->id) ?>" <?php if ($restaurant->id === $select_restaurant) echo 'selected' ?> ><?= $restaurant->name ?></option>
                    <?php endforeach; ?>
                </select>
                <?= display_error('select_restaurant', $validation_errors) ?>
            </div>

            <hr>

            <div class="mb-3">
                <input class="form-control" type="text" id="text_username" name="text_username" placeholder="Utilizador" value="<?= old('text_username') ?>">
                <?= display_error('text_username', $validation_errors) ?>
            </div>
            <div class="mb-3">
                <input class="form-control" type="password" id="text_password" name="text_password" placeholder="Senha" value="<?= old('text_password') ?>">
                <?= display_error('text_password', $validation_errors) ?>
            </div>
            <div class="mb-3">
                <input type="submit" class="btn-login" value="ENTRAR">
            </div>
        <?= form_close() ?>

        <div class="text-center">
            <p>Não tem conta? <a href="#" class="login-link">Cadastre-se</a></p>
            <p><a href="#" class="login-link">Recuperar senha</a></p>
        </div>
    </div>
<?= $this->endSection() ?>