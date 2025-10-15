<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="page-header">
    <h1>Gestión de Proveedores</h1>
    <button id="addSupplierBtn" class="btn btn-primary">Agregar Nuevo Proveedor</button>
</div>

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

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Persona de Contacto</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['suppliers'] as $supplier): ?>
                <tr>
                    <td><?php echo Helpers::escape($supplier['name']); ?></td>
                    <td><?php echo Helpers::escape($supplier['contact_person']); ?></td>
                    <td><?php echo Helpers::escape($supplier['email']); ?></td>
                    <td><?php echo Helpers::escape($supplier['phone']); ?></td>
                    <td>
                        <button class="btn btn-edit" data-id="<?php echo $supplier['id']; ?>" data-name="<?php echo Helpers::escape($supplier['name']); ?>" data-contact="<?php echo Helpers::escape($supplier['contact_person']); ?>" data-email="<?php echo Helpers::escape($supplier['email']); ?>" data-phone="<?php echo Helpers::escape($supplier['phone']); ?>" data-address="<?php echo Helpers::escape($supplier['address']); ?>">Editar</button>
                        <a href="/proveedores/delete?id=<?php echo $supplier['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este proveedor?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="supplierModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2 id="modalTitle"></h2>
        <form id="supplierForm" action="/proveedores/add" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo Helpers::escape($data['csrf_token']); ?>">
            <input type="hidden" id="supplierId" name="id">
            <div class="form-group">
                <label for="supplierName">Nombre</label>
                <input type="text" id="supplierName" name="name" required>
            </div>
            <div class="form-group">
                <label for="supplierContact">Persona de Contacto</label>
                <input type="text" id="supplierContact" name="contact_person">
            </div>
            <div class="form-group">
                <label for="supplierEmail">Email</label>
                <input type="email" id="supplierEmail" name="email">
            </div>
            <div class="form-group">
                <label for="supplierPhone">Teléfono</label>
                <input type="text" id="supplierPhone" name="phone">
            </div>
            <div class="form-group">
                <label for="supplierAddress">Dirección</label>
                <textarea id="supplierAddress" name="address"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>
<script>
    // Lógica para mostrar y ocultar el modal
    const modal = document.getElementById('supplierModal');
    const addBtn = document.getElementById('addSupplierBtn');
    const closeBtn = document.querySelector('.close-btn');
    const form = document.getElementById('supplierForm');
    const modalTitle = document.getElementById('modalTitle');
    const supplierId = document.getElementById('supplierId');
    const supplierName = document.getElementById('supplierName');
    const supplierContact = document.getElementById('supplierContact');
    const supplierEmail = document.getElementById('supplierEmail');
    const supplierPhone = document.getElementById('supplierPhone');
    const supplierAddress = document.getElementById('supplierAddress');
    const editBtns = document.querySelectorAll('.btn-edit');

    addBtn.onclick = () => {
        modalTitle.textContent = 'Agregar Nuevo Proveedor';
        form.action = '/proveedores/add';
        supplierId.value = '';
        supplierName.value = '';
        supplierContact.value = '';
        supplierEmail.value = '';
        supplierPhone.value = '';
        supplierAddress.value = '';
        modal.style.display = 'block';
    };

    closeBtn.onclick = () => {
        modal.style.display = 'none';
    };

    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };

    editBtns.forEach(btn => {
        btn.onclick = () => {
            modalTitle.textContent = 'Editar Proveedor';
            form.action = '/proveedores/edit';
            supplierId.value = btn.dataset.id;
            supplierName.value = btn.dataset.name;
            supplierContact.value = btn.dataset.contact;
            supplierEmail.value = btn.dataset.email;
            supplierPhone.value = btn.dataset.phone;
            supplierAddress.value = btn.dataset.address;
            modal.style.display = 'block';
        };
    });
</script>
