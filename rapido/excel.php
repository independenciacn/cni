<!--Listado de los servicios contradados por el cliente en el mes seleccionado. Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007 -->
<?php 
require_once '../inc/variables.php';
include_once 'datos.php';
?>
<html>
<head><title>Vista Impresion Listado</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
if (isset($_GET['mes']) && isset($_GET['cliente'])) {
    $cantidad = 0;
    $j = 0;
    $celdas = 0;
    $vars = array("mes"=>$_GET['mes'],"cliente"=>$_GET['cliente']);
    $mes_servicios = dame_el_mes($vars['mes']);
    $mes2 = $_GET['mes']-1;
    $mes_fijos = dame_el_mes($mes2);
    $cliente_servicios = explode(";",dame_nombre_cliente($vars));
//echo ver_servicios_contratados($vars);<= Version Rapida OK
//Version retocada estilo
    $mes_buscado = ( $_GET['mes'] <= 9 ) ? "0".$vars['mes'] : $vars['mes'];
	$cadena =  "<style>
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
	$cadena .= "<table cellpadding='0px' cellspacing='0px' width='100%' 
	border='1' id='tabloide'>";
	$cadena .= "<tr bgcolor='#bbbbbb'><th colspan='7'>
	    Listado de Servicios de ".$cliente_servicios[1]." 
	    en ".$mes_servicios." de ".$_GET['anyo']."</th></tr>";
	$cadena .= "<tr bgcolor='#bbbbbb'>
	    <th>Fecha</th>
	    <th>Servicio</th>
	    <th>Cantidad</th>
	    <th>Precio Unidad</th>
	    <th>Importe</th>
	    <th>Iva</th>
	    <th>Total</th></tr>";
//PARTE DEL CONTRATO Y DEL ALMACENAJE SI PROCEDE cuidado con el mes
//la primera linea tiene que ser el importe del mes del tipo de cliente
	$sql = "Select * from tarifa_cliente 
	where ID_Cliente like ".$vars['cliente']." order by Servicio";
	$consulta = mysql_query($sql,$con);
	while ( true == ($resultado = mysql_fetch_array($consulta))) {
		//parciales
		$parcial = $parcial + $resultado[4];
		//subtotal
		$subtotal = $resultado[4]+($resultado[4]*$resultado[5])/100;	
//acumulados
		$total = $subtotal + $total;
		//$cantidad = $resultado[1] + $cantidad;
		$j++;
		$color = "bgcolor = ";
		$color .= ( $j%2 == 0) ? "'#dddddd'": "'#ffffff'";
		$cadena .= "<tr>
		<td ".$color."><p class='texto'>Mensualidad </p></td>
		<td ".$color."><p class='texto'>".ucfirst($resultado[2])." 
		".ucfirst($resultado[6])."</p></td>
		<td ".$color." align='right'>".number_format("1",2,',','.')."&nbsp;</td>
		<td ".$color." align='right'>".
		    number_format($resultado[4],2,',','.')."&euro;&nbsp;</td>
		<td ".$color." align='right'>".
		    number_format($resultado[4],2,',','.')."&euro;&nbsp;</td>
		<td ".$color." align='right'>".$resultado[5]."%&nbsp;</td>
		<td ".$color." align='right'>".
		    number_format($subtotal,2,',','.')."&euro;</td></tr>";
		$cantidad++;
	}
//Hasta aqui el importe del contrato base
//almacenaje
	//echo $sql;
	//$cadena .= almacenaje($vars[cliente],$mes_buscado);
	$sql = "Select bultos, datediff(fin,inicio) ,inicio, fin 
	    from z_almacen where cliente like ".$vars['cliente']." 
	    and month(fin) like ".$vars['mes']." and year(fin) like ".$_GET['anyo'];
	$consulta = mysql_query($sql,$con);
    while ( true == ($resultado = mysql_fetch_array($consulta))) {
		$dias_almacen = $resultado[1];
		$subtotala = $resultado[0]*$dias_almacen*0.70;
		$totala = $subtotala* 1.16;
		$cadena.="<tr>
		<td>&nbsp;</td>
		<td ><p class='texto'>Bultos Almacenados del  
		".cambiaf($resultado[2])." al ".cambiaf($resultado[3])."</p></td>
		<td align='right'>".number_format($resultado[0],2,',','.')."&nbsp;</td>
		<td align='right'>0,70&euro;&nbsp;</td>
		<td align='right'>".number_format($subtotala,2,',','.')."&euro;&nbsp;</td>
		<td align='right'>16%&nbsp;</td>
		<td align='right'>".number_format($totala,2,',','.')."&euro;&nbsp;</td></tr>";
		$cantidad = $resultado[0] + $cantidad;
		$bruto = $bruto + $subtotala;
		$total = $totala + $total;
		$celdas++;
		$parcial = $parcial + $subtotala;
	}
//Segunda pasada de almacenaje, los que no han salido 
//fin del almacenaje
    if (($vars['cliente'] == '295') || ($vars['cliente'] == '301')) {
    	$mes_ant = date('m') - 1;
    	$fecha_inicial = date('Y')."-".$mes_ant."-20";
    	$fecha_final = date('Y')."-".date('m')."-20";
    	$sql = "Select d.Servicio, d.Cantidad, 
    	date_format(c.fecha,'%d-%m-%Y') as fecha, 
    	d.PrecioUnidadEuros, d.ImporteEuro, d.iva, c.`Id Pedido` ,
    	d.observaciones from `detalles consumo de servicios` 
    	as d join `consumo de servicios` as c 
    	on c.`Id Pedido` = d.`Id Pedido` where c.Cliente 
    	like ".$vars['cliente']." and 
    	(fecha > '".$fecha_inicial."' and fecha <= '".$fecha_final."') 
    	 order by c.fecha , d.Servicio asc";
        echo "<br />";
    } else {
    	$sql = "Select d.Servicio, d.Cantidad, date_format(c.fecha,'%d-%m-%Y') 
    	as fecha, 
    	d.PrecioUnidadEuros, d.ImporteEuro, d.iva, c.`Id Pedido` ,
    	d.Observaciones from `detalles consumo de servicios` as d 
    	join `consumo de servicios` as c on c.`Id Pedido` = d.`Id Pedido` 
    	where c.Cliente like ".$vars['cliente']." and '".$_GET['anyo']."' 
    	like year(c.fecha)
    	and '".$_GET['mes']."' like month(c.fecha) order by c.fecha asc";
    }
    $consulta = mysql_query($sql,$con);
	while (true == ( $resultado=mysql_fetch_array($consulta)) ){
		$color = "bgcolor = ";
	    $parcial = $parcial + $resultado[4];
		$subtotal = $resultado[4]+($resultado[4]*$resultado[5])/100;	
//acumulados
		$total = $subtotal + $total;
		$cantidad = $resultado[1] + $cantidad;
//fin acumulados
		$j++;
		$color .= ( $j%2 == 0 ) ? "'#dddddd'" : "'#ffffff'";
		//number_format($resultado[4],2,',','.')
		$cadena.= "<tr><td ".$color.">
		<p class='texto'>".$resultado[2]."</p></td>
		<td ".$color."><p class='texto'>".ucfirst($resultado[0])." 
		".ucfirst($resultado[7])."</p></td>
		<td ".$color." align='right'>".number_format($resultado[1],2,',','.').
		"&nbsp;</td>
		<td ".$color." align='right'>".number_format($resultado[3],2,',','.').
		"&euro;&nbsp;</td>
		<td ".$color." align='right'>".number_format($resultado[4],2,',','.').
		"&euro;&nbsp;</td>
		<td ".$color." align='right'>".$resultado[5]."%&nbsp;</td>
		<td ".$color." align='right'>".number_format($subtotal,2,',','.').
		"&euro;&nbsp;</td></tr>";
	}
	$cadena.= "<tr><th>&nbsp;</th>
	<th>&nbsp;</th>
	<th>&nbsp;".number_format($cantidad,2,',','.')."</th>
	<th>&nbsp;</th>
	<th>&nbsp;".number_format($parcial,2,',','.')."&euro;</th>
	<th>&nbsp;</th><th>".number_format($total,2,',','.')."&euro;</th></tr>";
	$cadena.= "</table>";
    echo $cadena;
} else {
    echo "Datos Incorrectos";
}
?>
</body>
</html>