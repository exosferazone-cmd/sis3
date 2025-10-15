<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="container mt-4">
    <h2>Mi Perfil</h2>
    <form action="/mi-perfil/update" method="POST" class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
        </div>
        <div class="col-md-6">
            <label for="password" class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Solo si desea cambiar">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
        </div>
    </form>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>
