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
require_once '../inc/Facturas.php';
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
			WHERE a.idemp LIKE ? 
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
				$valoresPie['pedidoCliente'] = $resultado->pedidoCliente;
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
			Forma de pago: ". $valoresPie['formaPago'] ."<br/>" .
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
 * @param string $mesFactura
 * @param string $inicial
 * @param string $final
 * @return string
 */
function consultaAlmacenaje($cliente, $mesFactura, $inicial, $final)
{
	$sqlFinal = "SELECT
				bultos,
				datediff(fin,inicio) AS dias,
				DATE_FORMAT(inicio, '%d-%m-%Y') as inicio,
				DATE_FORMAT(fin, '%d-%m-%Y') as fin
				FROM z_almacen
				WHERE cliente LIKE '".$cliente."' ";
	if (($inicial == '00-00-0000') && ($final == '00-00-0000')) {
		/**
		 * Consultamos si hay datos agrupados
		 */
		$sql = "SELECT * 
				FROM agrupa_factura 
				WHERE concepto LIKE 'dia' 
				AND idemp LIKE ? 
				AND valor NOT LIKE '' " ;
		$resultados = Cni::consultaPreparada(
				$sql,
				array($cliente),
				PDO::FETCH_CLASS
				);
		if (Cni::totalDatosConsulta() > 0) {
			foreach ($resultados as $resultado) {
				$sqlFinal .= "AND (month(inicio) LIKE (".$mesFactura."-1) 
					AND month(fin) LIKE ".$mesFactura."
					AND DAY(inicio) >= ".$resultado->valor."  
					AND DAY(fin) <= ".$resultado->valor." 
					AND YEAR(inicio) LIKE YEAR(curdate()) 
					AND YEAR(fin) LIKE YEAR(curdate()))";
			}
		} else {
			$sqlFinal .= "AND MONTH(fin) LIKE ".$mesFactura." 
					AND YEAR(fin) LIKE year(curdate())";
		}
	} else {
	 	if (($inicial != "00-00-0000" ) && ($final != "00-00-0000")) {
			$sqlFinal .= "AND MONTH(fin) LIKE 
					MONTH(STR_TO_DATE('".$final."', '%d-%m-%Y')) 
					AND YEAR(fin) LIKE 
					YEAR(STR_TO_DATE('".$final."', '%d-%m-%Y))";
	 	} else {
			$sqlFinal .= "AND fin <= STR_TO_DATE('".$final."', %d-%m-%Y)";
		}
	}
	return $sqlFinal;
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
			AND fecha LIKE STR_TO_DATE(?, '%d-%m-%Y')";
	$params = array($cliente, $codigo, $fecha);
	$resultados = Cni::consultaPreparada($sql, $params, PDO::FETCH_CLASS);
	if (Cni::totalDatosConsulta() == 0) {
		return true;
	} else {
		$sql = "UPDATE regfacturas SET
			iva =  ?, importe = ?
			WHERE id_cliente LIKE ? 
			AND codigo LIKE ? 
			AND fecha like STR_TO_DATE(?, '%d-%m-%Y')";
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
 * Devuelve la linea de la tabla con los datos formateados
 * 
 * @param object $datosServicio
 * @return string
 */
function lineaTabla($datosServicio) {
    $html = "
 		<tr>
		<td>".$datosServicio->servicio."</td>
		<td>".Cni::formateaNumero($datosServicio->cantidad)."</td>
		<td>".Cni::formateaNumero($datosServicio->unitario, true)."</td>
		<td>".Cni::formateaNumero($datosServicio->importe, true)."</td>
		<td>".Cni::formateaNumero($datosServicio->iva)."%</td>
		<td>".Cni::formateaNumero($datosServicio->total, true)."</td>
		</td>
		</tr>";
    return $html;
}
/**
 * Creamos la clase estandar datosServicio, con la propiedades
 */
$datosServicio = new stdClass();
$datosServicio->servicio = null;
$datosServicio->cantidad = null;
$datosServicio->unitario = null;
$datosServicio->importe = null;
$datosServicio->iva = null;
$datosServicio->total = null;

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
	( $inicio!= "00-00-0000") ? "ocupacion puntual" : Cni::$meses[$mesFactura];
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
		$datosServicio->servicio = ucfirst($resultado->servicio)." 
 				".ucfirst($resultado->obs);
 		$datosServicio->cantidad = $resultado->cantidad;
 		$datosServicio->unitario = $resultado->unitario;
 		$datosServicio->importe = $importe;
 		$datosServicio->iva = $resultado->iva;
 		$datosServicio->total = $totalConIva;
		$html .= lineaTabla($datosServicio);
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
					$resultado->unidades,
					$resultado->Imp_Euro,
					$resultado->iva,
					$resultado->observaciones
				);
			}
		}
	}
	/**
	 * Seccion de almacenaje
	 */
    $servicio = new Servicio($fechaFactura);
    $servicio->setServicioByName('Almacenaje');
    $sql = consultaAlmacenaje($cliente, $mesFactura, $inicio, $final);
    $resultados = Cni::consultaPreparada($sql, array(), PDO::FETCH_CLASS);
	foreach ($resultados as $resultado) {
		$importe = $resultado->bultos * $resultado->dias * $servicio->precio;
		$totalConIva = Cni::totalconIva($importe, $servicio->iva);
		$html .= "
 			<tr>
			<td>Bultos Almacenados del " . $resultado->inicio . " al 
					" . $resultado->fin . "
			</td>
			<td>".Cni::formateaNumero($resultado->bultos)."</td>
			<td>".Cni::formateaNumero($servicio->precio, true)."</td>
			<td>".Cni::formateaNumero($importe, true)."</td>
			<td>".Cni::formateaNumero($servicio->iva)."%</td>
			<td>".Cni::formateaNumero($totalConIva, true)."</td>
			</td>
			</tr>";
		$total += $totalConIva;
		$bruto += $importe;
		$cantidad += $resultado->bultos;
		$celdas ++;
		if (!isset($_GET['prueba'])) {
			agregaHistorico(
			$codigo,
			"Bultos Almacenados",
			$resultado->bultos,
			$servicio->precio,
			$servicio->iva,
			" del " . $resultado->inicio . " al " . $resultado->fin
			);
		}
	}
	/**
 	 * Servicios no Agrupados
 	 */
	$sql = "SELECT 
 		d.Servicio AS Servicio, 
 		d.Cantidad AS unidades, 
 		date_format(c.fecha,'%d-%m-%Y') AS fecha, 
		d.PrecioUnidadEuros AS precioUnidad, 
 		d.ImporteEuro AS importe, 
 		d.iva AS iva, 
 		c.`Id Pedido` AS idPedido,
		d.observaciones AS observaciones 
 		FROM `detalles consumo de servicios` as d 
 		INNER JOIN `consumo de servicios` as c 
		ON c.`Id Pedido` = d.`Id Pedido` 
 		WHERE c.Cliente like ? ";
		$sql .= consultaFecha($cliente, $mesFactura, $inicio, $final);
		$sql .= consultaAgrupado($cliente, false);
		$resultados = Cni::consultaPreparada(
					$sql,
					array($cliente),
					PDO::FETCH_CLASS
					);
	foreach ($resultados as $resultado) {
		$importe = $resultado->unidades * $resultado->precioUnidad;
		$totalConIva = Cni::totalconIva($importe, $resultado->iva);
		$html .= "
 			<tr>
			<td>".ucfirst($resultado->Servicio)."
 				".ucfirst($resultado->observaciones)."
			</td>
			<td>".Cni::formateaNumero($resultado->unidades)."</td>
			<td>".Cni::formateaNumero($resultado->precioUnidad, true)."</td>
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
				$resultado->unidades,
				$resultado->importe,
				$resultado->iva,
				$resultado->observaciones
			);
		}
	}
	/**
 	 * Servicios Agrupados
 	 */
	$sql = "SELECT 
		d.Servicio AS Servicio, 
		sum(d.Cantidad) AS unidades,
		date_format(c.fecha,'%d-%m-%Y') AS fecha, 
		d.PrecioUnidadEuros AS precioUnidad, 
		sum(d.ImporteEuro) AS importe, 
		d.iva AS iva, 
		c.`Id Pedido` AS idPedido,
		d.observaciones AS observaciones 
		FROM `detalles consumo de servicios` AS d 
		INNER JOIN `consumo de servicios` AS c 
		ON c.`Id Pedido` = d.`Id Pedido` 
		WHERE c.Cliente LIKE ? ";
	$sql .= consultaFecha($cliente, $mesFactura, $inicio, $final);
	$sql .= consultaAgrupado($cliente, true);
	$resultados = Cni::consultaPreparada(
				$sql,
				array($cliente),
				PDO::FETCH_CLASS
				);
	foreach ($resultados as $resultado) {
		$importe = $resultado->unidades * $resultado->precioUnidad;
		$totalConIva = Cni::totalconIva($importe, $resultado->iva);
		$html .= "
 			<tr>
			<td>".ucfirst($resultado->Servicio)."
 				".ucfirst($resultado->observaciones)."
			</td>
			<td>".Cni::formateaNumero($resultado->unidades)."</td>
			<td>".Cni::formateaNumero($resultado->precioUnidad, true)."</td>
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
			$resultado->unidades,
			$resultado->importe,
			$resultado->iva,
			$resultado->observaciones
			);
		}
	}
	/**
 	* El descuento se calcula del total de los servicios fijos
 	* Esta como un servicio FIJO MENSUAL
 	*/
	$sql = "SELECT razon FROM clientes WHERE id like ? AND razon NOT LIKE ''";
	$resultados = Cni::consultaPreparada(
				$sql,
				array($cliente),
				PDO::FETCH_CLASS
				);
	if (Cni::totalDatosConsulta() > 0) {
		foreach ($resultados as $resultado) {
			$porcentaje = explode("%", $resultado->razon);
			$descuento = ($importeServiciosFijos * $porcentaje[0]) / 100;
			$descuentoConIva = Cni::totalconIva($descuento, IVA);
			$html .= "
 			<tr>
			<td>Descuento del ".$porcentaje[0]."%</td>
			<td>1</td>
			<td>".Cni::formateaNumero($descuento, true)."</td>
			<td>".Cni::formateaNumero($descuento, true)."</td>
			<td>".Cni::formateaNumero(IVA)."%</td>
			<td>".Cni::formateaNumero($descuentoConIva, true)."</td>
			</td>
			</tr>";
			if (!isset($_GET['prueba'])) {
				agregaHistorico(
				$codigo,
				"Descuento",
				"1",
				"-".$descuento,
				IVA,
				"del " .$porcentaje[0]
				);
			}
		}
	} else {
		$descuento = 0;
		$descuentoConIva = 0;
	}
	/**
	 * Para el resultado de pie esta bien
	 */
	$bruto = $bruto - $descuento;
	$total = $total - $descuentoConIva;
}
$totalIva = $total - $bruto;
/**
 * Compensacion del diseño para el llenado del A4
 */
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
	</table>
	<br/>
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
			<th>".Cni::formateaNumero($totalIva, true)."</th>
			<th>&nbsp;</th>
			<th>".Cni::formateaNumero($total, true)."</th>
		</tr>
	</thead>
	</table>";
$html .= pieFactura($cliente, $observaciones, $codigo);
if (($fichero != "PROFORMA") && (!isset($_GET['duplicado']))) {
	if (isset($inicio) && ($final != '00-00-0000')) {
		$puntual = 1;
	}
	$importeIva = Cni::formateaNumero($totalIva);
	$importeTotal = Cni::formateaNumero($total);
	if (compruebaFactura($cliente, $codigo, $fechaFactura, $totalIva, $total)) {
		$params = array(
			$cliente,
			$codigo,
			$fechaFactura,
			$importeIva,
			$importeTotal,
			$observaciones,
			$mesFactura,
			$anoFactura
		);
		$sql = "Insert into regfacturas (
				id_cliente,
				codigo,
				fecha,
				iva,
				importe,
				obs_alt,
				mes,
				ano
				) values (
				?, ?, STR_TO_DATE(?, '%d-%m-%Y'), ?, ?, ?, ?, ?)";
		if ($puntual == 1) {
			$params[] = $fechaInicial;
			$params[] = $fechaFinal;
			$sql = substr($sql, 0, strlen($sql) - 1);
			$sql .= ", STR_TO_DATE(?, '%d-%m-%Y'), STR_TO_DATE(?, '%d-%m-%Y) )";
		}
		Cni::consultaPreparada($sql, $params);
	}
}
echo $html;
?>
</body>
</html>
<!-- Linea final 807 -->
