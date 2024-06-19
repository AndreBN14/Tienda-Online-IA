<?php
session_start();
include('config/config.php');

/**
 * Función para obtener los datos de todos los productos
 */
function getProductData($con)
{
    $sqlProducts = "
        SELECT 
            p.id AS prodId,
            p.nameProd,
            p.precio,
            f.foto1
        FROM 
            products AS p
        INNER JOIN
            fotoproducts AS f
        ON 
            p.id = f.products_id;
    ";
    $queryProducts = mysqli_query($con, $sqlProducts);

    if (!$queryProducts) {
        return false;
    }
    // Si todo está bien, devuelves el resultado del query
    return $queryProducts;
}

/**
 * Función para obtener los detalles del producto seleccionado
 */
function detalles_producto_seleccionado($con, $idProd)
{
    if ($idProd === null) {
        return null;
    }

    $idProd = mysqli_real_escape_string($con, $idProd);

    $sqlDetalleProducto = "
        SELECT 
            p.id AS prodId,
            p.nameProd,
            p.description_Prod,
            p.precio,
            f.foto1,
            f.foto2,
            f.foto3
        FROM 
            products AS p
        INNER JOIN
            fotoproducts AS f
        ON 
            p.id = f.products_id
        WHERE
            p.id = '$idProd'
        LIMIT 1;
    ";

    $queryProductoSeleccionado = mysqli_query($con, $sqlDetalleProducto);
    if (!$queryProductoSeleccionado) {
        return null;
    }
    return mysqli_fetch_assoc($queryProductoSeleccionado);
}

/**
 * Función para validar si el carrito tiene algún producto
 */
function validando_carrito()
{
    if (!isset($_SESSION['tokenStoragel']) || $_SESSION['tokenStoragel'] == "") {
        return '
            <div class="row align-items-center">
                <div class="col-lg-12 text-center mt-5">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Ops.!</strong> Tu carrito está vacío.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="col-lg-12 text-center mt-5 mb-5">
                    <a href="./" class="red_button btn_raza" style="padding: 5px 20px;">
                    <i class="bi bi-arrow-left-circle"></i>  Volver a la Tienda</a>
                </div>
            </div>';
    }
}

/**
 * Función para obtener los productos del carrito de compra
 */
function mi_carrito_de_compra($con)
{
    if (isset($_SESSION['tokenStoragel']) && $_SESSION['tokenStoragel'] != "") {
        $sqlCarritoCompra = "
                SELECT 
                    p.id AS prodId,
                    p.nameProd,
                    p.description_Prod,
                    p.precio,
                    f.foto1,
                    pt.id AS tempId,
                    pt.producto_id,
                    pt.cantidad,
                    pt.tokenCliente
                FROM 
                    products AS p
                INNER JOIN
                    fotoproducts AS f ON p.id = f.products_id
                INNER JOIN
                    pedidostemporales AS pt ON p.id = pt.producto_id
                WHERE 
                    pt.tokenCliente = '" . mysqli_real_escape_string($con, $_SESSION['tokenStoragel']) . "'
        ";
        $queryCarrito = mysqli_query($con, $sqlCarritoCompra);
        if (!$queryCarrito) {
            return false;
        }
        return $queryCarrito;
    } else {
        return 0;
    }
}

/**
 * Función para obtener la cantidad total de productos en el carrito
 */
function iconoCarrito($con)
{
    if (isset($_SESSION['tokenStoragel']) && $_SESSION['tokenStoragel'] !== "") {
        $sqlTotalProduct = "SELECT SUM(cantidad) AS totalProd FROM pedidostemporales WHERE tokenCliente='" . mysqli_real_escape_string($con, $_SESSION['tokenStoragel']) . "' GROUP BY tokenCliente";
        $jqueryTotalProduct = mysqli_query($con, $sqlTotalProduct);

        if ($jqueryTotalProduct) {
            // La consulta se ejecutó correctamente
            $dataTotalProducto = mysqli_fetch_array($jqueryTotalProduct);
            return '<span id="checkout_items" class="checkout_items">' . $dataTotalProducto["totalProd"] . '</span>';
        } else {
            return '<span id="checkout_items" class="checkout_items">0</span>';
        }
    } else {
        return '<span id="checkout_items" class="checkout_items">0</span>';
    }
}

/**
 * Función para obtener el total acumulado de la deuda del carrito
 */
function totalAcumuladoDeuda($con)
{
    if (isset($_SESSION['tokenStoragel']) && $_SESSION['tokenStoragel'] != "") {
        $SqlDeudaTotal = "
        SELECT SUM(p.precio * pt.cantidad) AS totalPagar 
        FROM products AS p
        INNER JOIN pedidostemporales AS pt
        ON p.id = pt.producto_id
        WHERE pt.tokenCliente = '" . mysqli_real_escape_string($con, $_SESSION["tokenStoragel"]) . "'
        ";
        $jqueryDeuda = mysqli_query($con, $SqlDeudaTotal);
        $dataDeuda = mysqli_fetch_array($jqueryDeuda);
        return  number_format($dataDeuda['totalPagar'], 0, '', '.');
    } else {
        return '0';
    }
}
?>
