<?php
// Enviamos los encabezados de hoja de calculo
//session_cache_limiter("public");
session_start();
if(session_id() == $_GET[id])
{
require_once("../inc/variables.php");
$sql = $_SESSION['metagenerator'];
$empresa = $_SESSION['metaempresa'];
$mostrada = $_SESSION['metafecha'];
$sersel = $_SESSION['metaservicio'];
$agrupado = $_SESSION['metagrupado'];
header("Content-type:  application/msword");
header("Content-Disposition: attachment; filename=word.doc");
function cambiaf($stamp) //funcion del cambio de fecha
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}

// Creamos la tabla
		
		$consulta = mysql_query($sql,$con);
		//dise�o de la tabla con el boton de eliminar
		print("<table width=100% cellpadding=0 cellspacing=0>");
		print("<tr><th colspan=7>Servicios contratados por $empresa - Periodo $mostrada - Servicio: $sersel</th></tr>");
		print("<tr><th align='left'>Fecha</th><th align='left'>Servicio</th><th align='left'>Cantidad</th><th align='left'>Precio unidad</th><th align='left'>Subtotal</th><th align='left'>Iva</th><th align='left'>Total</th></tr>");
		while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
		{
			if($_SESSION['metagrupado']==1)
			$fecha = "Agrupado";
			else
			$fecha = cambiaf($resultado[2]);
			$total = ((round($resultado[4],2) * $resultado[5])/100) + round($resultado[4],2);
			$total = round($total,2);
			$stotal = $stotal + $total;
			$unitario = round($resultado[3],2);
			$subtotal = round($resultado[4],2);
			if($resultado[7]!='')
			$observa = "<div>".$resultado[7]."</div>";
			else 
			$observa = "";
			print( "<tr><td align='left' valign='top'>$fecha</td><td align='left' valign='top'>$resultado[0] $observa</td><td align='left' valign='top'>$resultado[1]</td><td align='left' valign='top'>$unitario &euro;</td><td align='left' valign='top'>$subtotal &euro;</td><td align='left' valign='top'>$resultado[5]</td><td align='left' valign='top'>$total &euro;</td></tr>");
			$servicios++;
			$toserv = $toserv+$resultado[1];
		}
		print("<tr><th>Totales</th><th align='left'>Servicios: $servicios</th><th align='left'>Cantidad: $toserv</th><th></th><th></th><th></th><th align='left'>$stotal &euro;</th></tr>");
		print("</table>");
}
else
{
header("Content-type:  application/msword");
header("Content-Disposition: attachment; filename=word.doc");
echo "Acceso denegado";
}
