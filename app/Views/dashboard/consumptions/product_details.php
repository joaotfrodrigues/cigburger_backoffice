<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<style>
    body { overflow-x: hidden; }
</style>

<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-11 content-box p-4">

            <div class="d-flex align-items-center">
                <!-- image -->
                <div class="me-3">
                    <?php if (!file_exists('assets/images/products/' . $product->image)) : ?>
                        <img src="<?= base_url('assets/images/products/no_image.png') ?>" class="img-fluid stock-image" alt="Sem imagem">
                    <?php else : ?>
                        <img src="<?= base_url('assets/images/products/' . $product->image) ?>" class="img-fluid stock-image" alt="<?= $product->image ?>">
                    <?php endif; ?>
                </div>

                <!-- name and description -->
                <div class="flex-fill me-3">
                    <h4 class="mb-0"><strong><?= $product->name ?></strong></h4>
                    <p class="mb-0"><?= $product->description ?></p>
                    <?php if (!$product->availability) : ?>
                        <span class="badge bg-danger">Indisponível</span>
                    <?php endif; ?>
                </div>

                <!-- current stock -->
                <div class="text-end">
                    <h5>Stock atual</h5>
                    <h3 class="<?= $product->stock <= $product->stock_min_limit ? 'text-danger' : '' ?>"><strong><?= $product->stock ?></strong></h3>
                </div>

                <!-- close page -->
                <div class="ms-5">
                    <a href="<?= site_url('/consumptions') ?>" class="btn btn-outline-danger"><i class="fa-solid fa-xmark"></i></a>
                </div>
            </div>

            <hr>

            <?php if (count($consumptions) == 0) : ?>
                <div class="my-5 text-center opacity-50 ">
                    <h4>Não foram encontrados consumos relacionados com este produto.</h4>
                </div>
            <?php else :  ?>
                <div class="row">

                    <div class="col-lg-6 col-12">

                        <div class="card p-4" style="overflow: auto">
                            <table class="table table-striped table-bordered w-100" id="table_consumptions">
                                <thead class="table-dark">
                                    <th>Dia</th>
                                    <th class="text-end">Quantidade</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>

                    <div class="col-lg-6 col-12 mt-3 mt-lg-0">
                        <div class="card p-4">
                            <h4>Consumo total do produto:</h4>
                            <div class="display-5 text-secondary mb-5"><?= $total_consumption ?></div>
                            <div>
                                <a href="<?= site_url('/products/edit/' . Encrypt($product->id)) ?>" class="btn btn-sm btn-outline-secondary px-3 m-1">
                                    <i class="fa-regular fa-pen-to-square me-2"></i>Editar produto
                                </a>
                                <a href="<?= site_url('/stocks/movements/' . Encrypt($product->id)) ?>" class="btn btn-sm btn-outline-secondary px-3 m-1">
                                    <i class="fa-solid fa-cubes-stacked me-2"></i>Stock do produto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // datatables
        $('#table_consumptions').DataTable({
            pageLength: 10,
            // responsive: true,
            data: <?= json_encode($consumptions) ?>,
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'order_date'
                },
                {
                    data: 'quantity',
                    className: 'text-end'
                },
            ],
            language: {
                decimal: "",
                emptyTable: "Sem dados disponíveis na tabela.",
                info: "Mostrando _START_ até _END_ de _TOTAL_ registos",
                infoEmpty: "Mostrando 0 até 0 de 0 registos",
                infoFiltered: "(Filtrando _MAX_ total de registos)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrando _MENU_ registos por página.",
                loadingRecords: "Carregando...",
                processing: "Processando...",
                search: "Filtrar:",
                zeroRecords: "Nenhum registro encontrado.",
                paginate: {
                    first: "Primeira",
                    last: "Última",
                    next: "Seguinte",
                    previous: "Anterior"
                },
                aria: {
                    sortAscending: ": ative para classificar a coluna em ordem crescente.",
                    sortDescending: ": ative para classificar a coluna em ordem decrescente."
                }
            },
        });
    });
</script>

<?= $this->endSection() ?>