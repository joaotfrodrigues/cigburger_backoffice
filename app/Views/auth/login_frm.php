<?= $this->extend('layouts/layout_auth') ?>

<?= $this->section('content') ?>
    <div class="login-box">
        <div class="text-center mb-3">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
        </div>

        <form action="#" method="post">
            <div class="mb-3">
                <p class="mb-2">Restaurante</p>
                <select name="select-restaurant" id="select-restaurant" class="form-select">
                    <option value=""></option>
                    <option value="">Restaurante 1</option>
                    <option value="">Restaurante 2</option>
                    <option value="">Restaurante 3</option>
                </select>
            </div>

            <hr>

            <div class="mb-3">
                <input class="form-control" type="email" id="text-username" name="text-username" placeholder="Utilizador">
            </div>
            <div class="mb-3">
                <input class="form-control" type="password" id="text-password" name="text-password" placeholder="Senha">
            </div>
            <div class="mb-3">
                <input type="submit" class="btn-login" value="ENTRAR">
            </div>
        </form>

        <div class="text-center">
            <p>Não tem conta? <a href="#" class="login-link">Cadastre-se</a></p>
            <p><a href="#" class="login-link">Recuperar senha</a></p>
        </div>
    </div>
<?= $this->endSection() ?>