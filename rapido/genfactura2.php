<?php //fichero genfactura.php le llegan el mes y el cliente y genera un word.
/*Que tiene que salir en la factura.
1.- Los servicios fijos contratados, o sea el tipo que esta en tarifa_cliente
2.- Los servicios variables consumidos este mes
3.- Preguntarles que tal y que mas necesitan
*/
//datos.php muestra los datos del cliente el en mes actual 
include("../inc/variables.php");
include("telecos.php");
setlocale(LC_ALL, 'es_ES');
function traduce($texto)
{
//en algunos casos
//if(SISTEMA == "windows")
//	$bien = utf8_encode($texto); //para windows
//else
	$bien = $texto;//para sistemas *nix
return $bien;
}
function codifica($texto)
{
//en algunos casos
//if(SISTEMA == "windows")
	//$bien = utf8_decode($texto); //para windows
//else
	$bien = $texto;//para sistemas *nix
return $bien;
}
//si marcamos el cliente
//funciones axiliares********************************************************/
//calculo del total con iva
function iva($importe,$iva)
{
	$total = round($importe + ($importe * $iva)/100,2);
	return $total;
}
//Generacion del codigo de factura, posible almacenaje
function dame_el_mes($mes)
{
	switch($mes)
	{
		case 1: $marcado = "Enero";breaK;
		case 2: $marcado = "Febrero";breaK;
		case 3: $marcado = "Marzo";breaK;
		case 4: $marcado = "Abril";breaK;
		case 5: $marcado = "Mayo";breaK;
		case 6: $marcado = "Junio";breaK;
		case 7: $marcado = "Julio";breaK;
		case 8: $marcado = "Agosto";breaK;
		case 9: $marcado = "Septiembre";breaK;
		case 10: $marcado = "Octubre";breaK;
		case 11: $marcado = "Noviembre";breaK;
		case 12: $marcado = "Diciembre";breaK;
	}
	return $marcado;
}
function genera_codigo_factura($cliente,$mes)
{
	//a�adimos al final el mes y a�o
	include("../inc/variables.php");
	$sql = "Select valor from z_sercont where idemp like $cliente and servicio like 'negocio'";
	$consulta = mysql_query($sql,$con);
	if ($mes <= 9)
	$mes = "0".$mes;
	if(mysql_numrows($consulta) >= 1)
	{
		$resultado = mysql_fetch_array($consulta);
		$codigo = $resultado[0] ."".$mes."".date(Y);
	}
	else
	{
		$codigo = $cliente ."".$mes."".date(mY);
	}
	return $codigo;
}
//fin funciones axiliares*****************************************************
if(isset($_GET[cliente]))
{
	$ano_domini=date(Y);
	$cliente = $_GET[cliente];
	$mes = $_GET[mes];
//Seccion detalles, nombre del cliente, mes de la factura nombre del fichero
	$lista_meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
//nombre del cliente y del fichero
	$sql = "Select * from clientes where id like '$cliente'";
	$consulta = mysql_query($sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$nombre_fichero = "<span style='font-size:16.0pt'>Factura</span>";//.$resultado[1]." - ".$lista_meses[$mes]." - ". $ano_domini."</span>";
//cabezera de word ya bien
	header("Content-type:  application/msword");
	header("Content-Disposition: attachment; filename=Factura.doc");
//fin de los detallitos
//cabezera de la factura, hay que hablarla a ver si hace falta algo mas
//OPcion alternativa generacion codigo factura -- Posible insercion de datos para registrar las facturas y consultarlas despues
$codigo_factura = genera_codigo_factura($cliente,$mes);
//elcodigo de factura pues sera mmaa-cliente, preguntamos a ver que opinan
	$cabezera_factura = "
	<br/><br/><br/><br/><br/><br/>
	<table width='100%'  cellpadding='2px' cellspacing='2px' >
	<tr>
	<th colspan='2' align='right'>".strtoupper($nombre_fichero)."<br/><br/><br/><br/></th>
	</tr>
	<tr>
	<th valing='top' align='left' width='25%'>
		<table style='font-size:8.0pt'>
		<tr><th align='left'>FECHA: ".date("d-m-Y")."</th></tr>
		<tr><th align='left' >N&deg; FACTURA:".$codigo_factura."</th></tr>
		</table>
	</th>
	<th align='left' width='75%' style='border-style:solid;border-width:1px;border-color:#000000;'>
	<table style='font-size:10.0pt'>
	<tr>
	<td>".strtoupper($resultado[1])."</td>
	</tr><tr>
	<td>".$resultado[6]."</td>
	</tr><tr>
	<td>".$resultado[7]."&nbsp;&nbsp;-&nbsp;&nbsp;".$resultado[8]."</td>
	</tr><tr>
	<td>NIF:".$resultado[5]."</td>
	</tr></table>
	</th>
	</tr>";
//<tr><th align='left'>Contrato</th><td colspan='5' align='left'>".$resultado[3] ."</td></tr>";
//formas de pago y + datos de facturacion
	$sql = "SELECT * from facturacion where idemp like $cliente";
	if ( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
	{
		$resultado = mysql_fetch_array($consulta);
		$pie_factura = "<table width='100%' cellpadding='1px' cellspacing='1px' style='font-size:10.0pt'>
		<tr>
		<th align='left' >Forma de pago: ".$resultado[fpago]."</th>
		</tr><tr>
		<th align='left' >N&deg; Cuenta: ".$resultado[cc]."</th>
		</tr></table>";
	/*	<tr>
		<th align='left' >Supervisa Factura: ".$resultado[sfactura]."</th>
		<th align='left' >Direccion Factura: ".$resultado[direccion]."</th>
		</tr></table>";*/
	}
//fin de las formas de pago si proceden
	$cabezera_factura .= "</table>";//<hr>";
	echo $cabezera_factura;
//Servicios Contratados
//echo listado_telecos($cliente);
//echo "<hr>";
	echo "<table cellpadding='0px' cellspacing='0px' width='100%'  style='font-size:10.0pt;border-style:solid;border-width:1px;border-color:#000000;'>
	<tr><th colspan='7'>CONSUMO DE SERVICIOS</th></tr>
	<tr>
	<th style='border-bottom-style:solid; border-width:1px;border-color:#000000;' align='center'>Fecha</th>
	<th style='border-bottom-style:solid; border-width:1px;border-color:#000000;' align='center'>Servicio</th>
	<th style='border-bottom-style:solid; border-width:1px;border-color:#000000;' align='center'>Cant.</th>
	<th style='border-bottom-style:solid; border-width:1px;border-color:#000000;' align='center'>P/Unitario</th>
	<th style='border-bottom-style:solid; border-width:1px;border-color:#000000;' align='center'>IMPORTE</th>
	<th style='border-bottom-style:solid; border-width:1px;border-color:#000000;' align='center'>IVA</th>
	<th style='border-bottom-style:solid; border-width:1px;border-color:#000000;' align='center'>TOTAL</th></tr>";
//PARTE DEL CONTRATO Y DEL ALMACENAJE SI PROCEDE cuidado con el mes
//la primera linea tiene que ser el importe del mes del tipo de cliente
	$sql = "Select * from tarifa_cliente where ID_Cliente like $cliente";
	$consulta = mysql_query($sql,$con);
	while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
	{
		echo "<tr>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>Mensual</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' >".codifica($resultado[2])." ".codifica($resultado[6])."</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>1</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>".$resultado[4]."&euro;</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>".$resultado[4]."&euro;</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>".$resultado[5]."%</td>
		<td align='center'>".iva($resultado[4],$resultado[5])."&euro;</td></tr>";
		$total = $total + iva($resultado[4],$resultado[5]);
		$bruto = $bruto + $resultado[4];
	}
//Hasta aqui el importe del contrato base
//almacenaje
	$sql = "Select bultos, datediff(fin,inicio) from z_almacen where cliente like $cliente and month(fin) like $mes";
	$consulta = mysql_query($sql,$con);
	while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
	{
		$almacen_con_iva = round($resultado[1]*1.16,2);
		$total = $almacen_con_iva + $total;
		echo "<tr>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>".$lista_meses[$mes]."</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;'>Almacenaje</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>".$resultado[0]."</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>0,68&euro;</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>".$resultado[1]."&euro;</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;' align='center'>16%</td>
		<td align='center'>".$almacen_con_iva."&euro;</td></tr>";
		$cantidad = $resultado[0] + $cantidad;
		$bruto = $bruto + $resultado[1];
	}
//fin del almacenaje
//FIN DE ESTA PARTE
//Servicio contratado
	$sql = "Select d.Servicio, sum(d.Cantidad), date_format(c.fecha,'%d-%m-%Y') as fecha, sum(d.PrecioUnidadEuros), sum(d.ImporteEuro), d.iva, c.`Id Pedido` ,d.observaciones from `detalles consumo de servicios` as d join `consumo de servicios` as c on c.`Id Pedido` = d.`Id Pedido` where c.Cliente like $cliente and (date_format(curdate(),'%Y') like date_format(c.fecha,'%Y') and '$mes' like date_format(c.fecha,'%c')) group by d.servicio";
	$consulta = mysql_query($sql,$con);
	while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
	{
		//$subtotal = round(round($resultado[4],2)+(round($resultado[4],2)*$resultado[5])/100,2);
		//$subtotal = round($resultado[4] + ($resultado[4]*$resultado[5])/100,2);
		$subtotal = $resultado[4] + ($resultado[4]*$resultado[5])/100;
//acumulados
		$total = $subtotal + $total;
		$cantidad = $resultado[1] + $cantidad;
//fin acumulados
		echo "<tr>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;'align='center'>".dame_el_mes($mes)."/".date(Y)."</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;'>".$resultado[0]." ".$resultado[7]."</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;'align='center'>".round($resultado[1],2)."</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;'align='center'>".round($resultado[3],2)."&euro;</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;'align='center'>".round($resultado[4],2)."&euro;</td>
		<td style='border-right-style:solid; border-width:1px;border-color:#000000;'align='center'>".$resultado[5]."%</td>
		<td align='center'>".round($subtotal,2)."&euro;</td></tr>";
		//$bruto = $bruto + round($resultado[4],2);
		$bruto = $bruto + $resultado[4];
	}
	echo "<tr>
	<th style='border-top-style:solid; border-width:1px;border-color:#000000;'align='center'>&nbsp;</th>
	<th style='border-top-style:solid; border-width:1px;border-color:#000000;'align='center'>&nbsp;</th>
	<th style='border-style:solid;border-width:1px;border-color:#000000;'>&nbsp;".$cantidad."</th>
	<th style='border-top-style:solid; border-width:1px;border-color:#000000;'align='center'>&nbsp;</th>
	<th style='border-style:solid;border-width:1px;border-color:#000000;'>".$bruto."&euro;</th>
	<th style='border-top-style:solid; border-width:1px;border-color:#000000;'align='center'>&nbsp;</th>
	<th style='border-style:solid;border-width:1px;border-color:#000000;'>".round($total,2)."&euro;</th>";
	echo "</table><br/><br/><br/>";
//RESUMEN
	$total_iva = $total - $bruto;
	echo "<table width='100%' cellpadding='0px' cellspacing='0px' style='font-size:10.0pt'><tr>
	<th width='15%'>&nbsp;</th>
	<th style='border-style:solid;border-width:1px;border-color:#000000;'>TOTAL BRUTO</th>
	<th width='15%'>&nbsp;</th>
	<th style='border-style:solid;border-width:1px;border-color:#000000;'>IVA</th>
	<th width='15%'>&nbsp;</th>
	<th style='border-style:solid;border-width:1px;border-color:#000000;'>TOTAL</th></tr>
	<tr>
	<th width='15%'>&nbsp;</th>
	<th style='border-style:solid;border-width:1px;border-color:#000000;'>".round($bruto,2)."&euro;</th>
	<th width='15%'>&nbsp;</th>
	<th style='border-style:solid;border-width:1px;border-color:#000000;'>".round($total_iva,2)."&euro;</th>
	<th width='15%'>&nbsp;</th>
	<th style='border-style:solid;border-width:1px;border-color:#000000;'>".round($total,2)."&euro;</th></tr></table><br/>";
	echo $pie_factura;
}

?>

