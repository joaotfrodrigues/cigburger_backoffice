<?= $this->extend('layouts/layout_auth') ?>
<?= $this->section('content') ?>

<div class="login-box">
    <div class="text-center mb-3">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
    </div>

    <h5 class="text-center">Se tem conta registada nesta plataforma, receberá um e-mail nos próximos minutos com um link para recuperação da sua senha.</h5>

    <div class="my-5 text-center">
        <a href="<?= site_url('/auth/login') ?>" class="btn-login px-4"><i class="fas fa-chevron-left"></i></a>
    </div>
</div>

<?= $this->endSection() ?>