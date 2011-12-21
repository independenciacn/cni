<!--Listado de los servicios contradados por el cliente en el mes seleccionado. Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007 -->
<!-- Reescribir para adaptar - Informe Gestion -->
<?php 
require_once '../inc/configuracion.php';
require_once 'funcionesFacturacion.php';
if ( !isset($_SESSION['usuario']) ) {
	notFound();
}
sanitize( $_GET );
?>
<html>
<head><title>Vista Impresion Listado</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type='text/css' href="../estilo/print.css" />
</head>
<body>
<?php
$cadena = "
<table cellpadding='0px' cellspacing='0px' width='100%' border='1' id='tabloide'>
<tr bgcolor='#bbbbbb'>
	<th colspan='7'>
		Listado de Servicios de ".$_GET['cliente']." 
		en ".$meses[$_GET['mes']]." de ".$_GET['anyo']."
	</th>
</tr>
<tr bgcolor='#bbbbbb'>
	<th>Fecha</th>
	<th>Servicio</th>
	<th>Cantidad</th>
	<th>Precio Unidad</th>
	<th>Importe</th>
	<th>Iva</th>
	<th>Total</th>
</tr>";
//PARTE DEL CONTRATO Y DEL ALMACENAJE SI PROCEDE cuidado con el mes
//la primera linea tiene que ser el importe del mes del tipo de cliente
	$sql = "Select * from tarifa_cliente 
	where ID_Cliente like " . $_GET['idCliente'] ." order by Servicio";
	$resultados = consultaGenerica($sql);
	$cantidad = 0;
	$j = 0;
	foreach ($resultados as $resultado) {
		//parciales
		$parcial = $parcial + $resultado[4];
		//subtotal
		$subtotal = $resultado[4]+($resultado[4]*$resultado[5])/100;	
//acumulados
		$total = $subtotal + $total;
		//$cantidad = $resultado[1] + $cantidad;
		$j++;
		$color = ( $j % 2 == 0 )? "bgcolor = '#dddddd'" : "bgcolor = '#ffffff'";
		$cadena .= "<tr ".$color.">
		<td ><p class='texto'>Mensualidad </p></td>
		<td ><p class='texto'>".ucwords($resultado[2]) ." ".($resultado[6])."</p></td>
		<td align='right'>".number_format("1",2,',','.')."</td>
		<td align='right'>".precioFormateado( $resultado[4] )."</td>
		<td align='right'>".precioFormateado( $resultado[4] )."</td>
		<td align='right'>".$resultado[5]."%</td>
		<td align='right'>".precioFormateado( $subtotal )."</td></tr>";
		$cantidad++;
	}
//Hasta aqui el importe del contrato base
//almacenaje
	//echo $sql;
	//$cadena .= almacenaje($vars[cliente],$mes_buscado);
	//IVA DE ALMACENAJE
	$sql = "Select iva, PrecioEuro from servicios2 where nombre like 'Almacenaje'";
	$valor = consultaUnica($sql);
	$sql = "Select bultos, datediff(fin,inicio) ,
	date_format(inicio,'%d-%m-%Y') as inicio, 
	date_format(fin,'%d-%m-%Y') as fin from z_almacen 
	where cliente like " . $_GET['idCliente'] . " 
	and month(fin) like " . $_GET['mes'] . " 
	and year(fin) like " . $_GET['anyo'];
	$resultados = consultaGenerica($sql);
    $celdas = 0;
    foreach ( $resultados as $resultado ) {
		$dias_almacen = $resultado[1];
		$subtotala = $resultado[0] * $dias_almacen * $valor['PrecioEuro'];
		$totala = $subtotala * (1 + $valor['iva'] / 100); // IVA ALMACENAJE!!!!!
		$j++;
		$color = ( $j % 2 == 0 )? "bgcolor = '#dddddd'" : "bgcolor = '#ffffff'";
		$cadena.="
		<tr ". $color. ">
			<td>&nbsp;</td>
			<td>
				<p class='texto'>Bultos Almacenados del  ".$resultado[2] . " 
				al ".$resultado[3]."</p>
			</td>
			<td align='right'>" . number_format($resultado[0],2,',','.')."</td>
			<td align='right'>" . precioFormateado( $valor['PrecioEuro'] ) . "</td>
			<td align='right'>" . precioFormateado( $subtotala ) . "</td>
			<td align='right'>" . $valor['iva']."%</td>
			<td align='right'>" . precioFormateado( $totala ) . "</td>
		</tr>";
		$cantidad = $resultado[0] + $cantidad;
		$bruto = $bruto + $subtotala;
		$total = $totala + $total;
		$celdas++;
		$parcial = $parcial + $subtotala;
	}
//Segunda pasada de almacenaje, los que no han salido  // Son los clientes
// que facturan a dia 20 Consultar los clientes que tienen ACCENTURE y CORITEL
//fin del almacenaje
	if ( ( $_GET['idCliente'] == '295') || (  $_GET['idCliente'] == '301') ) {
		$mes_ant = date('m') - 1;
		$fecha_inicial = date('Y')."-".$mes_ant."-20";
		$fecha_final = date('Y')."-".date('m')."-20";
		$sql = "Select d.Servicio, d.Cantidad, 
		date_format(c.fecha,'%d-%m-%Y') as fecha, 
		d.PrecioUnidadEuros, d.ImporteEuro, d.iva, c.`Id Pedido` ,
		d.observaciones from `detalles consumo de servicios` as d join `consumo de servicios` as c 
		on c.`Id Pedido` = d.`Id Pedido` 
		where c.Cliente like " . $_GET['idCliente']." and 
		(fecha > '".$fecha_inicial."' and fecha <= '".$fecha_final."') 
		 order by c.fecha , d.Servicio asc";
	//echo $sql."<br />";
	}
	else {
		$sql = "Select d.Servicio, d.Cantidad, 
		date_format(c.fecha,'%d-%m-%Y') as fecha, 
		d.PrecioUnidadEuros, d.ImporteEuro, d.iva, c.`Id Pedido` ,
		d.Observaciones from `detalles consumo de servicios` as d 
		join `consumo de servicios` as c on c.`Id Pedido` = d.`Id Pedido` 
		where c.Cliente like ".$_GET['idCliente']." and '".$_GET['anyo']."' 
		like year(c.fecha)
		and '".$_GET['mes']."' like month(c.fecha) order by c.fecha asc";
	}
	$resultados = consultaGenerica($sql);
	foreach( $resultados as $resultado ) {
		$parcial = $parcial + $resultado[4];
		$subtotal = $resultado[4]+($resultado[4]*$resultado[5])/100;	
//acumulados
		$total = $subtotal + $total;
		$cantidad = $resultado[1] + $cantidad;
//fin acumulados
		$j++;
		$color = ( $j % 2 == 0 ) ? "bgcolor = '#dddddd'" : "bgcolor = '#ffffff'";
		$cadena.= "
		<tr ".$color.">
		<td><p class='texto'>".$resultado[2]."</p></td>
		<td ".$color."><p class='texto'>".ucwords($resultado[0] . " " . $resultado[7] )."</p></td>
		<td ".$color." align='right'>".number_format($resultado[1],2,',','.')."</td>
		<td ".$color." align='right'>".precioFormateado( $resultado[3] )."</td>
		<td ".$color." align='right'>".precioFormateado( $resultado[4] )."</td>
		<td ".$color." align='right'>".$resultado[5]."%</td>
		<td ".$color." align='right'>".precioFormateado($subtotal)."</td></tr>";
	}
	$cadena.= "
	<tr>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
	<th>&nbsp;".number_format($cantidad,2,',','.')."</th>
	<th>&nbsp;</th>
	<th>&nbsp;".precioFormateado($parcial)."</th>
	<th>&nbsp;</th>
	<th>".precioFormateado($total)."</th>
	</tr>";
	$cadena.= "</table>";
echo $cadena;
?>
</body>
</html>