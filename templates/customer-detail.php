<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<?php
/**
 * Vista: Detalle de Cliente
 * Muestra la información completa de un cliente y sus acciones.
 * Uso: Helpers::loadView('customer-details', ['customer' => $customer]);
 */
$customer = $customer ?? [];
?>
<div class="container mt-4">
    <h2>Detalle de Cliente</h2>
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Nombre:</strong> <?= htmlspecialchars($customer['name'] ?? '') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($customer['email'] ?? '') ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($customer['phone'] ?? '') ?></p>
            <p><strong>Dirección:</strong> <?= htmlspecialchars($customer['address'] ?? '') ?></p>
        </div>
    </div>
    <div class="mb-3">
        <button 
            class="btn btn-warning btn-edit"
            data-id="<?= $customer['id'] ?>"
            data-name="<?= htmlspecialchars($customer['name']) ?>"
            data-email="<?= htmlspecialchars($customer['email']) ?>"
            data-phone="<?= htmlspecialchars($customer['phone']) ?>"
            data-address="<?= htmlspecialchars($customer['address']) ?>"
        >Editar</button>
        <form action="/clientes/delete" method="POST" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '') ?>">
            <input type="hidden" name="id" value="<?= $customer['id'] ?>">
            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar cliente?');">Eliminar</button>
        </form>
        <a href="/clientes" class="btn btn-secondary">Volver al listado</a>
    </div>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>