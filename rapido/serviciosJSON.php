<?php
/**
 * ServiciosJSON File Doc Comment
 *
 * Genera el listado de servicios seleccionado
 *
 * PHP Version 5.2.6
 *
 * @category ServiciosJSON
 * @package  cni/rapido
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
sanitize( $_GET );
$page = $_REQUEST['page']; // get the requested page
$limit = $_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = $_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = $_REQUEST['sord']; // get the direction
if(!$sidx) $sidx =1;
$sql = "
Select d.Servicio as servicio, d.Cantidad as cantidad,
date_format(c.fecha,'%d-%m-%Y') as fecha,
d.PrecioUnidadEuros as precio, d.ImporteEuro as importe,
d.iva as iva, c.`Id Pedido` as idPedido,
d.Observaciones as observaciones,
d.Id as id, 
c.Cliente as idCliente,
(d.ImporteEuro * (1 + (d.iva/100) ) ) as subtotal
from `detalles consumo de servicios` as d
join `consumo de servicios` as c on c.`Id Pedido` = d.`Id Pedido`
where c.Cliente like " . $_GET['idCliente'] ." and (" . $_GET['anyo'] . "
like date_format(c.fecha,'%Y') and '" . $_GET['mes'] . "'
like date_format(c.fecha,'%c')) order by c.fecha asc";
$count = totalCeldas( $sql );
if( $count >0 ) {
    $total_pages = ceil($count/$limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages) {
    $page = $total_pages;
}
$start = $limit * $page - $limit;
$resultados = consultaGenerica( $sql, MYSQL_ASSOC );
$responce = new stdClass();
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
$total = 0;
$importe = 0;
$totalCantidad = 0;
foreach ( $resultados as $resultado ) {
    $responce->rows[$i]['id'] = $resultado['id'];
    $responce->rows[$i]['cell'] = array(
            $resultado['id'],
            $resultado['idPedido'],
            $resultado['idCliente'],
            $resultado['fecha'],
            $resultado['servicio'],
            $resultado['observaciones'],
            $resultado['cantidad'],
            precioFormateado( $resultado['precio'] ),
            precioFormateado( $resultado['importe'] ),
            $resultado['iva'],
            precioFormateado( $resultado['subtotal'] )
    );
    $i++;
    $importe += $resultado['importe'];
    $totalCantidad += $resultado['cantidad'];
    $total += $resultado['subtotal'];
}
$responce->userdata['servicio'] = $i;
$responce->userdata['cantidad'] = $totalCantidad;
$responce->userdata['importe'] = precioFormateado( $importe );
$responce->userdata['subtotal'] = precioFormateado( $total );
$responce->userdata['fecha'] = 'Totales:';
echo json_encode( $responce );
?>