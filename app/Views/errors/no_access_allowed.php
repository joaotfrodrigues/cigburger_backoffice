<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="row mt-5">
    <div class="col-12 text-center">
        <p class="display-6 text-danger" style="text-transform: uppercase;">Acesso negado</p>
        <p class="opacity-50">Não tem acesso a esta funcionalidade</p>
    </div>
</div>

<?= $this->endSection() ?>