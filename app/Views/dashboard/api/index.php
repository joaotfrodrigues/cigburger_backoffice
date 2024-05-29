<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="d-flex flex-column flex-lg-row py-2 justify-content-between border-secondary border-top border-bottom">
    <h4 class="my-2 my-md-0 text-center">Nome: <?= session('user')['restaurant_name'] ?></h4>
    <h4 class="my-2 my-md-0 text-center">ID: <?= $project_id ?></h4>
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
                                <a href="<?= site_url('/api_restaurant/download/' . Encrypt($machine->machine_id)) ?>" class="btn-cig">
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

    <hr>

    <div class="row">
        <div class="col">
            <p class="mb-3" style="text-transform: uppercase;"><strong>Criar nova máquina</strong></p>

            <div class="row">
                <div class="col">
                    <button class="btn-cig" id="new_machine_btn">
                        <i class="fa-regular fa-square-plus me-2"></i>
                        Criar Máquina
                    </button>

                    <div class="mt-3">
                        <small>
                            Será criado um novo ficheiro <strong>config.json</strong>.
                            <br>
                            Siga as instruções:<br>
                            <ol>
                                <li>Guarde o ficheiro no seu computador.</li>
                                <li>Coloque o ficheiro na raiz da pasta da aplicação CigRequest na nova máquina de venda automática.</li>
                                <li>Reinicie a máquina de venda automática.</li>
                            </ol>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-12">
            <p class="mb-3" style="text-transform: uppercase;"><strong>Alterar API key do restaurante:</strong></p>

            <div class="row">
                <div class="col">
                    <button class="btn-cig" data-bs-toggle="modal" data-bs-target="#new_api_key_modal"><i class="fa-solid fa-key me-2"></i>Criar nova API KEY</button>
                    <div class="mt-3">
                        <small>
                            Será criada uma nova API KEY. <span style="color: #880000"><strong>A API KEY atual será desativada.</strong></span><br>
                            Deverá atualizar todos os ficheiros <strong>config.json</strong> das máquinas de venda automática.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="new_api_key_modal" tabindex="-1" aria-labelledby="new_api_key_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #880000;">
                    <h1 class="modal-title fs-5" id="new_api_key_modal_label">NOVA API KEY</h1>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 text-center">
                    <h4>Pretende alterar a API KEY deste restaurante?</h4>
                    <p class="pb-0 mb-0">API KEY atual:
                    <p>
                    <h3 class="mb-3"><?= $api_key_openssl ?></h3>
                    <p class="alert alert-danger p-2 text-center">Todos os ficheiros <strong>config.json</strong> deverão ser atualizados nas máquinas de venda automática.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cig" data-bs-dismiss="modal"><i class="fa-solid fa-ban me-2"></i>Cancelar</button>
                    <a href="<?= site_url('/api_restaurant/change_api_key') ?>" class="btn-cig"><i class="fa-solid fa-key me-2"></i>Criar nova API KEY</a>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const newMachineBtn = document.getElementById('new_machine_btn');
    newMachineBtn.addEventListener('click', () => {
        window.location.href = '<?= site_url('/api_restaurant/create_new_machine') ?>';
    });
</script>

<?= $this->endSection() ?>