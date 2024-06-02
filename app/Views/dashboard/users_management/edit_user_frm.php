<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="container-fluid mb-5">
    <div class="row">
        <div class="col content-box p-4">
            <div class="row">
                <div class="col-12">
                    <?= form_open('/users_management/edit_user_submit') ?>

                    <input type="hidden" name="hidden_id" value="<?= Encrypt($user->id) ?>">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <p>Restaurante_ <strong><?= session('user')['restaurant_name'] ?></strong></p>
                                <hr>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-sm-6">
                            <p>Utilizador</p>
                            <p><strong><?= $user->username ?></strong></p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <p>Nome do utilizador</p>
                            <p><strong><?= $user->name ?></strong></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-sm-6">
                            <p>E-mail</p>
                            <p><strong><?= $user->email ?></strong></p>
                        </div>
                        <div class="col-12 col-sm-6">
                            <p>Telefone</p>
                            <p><strong><?= $user->phone ?></strong></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Cargo</label>
                            <select name="select_role" class="form-select">
                                <option value="admin" <?= $user->roles === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                <option value="user" <?= $user->roles === 'user' ? 'selected' : '' ?>>Colaborador</option>
                            </select>
                            <?= display_error('select_role', $validation_errors) ?>
                        </div>
                        <div class="col-12 col-md-6 mt-3 mt-md-0">
                            <label class="form-label">Bloquear utilizador até:</label>
                            <div class="d-flex">
                                <input type="text" name="date_blocked_until" id="date_blocked_until" class="form-control" value="<?= $user->blocked_until ?>">
                                <button id="reset_date_blocked_until" class="btn btn-outline-secondary ms-2"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            <?= display_error('date_blocked_until', $validation_errors) ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="me-3">Estado:</p>
                            <div class="d-flex">
                                <div class="me-3">
                                    <input type="radio" name="radio_active" id="radio_active_active" class="form-check-input me-2" value="1" <?= $user->active ? 'checked' : '' ?>>
                                    <label for="radio_active_active" class="form-check-label">Ativo</label>
                                </div>
                                <div>
                                    <input type="radio" name="radio_active" id="radio_inactive_inactive" class="form-check-input me-2" value="0" <?= !$user->active ? 'checked' : '' ?>>
                                    <label for="radio_inactive_inactive" class="form-check-label">Inativo</label>
                                </div>
                            </div>
                            <?= display_error('radio_active', $validation_errors) ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <a href="<?= site_url('/users_management') ?>" class="btn btn-outline-secondary col-12 col-md-3">
                                <i class="fa-solid fa-ban me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-outline-success col-12 col-md-4 col-lg-3 mt-3 mt-md-0">
                                <i class="fa-solid fa-check me-2"></i>
                                Editar utilizador
                            </button>
                        </div>
                    </div>
                    <?= form_close() ?>

                    <?php if (!empty($server_error)) : ?>
                        <div class="alert alert-danger">
                            <?= $server_error ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-lg-6 mt-2 mx-auto text-center">
                    <?php if (!empty($user->deleted_at)) : ?>
                        <div class="card p-3">
                            <p class="text-center">Este utilizador foi removido em<br><strong><?= $user->deleted_at ?></strong></p>
                            <button class="btn btn-outline-success px-5" data-bs-toggle="modal" data-bs-target="#modal_recover"><i class="fa-solid fa-trash-can-arrow-up me-2"></i>Recuperar utilizador</button>
                        </div>
                    <?php else : ?>
                        <div class="card p-3">
                            <p class="text-danger text-center">Clique no botão abaixo para eliminar este utilizador.<br><small>Esta ação é reversível.</small></p>
                            <button class="btn btn-outline-danger px-5" data-bs-toggle="modal" data-bs-target="#modal_delete"><i class="fa-solid fa-trash me-2"></i>Eliminar utilizador</button>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- modal delete -->
<div class="modal fade" id="modal_delete" tabindex="-1" aria-labelledby="modal_delete_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal_delete_label">Eliminar utilizador</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="text-center">Pretende eliminar este utilizador?</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-2"></i>Cancelar</button>
                <a href="<?= site_url('/users_management/delete_user/' . Encrypt($user->id)) ?>" class="btn btn-outline-danger"><i class="fa-solid fa-check me-2"></i>Eliminar</a>
            </div>
        </div>
    </div>
</div>

<!-- modal recover -->
<div class="modal fade" id="modal_recover" tabindex="-1" aria-labelledby="modal_recover_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal_recover_label">Recuperar utilizador</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="text-center">Pretende recuperar este utilizador?</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-2"></i>Cancelar</button>
                <a href="<?= site_url('/users_management/recover_user/' . Encrypt($user->id)) ?>" class="btn btn-outline-success"><i class="fa-solid fa-check me-2"></i>Recuperar</a>
            </div>
        </div>
    </div>
</div>


<script>
    window.addEventListener('DOMContentLoaded', () => {

        // flatpickr
        flatpickr('#date_blocked_until', {
            minDate: 'today',
            dateFormat: 'Y-m-d'
        });

        // reset blocked until date
        document.getElementById('reset_date_blocked_until').addEventListener('click', (event) => {
            event.preventDefault();

            document.getElementById('date_blocked_until').value = null;
        });
    });
</script>

<?= $this->endSection() ?>