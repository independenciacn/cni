<?php
/**
 * NO USADO - BORRAR
 */
require_once "../inc/variables.php";
checkSession();
function dame_nombre_cliente($cliente)
{
    global $con;
    $sql = "Select * from `clientes` where id like $cliente";
    $consulta = mysql_query($sql,$con);
    $resultado = mysql_fetch_array($consulta);
    $cadena =$resultado[1];
    return $cadena;
}
function cambiaf($stamp) //funcion del cambio de fecha
{
    //formato en el que llega aaaa-mm-dd o al reves
    $fdia = explode("-",$stamp);
    $fdia2 = explode(" ",$fdia[2]);
    $fecha = $fdia2[0]."-".$fdia[1]."-".$fdia[0];
    return $fecha;
}
if(session_id() == $_GET[id]) {
    $sql = $_SESSION['metagenerator'];
    $empresa = dame_nombre_cliente($_SESSION['metaempresa']);
    $mostrada = $_SESSION['metafecha'];
    $sersel = $_SESSION['metaservicio'];
    $servicios = 0;
//header("Content-type: application/vnd.ms-excel");
//header("Content-Disposition: attachment; filename=excel.xls");

// Creamos la tabla
		
		$consulta = mysql_query($sql,$con);
		//dise�o de la tabla con el boton de eliminar
		echo "<style>
		#tabloide td
		{
			border-style:solid;
			border-width:1px;
			border-color:#333;
			font-family: Tahoma, Verdana, Arial, Georgia;
			font-style: normal;
			font-size:12px;
		}
		#tabloide th
		{
			border-style:solid;
			border-width:1px;
			border-color:#333;
			background:#aaa;
			color:#fff;
			font-family: Tahoma, Verdana, Arial, Georgia;
			font-style: normal;
			font-weight:bold;
			font-size:12px;
		}
		.texto
		{
			text-indent:16px;
		}
		</style>";
		print("<table id='tabloide' width=100% cellpadding=0 cellspacing=0>");
		print("<tr><th colspan=7>Servicios contratados por $empresa - Periodo $mostrada - Servicio: $sersel</th></tr>");
		print("<tr>
		<th align='center'>Fecha</th>
		<th align='center'>Servicio</th>
		<th align='center'>Cantidad</th>
		<th align='center'>Precio unidad</th>
		<th align='center'>Subtotal</th>
		<th align='center'>Iva</th>
		<th align='center'>Total</th></tr>");
		while(true == ($resultado = mysql_fetch_array($consulta))) {
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
			echo "<tr><td align='center' width='10%' valign='top'>".$fecha."</td>
			<td align='left' width='40%' valign='top' class='texto'>".$resultado[0]." ".$observa."</td>
			<td align='right' width='10%' valign='top'>".number_format($resultado[1],2,',','.')."</td>
			<td align='right' width='10%' valign='top'>".number_format($unitario,2,',','.')." &euro;</td>
			<td align='right' width='10%' valign='top'>".number_format($subtotal,2,',','.')." &euro;</td>
			<td align='right' width='10%'valign='top'>".number_format($resultado[5],2,',','.')."</td>
			<td align='right' width='10%'valign='top'>".number_format($total,2,',','.')." &euro;</td></tr>";
			$servicios++;
			$toserv = $toserv+$resultado[1];
		}
		print("<tr>
		<th>Totales</th>
		<th align='left' class='texto'>Servicios: $servicios</th>
		<th align='right'>".number_format($toserv,2,',','.')."</th>
		<td></td><td></td><td></td>
		<th align='right'>".number_format($stotal,2,',','.')." &euro;</th></tr>");
		print("</table>");
}
else
{
//     header("Content-type: application/vnd.ms-excel");
//     header("Content-Disposition: attachment; filename=excel.xls");
    echo "Acceso denegado";
}
