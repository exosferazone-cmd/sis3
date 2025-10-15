<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.css" />
        <!-- Bootstrap Bundle JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>
    <title><?php echo Helpers::escape(SITE_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <link rel="stylesheet" href="/assets/css/style.css">

</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <a href="/admin" class="logo">Gestión Comercial</a>
            <nav class="main-nav">
                <ul>
                    <li><a href="/clientes">Clientes</a></li>
                    <li><a href="/productos">Productos</a></li>
                    <li><a href="/ventas">Ventas</a></li>
                    <li><a href="/reportes">Reportes</a></li>
                    <li><a href="/caja">Caja</a></li>
                </ul>
            </nav>
            <div class="user-info">
                <span>Hola, <?php echo Helpers::escape($_SESSION['user_name']); ?></span>
                <a href="/logout" class="btn btn-logout">Salir</a>
            </div>
        </div>
    </header>
    <main class="content">
