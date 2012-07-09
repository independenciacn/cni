<?php 
// datos.php este solo devuelve datos
/*
 * TODO: Deberia crear un clase para que hiciera todo esto, para la siguiente
 * version TODO: Coger el importe del almacenaje de la base de datos, no fijo
 */
require_once '../inc/variables.php';
$hoy = date ( "d/m/Y" );
/**
 * Calcula el importe del almacenamiento
 * 
 * @return float
 */
function importeAlmacen() {
	global $con;
	$sql = "Select PrecioEuro from servicios2 where nombre like 'Almacenaje'";
	$consulta = mysql_query ( $sql, $con );
	$resultado = mysql_fetch_array ( $consulta );
	return $resultado [0];
}
/**
 * Cambia el formato de fecha
 * 
 * @deprecated
 * @param string $stamp
 * @return string $fecha
 */
function cambiaf($stamp) // funcion del cambio de fecha
{
	// formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode ( "-", $stamp );
	$fecha = $fdia [2] . "/" . $fdia [1] . "/" . $fdia [0];
	return $fecha;
}
/**
 * Calculo del inicio y fin del almacenamiento
 * 
 * @param date $inicio
 * @param date $fin
 * @param int $bultos
 * @return string
 */
function calculo($inicio, $fin, $bultos) {
	global $hoy;
	if ($fin == '0000-00-00') {
		$dias = round ( (strtotime ( $hoy ) - strtotime ( $inicio )) / (24 * 60 * 60), 0 );
		$total = $bultos * $dias * importeAlmacen ();
		$total = 'En Almacen';
	} else {
		$dias = round ( (strtotime ( $fin ) - strtotime ( $inicio )) / (24 * 60 * 60), 0 );
		$total = $bultos * $dias * importeAlmacen ();
		$total = round ( $total, 2 );
		$total = $total . "&euro;";
	}
	return $total;
}
/**
 * Aplicacion General
 */
if (isset ( $_POST ['cliente'] )) {
	if (isset ( $_POST ['item'] )) {
		$sql = "Select * from z_almacen where id like ".$_POST['item'];
		$consulta = mysql_query ( $sql, $con );
		$resultado = mysql_fetch_array ( $consulta );
		$s_bulto = $resultado [2];
		$s_finicio = cambiaf ( $resultado [3] );
		$s_ffinal = cambiaf ( $resultado [4] );
		$opcion = $_POST ['item'];
		$boton = "[*]Modificar";
	} else {
		$s_bulto = '';
		$s_finicio = $hoy;
		$s_ffinal = '';
		$opcion = "alta";
		$boton = "[+]Agregar";
	}
	// consultamos el nombre del cliente
	importeAlmacen ();
	$sql = "Select * from z_almacen where cliente like ".$_POST['cliente']." 
	and (Year( now( ) ) - Year( inicio )) <=1";
	$consulta = mysql_query ( $sql, $con );
	$sql2 = "Select Id, Nombre from clientes where id like ".$_POST['cliente'];
	$consulta2 = mysql_query ( $sql2, $con );
	$previo = mysql_fetch_array ( $consulta2 );
	// ya que estamos ponemos el formulario aqui
	$muestra .= "<div id='etq_cliente'>Cliente :<b>" . $previo [1] . "</b></div>
	<input type='hidden' id='cliente' value='" . $previo [0] . "'/>
	<input type='hidden' id='opcion' value='" . $opcion . "' /><br/>
	<div id='etq_resto'><span class='etiqueta'>Bultos:</span>&nbsp;
	<input type='text' id='bultos' size='3' value='" . $s_bulto . "'/>&nbsp;&nbsp;
	<span class='etiqueta'>Fecha Inicio:</span>(dd/mm/aaaa)&nbsp;
	<input type='text' id='finicio' value='" . $s_finicio . "'/>
	<button type='reset' id='f_trigger_a'>...</button>&nbsp;&nbsp;
	<span class='etiqueta'>Fecha Fin:</span>(dd/mm/aaaa)&nbsp;
	<input type='text' id='ffinal' value='" . $s_ffinal . "'/>
	<button type='reset' id='f_trigger_b'>...</button>&nbsp;&nbsp;
	<input type='submit' class='boton' id='btn_almacen' onclick='a_almacen()' value='" . $boton . "' /></div>";
	echo $muestra;
}
if (isset ( $_GET ['tabla'] )) {
	// $sql = "Select * from z_almacen where cliente like $_GET[tabla]";
	$sql = "Select * from z_almacen where cliente like ".$_GET['tabla']." 
	and (Year( now( ) ) - Year( inicio )) <=1";
	// echo $sql;
	$consulta = mysql_query ( $sql, $con );
	$muestra .= "<center>
	<table  class='tabla' id='datos' cellspacing='2' cellpadding='2' style='width:500px'>
	<tr>
	<th style='width:25px'>#</th>
	<th style='width:25px'>Bultos</th>
	<th style='width:150px'>Fecha Inicio</th>
	<th style='width:150px'>Fecha Fin</th>
	<th style='width:150px'>Total &euro;</th>
	<th style='width:50px'></th>
	<tr/>";
	$i = 0;
	while ( true == ($resultado = mysql_fetch_array ( $consulta )) ) {
		$i ++;
		$inicio = cambiaf ( $resultado [3] );
		$fin = cambiaf ( $resultado [4] );
		$promedio = calculo ( $resultado [3], $resultado [4], $resultado [2] );
		if ($fin == "00/00/0000")
			$fin = "En Almacen"; // $hoy;
		if ($fin == "En Almacen")
			$color = "Orange"; // "#DDE0FF";
		else if ($i % 2 == 0)
			$color = "#eeeeee";
		else
			$color = "#dddddd"; // "#DDE0FF";
		$muestra .= "<tr >
		<td bgcolor='" . $color . "' class='celda' style='width:25px' align='center'>" . $i . "</td>
		<td bgcolor='" . $color . "' class='celda' style='width:25px' align='center'>" . $resultado [2] . "</td>
		<td bgcolor='" . $color . "' class='celda' style='width:150px' align='center'>" . $inicio . "</td>
		<td bgcolor='" . $color . "' class='celda' style='width:150px' align='center'>" . $fin . "</td>
		<td bgcolor='" . $color . "' class='celda' style='width:150px' align='center'>" . $promedio . "</td>
		<td bgcolor='" . $color . "' class='celda' style='width:50px' align='center'>
		<img src='../imagenes/editar.png' class='img_edit' onclick='edita_almacen(" . $resultado [0] . ")' />
		<img src='../imagenes/borrar.png' class='img_edit' onclick='borra_almacen(" . $resultado [0] . ")' /></th>
		</tr>";
	}
	$muestra .= "</table></center>";
	echo $muestra;
}

if (isset ( $_POST ['bcliente'] )) {
	$cliente = $_POST ['bcliente'];
	$bultos = $_POST ['bultos'];
	$finicio = $_POST ['finicio'];
	$ffinal = $_POST ['ffinal'];
	if ($_POST ['op'] == "alta")
		$sql = "Insert into z_almacen (`cliente`,`bultos`,`inicio`,`fin`) 
		values ('$cliente','$bultos',STR_TO_DATE('$finicio','%d/%m/%Y'),
		STR_TO_DATE('$ffinal','%d/%m/%Y'))";
	else
		$sql = "Update z_almacen set `bultos` = '$bultos', 
		`inicio` = STR_TO_DATE('$finicio','%d/%m/%Y'), 
		`fin` = STR_TO_DATE('$ffinal','%d/%m/%Y') where id like ".$_POST['op'];
	$consulta = mysql_query ( $sql, $con );

}
/**
 * *************************************************************************************
 */
if (isset ( $_POST ['borra'] )) {
	$sql = "Delete from z_almacen where id like ".$_POST['borra'];
	$consulta = mysql_query ( $sql, $con );
}