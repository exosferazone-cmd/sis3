<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="container mt-4">
    <h2>Categorías de Productos</h2>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><?= htmlspecialchars($cat['description']) ?></td>
                    <td>
                        <a href="/categorias/edit?id=<?= $cat['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="/categorias/delete?id=<?= $cat['id'] ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/categorias/add" class="btn btn-success mt-3">Agregar Categoría</a>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>
