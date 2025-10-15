
<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<?php
/**
 * Vista: Detalle de Venta
 * Muestra la información completa de una venta y sus ítems.
 * Dependencias: Bootstrap (opcional), helpers de formateo.
 * Uso: Helpers::loadView('sale-details', ['sale' => $sale]);
 */
$sale = $sale ?? [];
?>
<div class="container mt-4">
    <h2>Detalle de Venta #<?= htmlspecialchars($sale['id'] ?? '') ?></h2>
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Cliente:</strong> <?= htmlspecialchars($sale['customer_name'] ?? 'Sin cliente') ?></p>
            <p><strong>Vendedor:</strong> <?= htmlspecialchars($sale['user_name'] ?? '') ?></p>
            <p><strong>Canal:</strong> <?= htmlspecialchars($sale['channel_name'] ?? '') ?></p>
            <p><strong>Fecha:</strong> <?= htmlspecialchars($sale['sale_date'] ?? '') ?></p>
            <p><strong>Total:</strong> $<?= number_format($sale['total_amount'] ?? 0, 2, ',', '.') ?></p>
        </div>
    </div>
    <h4>Ítems vendidos</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($sale['items'])): foreach ($sale['items'] as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['quantity'] ?? '') ?></td>
                <td>$<?= number_format($item['price_at_sale'] ?? 0, 2, ',', '.') ?></td>
                <td>$<?= number_format(($item['price_at_sale'] ?? 0) * ($item['quantity'] ?? 0), 2, ',', '.') ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="4">No hay ítems registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="/ventas" class="btn btn-secondary">Volver al listado</a>
</div>

<!--
Documentación:
- Muestra todos los datos relevantes de la venta y sus ítems.
- Si no hay cliente, muestra "Sin cliente".
- Si no hay ítems, muestra mensaje.
- Usa helpers para evitar XSS.
-->
