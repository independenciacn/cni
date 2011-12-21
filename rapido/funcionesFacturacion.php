<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
	notFound();
}
/**
 * Devuelve las observaciones especiales de la factura
 * @param integer $cliente
 * @param integer $factura
 * @return string
 */
function observacionesEspeciales( $cliente, $factura )
{
	$sql = "Select obs_alt, pedidoCliente 
	from regfacturas where codigo like $factura 
	and obs_alt is not null";
	$resultado = consultaUnica($sql,MYSQL_ASSOC );
	if ( count($resultado)  !=0 ) {
		$obser = $resultado['obs_alt'];
		$pedidoCliente = "<br/>" . $resultado['pedidoCliente'];
	} else {
		$obser = "";
		$pedidoCliente = "";
	}
	return $obser . $pedidoCliente ;
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
function consultaFecha($cliente,$mes,$inicial,$final) //consulta los rangos de la fecha
{
	$check1=$inicial{4};
	$check2=$final{4};
	if ($check1!='-') { $inicial = cambiaf( $inicial ); }
	if ($check2!='-') { $final = cambiaf($final); }
	if ($inicial!='0000-00-00') {
		if ( ($final!="0000-00-00") && ($final!="--") && ($final!="") ) {
			$cadena .= " and datediff(c.fecha,'" . $inicial ."') >= 0 
			and datediff(c.fecha,'" . $final ."') <=0 ";
		} else {
			$cadena = " and c.fecha like '$inicial' ";
		}
	} else {
		//include("../inc/variables.php");
		$sql = "Select valor from agrupa_factura 
		where idemp like ". $cliente ." and concepto like 'dia'";
		$resultado = consultaUnica($sql);
		if ( count($resultado) != 0 ) {
			if($resultado[0]!="") {
				$mes_ant = $mes - 1;
				$fecha_inicial = date('Y')."-".$mes_ant."-".$resultado[0];
				$fecha_final = date('Y')."-".$mes."-".$resultado[0];
				$cadena =" and (c.fecha > '".$fecha_inicial."' 
				and c.fecha <= '".$fecha_final."')";
			} else {
				$cadena =" and (date_format(curdate(),'%Y')
				like date_format(c.fecha,'%Y') and '".$mes."' 
				like date_format(c.fecha,'%c')) ";
			}
		} else {
			$cadena=" and (date_format(curdate(),'%Y')
			like date_format(c.fecha,'%Y') and '".$mes."' 
			like date_format(c.fecha,'%c')) ";
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
function consultaNoAgrupado( $cliente )
{
	global $agrupados;
	$pila = $agrupados;
	$i=5;
	$sql = "Select s.Nombre,a.valor from 
	agrupa_factura as a join servicios2 as s on a.valor = s.id where a.idemp 
	like ".$cliente." and a.concepto like 'servicio'";
	$resultados = consultaGenerica($sql);
	if ( count($resultados) !=0 ) {
		foreach ( $resultados as $resultado ) {
				$pila[]=$resultado[0];
				$i++;
		}
	}
	$cadena = "and (";
	for($j=0;$j<=count($pila)-1;$j++) {
			$cadena .= " d.Servicio like '".$pila[$j]."' ";
			if ( $j != count($pila)-1 ) {
				$cadena .= " or ";
			}
	}
	$cadena .=") order by d.ImporteEuro desc , d.Servicio asc";
	return $cadena;
}

//Genaracion de consulta de los agrupamientos
/**
 * Generacion de los Agrupados
 * @param unknown_type $cliente
 */
function consultaAgrupado( $cliente )
{
	global $agrupados;
	$pila = $agrupados;
	$i=5;
	$sql = "Select s.Nombre,a.valor from agrupa_factura as a 
	join servicios2 as s on a.valor = s.id where a.idemp 
	like ".$cliente." and a.concepto like 'servicio'";
	$consulta = mysql_query( $sql, $con );
	if(mysql_numrows($consulta)!=0)
		while($resultado = mysql_fetch_array($consulta))
		{
			$pila[]=$resultado[0];
			$i++;
		}
		$cadena = "and (";
		for($j=0;$j<=count($pila)-1;$j++)
		{
		$cadena .= " d.Servicio not like '$pila[$j]' ";
		if ($j!=count($pila)-1)
			$cadena .= " and ";
		}
		$cadena .=") group by d.Servicio order by d.ImporteEuro desc , d.Servicio asc";
		return $cadena;
}