<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="assets/images/icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/styles/bootstrap4/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/styles/main_styles.css">
    <link rel="stylesheet" type="text/css" href="assets/styles/responsive.css">
    <link rel="stylesheet" href="assets/styles/loader.css">
    <title>Contacto - Tu Tienda Online</title>
</head>

<body>
    <div class="page-loading active">
        <div class="page-loading-inner">
            <div class="page-spinner"></div>
            <span>cargando...</span>
        </div>
    </div>
    <?php include('funciones/funciones_tienda.php'); ?>
    <?php include('header.php'); ?>

    <!-- Contenido específico de la página de contacto -->
    <div class="super_container">
        <div class="container mt-5 pt-5">
            <!-- Aquí puedes agregar el contenido de contacto -->
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="section_title">
                        <h2>Contacto</h2>
                    </div>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-12 text-center mt-5">
                    <p>Formulario de contacto, mapa, detalles de contacto, etc.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.html'); ?>
    <?php include('includes/js.html'); ?>
</body>

</html>
