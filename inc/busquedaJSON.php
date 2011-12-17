<?php
/**
 * busquedaJSON
 *
 * Devuelve los resultados de la busqueda en las whiteTables en formato JSON
 * Se usa en los autocomplete
 *
 * PHP Version 5.2.10
 *
 * @category busquedaJSON
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once 'configuracion.php';
sanitize( $_GET );
if ( in_array( $_GET['table'], $whiteTables ) && isset($_SESSION['usuario'] ) ) {
    $sql = "Select id, nombre as value FROM ".$_GET['table']. " 
    WHERE nombre like '%". $_GET['text']."%'
    ORDER by nombre
    limit ".$_GET['maxrows'];
    $resultados = consultaGenerica( $sql,  MYSQL_ASSOC );
    echo json_encode( $resultados );
}