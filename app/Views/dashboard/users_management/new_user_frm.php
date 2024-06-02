<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="container-fluid mb-5">
    <div class="row">
        <div class="col content-box p-4">
            <div class="row">
                <div class="col-12">
                    <?= form_open('/users_management/new_user_submit') ?>

                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <p>Restaurante: <strong><?= session('user')['restaurant_name'] ?></strong></p>
                                <hr>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="text_username" class="form-label">Utilizador</label>
                            <input type="text" name="text_username" class="form-control" required value="<?= old('text_username') ?>">
                            <?= display_error('text_username', $validation_errors) ?>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="text_name" class="form-label">Nome do utilizador</label>
                            <input type="text" name="text_name" class="form-control" required value="<?= old('text_name') ?>">
                            <?= display_error('text_name', $validation_errors) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="text_email" class="form-label">E-mail</label>
                            <input type="email" name="text_email" class="form-control" required value="<?= old('text_email') ?>">
                            <?= display_error('text_email', $validation_errors) ?>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="text_phone" class="form-label">Telefone</label>
                            <input type="text" name="text_phone" class="form-control" required value="<?= old('text_phone') ?>">
                            <?= display_error('text_phone', $validation_errors) ?>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-6">
                            <label for="form-label">Cargo</label>
                            <select name="select_role" class="form-select">
                                <option value="admin" <?= old('select_role') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                <option value="user" <?= old('select_role') === 'user' ? 'selected' : '' ?>>Colaborador</option>
                            </select>
                            <?= display_error('select_role', $validation_errors) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <a href="<?= site_url('/users_management') ?>" class="btn btn-outline-secondary px-5"><i class="fa-solid fa-ban me-2"></i>Cancelar</a>
                            <button type="submit" class="btn btn-outline-success px-5"><i class="fa-solid fa-check me-2"></i>Criar utilizador</button>
                        </div>
                    </div>

                    <?= form_close() ?>

                    <?php if (!empty($server_error)) : ?>
                        <div class="alert alert-danger">
                            <?= $server_error ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>