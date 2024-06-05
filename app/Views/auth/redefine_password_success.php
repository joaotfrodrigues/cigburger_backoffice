<?= $this->extend('layouts/layout_auth') ?>
<?= $this->section('content') ?>

<div class="login-box">
    <div class="text-center mb-3">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
    </div>

    <h5 class="text-center">A sua senha foi redefinida com sucesso.<br>Já poderá efetuar o login na aplicação.</h5>

    <div class="my-5 text-center">
        <a href="<?= site_url('/auth/login') ?>" class="btn-login px-4"><i class="fas fa-login"></i>Login</a>
    </div>
</div>

<?= $this->endSection() ?>