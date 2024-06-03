<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="content-box">
    <h5>Editar Perfil - <strong><?= $user->username ?></strong></h5>
    <hr class="my-2">
    <div class="d-flex justify-content-between flex-wrap">
        <p class="mb-0">Utilizador: <strong><?= $user->username ?></strong></p>
        <p class="mb-0">Cargo: <strong><?= $user->role ?></strong></p>
        <p class="mb-0">Conta criada em: <strong><?= $user->created_at ?></strong></p>
    </div>
    <hr class="mt-1 mb-3">
    <div class="row">
        <div class="col-12 col-lg-6 my-1">
            <div class="card p-4">
                <h5><i class="fa-solid fa-user-pen me-3"></i><strong>Identidade</strong></h5>
                <hr class="my-2">
                <?= form_open('/auth/profile_submit') ?>
                <div class="mb-3">
                    <label for="text_name">Nome</label>
                    <input type="text" name="text_name" class="form-control" value="<?= old('text_name', $user->name)  ?>">
                    <?= display_error('text_name', $validation_errors) ?>
                </div>
                <div class="mb-3">
                    <label for="text_email">Email</label>
                    <input type="email" name="text_email" class="form-control" value="<?= old('text_email', $user->email) ?>">
                    <?= display_error('text_email', $validation_errors) ?>
                </div>
                <div class="mb-3">
                    <label for="text_phone">Telefone</label>
                    <input type="text" name="text_phone" class="form-control" value="<?= old('text_phone', $user->phone) ?>">
                    <?= display_error('text_phone', $validation_errors) ?>
                </div>
                <button type="submit" class="btn btn-outline-success px-3"><i class="fa-solid fa-check me-2"></i>Salvar</button>
                <?= form_close() ?>
            </div>
        </div>
        <div class="col-12 col-lg-6 my-1">
            <div class="card p-4">
                <h5><i class="fa-solid fa-key me-3"></i><strong>Alterar Senha</strong></h5>
                <hr class="my-2">
                <?= form_open('/auth/change_password_submit') ?>
                <div class="mb-3">
                    <label for="text_password">Senha atual</label>
                    <input type="password" name="text_password" class="form-control">
                    <?= display_error('text_password', $validation_errors) ?>
                </div>
                <div class="mb-3">
                    <label for="text_new_password">Nova senha</label>
                    <input type="password" name="text_new_password" class="form-control">
                    <?= display_error('text_new_password', $validation_errors) ?>
                </div>
                <div class="mb-3">
                    <label for="text_new_password_confirm">Confirmar nova senha</label>
                    <input type="password" name="text_new_password_confirm" class="form-control">
                    <?= display_error('text_new_password_confirm', $validation_errors) ?>
                </div>
                <button type="submit" class="btn btn-outline-success px-3"><i class="fa-solid fa-check me-2"></i>Salvar</button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>