<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="container">
    <div class="row my-4">
        <div class="col text-center">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
        </div>
    </div>

    <div class="row">
        <div class="col-12 p-3">
            <div class="border border-1 border-secondary rounded-3 p-4 text-center">
                <h5>Restaurante: <strong><?= $restaurant->name ?></strong></h5>
            </div>
        </div>
        <div class="col-12 p-3">
            <div class="border border-1 border-secondary rounded-3 p-4 text-center">
                <a href="tel:<?= $restaurant->phone ?>" class="home-link">
                    <h5><i class="fa-solid fa-mobile-screen me-2"></i><?= $restaurant->phone ?></h5>
                </a>
                <a href="mailto:<?= $restaurant->email ?>" class="home-link">
                    <h5><i class="fa-solid fa-envelope-open-text me-2"></i><?= $restaurant->email ?></h5>
                </a>
            </div>
        </div>
        <div class="col-12 p-3">
            <div class="border border-1 border-secondary rounded-3 p-4 text-center">
                <p><strong><?= $restaurant->address ?></strong></p>
                <p>Criado em: <strong><?= $restaurant->created_at ?></strong></p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>