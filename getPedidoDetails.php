<?php
include('config/config.php');

$codPedido = isset($_GET['codPedido']) ? $_GET['codPedido'] : '';

if (!$codPedido) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó el código del pedido']);
    exit;
}

$token = substr($codPedido, 0, 5);

// Obtener la información del pedido
$infoPedido = "
SELECT 
    MAX(DATE_FORMAT(fecha, '%d de %b %Y')) AS fecha_pedido,
    MAX(DATE_FORMAT(fecha, '%h:%i %p')) AS hora_fecha_pedido
FROM temp_pedido 
WHERE token = '$token' AND estado = 'A'
";
$queryInfo = mysqli_query($con, $infoPedido);
$data = mysqli_fetch_assoc($queryInfo);

// Obtener los detalles del carrito
$carritoQuery = "
SELECT nameProd, cantidad, (cantidad * precio) AS total_a_pagar 
FROM temp_pedido 
WHERE token = '$token' AND estado = 'A'
";
$queryCarrito = mysqli_query($con, $carritoQuery);

$carrito = [];
while ($item = mysqli_fetch_assoc($queryCarrito)) {
    $carrito[] = $item;
}

$response = [
    'success' => true,
    'data' => [
        'fecha_pedido' => $data['fecha_pedido'],
        'hora_fecha_pedido' => $data['hora_fecha_pedido'],
        'carrito' => $carrito
    ]
];

echo json_encode($response);
?>
    