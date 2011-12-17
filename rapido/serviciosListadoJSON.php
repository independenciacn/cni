<?php
/**
 * ServiciosListadoJSON File Doc Comment
 *
 * Genera el listado de servicios con los importes de los servicios
 *
 * PHP Version 5.2.6
 *
 * @category ServiciosListadoJSON
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
sanitize( $_REQUEST );
$sql = "Select id, nombre as value, PrecioEuro as precio, iva, 
ROUND((precioEuro + precioEuro * iva/100),2) as subtotal 
FROM servicios2
WHERE nombre like '%". $_GET['text']."%'
ORDER by nombre
limit ".$_GET['maxrows'];
$resultados = consultaGenerica( $sql,  MYSQL_ASSOC );
echo json_encode( $resultados );