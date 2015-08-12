<?php
/**
 * Funcion deshabilitada para convertir los caracteres de un mapa a otro
 */
/*require_once 'variables.php';
set_time_limit( 60000 );
$sql = "Select * from historico2";
$consulta = mysql_db_query( $dbname, $sql, $con);
while ( true == ($resultado = mysql_fetch_array( $consulta ))) {
	//echo utf8_decode($resultado['servicio']).  " - " . utf8_decode($resultado['obs']) . "<br/>";
	$sql = "Update historico2 
	set servicio = '". utf8_decode($resultado['servicio']) . "'
	where id like " . $resultado['id'];
	if ( mysql_db_query( $dbname, $sql, $con)) {
		echo $resultado['id'] . "<br/>";
	}
}
echo "Actualizacion servicios historico realizada";*/