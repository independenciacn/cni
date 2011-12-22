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
	$resultados = consultaGenerica($sql);
	if ( count($resultados)!=0)
		foreach ( $resultados as $resultado ) {
			$pila[ ]= $resultado[0];
			$i++;
		}
		$cadena = "and (";
		for ($j=0;$j<=count($pila)-1;$j++) {
			$cadena .= " d.Servicio not like '".$pila[$j]."' ";
			if ($j!=count($pila)-1) { $cadena .= " and "; }
		}
		$cadena .=") group by d.Servicio 
		order by d.ImporteEuro desc , d.Servicio asc";
		return $cadena;
}
/**
 * Generamos la cabezera de la factura
 *
 * @param string $nombre_fichero
 * @param string $fecha_factura
 * @param string $codigo
 * @param string $cliente
 * @return string $cabezera
 */
function cabezeraFactura( $nombre_fichero, $fecha_factura, $codigo, $cliente )
{
	global $meses;
	$fecha_factura = explode("-",$fecha_factura);
	$fecha_de_factura = $fecha_factura[2] 
			. " de ". $meses[ intval($fecha_factura[1]) ]
			." de ". $fecha_factura[0];
	$sql = "Select * from clientes where id like " .$cliente;
	$resultado = consultaUnica($sql);
	$cabezera = "
	<br/><br/><br/>
	<div class='titulo'>".strtoupper($nombre_fichero)."</div><br/>
	<div class='cabezera'>
	<table width='100%'>
	<tr>
	<td  align='left' class='celdilla_sec'>
	<br/>FECHA:". $fecha_de_factura . "
	<br/>";
	if($nombre_fichero =='PROFORMA') {
		$cabezera .= "<br/>" . $nombre_fichero;
	} else {
		$cabezera .= "<br/>N&deg;" . $nombre_fichero.":".$codigo;
	}
	$cabezera .= "</td>
	<td  class='celdilla_imp'>
	".strtoupper($resultado[1])."<br>
	".$resultado[6]."<br>
	".$resultado[8]."&nbsp;&nbsp;-&nbsp;&nbsp;".$resultado[7]."<br>
	NIF:".$resultado[5]."
	</td></tr></table></div><br/>";
	return $cabezera;
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
	$pie_factura = "";
	// Con estos tipos de formas de pago aparecera
	$pagoCC = array("Cheque","Contado","Tarjeta credito","Liquidación");
	$pagoNCC = array("Cheque");
	/*
	 * Comprobamos si esta metido dentro de regfacturas,
	* si no lo consultamos, lo metemos y lo mostramos
	*/
	$sql="Select * from regfacturas where codigo like '" . $codigo ."'";
	$resultado = consultaGenerica($sql);
	$camposPie = array( 0=>'fpago', 1=>'obs_fpago', 2=>'obs', 3=>'pedidoCliente');
	//$camposPieFac = array( 0=>'fpago', 1=>'cc', 2=>'obs', 3=>'dpago');
	// Si es 1 la factura esta dada de alta
	if ( count( $resultado )!= 0 ) {
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
			$resultado = consultaGenerica($sql);
			if ( count( $resultado ) != 0  ) {
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
				ejecutaConsulta($sql);
			}
		}
		$pie_factura = "<br/>
		<div class='celdia_sec'>
		Forma de pago: ". $valoresPie['fpago'] ."<br/>" .
		$valoresPie['obs_fpago']."<br/>" .
		$valoresPie['pedidoCliente'] .
		observacionesEspeciales( $cliente, $codigo ) .
		"</div>";
	}
	return $pie_factura;
}
//GENERA LA CONSULTA DEL ALMACENAJE DEPENDIENDO DE 
//LOS PARAMETROS DE AGRUPA_FACTURA
function consultaAlmacenaje( $vars )
{
	//$cliente, $mes, $inicial, $final
	$datos = array();
	$sql = "Select iva, PrecioEuro from servicios2 
	WHERE nombre like 'Almacenaje'";
	$valor = consultaUnica($sql);
	
	if( isset($vars['fecha_inicial_factura']) && isset($vars['fecha_final_factura'])) {
		if ( strlen($vars['fecha_inicial_factura'] == 10 ) ) {
			$inicial = cambiaf( $vars['fecha_inicial_factura'] );
		} else {
			$inicial = "0000-00-00";
		}
		if ( strlen($vars['fecha_final_factura'] == 10 ) ) {
			$final = cambiaf( $vars['fecha_final_factura'] );
		} else {
			$final = "0000-00-00";
		}
	} else {
		$inicial = "0000-00-00";
		$final = "0000-00-00";
	}
	$sql = "Select bultos, datediff(fin,inicio),
	date_format(inicio,'%d-%m-%Y'),
	date_format(fin,'%d-%m-%Y')
	FROM z_almacen where cliente like ".$vars['idCliente']." ";
	if(($inicial == '0000-00-00') && ($final == '0000-00-00')) {
		$agrupado = "Select * from agrupa_factura where concepto like 'dia' 
		and idemp like ".$vars['idCliente']." and valor not like ''" ;
		if ( totalCeldas( $agrupado ) !=0 ) {
			$resultado = consultaUnica($agrupado);
			$sql .= " 
			and (month(inicio) like (".$vars['mes']."-1) 
			and month(fin) like ".$vars['mes']." 
			and day(inicio) >= ". $resultado['valor']."  
			and  day(fin) <= ". $resultado['valor'] ." 
			and year(inicio) like year(curdate()) 
			and year(fin) like year(curdate()))";
		} else {
			$sql .= "
			and month(fin) like ".$vars['mes']." and year(fin) like year(curdate())";
		}
	} else {
		if(($inicial != "" ) && ($final != "")){
			$sql .= " and month(fin) like month('".$final."') 
			and year(fin) like year('".$final."')";
		} else {
			$sql .= " and fin <= '".$final."'";
		}
	}
	$resultados = consultaGenerica($sql);
	foreach( $resultados as $resultado ){
		$datos[] = array(
				'servicio' => "Bultos Almacenados del  ".$resultado[2] . " al ".$resultado[3],
				'cantidad' => $resultado[0],
				'unitario' => $valor['PrecioEuro'] * $resultado[1],
				'iva'	=> $valor['iva'],
				'obs'	=> ''
		);
	}
	return $datos;
}
/**
 * Consulta si la factura esta en el historico devuelve ok o ko
 *
 * @param string $factura
 * @return string
 */
function historico( $factura )
{
	$sql = "Select * from historico where factura like " . $factura;
	$resultado = totalCeldas($sql);
	if ( $resultado !=0 ) {
		return "ok";
	} else {
		return "ko";
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
function agregaHistorico( $factura, $servicio, $cantidad, $unitario, $iva, $obs )
{
	$servicio = trim( $servicio );//Eliminamos espacios en blanco al principio y final
	$sql = "Insert into historico 
	(factura,servicio,cantidad,unitario,iva,obs)
	values
	('".$factura."','".$servicio."','".$cantidad."',
	'".$unitario."','".$iva."','".$obs."')";
	ejecutaConsulta($sql);
}
/**
 * Comprueba la factura
 * @param unknown_type $cliente
 * @param unknown_type $codigo
 * @param unknown_type $fecha
 * @param unknown_type $total_iva
 * @param unknown_type $total
 */
function compruebaFactura( $cliente,$codigo,$fecha,$total_iva,$total )
{
	$sql = "Select * from regfacturas 
	where id_cliente like ".$cliente." 
	and codigo like ".$codigo." 
	and fecha like '".$fecha."'";
	if ( totalCeldas( $sql ) == 0 ) {
		return true;
	} else {
		$resultado = consultaUnica( $sql );
		if (($resultado['iva']!=$total_iva) && ($resultado['importe']!=$total)) {
			$sql = "Update regfacturas 
			set iva='".$total_iva."',importe='".$total."' 
			where id_cliente like '".$cliente."' 
			and codigo like '".$codigo."' and fecha like '".$fecha."'";
			ejecutaConsulta($sql);
		}
		return false;
	}
}
//FUNCIONES NUEVAS
/**
 * Devuelve los datos de la factura del fichero historico en base al id
 * de registro de facturas
 * @param unknown_type $codigo
 */
function datosHistorico( $codigo, $campo ) {
	$sql = "Select h.* from historico as h 
	INNER JOIN regfacturas r on r.codigo = h.factura AND
	r.".$campo." = ".$codigo;
	return consultaGenerica( $sql, MYSQL_ASSOC );
}
/**
 * Devuelve los datos del cliente en base al id del registro de facturas
 * @param unknown_type $codigo
 * @return Ambigous <multitype:, boolean>
 */
function datosCliente( $codigo, $campo ) {
	$sql = "Select c.* from clientes as c
	INNER JOIN regfacturas r on r.id_cliente = c.id AND
	r.id = ".$codigo;
	return consultaUnica( $sql, MYSQL_ASSOC );
}
/**
 * Devuelve los datos de la factura
 * @param unknown_type $codigo
 */
function datosFactura( $codigo, $campo ) {
	$sql = "Select * from regfacturas WHERE ".$campo." like ".$codigo;
	return consultaUnica( $sql, MYSQL_ASSOC );
}
/**
 * Devuelve los servicios que tiene fijos mensualmente el cliente
 * @param unknown_type $cliente
 * @return Ambigous <multitype:, multitype:multitype: >
 */
function fijosMensuales( $cliente ) {
	$sql = "Select Servicio as servicio, Imp_Euro as unitario, iva, 
	unidades as cantidad, observaciones as obs
	FROM tarifa_cliente WHERE ID_Cliente like ".$cliente;
	return consultaGenerica($sql);
}
/**
 * Devuelve los servicios no agrupados que tiene el cliente
 * @param array $vars
 * @return array $resultados
 */
function serviciosNoAgrupados( $vars ) {
    $fecha = fechaInicioFin($vars);
    $sql = "Select d.Servicio as servicio, 
    d.Cantidad as cantidad, date_format(c.fecha,'%d-%m-%Y') as fecha,
    d.PrecioUnidadEuros as unitario, d.ImporteEuro as importe, 
    d.iva as iva,
    d.observaciones as obs 
    FROM `detalles consumo de servicios` as d 
    INNER JOIN `consumo de servicios` as c
    on c.`Id Pedido` = d.`Id Pedido` 
    WHERE c.Cliente like ".$vars['idCliente']." ";
    $sql .= consultaFecha(
        $vars['idCliente'],
        $vars['mes'],
        $fecha['inicio'],
        $fecha['final']
    );
    $sql .= consultaNoAgrupado( $vars['idCliente'] );
    $resultados = consultaGenerica($sql);
    return $resultados;
}
/**
 * Devuelve los servicios agrupados que tiene el cliente
 * @param array $vars
 * @return array $resultados
 */
function serviciosAgrupados( $vars ) {
    $fecha = fechaInicioFin( $vars );
    $sql = "Select d.Servicio as servicio, 
    sum(d.Cantidad) as cantidad, date_format(c.fecha,'%d-%m-%Y') as fecha,
    d.PrecioUnidadEuros as unitario, sum(d.ImporteEuro) as importe, 
    d.iva as iva, d.observaciones as obs
    FROM `detalles consumo de servicios` as d 
    INNER JOIN `consumo de servicios` as c
    on c.`Id Pedido` = d.`Id Pedido` 
    WHERE c.Cliente like ".$vars['idCliente']." ";
    $sql .= consultaFecha(
        $vars['idCliente'],
        $vars['mes'],
        $fecha['inicio'],
        $fecha['final']
    );
    $sql .= consultaAgrupado( $vars['idCliente'] );
    $resultados = consultaGenerica($sql);
    return $resultados;
}
/**
 * Devuelve la fecha inicial y la final del rango
 * @param array $vars
 * @return array $vars
 */
function fechaInicioFin( $vars ) {
    if ( !isset( $vars['fecha_inicial_factura'] ) && !isset($vars['fecha_final_factura'])) {
        $fecha['inicio'] = "0000-00-00";
        $fecha['final'] = "0000-00-00";
    } else {
        $fecha['inicio'] = $vars['fecha_inicial_factura'];
        $fecha['final'] = $vars['fecha_final_factura'];
    }
    return $fecha;
}

function descuento( $cliente ) {
    $sql = "Select razon as descuento from clientes where id like ".$cliente;
    if ( totalCeldas($sql) == 1 ){
        $resultado = consultaUnica($sql, MYSQL_ASSOC);
        $porcentaje = explode("%",$resultado['descuento']);
        return $porcentaje[0];
    } else {
        return false;
    }
    
}
/**
 * Si estamos generando la factura, procesa los datos y los ingresa en la
 * base de datos, si no solo los muestra por pantalla (proforma)
 * @param unknown_type $factura
 * @param unknown_type $datos
 * @param unknown_type $porcentaje
 * @param unknown_type $fichero
 * @return multitype:string unknown number |Ambigous <multitype:, multitype:multitype: >
 */
function procesaHistorico( $factura, $datos, $porcentaje, $fichero ) {
    $bruto = 0;
    foreach( $datos as $dato ) {
        if( $fichero == 'false' ) {
            agregaHistorico( $factura, 
                $dato['servicio'], 
                $dato['cantidad'], 
                $dato['unitario'], 
                $dato['iva'],
                $dato['obs']
            );
           $bruto = $bruto + $dato['cantidad'] * $dato['unitario']; 
        }
    }
    if ( $porcentaje ) {
        $descuento = $bruto * $porcentaje/100;
        if ( $fichero == 'false' ) {
            agregaHistorico( $factura,
                "Descuento",
                "1",
                ($descuento*-1),
                $conf['iva'],
                "del ".$porcentaje 
            );
        } else {
           $datos[] = array(
               "Descuento",
               "1",
               ($descuento*-1),
               $conf['iva'],
               "del ".$porcentaje
           ); 
        }
    }
    if ( $fichero == 'true') {
        return $datos;
    } else {
        return datosHistorico($factura,'codigo');
    }
}