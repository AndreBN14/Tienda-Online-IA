<?php
session_start();
include('../config/config.php'); // Archivo de configuración de la base de datos

// Establecer las cabeceras para indicar que la respuesta es en formato JSON
header('Content-Type: application/json');

/**
 * Función para generar un token único
 */
function tokenUnico() {
    return bin2hex(random_bytes(16)); // Generar un token único
}

/**
 * Verificar y establecer $_SESSION['tokenStoragel'] si no está definido
 */
if (!isset($_SESSION['tokenStoragel'])) {
    $_SESSION['tokenStoragel'] = tokenUnico();
}

/**
 * Función para agregar a la cantidad del producto en el carrito
 */
if (isset($_POST["aumentarCantidad"])) {
    $idProd = $_POST['idProd'];
    $precio = $_POST['precio'];
    $tokenCliente = $_POST['tokenCliente'];
    $cantidadProducto = $_POST['aumentarCantidad'];

    // Actualizar la cantidad del producto en la tabla temporal
    $updateQuery = "UPDATE pedidostemporales SET cantidad='$cantidadProducto' WHERE tokenCliente='$tokenCliente' AND id='$idProd'";
    mysqli_query($con, $updateQuery);

    // Preparar la respuesta JSON con el estado y el total a pagar actualizado
    $responseData = [
        'estado' => 'OK',
        'totalPagar' => totalAccionAumentarDisminuir($con, $tokenCliente)
    ];

    // Enviar la respuesta JSON
    echo json_encode($responseData);
}

/**
 * Función para agregar un producto al carrito de compra
 */
if (isset($_POST["accion"]) && $_POST["accion"] == "addCar") {
    $_SESSION['tokenStoragel'] = $_POST['tokenCliente'];
    $idProduct = $_POST['idProduct'];
    $precio = $_POST['precio'];
    $tokenCliente = $_POST['tokenCliente'];

    // Verificar si el producto ya está en el carrito del cliente
    $query = "SELECT * FROM pedidostemporales WHERE tokenCliente='$tokenCliente' AND producto_id='$idProduct'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Si el producto ya existe, incrementar la cantidad
        $row = mysqli_fetch_assoc($result);
        $newCantidad = $row['cantidad'] + 1;
        $updateQuery = "UPDATE pedidostemporales SET cantidad='$newCantidad' WHERE producto_id='$idProduct' AND tokenCliente='$tokenCliente'";
        mysqli_query($con, $updateQuery);
    } else {
        // Si el producto no existe, agregarlo al carrito
        $insertQuery = "INSERT INTO pedidostemporales (producto_id, cantidad, tokenCliente) VALUES ('$idProduct', 1, '$tokenCliente')";
        mysqli_query($con, $insertQuery);
    }

    // Obtener y enviar la cantidad total de productos en el carrito
    $totalQuery = "SELECT SUM(cantidad) AS totalProd FROM pedidostemporales WHERE tokenCliente='$tokenCliente'";
    $totalResult = mysqli_query($con, $totalQuery);
    $totalProd = mysqli_fetch_assoc($totalResult)['totalProd'];

    echo json_encode(['totalProductos' => $totalProd, 'estado' => 'OK']);
}

/**
 * Función para disminuir la cantidad de un producto en el carrito
 */
if (isset($_POST["accion"]) && $_POST["accion"] == "disminuirCantidad") {
    $_SESSION['tokenStoragel'] = $_POST['tokenCliente'];
    $idProd = mysqli_real_escape_string($con, $_POST['idProd']);
    $precio = mysqli_real_escape_string($con, $_POST['precio']);
    $tokenCliente = mysqli_real_escape_string($con, $_POST['tokenCliente']);
    $cantidadDisminuida = mysqli_real_escape_string($con, $_POST['cantidad_Disminuida']);

    // Actualizar la cantidad del producto en la tabla temporal
    if ($cantidadDisminuida == 0) {
        $deleteQuery = "DELETE FROM pedidostemporales WHERE tokenCliente='$tokenCliente' AND id='$idProd'";
        mysqli_query($con, $deleteQuery);
        $responseData = [
            'totalProductos' => totalProductosSeleccionados($con, $tokenCliente),
            'totalPagar' => totalAccionAumentarDisminuir($con, $tokenCliente),
            'estado' => 'OK'
        ];
    } else {
        $updateQuery = "UPDATE pedidostemporales SET cantidad='$cantidadDisminuida' WHERE tokenCliente='$tokenCliente' AND id='$idProd'";
        mysqli_query($con, $updateQuery);
        $responseData = [
            'totalProductos' => totalProductosSeleccionados($con, $tokenCliente),
            'totalPagar' => totalAccionAumentarDisminuir($con, $tokenCliente),
            'estado' => 'OK'
        ];
    }

    // Enviar la respuesta JSON
    echo json_encode($responseData);
}

/**
 * Función para eliminar un producto del carrito
 */
if (isset($_POST["accion"]) && $_POST["accion"] == "borrarproductoModal") {
    $nameTokenProducto = $_POST['tokenCliente'];
    $idProduct = $_POST['idProduct'];

    // Eliminar el producto del carrito
    $deleteQuery = "DELETE FROM pedidostemporales WHERE id='$idProduct'";
    mysqli_query($con, $deleteQuery);

    // Preparar la respuesta JSON con datos actualizados
    $respData = [
        'totalProductos' => totalProductosSeleccionados($con, $nameTokenProducto),
        'totalPagar' => totalAccionAumentarDisminuir($con, $nameTokenProducto),
        'estado' => 'OK'
    ];

    // Enviar la respuesta JSON
    echo json_encode($respData);
}

/**
 * Función para limpiar todo el carrito
 */
if (isset($_POST["accion"]) && $_POST["accion"] == "limpiarTodoElCarrito") {
    // Cerrar todas las variables de sesión
    session_unset();

    // Destruir la sesión
    session_destroy();

    // Respuesta JSON indicando que se limpió el carrito
    echo json_encode(['mensaje' => 'Carrito limpiado']);
}

/**
 * Función para calcular el total de productos seleccionados en el carrito
 */
function totalProductosSeleccionados($con, $tokenCliente) {
    $query = "SELECT * FROM pedidostemporales WHERE tokenCliente='$tokenCliente'";
    $result = mysqli_query($con, $query);
    return mysqli_num_rows($result);
}

/**
 * Función para calcular el total a pagar por los productos en el carrito
 */
function totalAccionAumentarDisminuir($con, $tokenCliente) {
    $query = "SELECT SUM(p.precio * pt.cantidad) AS totalPagar FROM products AS p INNER JOIN pedidostemporales AS pt ON p.id = pt.producto_id WHERE pt.tokenCliente = '$tokenCliente'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['totalPagar'];
}

/**
 * Función para obtener el total de productos en el carrito desde la base de datos
 */
function totalProductosBD($con, $tokenCliente) {
    $query = "SELECT SUM(cantidad) AS totalProd FROM pedidostemporales WHERE tokenCliente='$tokenCliente' GROUP BY tokenCliente";
    $result = mysqli_query($con, $query);
    $dataTotalProducto = mysqli_fetch_assoc($result);
    return $dataTotalProducto['totalProd'] ?? 0;
}
?>
