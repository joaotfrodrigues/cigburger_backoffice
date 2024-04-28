<div class="col">
    <div class="row content-box">
        <div class="col-lg-9 col-12 align-items-center">
            <div class="d-flex align-items-center">
                <!-- product image -->
                <div class="me-3">
                    <?php if(!file_exists('assets/images/products/' . $product->image)): ?>
                        <img src="<?= base_url('assets/images/products/no_image.png') ?>" class="img-fluid stock-image" alt="Sem imagem">
                    <?php else: ?>
                        <img src="<?= base_url('assets/images/products/' . $product->image) ?>" class="img-fluid stock-image" alt="<?= $product->name ?>">
                    <?php endif; ?>
                </div>

                <!-- product name and description -->
                <div>
                    <h4 class="mb-0"><strong><?= $product->name ?></strong></h4>
                    <p class="mb-0"><?= $product->description ?></p>
                    <?php if(!$product->availability): ?>
                        <span class="badge bg-danger">Indisponível</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12 text-end align-self-center">
            <!-- current stock -->
            <div>
                <h5>Stock atual</h5>
                <h3 class="<?= $product->stock < $product->stock_min_limit ? 'text-danger' : '' ?>"><strong><?= $product->stock ?></strong></h3>
            </div>
        </div>

        <div class="col-12 text-end">
            <a href="<?= site_url('/stocks/add/' . Encrypt($product->id)) ?>" class="btn btn-sm btn-outline-success px-3 m-1"><i class="fa-regular fa-square-plus me-3"></i>Adicionar Stock</a>
            <a href="<?= site_url('/stocks/remove/' . Encrypt($product->id)) ?>" class="btn btn-sm btn-outline-danger px-3 m-1"><i class="fa-regular fa-square-minus me-3"></i>Eliminar Stock</a>
            <a href="#" class="btn btn-sm btn-outline-secondary px-3 m-1"><i class="fa fa-solid fa-right-left me-3"></i>Entradas e Saídas</a>
        </div>
    </div>
</div>