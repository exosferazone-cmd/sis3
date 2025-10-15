<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<?php
/**
 * Vista: Detalle de Producto
 * Muestra la información completa de un producto y sus acciones.
 * Uso: Helpers::loadView('product-details', ['product' => $product]);
 */
$product = $product ?? [];
?>
<div class="container mt-4">
    <h2>Detalle de Producto</h2>
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Nombre:</strong> <?= htmlspecialchars($product['name'] ?? '') ?></p>
            <p><strong>SKU:</strong> <?= htmlspecialchars($product['sku'] ?? '') ?></p>
            <p><strong>Descripción:</strong> <?= htmlspecialchars($product['description'] ?? '') ?></p>
            <p><strong>Tipo:</strong> <?= htmlspecialchars(ucfirst($product['woo_type'] ?? '')) ?></p>
            <p><strong>Precio Normal:</strong> $<?= number_format($product['regular_price'] ?? 0, 2, ',', '.') ?></p>
            <p><strong>Precio Rebajado:</strong> $<?= number_format($product['sale_price'] ?? 0, 2, ',', '.') ?></p>
            <p><strong>Stock:</strong> <?= htmlspecialchars($product['stock'] ?? '') ?></p>
            <p><strong>Alerta Stock Mínimo:</strong> <?= htmlspecialchars($product['min_stock_alert'] ?? '') ?></p>
            <p><strong>Imágenes:</strong> <?= htmlspecialchars($product['images_url'] ?? '') ?></p>
            <p><strong>Atributos:</strong> <?= htmlspecialchars($product['attributes_input'] ?? '') ?></p>
        </div>
    </div>
    <div class="mb-3">
        <a href="/productos/edit?id=<?= $product['id'] ?>" class="btn btn-warning">Editar</a>
        <a href="/productos/delete?id=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar producto?');">Eliminar</a>
        <a href="/sync/product?id=<?= $product['id'] ?>" class="btn btn-success">Sincronizar</a>
        <a href="/productos" class="btn btn-secondary">Volver al listado</a>
    </div>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>