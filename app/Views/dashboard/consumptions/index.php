<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-12 content-box p-4">
            <!-- filters -->
            <div class="mb-3 d-flex flex-wrap">
                <!-- categories -->
                <div class="d-flex align-items-center">
                    <span class="me-3">Categorias:</span>
                    <select id="select_categories" class="form-select">
                        <option value="<?= Encrypt('all') ?>" <?= set_selected('all', $filter_category) ?>>Todas</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?= Encrypt($category['category']) ?>" <?= set_selected($category['category'], $filter_category) ?>><?= $category['category'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- date interval -->
                <div class="align-self-end">
                    <button class="btn btn-outline-secondary px-5 ms-3" data-bs-toggle="modal" data-bs-target="#calendar_modal">
                        <i class="fa-regular fa-calendar-days me-2"></i>
                        Intervalo de datas
                    </button>
                    <!-- last 7 days -->
                    <a href="<?= site_url('/consumptions/last_seven_days') ?>" class="btn btn-outline-secondary mx-2"><i class="fa-solid fa-calendar-days me-2"></i>Últimos 7 dias</a>
                    <a href="<?= site_url('/consumptions/reset_date_interval') ?>" class="btn btn-outline-secondary" title="Limpar datas"><i class="fa-regular fa-calendar-xmark"></i></a>
                    <span class="ms-3"><?= !empty($filter_date_interval) ? $filter_date_interval : 'Desde Sempre' ?></span>
                </div>

                <!-- reset all -->
                <div class="align-self-end">
                    <a href="<?= site_url('/consumptions/reset_all_filters') ?>" class="btn btn-outline-secondary ms-3" title="Limpar filtros"><i class="fa-solid fa-arrows-rotate"></i></a>
                </div>
            </div>
            <?php if (empty($products)) : ?>
                <h4 class="text-center my-5 opacity-50">Não existe registo de produtos em encomendas.</h4>
            <?php else : ?>
                <table class="table table-striped table-bordered" id="table_products">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%"></th>
                            <th width="30%">Produto</th>
                            <th width="30%">Categoria</th>
                            <th width="10%" class="text-center">Disponível</th>
                            <th width="10%" class="text-end">Stock atual</th>
                            <th width="10%" class="text-end">Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product) : ?>
                            <tr class="font-size-18px">
                                <!-- product image -->
                                <td class="text-center">
                                    <img src="<?= base_url('assets/images/products/' . $product['image']) ?>" alt="<?= $product['name'] ?>" class="img-fluid" style="max-width: 50px;">
                                </td>
                                <td class="align-middle">
                                    <a href="<?= site_url('/consumptions/product_details/' . Encrypt($product['id'])) ?>"><?= $product['name'] ?></a>
                                </td>
                                <td class="align-middle"><?= $product['category'] ?></td>
                                <!-- availability -->
                                <td class="text-center align-middle">
                                    <?php if ($product['availability']) : ?>
                                        <i class="fa-regular fa-circle-check fa-xl text-success"></i>
                                    <?php else : ?>
                                        <i class="fa-regular fa-circle-xmark fa-xl text-danger"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end align-middle"><?= $product['stock'] ?></td>
                                <td class="text-end align-middle <?= $product['quantity'] === 0 ? 'opacity-25' : '' ?>"><?= $product['quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- calendar modal -->
<div class="modal fade" id="calendar_modal" tabindex="-1" aria-labelledby="calendar_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="calendar_modal_label">Intervalo de datas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open('/consumptions/filter_date_interval') ?>
            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <input type="text" name="text_date_interval" id="text_date_interval" class="d-none" value="<?= !empty($filter_date_interval) ? $filter_date_interval : date('Y-m-d')  ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal"><i class="fa-solid fa-ban me-2"></i>Cancelar</button>
                <button type="submit" id="btn_apply_filter" class="btn btn-outline-success px-4"><i class="fa-solid fa-check me-2"></i>Aplicar filtro</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // flatpickr
        flatpickr('#text_date_interval', {
            inline: true,
            mode: 'range',
            maxDate: 'today',
            dateFormat: 'Y-m-d'
        });

        // select category
        const selectCategories = document.getElementById('select_categories');
        selectCategories.addEventListener('change', () => {
            const category = selectCategories.value;
            window.location.href = `<?= site_url('/consumptions/set_category') ?>/${category}`;
        });

        // datatable
        $('#table_products').DataTable({
            columnDefs: [{
                    "targets": [0],
                    "orderable": false
                } // Disable ordering for columns 0
            ],
            order: [
                [5, "desc"] // Order on column 5 (quantity)
            ],
            pageLength: 50,
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