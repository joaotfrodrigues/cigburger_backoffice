<!-- top bar -->
<header class="top-bar d-flex justify-content-between align-items-center">
    <div class="d-flex">
        <div class="btn-main-menu me-3"><i class="fa-solid fa-bars"></i></div>
        <a href="<?= site_url('/') ?>">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo CigBurger Backoffice" class="img-fluid" width="48px">
        </a>
    </div>

    <div>
        <i class="fa-solid fa-user me-2"></i><?= session()->user['name'] ?>
        <i class="fa-solid fa-ellipsis-vertical mx-3"></i>
        <a href="<?= site_url('/auth/logout') ?>"><i class="fa-solid fa-right-from-bracket me-2"></i>Sair</a>
    </div>
</header>