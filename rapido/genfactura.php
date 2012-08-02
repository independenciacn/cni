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
require_once '../inc/Servicio.php';
require_once 'telecos.php';
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
	$cadena = "";
	if ($inicial != '00-00-0000') {
		if ($final != '00-00-0000') {
			$cadena = " AND DATEDIFF(
						c.fecha,
						STR_TO_DATE('".$inicial."', '%d-%m-%Y')
					) >= 0 
					AND DATEDIFF(
						c.fecha,
						STR_TO_DATE('".$final."', '%d-%m-%Y')
					) <=0 ";
		} else {
			$cadena = " AND c.fecha LIKE 
					STR_TO_DATE('".$inicial."', '%d-%m-%Y') ";
		}
	} else {
		/**
		 * Para los que tienen dia de facturacion
		 */
		$sql = "SELECT 
				valor as dia 
				FROM agrupa_factura 
				WHERE idemp LIKE ?
				AND concepto LIKE 'dia'
				AND valor NOT LIKE ''";
		$resultados = Cni::consultaPreparada(
						$sql,
						array($cliente),
						PDO::FETCH_CLASS
						);
		if (Cni::totalDatosConsulta() > 0) {
			$anyo = date('Y');
			foreach ($resultados as $resultado) {
				$fecha = $anyo."-".$mes."-".$resultado->dia;
				$cadena = " AND (
						c.fecha > DATE_SUB('','".$fecha."', INTERVAL 1 MONTH)
						AND c.fecha <= '".$fecha."')";
			}
		} else {
			$cadena = "AND YEAR(curdate()) LIKE YEAR(c.fecha)
					AND '".$mes."' LIKE MONTH(c.fecha)";
		}
	}
	return $cadena;
}
/**
 * Generacion de la parte de la consutla de lso no Agrupados y no agrupados
 * 
 * @param unknown_type $cliente
 * @param boolean $agrupado true = agrupado, false = no agrupado
 * 
 * @return string
 */
function consultaAgrupado($cliente, $agrupado = false)
{
	$union = "OR";
	$like = "LIKE";
	$groupBy = "";
	if ($agrupado) {
		$union = "AND";
		$like = "NOT LIKE";
		$groupBy = "GROUP BY d.Servicio";
	}
	$noAgrupados = array(
			"Franqueo","Consumo Tel%fono",
			"Material de oficina","Secretariado","Ajuste");
	$sql = "SELECT 
			s.Nombre AS nombre,
			FROM agrupa_factura AS a 
			INNER JOIN servicios2 AS s 
			ON a.valor = s.id 
			WHERE a.idemp LIKE ?".$cliente." 
			AND a.concepto LIKE 'servicio'";
	$resultados = Cni::consultaPreparada(
			$sql,
			array($cliente),
			PDO::FETCH_CLASS
			);
	if (Cni::totalDatosConsulta() > 0) {
		foreach ($resultados as $resultado) {
			$noAgrupados[] = $resulado->nombre;
		}
	}
	$cadena = "and (";
	foreach ($noAgrupados as $noAgrupado) {
		$cadena .= " d.Servicio ".$like." '".$noAgrupado."' ".$union." ";
	}
	$cadena = substr($cadena, 0, strlen($cadena) - (strlen($union) + 1));
	$cadena .=") ".$groupBy."  
			ORDER BY d.ImporteEuro DESC , d.Servicio ASC";
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
function pieFactura($cliente, $observaciones, $codigo)
{
	$html = "";
	// Con estos tipos de formas de pago aparecera
	$pagoCC = array("Cheque","Contado","Tarjeta credito","Liquidación");
	$pagoNCC = array("Cheque");
	$faltan = false;
	/** 
	 * Comprobamos si esta metido dentro de regfacturas,
	 * si no lo consultamos, lo metemos y lo mostramos
	 */
	$sql = "SELECT 
		fpago AS formaPago,
		obs_fpago AS obsFormaPago,
		pedidoCliente
		FROM regfacturas 
		WHERE codigo LIKE ?";
	$resultados = Cni::consultaPreparada(
			$sql,
			array($codigo),
			PDO::FETCH_CLASS
			);
	if (Cni::totalDatosConsulta() > 0) {
		foreach ($resultados as $resultado) {
			$valoresPie['formaPago'] = $resultado->formaPago;
			$valoresPie['obsFormaPago'] = $resultado->obsFormaPago;
			$valoresPie['pedidoCliente'] = $resultado->pedidoCliente;
		}
		/**
		 * Comprobamos si alguno de los datos almacenados no tienen datos
		 */
		foreach ($valoresPie as $valor) {
			if ( is_null($valor) || $valor == "") {
				$faltan = true;
				break;
			}
		}
		/**
		 * Ni no existe algun dato lo consultamos y lo agregamos
		 */
		if ($faltan) {
			$sql = "SELECT 
				fpago AS formaPago, 
				cc AS obsFormaPago, 
				dpago AS pedidoCliente 
		    	FROM facturacion 
				WHERE idemp LIKE ?";
			$resultados = Cni::consultaPreparada(
					$sql,
					array($cliente),
					PDO::FETCH_CLASS
					);
			foreach ($resultados as $resultado) {
				$valoresPie['formaPago'] = $resultado->formaPago;
				$valoresPie['obsFormaPago'] = $resultado->obsFormaPago;
				$valoresPie['pedidoCliente'] = $resultado->pedidCliente;
			}
			/**
			 * Reescribimos valores si se aplica
			 */
			if (!in_array($valoresPie['formaPago'], $pagoCC)) {
				$valoresPie['formaPago'] =
					"Cuenta: ". $valoresPie['formaPago'];
			} elseif (in_array($valoresPie['formaPago'], $pagoNCC)
					&& $valoresPie['cc'] != "" ) {
				$valoresPie['obsFormaPago'] =
					"Vencimiento: ". $valoresPie['obsFormaPago'];
			}
			/**
			 * Actualizamos regfacturas
			 */
			$sql = "Update regfacturas set
				fpago = ?, 
				obs_fpago = ?,
				pedidoCliente = ?,
				WHERE codigo LIKE ?";
			$params = array(
					$valoresPie['formaPago'],
					$valoresPie['obsFormaPago'],
					$valoresPie['pedidoCliente'],
					$codigo
					);
			Cni::consultaPreparada($sql, $params);
		}
		/**
		 * Construimos el pie y lo devolvemos
		 */
		$html = "
		<br/>
		<div class='celdia_sec'>
			Forma de pago: ". $valoresPie['formPago'] ."<br/>" .
				$valoresPie['obsFormaPago']."<br/>" .
				$valoresPie['pedidoCliente'] .
				observacionesEspeciales( $codigo ) .
		"</div>";
	}
	return $html;
}
/**
 * Genera la consulta del almacenaje dependiendo de los 
 * parametros de agrupa_factura
 * 
 * @param string $cliente
 * @param string $mes
 * @param string $inicial
 * @param string $final
 * @return string
 */
function consultaAlmacenaje($cliente, $mes, $inicial, $final)
{
	if (($inicial == '00-00-0000') && ($final == '00-00-0000')) {
		/**
		 * Consultamos si hay datos agrupados
		 */
		$sql = "SELECT * 
				FROM agrupa_factura 
				WHERE concepto LIKE 'dia' 
				AND idemp LIKE ? 
				AND valor NOT LIKE ''" ;
		$resultados = Cni::consultaPreparada(
				$sql,
				array($cliente),
				PDO::FETCH_CLASS
				);
		/**
		 * Consulta raiz del resto
		 */
		$sql = "SELECT 
				bultos, 
				datediff(fin,inicio), 
				inicio, 
				fin
				FROM z_almacen 
				WHERE cliente LIKE '".$cliente."' ";
		if (Cni::totalDatosConsulta() > 0) {
			foreach ($resultados as $resultado) {
				$sql.="AND (month(inicio) LIKE (".$mes."-1) 
					AND month(fin) LIKE ".$mes."
					AND DAY(inicio) >= ".$resultado->valor."  
					AND DAY(fin) <= ".$resultado->valor." 
					AND YEAR(inicio) LIKE YEAR(curdate()) 
					AND YEAR(fin) LIKE YEAR(curdate()))";
			}
		} else {
			$sql .= "AND MONTH(fin) LIKE ".$mes." 
					AND YEAR(fin) LIKE year(curdate())";
		}
	} else {
	 	if (($inicial != "00-00-0000" ) && ($final != "00-00-0000")) {
			$sql .= "AND MONTH(fin) LIKE 
					MONTH(STR_TO_DATE('".$final."', '%d-%m-%Y')) 
					AND YEAR(fin) LIKE 
					YEAR(STR_TO_DATE('".$final."', '%d-%m-%Y))";
	 	} else {
			$sql = "AND fin <= STR_TO_DATE('".$final."', %d-%m-%Y)";
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
/**
 * Casos posibles, mensual y Puntual.
 * En Puntual hay que pasar los limites fechaInicialFactura y FechaFinalFactura
 */
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
	/**
	 * Seccion de almacenaje
	 */
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
	$sql = consultaAlmacenaje($cliente,$mes,$inicio,$final);
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
	$sql .= consultaAgrupado($cliente, false);
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
	$sql .= consultaAgrupado($cliente, true);
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
	echo "<br/>
	<table class='table table-bordered table-striped'>
	<colgroup width='15%' />
	<colgroup />
	<colgroup width='15%' />
	<colgroup />
	<colgroup width='15%' />
	<colgroup />
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>TOTAL BRUTO</th>
			<th>&nbsp;</th>
			<th>IVA</th>
			<th>&nbsp;</th>
			<th>TOTAL</th>
		</tr>
 		<tr>
			<th>&nbsp;</th>
			<th>".Cni::formateaNumero($bruto, true)."</th>
			<th>&nbsp;</th>
			<th>".Cni::formateaNumero($total_iva, true)."</th>
			<th>&nbsp;</th>
			<th>".Cni::formateaNumero($total, true)."</th>
		</tr>
	</thead>
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
echo pieFactura($cliente,$observaciones,$codigo);
//echo $pie_factura;
?>
</body></html>

<!-- Linea final 807 -->