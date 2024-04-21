<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>

<?= $this->include('partials/page_title') ?>

<!-- new product -->
<div class="mb-3">
    <a href="<?= site_url('/products/new') ?>" class="btn btn-outline-secondary">
        <i class="fa-solid fa-plus me-2" style="vertical-align: middle;"></i> Novo produto
    </a>
</div>

<!-- products list -->
<div class="text-center mt-5">
    <h4 class="opacity-50 mb-3">Não existem produtos disponíveis.</h4>
    <span>Clique <a href="<?= site_url('/products/new') ?>">aqui</a> para adcionar o primeiro produto dos restaurante.</span>
</div>

<?= $this->endSection() ?>