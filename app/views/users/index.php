<?php use App\Core\Csrf; ?>
<div class="d-flex justify-content-end mb-3">
    <a class="btn btn-primary" href="<?= BASE_URL ?>/users/create">Nuevo usuario</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['role']) ?></td>
                        <td><span class="badge text-bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e($user['status']) ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= BASE_URL ?>/users/edit?id=<?= (int) $user['id'] ?>">Editar</a>
                            <form class="d-inline" method="post" action="<?= BASE_URL ?>/users/delete" data-confirm="Â¿Desactivar este usuario?">
                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Desactivar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
