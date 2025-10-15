
<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="container mt-4">
    <h2>Listado de Ventas</h2>
    <a href="/ventas/create" class="btn btn-primary mb-3">Crear Nueva Venta</a>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo Helpers::escape($_SESSION['success_message']); ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo Helpers::escape($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>ID Venta</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Monto Total</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['sales'] as $sale): ?>
                <tr>
                    <td><?= htmlspecialchars($sale['id']) ?></td>
                    <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                    <td><?= htmlspecialchars($sale['user_name']) ?></td>
                    <td>$<?= number_format($sale['total_amount'], 2, ',', '.') ?></td>
                    <td>
                        <?php 
                        $dt = new DateTime($sale['sale_date'], new DateTimeZone('UTC'));
                        $dt->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
                        echo htmlspecialchars($dt->format('d/m/Y H:i'));
                        ?>
                    </td>
                    <td>
                        <a href="/ventas/details?id=<?= $sale['id'] ?>" class="btn btn-sm btn-info">Ver Detalle</a>
                        <?php if (Helpers::hasRole([ROLE_ADMIN, ROLE_SUPERVISOR])): ?>
                        <a href="/ventas/edit?id=<?= $sale['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="/ventas/delete?id=<?= $sale['id'] ?>" class="btn btn-sm btn-danger">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>