<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
sanitize( $_POST );
$oper = array('edit','del','add');
if ( isset( $_POST['oper'] ) && ( $_POST['oper'] =='del' ) ) {
		$sql = "DELETE FROM r, h
		USING `historico` h,
		`regfacturas` r
		WHERE h.`factura` = d.`codigo`
		AND r.id = ".$_POST['id'];
}
/**
 * Ejecutamos la sentencia SQL
 */
if ( isset( $_POST['oper']) && in_array( $_POST['oper'], $oper ) ) {
	ejecutaConsulta($sql);
}