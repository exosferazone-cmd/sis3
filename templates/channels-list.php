<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="container mt-4">
    <h2>Canales de Venta</h2>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripci¨®n</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($channels as $ch): ?>
                <tr>
                    <td><?= htmlspecialchars($ch['name']) ?></td>
                    <td><?= htmlspecialchars($ch['description']) ?></td>
                    <td>
                        <a href="/canales/edit?id=<?= $ch['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="/canales/delete?id=<?= $ch['id'] ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/canales/add" class="btn btn-success mt-3">Agregar Canal</a>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>
