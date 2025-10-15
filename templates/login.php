<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo Helpers::escape(SITE_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="error-message">
                <?php echo Helpers::escape($_SESSION['login_error']); ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>
        <form action="/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo Helpers::escape($data['csrf_token']); ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>
</body>
</html>