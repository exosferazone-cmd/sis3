
<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="container mt-4">
    <h2>Clientes</h2>
    <button id="addCustomerBtn" class="btn btn-success mb-3">Agregar Cliente</button>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?= htmlspecialchars($customer['name']) ?></td>
                    <td><?= htmlspecialchars($customer['email']) ?></td>
                    <td><?= htmlspecialchars($customer['phone']) ?></td>
                    <td><?= htmlspecialchars($customer['address']) ?></td>
                    <td>
                        <button 
                            class="btn btn-info btn-sm btn-detail"
                            data-id="<?= $customer['id'] ?>"
                            data-name="<?= htmlspecialchars($customer['name']) ?>"
                            data-email="<?= htmlspecialchars($customer['email']) ?>"
                            data-phone="<?= htmlspecialchars($customer['phone']) ?>"
                            data-address="<?= htmlspecialchars($customer['address']) ?>"
                        >Ver Detalle</button>
                        <button 
                            class="btn btn-sm btn-warning btn-edit"
                            data-id="<?= $customer['id'] ?>"
                            data-name="<?= htmlspecialchars($customer['name']) ?>"
                            data-email="<?= htmlspecialchars($customer['email']) ?>"
                            data-phone="<?= htmlspecialchars($customer['phone']) ?>"
                            data-address="<?= htmlspecialchars($customer['address']) ?>"
                        >Editar</button>
                        <form action="/clientes/delete" method="POST" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token']) ?>">
                            <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar cliente?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>


<!-- Modal Bootstrap para edición/agregado de cliente -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="customerForm" action="/clientes/add" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo Helpers::escape($data['csrf_token']); ?>">
                    <input type="hidden" id="customerId" name="id">
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Nombre</label>
                        <input type="text" id="customerName" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Email</label>
                        <input type="email" id="customerEmail" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="customerPhone" class="form-label">Teléfono</label>
                        <input type="text" id="customerPhone" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="customerAddress" class="form-label">Dirección</label>
                        <textarea id="customerAddress" name="address" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addCustomerBtn');
    const customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
    const form = document.getElementById('customerForm');
    const modalTitle = document.getElementById('modalTitle');
    const customerId = document.getElementById('customerId');
    const customerName = document.getElementById('customerName');
    const customerEmail = document.getElementById('customerEmail');
    const customerPhone = document.getElementById('customerPhone');
    const customerAddress = document.getElementById('customerAddress');
    const editBtns = document.querySelectorAll('.btn-edit');
    const detailBtns = document.querySelectorAll('.btn-detail');

    addBtn.addEventListener('click', function() {
        modalTitle.textContent = 'Agregar Nuevo Cliente';
        form.action = '/clientes/add';
        customerId.value = '';
        customerName.value = '';
        customerEmail.value = '';
        customerPhone.value = '';
        customerAddress.value = '';
        customerModal.show();
    });

    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modalTitle.textContent = 'Editar Cliente';
            form.action = '/clientes/edit';
            customerId.value = btn.dataset.id;
            customerName.value = btn.dataset.name;
            customerEmail.value = btn.dataset.email;
            customerPhone.value = btn.dataset.phone;
            customerAddress.value = btn.dataset.address;
            customerModal.show();
        });
    });

    detailBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modalTitle.textContent = 'Detalle de Cliente';
            form.action = '/clientes/edit'; // Permite editar desde el detalle
            customerId.value = btn.dataset.id;
            customerName.value = btn.dataset.name;
            customerEmail.value = btn.dataset.email;
            customerPhone.value = btn.dataset.phone;
            customerAddress.value = btn.dataset.address;
            customerModal.show();
        });
    });
});
</script>
