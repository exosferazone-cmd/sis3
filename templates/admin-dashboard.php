<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="dashboard-grid">
    <div class="card">
        <h2>Bienvenido, <?php echo Helpers::escape($_SESSION['user_name']); ?></h2>
        <p>Este es tu panel de administración.</p>
    </div>
    <div class="card">
        <h3>Estadísticas Rápidas</h3>
        <ul>
            <li>Ventas totales: $XXXX.XX</li>
            <li>Productos en stock: YYY</li>
            <li>Clientes activos: ZZZ</li>
        </ul>
    </div>
    <div class="card">
        <h3>Enlaces Rápidos</h3>
        <ul>
            <li><a href="/clientes">Ver Clientes</a></li>
            <li><a href="/productos">Gestionar Productos</a></li>
            <li><a href="/ventas">Crear Venta</a></li>
        </ul>
    </div>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>