<?php
/**
 * FacturasJSON File Doc Comment
 *
 * Pagina que devuelve el listado de la seccion de Facturas
 *
 * PHP Version 5.2.6
 *
 * @category Servicios
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
$sql = "Select id, codigo, id_cliente as idCliente,
date_format(fecha,'%d-%m-%Y') as fecha,
importe, obs_alt as observaciones, 
(Select Nombre from clientes where id = id_cliente) as cliente
FROM regfacturas ";
if ( $_REQUEST['_search'] == 'false' && isset( $_GET['idCliente'] ) ) {
    $sql .= " WHERE id_cliente like ".$_GET['idCliente']." ";
}
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
// Comprobar cuando estamos con el filtrado de cliente y cuando no
// Buscamos en observaciones, buscamos por codigo factura
$campos = array(
        'codigo' => 'codigo', 
        'fecha' => 'fecha', 
        'importe' => 'importe', 
        'observaciones' => 'obs_alt', 
        'idCliente' => 'id_cliente');

if ( $_REQUEST['_search'] == 'true' ) {
    if (isset( $_REQUEST['fecha'] ) ) {
        $_REQUEST['fecha'] = cambiaf( $_REQUEST['fecha'] );
    }
    if (isset( $_REQUEST['observaciones'] ) ) {
        $_REQUEST['observaciones'] = "%".$_REQUEST['observaciones']."%";
    }
    $sql .= " WHERE ";
    foreach( $campos as $key => $campo ) {
        if (array_key_exists($key, $_REQUEST ) ) {
            $sql .= " ".$campo." LIKE '".$_REQUEST[$key]."' AND ";
        }
    }
    $sql = substr($sql, 0, strlen($sql) - 4 );
} 

$sql .= " ORDER BY $sidx $sord LIMIT $start , $limit";
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
$importe = 0;
foreach ( $resultados as $resultado ) {
    $responce->rows[$i]['id'] = $resultado['id'];
    $responce->rows[$i]['cell'] = array(
            $resultado['id'],
            $resultado['idCliente'],
            $resultado['cliente'],
            $resultado['codigo'],
            $resultado['fecha'],
            precioFormateado($resultado['importe']),
            $resultado['observaciones']
            );
    $i++;
    $importe += $resultado['importe'];
}
$responce->userdata['codigo'] = $i;
$responce->userdata['importe'] = precioFormateado( $importe );
$responce->userdata['cliente'] = 'Totales:';
echo json_encode( $responce );