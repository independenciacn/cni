<?php
//REESCRIBIR - GENERA LA FACTURA, LA ESCRIBE Y LA MUESTRA POR PANTALLA, SI ES PROFORMA NO GUARDA
//Fichero genfactura.php (Genera la factura dependiendo de lo que se pida). Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007 
//error_reporting(E_ALL);//fichero genfactura.php le llegan el mes y el cliente y genera un word.
require_once '../inc/configuracion.php';
require_once 'funcionesFacturacion.php';
if ( !isset($_SESSION['usuario']) ) {
	notFound();
}
sanitize( $_GET );
//Puede llegar de 4 sitios
// Desde la seccion del formulario de facturas proforma true o false
// parametros proforma y los campos de los dos formularios
// AGREGA A HISTORICO cuando proforma es false
// Desde la seccion del listado de facturas duplicado true o false
// pasa el parametro duplicado y el id de factura en regfacturas 
// NO AGREGA A HISTORICO NINGUNA DE LAS 2
/***********************************************************************************************/
//fin funciones axiliares*****************************************************
//FUNCION PRINCIPAL -- OBLIGATORIO EL CLIENTE
//Parametros del get cliente,mes,fecha_factura,codigo
//En puntual: fecha_inicial_factura, fecha_final_factura para filtrado
//Proforma: prueba = 1
/*var_dump($_GET);
if( isset( $_GET['proforma'] ) ) {
	//$ano_domini=date(Y);
	$ano_factura = explode( "-", $_GET['fecha_factura'] );
	$cliente = $_GET['idCliente'];
	$mes = $_GET['mes'];
	$ano = $ano_factura[0];
	$codigo = $_GET['codigo'];
	$historico = historico($codigo); // llamamos a la funcion historico
	$fecha_factura = $_GET['fecha_factura'];
	if ( $_GET['proforma'] == "true" ) {
		$fecha_inicial_factura = $_GET['fecha_inicial_factura'];
		$fecha_final_factura = $_GET['fecha_final_factura'];
		$fichero = "PROFORMA";
		$titulo = "FACTURA<BR/>PROFORMA";//Guardamos datos en profroma
	} else {
		$fecha_inicial_factura = "";
		$fecha_final_factura = "";
		$fichero = "FACTURA";
		$titulo = $fichero; // Guardamos datos en factura
	}
	$observaciones = $_GET['observaciones'];
}
//CASOS DE Imprimir factura generada o ver el duplicado
if( isset( $_GET['factura'] ) || isset( $_GET['duplicado'] ) ) {
	if(isset($_GET['factura'])) {
		$datos = "Select * from regfacturas where id like " . $_GET['factura'];		
	} else {
		$datos = "Select * from regfacturas where id like " . $_GET['duplicado'];
	}
	$consulta = mysql_db_query( $dbname, $datos, $con);
	$resultado = mysql_fetch_array( $consulta ); // resultado de la consulta
	$cliente = $resultado['id_cliente'];
	$fecha_factura = cambiaf( $resultado['fecha'] );
	$ano_factura = explode( "-", $fecha_factura );
	$mes = intval( $resultado['mes'] );
	$codigo = $resultado['codigo'];
	$historico = historico($codigo); // devuelve ok o ko
	$fecha_inicial_factura = $resultado['fecha_inicial'];
	$fecha_final_factura = $resultado['fecha_final'];
	$observaciones = $resultado['obs_alt'];
	$pedidoCliente = $resultado['pedidoCliente'];
	
	// Si establecemos que la factura es duplicado
	if( isset( $_GET['duplicado'] ) ) {
		$fichero = "FACTURA (DUPLICADO)";
		$titulo = "FACTURA<BR/>DUPLICADO";//Guardamos datos en profroma
	} else {
		$fichero = "FACTURA";
		$titulo = $fichero;
		//Guardamos datos en factura
	}
}

$nombre_fichero = "<span style='font-size:16.0pt'>" . $titulo . "</span>";

//CABEZERA***************************************************************************/	
/*$cabezera_factura = cabezeraFactura($fichero,$fecha_factura,$codigo,$cliente);
//PRESENTACION************************************************************************/
//CASOS POSIBLES, MENSUAL y PUNTUAL en puntual hay que pasar los limites
//fecha_inicial_factura y fecha_final_factura
/*if(($fecha_inicial_factura != '0000-00-00') && ($fecha_final_factura != '0000-00-00'))
{
	$inicio = $fecha_inicial_factura;
	$final = $fecha_final_factura;
	
}
else
{
	$inicio = "0000-00-00";
	$final = "0000-00-00";
}


$tituloPagina = ( $inicio!= "0000-00-00") ? "ocupacion puntual" : dame_el_mes( "m" );
?>
<html>
<head>
<title><?php echo $fichero . " " . $tituloPagina ?></title>
<link rel="stylesheet" type='text/css' href="estilo.css" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
	echo $cabezera_factura;
	echo "
	<table cellpadding='2px' cellspacing='0px' width='100%' id='tabloide'>
	<tr>
	<th align='center' width='48%' >Servicio</th>
	<th align='center' width='8%' >Cant.</th>
	<th align='center' width='12%' >P/Unitario</th>
	<th align='center' width='12%' >IMPORTE</th>
	<th align='center' width='8%' >IVA</th>
	<th align='center' width='12%' >TOTAL</th>
	</tr>";
//PARTE DEL CONTRATO Y DEL ALMACENAJE SI PROCEDE cuidado con el mes
//la primera linea tiene que ser el importe del mes del tipo de cliente
//VALIDO DESDE MAYO DEL 07
//DATOS SERVICIOS FIJOS**********************************************************/
//solo se cargan los fijos si no son ocupacion puntual
/*CHEQUEO DE HISTORICO, si no esta en el historico se agrega*/
/*if($historico == "ok") {
	$sql = "Select * from historico where factura like $codigo";
	$consulta = mysql_query( $sql, $con );
	while($resultado=mysql_fetch_array($consulta))
	{
		$importe_sin_iva = $resultado['cantidad']*$resultado['unitario'];
		echo "<tr>
		<td><p class='texto'>".ucfirst($resultado[2])." ".ucfirst($resultado[6])."</td>
		<td align='right'>".number_format($resultado['cantidad'],2,',','.')."&nbsp;</td>
		<td align='right'>".number_format($resultado['unitario'],2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".number_format($importe_sin_iva,2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".$resultado[iva]."%&nbsp;</td>
		<td align='right'>".number_format(iva($importe_sin_iva,$resultado['iva']),2,',','.')."&euro;&nbsp;</td></tr>";
		$total = $total + iva($importe_sin_iva,$resultado[5]);
		$bruto = $bruto + $importe_sin_iva;
		$celdas++;
		$cantidad++;
	}
	//echo  consulta_almacenaje($cliente,$mes,$inicio,$final);
		
} else {
	/*echo $ano_factura[2];
	echo $inicio;
	echo $final;*/
	/*if(((($mes >= 3) && ($ano_factura[2] == 2007))||(($ano_factura[2]>= 2008)) && ($inicio == "0000-00-00")) && ($final == "0000-00-00"))
	{
		$sql = "Select * from tarifa_cliente where ID_Cliente like $cliente order by Imp_Euro desc";
		//echo $sql;/*PUNTO DE CONTROL*/
		/*$consulta = mysql_query( $sql, $con );
		while ($resultado = mysql_fetch_array($consulta))
		{
			$importe_sin_iva = $resultado[7]*$resultado[4];
			echo "<tr>
			<td><p class='texto'>".ucfirst($resultado[2])." ".$resultado[6]."</p></td>
			<td align='right'>".number_format($resultado[7],2,',','.')."&nbsp;</td>
			<td align='right'>".number_format($resultado[4],2,',','.')."&euro;&nbsp;</td>
			<td align='right'>".number_format($importe_sin_iva,2,',','.')."&euro;&nbsp;</td>
			<td align='right'>".$resultado[5]."%&nbsp;</td>
			<td align='right'>".number_format(iva($importe_sin_iva,$resultado[5]),2,',','.')."&euro;&nbsp;</td></tr>";
			$total = $total + iva($importe_sin_iva,$resultado[5]);
			$bruto = $bruto + $importe_sin_iva;
			$celdas++;
			$cantidad++;
			/*ALERTA LINEA A MODIFICAR EN EL CAMBIO*/
			/*$servicio_desc = ucfirst($resultado[2]);//." ".ucfirst($resultado[6]);
		
			if(($historico == "ko")&& (!isset($_GET['prueba']))) {
			//Agregamos al historico
				agregaHistorico($codigo,$servicio_desc,$resultado[7],$resultado[4],$resultado[5],ucfirst($resultado[6]));
			}
		}
	}
/************************************************************************************/
//Devuelve la consulta para generar el almacenaje
/*Parte de consulta de importe e iva de almacenaje*/
    /*Buscamos los datos de importe e iva de almacenaje*/
  /*  $sql = "Select datediff('".cambiaf($fecha_factura)."','2010-07-01')";
    //echo $sql;
    $consulta = mysql_query( $sql, $con );
    $diff = mysql_fetch_array($consulta);
    if($diff[0]>=0)
    {
        $sql = "select PrecioEuro, iva from servicios2 where nombre like '%Almacenaje%'";
        $consulta = mysql_query( $sql, $con );
        $par_almacenaje = mysql_fetch_array($consulta);
    }
    else
        $par_almacenaje = array('PrecioEuro'=>'0.70','iva'=>'16');
    /*Final datos de valores del almacenaje*/
	/*$sql = consultaAlmacenaje($cliente,$mes,$inicio,$final);
	//echo $sql;/*PUNTO DE CONTROL*/
	
/*	$consulta = @mysql_query( $sql, $con );
	while ($resultado = @mysql_fetch_array($consulta))
	{
		$dias_almacen = $resultado[1];
		$subtotala = $resultado[0]*$dias_almacen*$par_almacenaje['PrecioEuro'];
		
        $totala = iva($subtotala,$par_almacenaje['iva']);
		echo "<tr>
		<td ><p class='texto'>Bultos Almacenados del  ".cambiaf($resultado[2])." al ".cambiaf($resultado[3])."</p></td>
		<td align='right'>".number_format($resultado[0],2,',','.')."&nbsp;</td>
		<td align='right'>0,70&euro;&nbsp;</td>
		<td align='right'>".number_format($subtotala,2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".$par_almacenaje['iva']."%&nbsp;</td>
		<td align='right'>".number_format($totala,2,',','.')."&euro;&nbsp;</td></tr>";
		$cantidad = $resultado[0] + $cantidad;
		$bruto = $bruto + $subtotala;
		$total = $totala + $total;
		$celdas++;
		$cadena_texto = " del  ".cambiaf($resultado[2])." al ".cambiaf($resultado[3]);
	if(($historico == "ko")&& (!isset($_GET[prueba]))) //Agregamos al historico
		agregaHistorico($codigo,"Bultos Almacenados",1,$subtotala,$par_almacenaje['iva'],$cadena_texto);
		
	}
//fin del almacenaje**********************************************************************/
//FIN DE ESTA PARTE
//Servicio contratado
//#####################Servicios No agrupados#############################################
//control de puntuales
/*	$sql = "Select d.Servicio, d.Cantidad, date_format(c.fecha,'%d-%m-%Y') as fecha, 
	d.PrecioUnidadEuros, d.ImporteEuro, d.iva, c.`Id Pedido` ,
	d.observaciones from `detalles consumo de servicios` as d join `consumo de servicios` as c 
	on c.`Id Pedido` = d.`Id Pedido` where c.Cliente like $cliente ";
//consulta de fecha
	$sql .= consultaFecha($cliente,$mes,$inicio,$final); //con esta miramos los rangos de la factura
	$sql .= consultaNoAgrupado($cliente);
	//echo $sql;/*PUNTO DE CONTROL*/
/*	$consulta = mysql_query( $sql, $con );
	while ($resultado=mysql_fetch_array($consulta))
	{
		$subtotal = $resultado[4] + ($resultado[4]*$resultado[5])/100;
//acumulados
		$total = $subtotal + $total;
		$cantidad = $resultado[1] + $cantidad;
//fin acumulados
		echo "<tr>
		<td ><p class='texto'>".ucfirst($resultado[0])." ". ucfirst($resultado[7])."</p></td>
		<td align='right'>".number_format($resultado[1],2,',','.')."&nbsp;</td>
		<td align='right'>".number_format($resultado[3],2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".number_format($resultado[4],2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".$resultado[5]."%&nbsp;</td>
		<td align='right'>".number_format($subtotal,2,',','.')."&euro;&nbsp;</td></tr>";
		$bruto = $bruto + $resultado[4];
		$celdas++;
		//$servicio_desc = ucfirst($resultado[0])." ".ucfirst($resultado[7]);
		
		if(($historico == "ko")&& (!isset($_GET[prueba]))) //Agregamos al historico
			agregaHistorico($codigo,$resultado[0],$resultado[1],$resultado[3],$resultado[5],$resultado[7]);
		
	}
//#####################################Parte agrupada###############################################
	$sql = "Select d.Servicio, sum(d.Cantidad), date_format(c.fecha,'%d-%m-%Y') as fecha, 
	d.PrecioUnidadEuros, sum(d.ImporteEuro), d.iva, c.`Id Pedido` ,
	d.observaciones from `detalles consumo de servicios` as d join `consumo de servicios` as c 
	on c.`Id Pedido` = d.`Id Pedido` where c.Cliente like $cliente";
	$sql .= consultaFecha($cliente,$mes,$inicio,$final);
	$sql .= consultaAgrupado($cliente);
	//echo $sql;//<- Punto de Control
	//echo $cliente.",".$mes.",".$inicio.",".$final;
	$consulta = mysql_query( $sql, $con );
	while ($resultado=mysql_fetch_array($consulta))
	{
		$subtotal = $resultado[4]+ ($resultado[4]*$resultado[5])/100;
//acumulados
		$total = $subtotal + $total;
		$cantidad = $resultado[1] + $cantidad;
//fin acumulados
		echo "<tr>
		<td ><p class='texto'>".ucfirst($resultado[0])." ".ucfirst($resultado[7])."</p></td>
		<td align='right'>".number_format($resultado[1],2,',','.')."&nbsp;</td>
		<td align='right'>".number_format($resultado[3],2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".number_format($resultado[4],2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".$resultado[5]."%&nbsp;</td>
		<td align='right'>".number_format($subtotal,2,',','.')."&euro;&nbsp;</td></tr>";
		$bruto = $bruto + $resultado[4];
		$celdas++;
		//$servicio_desc = ucfirst($resultado[0])." ".ucfirst($resultado[7]);
		if(($historico == "ko")&& (!isset($_GET[prueba]))) //Agregamos al historico
			agregaHistorico($codigo,ucfirst($resultado[0]),$resultado[1],$resultado[3],$resultado[5],ucfirst($resultado[7]));
		
	}
//descuento si procede
		$esql = "Select razon from clientes where id like $cliente";
		$consulta = mysql_db_query($dbname,$esql,$con);
		$resultado = mysql_fetch_array($consulta);
		if(($resultado[0] != "") && ($resultado[0] != ""))
		{
			$porcentaje = explode("%",$resultado[0]);
			$descuento = ($bruto * $porcentaje[0])/100;
			$descuento_con_iva = $descuento * 1.16;
			echo "<tr>
			<td ><p class='texto'>Descuento del ".$porcentaje[0]."%</p></td>
			<td align='right'>1&nbsp;</td>
			<td align='right'>-".number_format($descuento,2,',','.')."&euro;&nbsp;</td>
			<td align='right'>-".number_format($descuento,2,',','.')."&euro;&nbsp;</td>
			<td align='right'>18%&nbsp;</td>
			<td align='right'>-".number_format($descuento_con_iva,2,',','.')."&euro;&nbsp;</td></tr>";
		$descuento_historico = "-".$descuento;
		if(($historico == "ko")&& (!isset($_GET[prueba]))) //Agregamos al historico
			agregaHistorico($codigo,"Descuento","1",$descuento_historico,"16", "del ".$porcentaje[0]);
		
		}
		else
		{
			$descuento = 0;
			$descuento_con_iva = 0;
		}
		$bruto = $bruto - $descuento;
		$total = $total - $descuento_con_iva;
} //Cierre de las que no estan en historico

//Compensacion de dise�o
	$coeficiente = 432 - ($celdas-1) * 18;
	if($coeficiente >= 1)
	{
		echo "<tr><td height='".$coeficiente."px'>&nbsp;</td>
		<td align='center'>&nbsp;</th>
		<td align='center'>&nbsp;</th>
		<td align='center'>&nbsp;</th>
		<td align='center'>&nbsp;</th>
		<td align='center'>&nbsp;</th>
		</tr>";
	}
	echo "<tr>
	<th align='center'>&nbsp;</th>
	<th align='right'>&nbsp;".$cantidad."&nbsp;</th>
	<th align='center'>&nbsp;</th>
	<th align='right'>".number_format($bruto,2,',','.')."&euro;&nbsp;</th>
	<th align='center'>&nbsp;</th>
	<th align='right'>".number_format($total,2,',','.')."&euro;&nbsp;</th>";
	echo "</table>";
//RESUMEN
	$total_iva = $total - $bruto;
	echo "<br/><table width='100%' cellpadding='2px' cellspacing='2px' style='font-size:10.0pt'><tr>
	<th width='15%'>&nbsp;</th>
	<th  class='celdilla_tot' >TOTAL BRUTO</th>
	<th width='15%'>&nbsp;</th>
	<th  class='celdilla_tot' >IVA</th>
	<th width='15%'>&nbsp;</th>
	<th  class='celdilla_tot' >TOTAL</th></tr>
	<tr>
	<th width='15%'>&nbsp;</th>
	<th  class='celdilla_tot' >".number_format($bruto,2,',','.')."&euro;</th>
	<th width='15%'>&nbsp;</th>
	<th  class='celdilla_tot' >".number_format($total_iva,2,',','.')."&euro;</th>
	<th width='15%'>&nbsp;</th>
	<th  class='celdilla_tot' >".number_format($total,2,',','.')."&euro;</th></tr></table>";
	//$pie_factura .= "<br />".$bruto."-".iva($bruto,16)."<br />";
	

//aqui insertaria la factura en la base de datos
//campos a insertar id_cliente, codigo, fecha, consulta,importe
//OPCIONES FACTURA NUEVA, PROFORMA, DUPLICADO o FACTURA
//if(($fichero!="PROFORMA") && (!isset($_GET[factura])) && (!isset($_GET[duplicado])))
//echo "COOOOOOOOOOOO".$inicio;
	//echo $final;
if(($fichero!="PROFORMA") && (!isset($_GET[duplicado])))
{
	$fecha = cambiaf($fecha_factura);
	if (isset($inicio) && ($final != '0000-00-00'))
	{
		$puntual = 1;
		$fecha_inicial = cambiaf($inicio);
		$fecha_final = cambiaf($final);
	}
	$importe_iva = number_format($total_iva,2,'.','');
	$importe_total = number_format($total,2,'.','');
	//estamos en Factura si es repetida no se agrega
	//Linea de teste de fechas
	
	if(compruebaFactura($cliente,$codigo,$fecha,$total_iva,$total)) //no existe
		if ($puntual == 1)
		{
			$esecuele = "Insert into regfacturas (id_cliente,codigo,fecha,iva,importe,obs_alt,fecha_inicial,fecha_final,mes,ano) values ('$cliente','$codigo','$fecha','$importe_iva','$importe_total',\"$observaciones\",'$fecha_inicial','$fecha_final','$mes','$ano')";	
		}
		else
		{
			$esecuele = "Insert into regfacturas (id_cliente,codigo,fecha,iva,importe,obs_alt,mes,ano) 	values ('$cliente','$codigo','$fecha','$importe_iva','$importe_total',\"$observaciones\",'$mes','$ano')";
		}
		//echo $esecuele;/*LINEA DE TEST*/
/*		$consulta=mysql_db_query($dbname,$esecuele,$con);
	//else
		//echo comprueba_la_factura($cliente,$codigo,$fecha,$total_iva,$total);
}
/******************COMPROBAMOS SI EXISTE LA FACTURA PARA NO CREARLA********************/

/**************************************************************************************/	
//PIE FACTURA*************************************************************************/
/*	echo pie_factura($cliente,$observaciones,$codigo);

//echo $pie_factura;
*/
if ( isset( $_GET['duplicado'] ) ) {
	$datosFactura = datosFactura($_GET['codigo']);
	$datos = datosHistorico($_GET['codigo']);
	if ( $_GET['duplicado'] == "true" ) {
		$fichero = "FACTURA (DUPLICADO)";
		$titulo = "FACTURA<BR/>DUPLICADO";
	} else {
		$fichero = "FACTURA";
		$titulo = $fichero;
	}
}
if( isset($_GET['proforma'] ) ) {
	if( $_GET['proforma'] == 'true') {
		$fichero = "PROFORMA";
		$titulo = "FACTURA<BR/>PROFORMA";
	} else {
		$fichero = "FACTURA";
		$titulo = $fichero;
	}
}
?>
<html>
<head>
	<link rel="stylesheet" type='text/css' href="../estilo/print.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $fichero; ?></title>
</head>
<body>
	<pre>
	<?php var_dump($_GET); ?>
	</pre>
	<?php echo cabezeraFactura(
			$fichero, 
			$datosFactura['fecha'], 
			$datosFactura['codigo'], 
			$datosFactura['id_cliente']
			); ?>
	<table id='tabloide'>
	<tr>
		<th align='center' width='48%'>Servicio</th>
		<th align='center' width='8%'>Cant.</th>
		<th align='center' width='12%'>P/Unitario</th>
		<th align='center' width='12%'>IMPORTE</th>
		<th align='center' width='8%'>IVA</th>
		<th align='center' width='12%'>TOTAL</th>
	</tr>
	<?php
	$importeTotal = 0;
	$total = 0;
	$cantidades = 0; 
	foreach( $datos as $dato ) {
		$importe = $dato['cantidad']* $dato['unitario'];
		$subtotal = $importe * ( 1 + $dato['iva'] / 100);
		$importeTotal = $importeTotal + $importe;
		$total = $total + $subtotal;
		$cantidades = $cantidades + $dato['cantidad'];
		echo "
		<tr>
			<td>".$dato['servicio']." ". $dato['obs']."</td>
			<td>".$dato['cantidad']."</td>
			<td>".precioFormateado( $dato['unitario'] )."</td>
			<td>".precioFormateado( $importe )."</td>
			<td>".$dato['iva']."%</td>
			<td>".precioFormateado( $subtotal )."</td>
		</tr>
		";
	}
	?>
	<tr>
		<th align='center'>&nbsp;</th>
		<th align='center'><?php echo $cantidades; ?></th>
		<th align='center'>&nbsp;</th>
		<th align='center'><?php echo precioFormateado( $importeTotal ); ?></th>
		<th align='center'><?php echo precioFormateado( $total - $importeTotal );?></th>
		<th align='center'><?php echo precioFormateado( $total );?></th>
	</tr>
	</table>
</body>
</html>


