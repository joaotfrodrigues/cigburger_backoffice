<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="container-fluid mb-5">
    <div class="row">
        <div class="col content-box p-4">
            <div class="mb-3 d-flex flex-wrap">
                <!-- date interval -->
                <div class="align-self-end">
                    <button class="btn btn-outline-secondary px-5 ms-3" data-bs-toggle="modal" data-bs-target="#calendar_modal">
                        <i class="fa-regular fa-calendar-days me-2"></i>
                        Intervalo de datas
                    </button>
                    <!-- last 7 days -->
                    <a href="<?= site_url('/sales/last_seven_days') ?>" class="btn btn-outline-secondary mx-2"><i class="fa-solid fa-calendar-days me-2"></i>Últimos 7 dias</a>
                    <a href="<?= site_url('/sales/reset_date_interval') ?>" class="btn btn-outline-secondary" title="Limpar datas"><i class="fa-regular fa-calendar-xmark"></i></a>
                    <span class="ms-3"><?= !empty($filter_date_interval) ? $filter_date_interval : 'Desde Sempre' ?></span>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card p-3" style="overflow: auto">
                        <table class="table table-striped table-bordered w-100" id="table_sales">
                            <thead class="table-dark">
                                <th>Data</th>
                                <th class="text-end">Valor total</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card p-3 mt-4 mt-lg-0 mb-4">
                        <?php if (empty($sales_chart['series'])) : ?>
                            <h4 class="opacity-50 text-center">Não existem dados para apresentar</h4>
                        <?php else : ?>
                            <h4 class="text-center">Gráfico de vendas dos últimos <strong><?= count($sales_chart['series']) ?></strong> dias</h4>
                        <?php endif; ?>
                        <div id="sales_chart"></div>
                    </div>

                    <div class="card p-3">
                        <?php if (empty($sales_chart_columns['series'])) : ?>
                            <h4 class="opacity-50 text-center">Não existem dados para apresentar</h4>
                        <?php else : ?>
                            <h4 class="text-center">Gráfico de vendas dos últimos <strong><?= count($sales_chart_columns['series']) ?></strong> dias</h4>
                        <?php endif; ?>
                        <div id="sales_chart_columns"></div>
                    </div>
                </div>
            </div>
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
            <?= form_open('/sales/filter_date_interval') ?>
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

        // datatables
        $('#table_sales').DataTable({
            pageLength: 10,
            // responsive: true,
            data: <?= json_encode($sales) ?>,
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'order_date'
                },
                {
                    data: 'total_price',
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

        // chart general options
        var generalOptions = {
            grid: {
                show: true
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 5,
                style: 'hollow'
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return val + '€';
                    }
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy'
                },
                y: {
                    formatter: function(val) {
                        return val + '€';
                    }
                }
            },
        };

        // sales chart
        // chart_sales_options
        let chartSalesOptions = {
            ...generalOptions,
            series: [{
                name: 'Vendas',
                data: <?= json_encode($sales_chart['series']) ?>
            }],
            chart: {
                type: 'area',
                height: 350,
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: true
                }
            },
            stroke: {
                curve: 'straight',
                width: 2
            },
            markers: {
                size: 5,
                style: 'hollow'
            },
            xaxis: {
                type: 'datetime',
                categories: <?= json_encode($sales_chart['labels']) ?>
            },
            colors: ['#880000'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: .7,
                    opacityTo: .3,
                    stops: [0, 90, 100]
                }
            }
        };

        const chartSales = new ApexCharts(document.getElementById('sales_chart'), chartSalesOptions);
        chartSales.render();

        // sales chart columns
        let chartSalesColumnsOptions = {
            ...generalOptions,
            series: [{
                name: 'Vendas',
                data: <?= json_encode($sales_chart_columns['series']) ?>
            }],
            chart: {
                type: 'bar',
                height: 350,
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: true
                }
            },
            colors: ['#880000'],
            plotOptions: {
                bar: {
                    columnWidth: '80%',
                    endingShape: 'rounded'
                }
            },
            xaxis: {
                type: 'datetime',
                categories: <?= json_encode($sales_chart_columns['labels']) ?>
            },
        };

        const chartSalesColumns = new ApexCharts(document.getElementById('sales_chart_columns'), chartSalesColumnsOptions);
        chartSalesColumns.render();
    });
</script>


<?= $this->endSection() ?>