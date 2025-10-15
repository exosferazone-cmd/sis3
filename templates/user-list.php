<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="container mt-4">
    <h2>Usuarios del Sistema</h2>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone']) ?></td>
                    <td><?= htmlspecialchars($user['role_name']) ?></td>
                    <td>
                        <a href="/usuarios/edit?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="/usuarios/delete?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>
