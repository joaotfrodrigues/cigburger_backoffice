<?= $this->extend('layouts/layout_auth') ?>
<?= $this->section('content') ?>

<div class="login-box p-5 text-center">
    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" class="mb-3">
    <h4 class="text-center mb-5">Registo concluído com sucesso!</h4>
    <a href="<?= base_url('/auth/login') ?>" class="btn btn-login">Login</a>
</div>

<?= $this->endSection() ?>