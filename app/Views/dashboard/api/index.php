<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="d-flex flex-column flex-md-row py-2 justify-content-between border-secondary border-top border-bottom">
    <h4 class="my-2 my-md-0 text-center"><?= session('user')['restaurant_name'] ?></h4>
    <h4 class="my-2 my-md-0 text-center"><?= $project_id ?></h4>
    <h4 class="my-2 my-md-0 text-center">API key: <span class="text-secondary"><?= $api_key_openssl ?></span></h4>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <?php if (empty($machines)) : ?>
                <h4 class=" text-center opacity-50 my-5">Não foram encontradas máquinas com pedidos associados a este restaurante.</h4>
            <?php else : ?>
                <p class="mt-3"><strong>Máquinas</strong></p>
                <table class="table table-striped table bordered shadow-sm">
                    <?php foreach ($machines as $machine) : ?>
                        <tr>
                            <td><?= $machine->machine_id ?></td>
                            <td class="text-end">
                                <a href="#" class="btn-cig">
                                    <i class="fas fa-download me-2"></i>
                                    Configuração API
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>