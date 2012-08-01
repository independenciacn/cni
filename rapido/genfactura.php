<?php
/**
 * genfactura.php File Doc Comment
 * 
 * Genera la factura dependiendo de lo que se pida
 * 
 * PHP Version 5.2.6
 * 
 * @category [categoria]
 * @package  cni/[paquete]
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
require_once '../inc/Cliente.php';
require_once 'telecos.php';
/**
 * Calculo del total con iva
 * @deprecated
 * @param  [type] $importe [description]
 * @param  [type] $iva     [description]
 * 
 * @return [type]          [description]
 */
function iva($importe,$iva)
{
	$total = round($importe + ($importe * $iva)/100,2);
	return $total;
}
/**
 * Devuelve las observaciones especiales en el caso de que las tenga
 * 
 * @param string $factura
 * @return string
 */
function observacionesEspeciales($factura)
{
	$observacion = "";
	$sql = "SELECT 
			obs_alt AS observacion, 
			pedidoCliente 
			FROM regfacturas 
			WHERE codigo LIKE ?
			AND obs_alt IS NOT NULL";
	$resultados = Cni::consultaPreparada(
			$sql,
			array($factura),
			PDO::FETCH_CLASS
			);
	if (Cni::totalDatosConsulta() > 0) {
		foreach ($resultados as $resultado) {
			$observacion = $resultado->observacion .
				"<br/>" . $resultado->pedidoCliente;
		}
	}
	return $observacion;
}
/**
 * Para distintas fechas de facturacion
 * 
 * @param string $cliente
 * @param string $mes
 * @param string $inicial
 * @param string $final
 * @return string $cadena
 */
function consultaFecha($cliente, $mes, $inicial, $final)
{
	global $con;
	$check1 = $inicial{4};
	$check2 = $final{4};
	if($check1!='-')
	$inicial=cambiaf($inicial);
	if($check2!='-')
	$final=cambiaf($final);
	if($inicial!='0000-00-00') {
		if(($final!="0000-00-00") && ($final!="--") && ($final!="")) {
			$cadena .= " and datediff(c.fecha,'".$inicial."') >= 0 
			and datediff(c.fecha,'".$final."') <=0 ";
		} else {
			$cadena = " and c.fecha like '".$inicial."' ";
		}
	} else {
		$sql = "Select valor from agrupa_factura 
		where idemp like ".$cliente." and concepto like 'dia'";
		$consulta = mysql_query($sql,$con);
		if(mysql_numrows($consulta)!=0) {
			$resultado = mysql_fetch_array($consulta);
			if($resultado[0]!="") {
				$mes_ant = $mes - 1;
				$fecha_inicial = date('Y')."-".$mes_ant."-".$resultado[0];
				$fecha_final = date('Y')."-".$mes."-".$resultado[0];
				$cadena =" and (c.fecha > '".$fecha_inicial."' 
				and c.fecha <= '".$fecha_final."')";
			} else {
				$cadena =" and (date_format(curdate(),'%Y') 
				like date_format(c.fecha,'%Y') 
				and '".$mes."' like date_format(c.fecha,'%c')) ";
			}
		} else {
		$cadena=" and (date_format(curdate(),'%Y') 
	like date_format(c.fecha,'%Y') and '$mes' like date_format(c.fecha,'%c')) ";
		}
	}
	//echo "Punto de control consulta_fecha valor cadena:".$cadena;
	return $cadena;
}
/**
 * Generacion de los no agrupados
 * 
 * @param string $cliente
 */
function consulta_no_agrupado($cliente)
{
	global $con;
	$pila = array(
			"Franqueo","Consumo Tel%fono",
			"Material de oficina","Secretariado","Ajuste");
	$i=5;
	$sql = "Select s.Nombre,a.valor from 
	agrupa_factura as a join servicios2 as s on a.valor = s.id 
	where a.idemp like ".$cliente." and a.concepto like 'servicio'";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0) {
		while(true == ($resultado = mysql_fetch_array($consulta))) {
			$pila[]=$resultado[0];
			$i++;
		}
	}
	$cadena = "and (";
	for($j=0;$j<=count($pila)-1;$j++) {
		$cadena .= " d.Servicio like '".$pila[$j]."' ";
		if ($j!=count($pila)-1) {
			$cadena .= " or ";
		}
	}
	$cadena .=") order by d.ImporteEuro desc , d.Servicio asc";
	return $cadena;
}
/**
 * Generacion de consulta de los agrupamientos
 * 
 * @param string $cliente
 * @return string
 */
function consulta_agrupado($cliente)
{
	global $con;
	$pila = array(
			"Franqueo","Consumo Tel%fono","Material de oficina",
			"Secretariado","Ajuste");
	$i=5;
	$sql = "Select s.Nombre,a.valor from agrupa_factura as a 
	join servicios2 as s on a.valor = s.id where a.idemp like ".$cliente."
	 and a.concepto like 'servicio'";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0) {
		while(true == ($resultado = mysql_fetch_array($consulta))) {
			$pila[]=$resultado[0];
			$i++;
		}
	}
	$cadena = "and (";
	for($j=0;$j<=count($pila)-1;$j++) {
		$cadena .= " d.Servicio not like '".$pila[$j]."' ";
		if ($j!=count($pila)-1) {
			$cadena .= " and ";
		}
	}
	$cadena .=") group by d.Servicio 
	order by d.ImporteEuro desc, d.Servicio asc";
	return $cadena;
}
/**
 * Generamos la cabezera de la factura
 * 
 * @param string $nombreFichero
 * @param string $fechaFactura
 * @param string $codigo
 * @param string $cliente
 * @return string $cabezera
 */
function cabezeraFactura($nombreFichero, $fechaFactura, $codigo, $cliente)
{
	$mes = Cni::$meses[Cni::verMes($fechaFactura)];
	$dia = Cni::verDia($fechaFactura);
	$anyo = Cni::verAnyo($fechaFactura);
	$fechaDeFactura = $dia . " de ". $mes . " de " . $anyo;
	$cliente = new Cliente($cliente);
	$tituloFichero = "N&deg;" . $nombreFichero . ":" . $codigo;
	if ($nombreFichero == 'PROFORMA') {
		$tituloFichero = $nombreFichero;
	}
	$html = "
	<br/>
	<br/>
	<br/>
	<div class='titulo'>
			".strtoupper($nombreFichero)."
	</div>
	<br/>
	<div class='cabezera'>
		<table width='100%'>
		<tr>
			<td  align='left' class='celdilla_sec'>
				<br/>
				FECHA:". $fechaDeFactura . "
				<br/>
				<br/>
				" . $tituloFichero . "
			</td>
			<td  class='celdilla_imp'>" .
				strtoupper($cliente->nombre) .
				"<br/>" .
				$cliente->direccion .
				"<br/>" .
				$cliente->cp .
				"&nbsp;&nbsp; - &nbsp;&nbsp;" .
				$cliente->ciudad .
				"<br/>
				NIF:". $cliente->nif .
			"</td>
		</tr>
		</table>
	</div>
	<br/>";
	return $html;
}
/**
 * Genera el Pie de la factura
 * 
 * @param string $cliente
 * @param string $observaciones
 * @param string $codigo
 * @return string $pie_factura;
 */
function pie_factura( $cliente, $observaciones, $codigo )
{
	global $con;
	$pie_factura = "";
	// Con estos tipos de formas de pago aparecera
	$pagoCC = array("Cheque","Contado","Tarjeta credito","Liquidación");
	$pagoNCC = array("Cheque");
	/* 
	 * Comprobamos si esta metido dentro de regfacturas,
	 * si no lo consultamos, lo metemos y lo mostramos
	 */
	$sql="Select * from regfacturas where codigo like '" . $codigo ."'";
	$consulta = mysql_query( $sql, $con );
	$resultado = mysql_fetch_array( $consulta );
	$camposPie = array( 0=>'fpago', 1=>'obs_fpago', 2=>'obs', 3=>'pedidoCliente');
	//$camposPieFac = array( 0=>'fpago', 1=>'cc', 2=>'obs', 3=>'dpago');
	// Si es 1 la factura esta dada de alta
	if ( mysql_num_rows( $consulta )!= 0 ) {
		foreach( $resultado as $key => $row ) {
			if ( in_array( $key, $camposPie ) ) {
				if ( !is_null( $row ) && $row != "" ) {
					$valoresPie[$key] = $row;
				}
			}
		}
		if ( is_null( $resultado['fpago'] ) || is_null( $resultado['obs_fpago'] )
		 || is_null( $resultado['pedidoCliente'] ) ) {
		    // Si no esta dada de alta consultamos los datos de facturacion
		    $sql = "SELECT fpago, cc as obs_fpago, dpago as pedidoCliente 
		    from facturacion where idemp like " . $cliente;
		    $consulta = mysql_query( $sql, $con );
		    $resultado = mysql_fetch_array( $consulta );
		    if ( mysql_num_rows( $consulta ) != 0  ) {
			    foreach( $resultado as $key => $row ) {
				    if ( in_array( $key, $camposPie ) ) {
					    if ( !is_null( $row ) && $row != "" ) {
					        $valoresPie[$key] = $row;
					    }
				    }
			    }
			    if ( !in_array( $valoresPie['fpago'], $pagoCC ) ) {
				    $valoresPie['obs_fpago']="Cuenta: ". $valoresPie['obs_fpago'];
			    } elseif ( in_array( $valoresPie['fpago'], $pagoNCC ) && $valoresPie['cc']!="" ) {
				    $valoresPie['obs_fpago']="Vencimiento: ". $valoresPie['obs_fpago'];
			    }
			    // Actualizamos regfacturas
			    $sql = "Update regfacturas set 
				fpago ='" . $valoresPie['fpago'] . "', 
				obs_fpago ='" . $valoresPie['obs_fpago'] . "',
				pedidoCliente ='". $valoresPie['pedidoCliente'] ." '   
				where codigo like " . $codigo;
			    mysql_query( $sql , $con );
		    }
	    }
	    $pie_factura = "<br/>
		<div class='celdia_sec'>
		Forma de pago: ". $valoresPie['fpago'] ."<br/>" .
	    $valoresPie['obs_fpago']."<br/>" .
	    $valoresPie['pedidoCliente'] . 
	    observacionesEspeciales( $codigo ) .
		"</div>";
	}
	return $pie_factura;
}
/**
 * Genera la consulta del almacenaje dependiendo de los parametros de agrupa_factura
 * 
 * @param string $cliente
 * @param string $mes
 * @param string $inicial
 * @param string $final
 * @return string
 */
function consulta_almacenaje($cliente,$mes,$inicial,$final)
{
	global $con;
	$check1=$inicial{4};
	$check2=$final{4};
	if ($check1!='-') {
        $inicial=cambiaf($inicial); 
	}
	if ($check2!='-') {
        $final=cambiaf($final);
	}
	if(($inicial == '0000-00-00') && ($final == '0000-00-00')) {
		$sql = "Select * from agrupa_factura where concepto like 'dia' 
		and idemp like ".$cliente." and valor not like ''" ;
		$consulta = mysql_query($sql,$con);
		if ( mysql_numrows( $consulta ) !=0 ) {
			$resultado = mysql_fetch_array($consulta);
			$sql .= "Select bultos, datediff(fin,inicio), inicio, fin  
			from z_almacen where cliente like ".$cliente." 
			and (month(inicio) like (".$mes."-1) and month(fin) like ".$mes." 
			and day(inicio) >= ".$resultado['valor']."  and 
			day(fin) <= ".$resultado['valor']." and year(inicio) 
			like year(curdate()) and year(fin) like year(curdate()))";
		} else {
			$sql = "Select bultos, datediff(fin,inicio), inicio, fin  
			from z_almacen where cliente like ".$cliente." 
			and month(fin) like ".$mes." and year(fin) like year(curdate())";
		}
	} else {
		$check1=$inicial{4};
		$check2=$final{4};
		if ($check1!='-') {
			$inicial=cambiaf($inicial);
		}
		if ($check2!='-') {
			$final=cambiaf($final);
		}
	 	if (($inicial != "" ) && ($final != "")) {
			$sql = "Select bultos, datediff(fin,inicio), inicio, fin 
			from z_almacen where cliente like ".$cliente." and month(fin) 
			like month('".$final."') and year(fin) like year('".$final."')";
	 	} else {
			$sql = "Select bultos, datediff(fin,inicio), inicio, fin 
			from z_almacen where cliente like ".$cliente." 
			and fin <= '".$final."'";
		}
	}
	return $sql;
}
/**
 * Consulta si la factura esta en el historico, si no esta devuelve false
 * si esta devuelve los datos
 * 
 * @param Integer $factura
 * @return mixed boolean|resource
 */
function historico($factura)
{
	$sql = "SELECT * FROM historico 
			WHERE factura LIKE ?";
	$resultados = Cni::consultaPreparada(
			$sql,
			array($factura),
			PDO::FETCH_CLASS
			);
	if (Cni::totalDatosConsulta() > 0 ) {
		return $resultados;
	} else {
		return false;
	}
}
/**
 * Comprueba la factura, si no existe devuelve true y si existe la actualiza
 * y devuelve false
 * 
 * @param Integer $cliente
 * @param Integer $codigo
 * @param date $fecha
 * @param float $iva
 * @param float $total
 * @return boolean
 */
function compruebaFactura($cliente, $codigo, $fecha, $iva, $total)
{
	$sql = "SELECT * 
			FROM regfacturas 
			WHERE id_cliente LIKE ?
			AND codigo LIKE ? 
			AND fecha LIKE ?";
	$params = array($cliente, $codigo, $fecha);
	$resultados = Cni::consultaPreparada($sql, $params, PDO::FETCH_CLASS);
	if (Cni::totalDatosConsulta() == 0) {
		return true;
	} else {
		$sql = "UPDATE regfacturas SET
			iva =  ?, importe = ?
			WHERE id_cliente LIKE ? 
			AND codigo LIKE ? 
			AND fecha like STR_TO_DATE(?)";
		$params = array($iva, $total, $cliente, $codigo, $fecha);
		$resultados = Cni::consultaPreparada($sql, $params);
		return false;
	}
}
/**
 * Agrega los datos al historico
 * 
 * @param string $factura
 * @param string $servicio
 * @param string $cantidad
 * @param string $unitario
 * @param string $iva
 * @param string $obs
 */
function agregaHistorico($factura, $servicio, $cantidad, $unitario, $iva, $obs)
{
	$servicio = trim($servicio);
	$sql = "
	Insert into historico (factura, servicio, cantidad, unitario, iva, obs) 
	values (?, ?, ?, ?, ?, ?)";
	$params = array($factura, $servicio, $cantidad, $unitario, $iva, $obs);
	Cni::consultaPreparada($sql, $params);
	return true;
}
/**
 * Funcion Principal - Obligatorio el cliente
 * Parametros del get cliente, mes, fecha_factura, codigo
 * En puntual: fecha_inicial_factura, fecha_final_factura para filtrado
 * Proforma: prueba = 1
 */
if (isset($_GET['cliente'])) {
	$cliente = $_GET['cliente'];
	$fechaFactura = $_GET['fecha_factura'];
	$codigo = $_GET['codigo'];
	$fechaInicial= $_GET['fecha_inicial_factura'];
	$fechaFinal = $_GET['fecha_final_factura'];
	$observaciones = $_GET['observaciones'];
	$pedidoCliente = "";
	$anoFactura = Cni::verAnyo($_GET['fecha_factura']);
	$mesFactura = $_GET['mes'];
	$fichero = "FACTURA";
	$titulo = $fichero;
	/**
	 * Clic en Proforma
	 */
	if ( isset($_GET['prueba'])) {
		$fichero = "PROFORMA";
		$titulo = "FACTURA<BR/>PROFORMA";
	}
}
/**
 * Casos de Imprimir factura generada o ver el duplicado
 */
if (isset($_GET['factura']) || isset($_GET['duplicado'])) {
	$param = (isset($_GET['factura'])) ? $_GET['factura'] : $_GET['duplicado'];
	$sql = "SELECT 
			id_cliente AS idCliente, 
			DATE_FORMAT(fecha, '%d-%m-%Y') AS fecha,
			codigo,
			DATE_FORMAT(fecha_inicial, '%d-%m-%Y') AS fechaInicial,
			DATE_FORMAT(fecha_final, '%d-%m-%Y') AS fechaFinal,
			obs_alt AS observaciones,
			pedidoCliente
			FROM regfacturas 
			WHERE id LIKE ?";
	$resultados = Cni::consultaPreparada($sql, array($param), PDO::FETCH_CLASS);
	foreach ($resultados as $resultado) {
		$cliente = $resultado->idCliente;
		$fechaFactura = $resultado->fecha;
		$codigo = $resultado->codigo;
		$fechaInicial = $resultado->fechaInicial;
		$fechaFinal = $resultado->fechaFinal;
		$observaciones = $resultado->observaciones;
		$pedidoCliente = $resultado->pedidoCliente;
		$anoFactura = Cni::verAnyo($fechaFactura);
		$mesFactura = Cni::verMes($fechaFactura);
		$fichero = "FACTURA";
		$titulo = $fichero;
	}
	/**
	 * Si la factura es un duplicado
	 */
	if (isset($_GET['duplicado'])) {
		$fichero = "FACTURA (DUPLICADO)";
		$titulo = "FACTURA<BR/>DUPLICADO";
	}
}
$nombreFichero = "<span style='font-size:16.0pt'>" . $titulo . "</span>";


//PRESENTACION************************************************************************/
//CASOS POSIBLES, MENSUAL y PUNTUAL en puntual hay que pasar los limites
//fecha_inicial_factura y fecha_final_factura
if (($fechaInicial != '00-00-0000') && ($fechaFinal != '00-00-0000')) {
	$inicio = $fechaInicial;
	$final = $fechaFinal;
} else {
	$inicio = "00-00-0000";
	$final = "00-00-0000";
}
/**
 * Titulo de la pagina
 */
$tituloPagina =
	( $inicio!= "00-00-0000") ? "ocupacion puntual" : Cni::$meses["m"];
/**
 * Cabezera de la factura - $ficher, $fecha_factura, $codigo, $cliente
 */
$cabezeraFactura = cabezeraFactura($fichero, $fechaFactura, $codigo, $cliente);
?>
<!-- Pagina de la factura -->
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<link  href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
	<title><?php echo $fichero . " " . $tituloPagina; ?></title>
</head>
<body>
<?php
	var_dump($_GET);
	$celdas = 0;
	$cantidad = 0;
	$total = 0;
	$bruto = 0;
	$resultadosHistorico =
		(isset($_GET['factura'])) ? historico($_GET['factura']) : false;
	$html =
	$cabezeraFactura.
	"
 	<table class = 'table table-bordered table-striped'>
 	<colgroup width = '48%' />
 	<colgroup width = '8%' />
 	<colgroup width = '12%' span='2'/>
 	<colgroup width = '8%' />
 	<colgroup width = '12%' />
 	<thead>
 		<tr>
		<th>Servicio</th>
		<th>Cant.</th>
		<th>P/Unitario</th>
		<th>IMPORTE</th>
		<th>IVA</th>
		<th>TOTAL</th>
	</tr>
 	</thead>";
	$html .= "<tbody>";
if ($resultadosHistorico) {
	foreach ($resultadosHistorico as $resultado) {
		$importe = $resultado->cantidad * $resultado->unitario;
		$totalConIva = Cni::totalconIva($importe, $resultado->iva);
		$html .= "
 			<tr>
			<td>".ucfirst($resultado->servicio)." 
 				".ucfirst($resultado->obs)."
			</td>
			<td>".Cni::formateaNumero($resultado->cantidad)."</td>
			<td>".Cni::formateaNumero($resultado->unitario, true)."</td>
			<td>".Cni::formateaNumero($importe, true)."</td>
			<td>".Cni::formateaNumero($resultado->iva)."%</td>
			<td>".Cni::formateaNumero($totalConIva, true)."</td>
			</td>
			</tr>";
		$total += $totalConIva;
		$bruto += $importe;
		$cantidad += $resultado->cantidad;
		$celdas++;
	}
} else {
	if (((($mesFactura >= 3) && ($anoFactura == 2007))
			||(($anoFactura >= 2008)) && ($inicio == "00-00-0000"))
			&& ($final == "0000-00-00")) {
		/**
		 * Acumulado del total de servicios fijos
		 * @var float $importeServiciosFijos
		 */
		$sql = "Select * 
 		FROM tarifa_cliente 
		WHERE ID_Cliente LIKE ? 
 		ORDER BY Imp_Euro DESC";
		$resultados = Cni::consultaPreparada(
			$sql,
			array($cliente),
			PDO::FETCH_CLASS
		);
		$importeServiciosFijos = 0;
		foreach ($resultados as $resultado) {
			$importe = $resultado->unidades * $resultado->Imp_Euro;
			$importeServiciosFijos += $importe;
			$totalConIva = Cni::totalconIva($importe, $resultado->iva);
			$html .= "
 			<tr>
			<td>".ucfirst($resultado->Servicio)."
 				".ucfirst($resultado->observaciones)."
			</td>
			<td>".Cni::formateaNumero($resultado->unidades)."</td>
			<td>".Cni::formateaNumero($resultado->Imp_Euro, true)."</td>
			<td>".Cni::formateaNumero($importe, true)."</td>
			<td>".Cni::formateaNumero($resultado->iva)."%</td>
			<td>".Cni::formateaNumero($totalConIva, true)."</td>
			</td>
			</tr>";
			$total += $totalConIva;
			$bruto += $importe;
			$cantidad += $resultado->unidades;
			$celdas ++;
			if (!isset($_GET['prueba'])) {
				agregaHistorico(
					$codigo,
					ucfirst($resultado->Servicio),
					$resultados->unidades,
					$resultados->Imp_Euro,
					$resultados->iva,
					$resultados->observaciones
				);
			}
		}
	}
/************************************************************************************/
//Devuelve la consulta para generar el almacenaje
/*Parte de consulta de importe e iva de almacenaje*/
    /*Buscamos los datos de importe e iva de almacenaje*/
    $sql = "Select datediff('".cambiaf($fechaFactura)."','2010-07-01')";
    //echo $sql;
    $consulta = mysql_query($sql,$con);
    $diff = mysql_fetch_array($consulta);
    if($diff[0]>=0)
    {
        $sql = "select PrecioEuro, iva from servicios2 where nombre like '%Almacenaje%'";
        $consulta = mysql_query($sql,$con);
        $par_almacenaje = mysql_fetch_array($consulta);
    } else {
        $par_almacenaje = array('PrecioEuro'=>'0.70','iva'=>'16');
    }
    /*Final datos de valores del almacenaje*/
	$sql = consulta_almacenaje($cliente,$mes,$inicio,$final);
	//echo $sql;/*PUNTO DE CONTROL*/
	
	$consulta = mysql_query($sql,$con);
	while (true == ($resultado = mysql_fetch_array($consulta))) {
		$dias_almacen = $resultado[1];
		$subtotala = $resultado[0]*$dias_almacen*$par_almacenaje['PrecioEuro'];
        $totala = iva($subtotala,$par_almacenaje['iva']);
		echo "<tr>
		<td ><p class='texto'>Bultos Almacenados del  ".
		cambiaf($resultado[2])." al ".cambiaf($resultado[3])."</p></td>
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
		if(($historico == "ko")&& (!isset($_GET['prueba']))) { //Agregamos al historico
			agregaHistorico($codigo,"Bultos Almacenados",$resultado[0],
					$subtotala,$par_almacenaje['iva'],$cadena_texto);
		}
	}
//fin del almacenaje**********************************************************************/
//FIN DE ESTA PARTE
//Servicio contratado
//#####################Servicios No agrupados#############################################
//control de puntuales
	$sql = "Select d.Servicio, d.Cantidad, date_format(c.fecha,'%d-%m-%Y') as fecha, 
	d.PrecioUnidadEuros, d.ImporteEuro, d.iva, c.`Id Pedido` ,
	d.observaciones from `detalles consumo de servicios` as d join `consumo de servicios` as c 
	on c.`Id Pedido` = d.`Id Pedido` where c.Cliente like ".$cliente;
//consulta de fecha
	$sql .= consultaFecha($cliente,$mes,$inicio,$final); //con esta miramos los rangos de la factura
	$sql .= consulta_no_agrupado($cliente);
	//echo $sql;/*PUNTO DE CONTROL*/
	$consulta = mysql_query($sql,$con);
	while (true == ($resultado=mysql_fetch_array($consulta))) {
		$subtotal = $resultado[4] + ($resultado[4]*$resultado[5])/100;
//acumulados
		$total = $subtotal + $total;
		$cantidad = $resultado[1] + $cantidad;
//fin acumulados
		echo "<tr>
		<td ><p class='texto'>".ucfirst($resultado[0])." 
		".ucfirst($resultado[7])."</p></td>
		<td align='right'>".number_format($resultado[1],2,',','.')."&nbsp;</td>
		<td align='right'>".number_format($resultado[3],2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".number_format($resultado[4],2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".$resultado[5]."%&nbsp;</td>
		<td align='right'>".number_format($subtotal,2,',','.')."&euro;&nbsp;</td></tr>";
		$bruto = $bruto + $resultado[4];
		$celdas++;
		//$servicio_desc = ucfirst($resultado[0])." ".codifica(ucfirst($resultado[7]));
		if(($historico == "ko")&& (!isset($_GET['prueba']))) { //Agregamos al historico
			agregaHistorico($codigo,$resultado[0],$resultado[1],$resultado[3],$resultado[5],$resultado[7]);
		}
	}
//#####################################Parte agrupada###############################################
	$sql = "Select d.Servicio, sum(d.Cantidad), date_format(c.fecha,'%d-%m-%Y') as fecha, 
	d.PrecioUnidadEuros, sum(d.ImporteEuro), d.iva, c.`Id Pedido` ,
	d.observaciones from `detalles consumo de servicios` as d join `consumo de servicios` as c 
	on c.`Id Pedido` = d.`Id Pedido` where c.Cliente like $cliente";
	$sql .= consultaFecha($cliente,$mes,$inicio,$final);
	$sql .= consulta_agrupado($cliente);
	//echo $sql;//<- Punto de Control
	//echo $cliente.",".$mes.",".$inicio.",".$final;
	$consulta = mysql_query($sql,$con);
	while ( true == ($resultado=mysql_fetch_array($consulta))) {
		$subtotal = $resultado[4]+ ($resultado[4]*$resultado[5])/100;
//acumulados
		$total = $subtotal + $total;
		$cantidad = $resultado[1] + $cantidad;
//fin acumulados
		echo "<tr>
		<td ><p class='texto'>".ucfirst($resultado[0])." 
		".ucfirst($resultado[7])."</p></td>
		<td align='right'>".number_format($resultado[1],2,',','.')."&nbsp;</td>
		<td align='right'>".number_format($resultado[3],2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".number_format($resultado[4],2,',','.')."&euro;&nbsp;</td>
		<td align='right'>".$resultado[5]."%&nbsp;</td>
		<td align='right'>".number_format($subtotal,2,',','.')."&euro;&nbsp;</td></tr>";
		$bruto = $bruto + $resultado[4];
		$celdas++;
		//$servicio_desc = ucfirst($resultado[0])." ".codifica(ucfirst($resultado[7]));
		if(($historico == "ko")&& (!isset($_GET['prueba']))) { //Agregamos al historico
			agregaHistorico($codigo,ucfirst($resultado[0]),$resultado[1],
					$resultado[3],$resultado[5],ucfirst($resultado[7]));
		}
	}
//descuento si procede
/**
 * El descuento se calcula del total de los servicios fijos
 * Esta como un servicio FIJO MENSUAL
 */
		$esql = "Select razon from clientes where id like ".$cliente;
		$consulta = mysql_query($esql,$con);
		$resultado = mysql_fetch_array($consulta);
		if(($resultado[0] != "") && ($resultado[0] != "")) {
			$porcentaje = explode("%",$resultado[0]); // Porcentaje del descuento
			$descuento = ($importeServiciosFijos * $porcentaje[0])/100;// @FIXME calculo en base al total de servicios fijos
			$descuento_con_iva = $descuento * 1.18; 
			echo "<tr>
			<td ><p class='texto'>Descuento del ".$porcentaje[0]."%</p></td>
			<td align='right'>1&nbsp;</td>
			<td align='right'>-".number_format($descuento,2,',','.')."&euro;&nbsp;</td>
			<td align='right'>-".number_format($descuento,2,',','.')."&euro;&nbsp;</td>
			<td align='right'>18%&nbsp;</td>
			<td align='right'>-".number_format($descuento_con_iva,2,',','.')."&euro;&nbsp;</td></tr>";
			$descuento_historico = "-".$descuento;
			if(($historico == "ko")&& (!isset($_GET['prueba']))){ //Agregamos al historico
				agregaHistorico($codigo,"Descuento","1",$descuento_historico,"18", "del ".$porcentaje[0]);
			}
		} else {
			$descuento = 0;
			$descuento_con_iva = 0;
		}
		/**
		 * Para el resultado de pie esta bien
		 */
		$bruto = $bruto - $descuento;
		$total = $total - $descuento_con_iva;
} //Cierre de las que no estan en historico

//Compensacion de diseño
	$coeficiente = 432 - ($celdas-1) * 18;
if ($coeficiente >= 1) {
	echo "<tr>
			<td height='".$coeficiente."px'>&nbsp;</td>
			<td>&nbsp;</th>
			<td>&nbsp;</th>
			<td>&nbsp;</th>
			<td>&nbsp;</th>
			<td>&nbsp;</th>
		 </tr>";
}
$html .= "
	</tbody>
	<tfoot>
		<tr>
		<th>&nbsp;</th>
		<th>".Cni::formateaNumero($cantidad)."</th>
		<th>&nbsp;</th>
		<th>".Cni::formateaNumero($bruto, true)."</th>
		<th></th>
		<th>".Cni::formateaNumero($total, true)."</th>
	</tfoot>
	</table>";
echo $html;
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
	<th  class='celdilla_tot' >".number_format($total,2,',','.')."&euro;</th></tr>
	</table>";
	//$pie_factura .= "<br />".$bruto."-".iva($bruto,16)."<br />";
//aqui insertaria la factura en la base de datos
//campos a insertar id_cliente, codigo, fecha, consulta,importe
//OPCIONES FACTURA NUEVA, PROFORMA, DUPLICADO o FACTURA
//if(($fichero!="PROFORMA") && (!isset($_GET[factura])) && (!isset($_GET[duplicado])))
//echo "COOOOOOOOOOOO".$inicio;
	//echo $final;
if(($fichero!="PROFORMA") && (!isset($_GET['duplicado']))) {
	$fecha = cambiaf($fechaFactura);
	if (isset($inicio) && ($final != '0000-00-00')) {
		$puntual = 1;
		$fecha_inicial = cambiaf($inicio);
		$fecha_final = cambiaf($final);
	}
	$importe_iva = number_format($total_iva,2,'.','');
	$importe_total = number_format($total,2,'.','');
	//estamos en Factura si es repetida no se agrega
	//Linea de teste de fechas
	if(compruebaFactura($cliente,$codigo,$fechaFactura,$total_iva,$total)) { //no existe
		if ($puntual == 1) {
			$esecuele = "Insert into regfacturas (id_cliente,codigo,fecha,
			iva,importe,obs_alt,fecha_inicial,fecha_final,mes,ano) 
			values ('".$cliente."','".$codigo."','".$fecha."','".$importe_iva."',
			'".$importe_total."','".$observaciones."','".$fecha_inicial."',
			'".$fecha_final."','".$mes."','".$ano."')";	
		} else {
			$esecuele = "Insert into regfacturas (id_cliente,codigo,fecha,
			iva,importe,obs_alt,mes,ano) values ('".$cliente."','".$codigo."',
			'".$fecha."','".$importe_iva."','".$importe_total."',
			'".$observaciones."','".$mes."','".$ano."')";
		}
		$consulta=mysql_query($esecuele,$con);
	}
	//echo $esecuele;/*LINEA DE TEST*/
	
	//else
		//echo comprueba_la_factura($cliente,$codigo,$fecha,$total_iva,$total);
}

/**************************************************************************************/	
//PIE FACTURA*************************************************************************/
echo pie_factura($cliente,$observaciones,$codigo);
//echo $pie_factura;
?>
</body></html>

<!-- Linea final 807 -->