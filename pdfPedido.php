<?php
require_once('tcpdf/tcpdf.php');
include('config/config.php');
date_default_timezone_set('America/Bogota');

$codPedido = isset($_POST['codPedido']) ? $_POST['codPedido'] : $_GET['codPedido'];
$token = substr($codPedido, 0, 5);

ob_end_clean(); // Limpiar el buffer de salida

// SQL para buscar información adicional del pedido
$infoPedido = "
SELECT 
    MAX(DATE_FORMAT(fecha, '%d de %b %Y')) AS fecha_pedido,
    MAX(DATE_FORMAT(fecha, '%h:%i %p')) AS hora_fecha_pedido
FROM pedidostemporales
WHERE tokenCliente = '" . mysqli_real_escape_string($con, $codPedido) . "' LIMIT 1";
$queryPedido = mysqli_query($con, $infoPedido);
$data = mysqli_fetch_array($queryPedido);

// Clase extendida de TCPDF
class MYPDF extends TCPDF
{
    // Método para el encabezado del PDF
    public function Header()
    {
        $image_file = dirname(__FILE__) . '/assets/images/logo.png';
        $this->Image($image_file, 10, 10, 40, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Factura de Pedido', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
    }

    // Método para el pie de página del PDF
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Crear un nuevo documento PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Establecer márgenes
$pdf->SetMargins(15, 35, 15);
$pdf->SetHeaderMargin(20);
$pdf->SetFooterMargin(15);
$pdf->SetAutoPageBreak(true, 15);

// Información del documento PDF
$pdf->SetCreator('UrianViera');
$pdf->SetAuthor('UrianViera');
$pdf->SetTitle('Factura de Pedido');

// Agregar una página al PDF
$pdf->AddPage();

// Configurar tipo de fuente y tamaño para el contenido
$pdf->SetFont('helvetica', '', 10);

// Mostrar el código, fecha y hora del pedido en el PDF
$pdf->SetXY(145, 20);
$pdf->Write(0, 'Código: ' . $token);
$pdf->SetXY(145, 25);
$pdf->Write(0, 'Fecha: ' . $data['fecha_pedido']);
$pdf->SetXY(145, 30);
$pdf->Write(0, 'Hora: ' . $data['hora_fecha_pedido']);

// Consulta SQL para obtener los detalles del carrito de compra
$sqlCarritoCompra = "
SELECT 
    p.nameProd,
    p.precio,
    pedtemp.cantidad,
    p.precio * pedtemp.cantidad AS total_a_pagar
FROM 
    products AS p
INNER JOIN
    pedidostemporales AS pedtemp ON p.id = pedtemp.producto_id
WHERE 
    pedtemp.tokenCliente = '" . mysqli_real_escape_string($con, $codPedido) . "'";
$queryCarrito = mysqli_query($con, $sqlCarritoCompra);

// Inicializar HTML para el contenido del PDF
$html = '
<h1 align="center">RESUMEN DE MI PEDIDO</h1>
<hr style="border: 0.5px solid #333;">
<br><br>
<table style="font-size: 12px; border-collapse: collapse;" align="center" width="80%">
    <tr style="background-color: #f2f2f2;">
        <th style="border: 1px solid #ddd; padding: 8px;">Producto</th>
        <th style="border: 1px solid #ddd; padding: 8px;">Cantidad</th>
        <th style="border: 1px solid #ddd; padding: 8px;">SubTotal</th>
    </tr>';

$total = 0;

// Iterar sobre los resultados de la consulta y construir la tabla HTML
while ($dataP = mysqli_fetch_array($queryCarrito)) {
    $precioFormateado = number_format($dataP['precio'], 0, '', '.');
    $html .= '
    <tr>
        <td style="border: 1px solid #ddd; padding: 8px;">' . $dataP['nameProd'] . '</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $dataP['cantidad'] . '</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">$ ' . $precioFormateado . '</td>
    </tr>';
    $total += $dataP['total_a_pagar'];
}

// Agregar fila para el total
$html .= '
    <tr style="background-color: #f2f2f2;">
        <td colspan="2" style="text-align: right; padding: 8px;"><strong>Total:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>$ ' . number_format($total, 0, '', '.') . '</strong></td>
    </tr>';

// Cerrar tabla y agregar contenido HTML al PDF
$html .= '</table>';
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y generar el PDF para descargarlo
$pdf->Output('Solicitud_pedido_' . date('d_m_Y_h_i_A') . '.pdf', 'D');

// Terminar la ejecución del script
exit;
?>
