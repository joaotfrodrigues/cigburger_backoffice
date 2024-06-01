<?= $this->extend('layouts/layout_main') ?>
<?= $this->section('content') ?>
<?= $this->include('partials/page_title') ?>

<div class="container-fluid mb-5">
    <div class="row">
        <div class="col content-box p-4">

            <?php if (empty($users)) : ?>
                <h4 class="text-center opacity-50 my-3">Não existem utilizadores</h4>
            <?php else : ?>
                <table class="table table-striped table-bordered" id="table_users">
                    <thead class="table-dark">
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th class="text-center">Cargo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Último Login</th>
                            <th class="text-center">Criado em</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td><?= $user['phone'] ?></td>
                                <td class="text-center"><?= implode(',', $user['roles']) ?></td>
                                <td class="text-center">
                                    <?php
                                    $icon  = 'fa-regular fa-circle-check';
                                    $color = 'text-success';
                                    $tip   = 'Ativo';

                                    // user without password defined | not fully registered
                                    if (!$user['has_password']) {
                                        $icon  = 'fa-solid fa-triangle-exclamation';
                                        $color = 'text-warning';
                                        $tip   = 'Registo incompleto';
                                    } else if (!$user['active']) {
                                        $icon  = 'fa-solid fa-user-slash';
                                        $color = 'text-danger';
                                        $tip   = 'Inativo';
                                    } else if (!empty($user['blocked_until'])) {
                                        // check if blocked until is in the passed
                                        $blocked_until = strtotime($user['blocked_until']);
                                        $now = strtotime(date('Y-m-d H:i:s'));

                                        // still on
                                        if ($blocked_until > $now) {
                                            $icon  = 'fa-solid fa-circle-pause';
                                            $color = 'text-danger';
                                            $tip   = 'Bloqueado até ' . $user['blocked_until'];
                                        }
                                    }

                                    if (!empty($user['deleted_at'])) {
                                        $icon  = 'fa-solid fa-circle-xmark';
                                        $color = 'text-danger';
                                        $tip   = 'Eliminado';
                                    }

                                    echo "<i class='$icon $color' title='$tip'></i>";
                                    ?>
                                </td>
                                <td class="text-center"><?= $user['last_login'] ?></td>
                                <td class="text-center"><?= $user['created_at'] ?></td>
                                <td class="text-center">
                                    <?php if ($user['id'] === session('user')['id']) : ?>
                                        <span class="btn btn-sm btn-outline-secondary opacity-50 disabled">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </span>
                                    <?php else : ?>
                                        <a href="<?= site_url('/users_management/edit/' . Encrypt($user['id'])) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {

        // datatable
        $('#table_users').DataTable({
            pageLength: 10,
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
    })
</script>

<?= $this->endSection() ?>