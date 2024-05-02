<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col content-box">
            <div class="d-flex align-items-center">
                <!-- image -->
                <div class="me-3">
                    <?php if (file_exists('assets/images/products/' . $product->image)) : ?>
                        <img src="<?= base_url('assets/images/products/' . $product->image) ?>" class="img-fluid stock-image" alt="<?= $product->image ?>">
                    <?php else : ?>
                        <img src="<?= base_url('assets/images/products/no_image.png') ?>" alt="Sem imagem" class="img-fluid stock-image">
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
            </div>

            <hr>

            <div class="my-3">
                <div class="row">
                    <div class="col-auto">
                        <a href="<?= site_url('/stocks/add/' . Encrypt($product->id)) ?>" class="btn btn-sm btn-outline-success px-3 m-1"><i class="fa-regular fa-square-plus me-3"></i>Adicionar Stock</a>
                        <a href="<?= site_url('/stocks/remove/' . Encrypt($product->id)) ?>" class="btn btn-sm btn-outline-danger px-3 m-1"><i class="fa-regular fa-square-minus me-3"></i>Remover Stock</a>
                        <a href="<?= site_url('/products/edit/' . Encrypt($product->id)) ?>" class="btn btn-sm btn-outline-secondary px-3 m-1"><i class="fa-regular fa-pen-to-square me-3"></i>Editar</a>
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <i class="fa-solid fa-filter ms-5 me-2"></i>
                        <select name="select_filter" id="select_filter" class="form-select">
                            <option value="<?= Encrypt('') ?>" <?= stock_movement_select_filter($filter, '') ?>>Todos os movimentos</option>
                            <option value="<?= Encrypt('IN') ?>" <?= stock_movement_select_filter($filter, 'IN') ?>>Entradas</option>
                            <option value="<?= Encrypt('OUT') ?>" <?= stock_movement_select_filter($filter, 'OUT') ?>>Saídas</option>
                            <optgroup label="Fornecedores">
                                <?php foreach($stock_suppliers as $supplier): ?>
                                    <option value="<?= Encrypt('stksup_' . $supplier->stock_supplier) ?>" <?= stock_movement_select_filter($filter, 'stksup_' . $supplier->stock_supplier) ?>>
                                        <?= $supplier->stock_supplier ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <table class="table table-striped table bordered w-100" id="table_movements">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">Data do movimento</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-center">Operação</th>
                        <th class="text-center">Fornecedor</th>
                        <th class="text-center">Observações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // datatable
        $('#table_movements').DataTable({
            data: JSON.parse('<?= json_encode($movements) ?>'),
            columns: [{
                    data: 'movement_date',
                    className: 'text-center'
                },
                {
                    data: 'stock_quantity',
                    className: 'text-center'
                },
                {
                    data: 'stock_in_out',
                    className: 'text-center'
                },
                {
                    data: 'stock_supplier',
                    className: 'text-center'
                },
                {
                    data: 'reason',
                    className: 'text-center'
                },
            ],
            order: [
                [0, 'desc']
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
            }
        });

        // filter - reload page with filter
        document.querySelector('#select_filter').addEventListener('change', (event) => {
            let filter = event.target.value;

            window.location.href = `<?= site_url('/stocks/movements/' . Encrypt($product->id)) ?>/${filter}`;
        });
    });
</script>

<?= $this->endSection() ?>