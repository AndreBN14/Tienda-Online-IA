<?php
include('config.php');
include('funciones/funciones_tienda.php');

// Obtener todas las categorías
$sqlCategorias = "SELECT * FROM categorias";
$queryCategorias = mysqli_query($con, $sqlCategorias);

// Verificar si hay categorías
$categorias = [];
if ($queryCategorias && mysqli_num_rows($queryCategorias) > 0) {
    while ($categoria = mysqli_fetch_assoc($queryCategorias)) {
        $categorias[] = $categoria;
    }
}
?>

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
    <title>Categorías de Productos - Nombre de tu Tienda</title>
</head>
<body>
    <div class="page-loading active">
        <div class="page-loading-inner">
            <div class="page-spinner"></div>
            <span>cargando...</span>
        </div>
    </div>
    
    <?php include('header.php'); ?>

    <div class="super_container">
        <div class="container mt-5 pt-5">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="section_title">
                        <h2>Categorías de Productos</h2>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <div class="col-6 col-md-3 mt-5 text-center Products">
                            <div class="card" style="max-height: 400px !important; min-height: 400px !important;">
                                <div>
                                    <img class="card-img-top" src="<?php echo htmlspecialchars($categoria['imagen']); ?>" alt="<?php echo htmlspecialchars($categoria['categoria']); ?>" style="max-width: 200px;">
                                </div>
                                <div class="card-body text-center">
                                    <h5 class="card-title card_title"><?php echo htmlspecialchars($categoria['categoria']); ?></h5>
                                    <hr>
                                    <p class="card-text p_puntos">
                                        <?php echo htmlspecialchars($categoria['descripcion']); ?>
                                    </p>
                                </div>
                                <a href="productos.php?categoria=<?php echo htmlspecialchars($categoria['id']); ?>" class="red_button btn_puntos" title="Ver <?php echo htmlspecialchars($categoria['categoria']); ?>">
                                    Ver Productos
                                    <i class="bi bi-arrow-right-circle"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No se encontraron categorías.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include('includes/footer.html'); ?>
    </div>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/styles/bootstrap4/popper.js"></script>
    <script src="assets/styles/bootstrap4/bootstrap.min.js"></script>
    <script src="assets/plugins/Isotope/isotope.pkgd.min.js"></script>
    <script src="assets/plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
    <script src="assets/plugins/easing/easing.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        // Remover el loader después de cargar la página
        window.addEventListener("load", function () {
            const loader = document.querySelector(".page-loading");
            setTimeout(function () {
                loader.classList.remove("active");
                loader.remove();
            }, 400);
        });
    </script>
</body>
</html>
