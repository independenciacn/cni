<?php
/*
 * FUNCIONES AUXILIARES
 */
require_once '../inc/variables.php';
/*
 * Muestra las telecomunicaciones
 */
function teleco_cliente($cliente,$servicio)
{
	global $con;
	$sql = "Select valor from z_sercont where idemp like $cliente and servicio like '".$servicio."'";
	$consulta = @mysql_query($sql,$con);
	while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
	{
		$cadena.=$resultado[0]."<br/>";
	}
	return $cadena;
}

/*
 * Funcion del cambio de fecha
 */
function cambiaf($stamp) 
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}

/*
 * Devuelve el nombre del cliente
 */
function nombre_cliente($id)
{
	global $con;
	$sql="Select Nombre from clientes where id like $id";
	$consulta = @mysql_query($sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	return $resultado[Nombre];
}

/*
 * Quita los segundos en la visualizacion
 */
function quita_segundos($hora)
{
	$sin_sec=explode(":",$hora);
	$final = $sin_sec[0].":".$sin_sec[1];
	return $final;
}

/*
 * Genera los datos de despacho para ver si esta ocupado
 * parcial o desocupado
 */ 
function datos_despacho($despacho)
{
	global $con;
	$sql="Select * from agenda where despacho like '$despacho' and 
		(datediff(curdate(),finc)<=0 or datediff(curdate(),ffin)<=0)
		order by finc asc, hinc asc limit 2";
	$consulta = @mysql_query($sql,$con);
	if(@mysql_numrows($consulta)!=0)
	{
		$cadena.="<div class='despacho_parcial' height='100%'>";
		$i=0;
		while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
		{
			$i++;
			$cadena.=nombre_cliente($resultado[id_cliente])."<br/>";
			$cadena.=cambiaf($resultado[finc])." - ".cambiaf($resultado[ffin]).
			"<br/>".quita_segundos($resultado[hinc])."-".quita_segundos($resultado[hfin]);
			$cadena.="<br/><span class='mini_boton' style='background:#666699;' onclick='informacion_cliente($resultado[id_cliente])'>[+Info]</span><p/>";
		}
		//Si hay mas de 2
		$sql="Select * from agenda where despacho like '$despacho' and 
		(datediff(curdate(),finc)<=0 or datediff(curdate(),ffin)<=0)
		order by finc asc, hinc asc";
		$consulta = @mysql_query($sql,$con);
		if(@mysql_numrows($consulta)>=2)
			$cadena.="<br/>Mas en detalles";
		//fin del si hay mas de dos
		$cadena.="</div>";
	}
	else
		$cadena.="";
	return $cadena;
	
}
?>
