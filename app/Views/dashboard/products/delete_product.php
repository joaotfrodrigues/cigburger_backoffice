<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="d-flex content-box">

    <div class="p-3">
        <img class="img-fluid" src="<?= file_exists('assets/images/products/' . $product->image) ? base_url('assets/images/products/' . $product->image) : base_url('assets/images/products/no_image.png') ?>" alt="">
    </div>

    <div class="p-3">
        <h3 class="text-black mb-3"><strong><?= $product->name ?></strong></h3>
        <p class="text-secondary mb-3"><?= $product->description ?></p>
        <p class="text-danger mb-3">Tem a certeza que deseja eliminar este produto?<br>É um processo irreversível.</p>
        <div class="d-flex">
            <a href="<?= site_url('/products') ?>" clasS="btn btn-outline-secondary px-5"><i class="fas fa-ban me-2"></i>Cancelar</a>
            <a href="<?= site_url('/products/delete_confirm/' . Encrypt($product->id)) ?>" clasS="btn btn-danger px-5 ms-3"><i class="fas fa-trash me-2"></i>Eliminar</a>
        </div>
    </div>

</div>

<?= $this->endSection() ?>