<?php
/**
 * Facturas.php File Doc Comment
 * 
 * Clase encargada de la generacion y gestion de Facturas
 * 
 * PHP Version 5.2.6
 * 
 * @category inc
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once 'Cni.php';
require_once 'Cliente.php';
class Facturas
{
    public $numeroFactura = null;
    public $nombreFactura = null;
    public $ficheroFactura = null;
    public $tituloFactura = null;
    public $fechaFactura = null;
    public $fechaInicialFactura = '00-00-0000';
    public $fechaFinalFactura = '00-00-0000';
    public $obsFactura = null;
    public $formaPago = null;
    public $obsFormaPago = null;
    public $pedidoCliente = null;
    public $idFactura = null;
    public $servicio = null;
    public $cantidad = null;
    public $unitario = null;
    public $iva = null;
    public $obs = null;
    public $cliente = null;
    public $resultados = false;
    public $totalGlobal = 0;
    public $totalBruto = 0;
    public $totalCantidad = 0;
    public $totalDescuento = 0;
    public $totalFijos = 0;
    public $html = "";
    private $fijos = false;
    private $historico = false;
    private $prueba = false;
    private $numeroLinea = 0;
    /**
     * Constructor Clase, si se le pasa factura la coge del historico
     * si se le pasa duplicado genera duplicado
     * 
     * @param Integer $factura
     * @param Bolean $duplicado
     */
    public function __construct($factura = null, $duplicado = false)
    {
        if (!is_null($factura)) {
            $this->duplicado = $duplicado;
            $this->historico = true;
            $this->idFactura = $factura;
			$this->datosFactura();
			$this->datosHistorico();
        }
    }
    /**
     * Generamos la factura en el caso que no Creada
     * 
     * @param  Array $vars Array con todos los datos para la factura
     * @return [type]       [description]
     */
    public function generacionFactura($vars)
    {
    	if (is_array($vars)) {
    		if (isset($vars['prueba'])) {
    			$this->nombreFactura = "FACTURA (PROFORMA)";
    			$this->tituloFactura = "FACTURA<BR/>PROFORMA";
    			$this->prueba = true;
    		} else {
    			$this->nombreFactura = "FACTURA";
    			$this->tituloFactura = "FACTURA";
    		}
    		$this->cliente = new Cliente($vars['cliente']);
    		$this->fechaFactura = $vars['fechaFactura'];
    		$this->numeroFactura = $vars['codigo'];
    		$this->fechaInicialFactura = $vars['fechaInicialFactura'];
    		$this->fechaFinalFactura = $vars['fechaFinalFactura'];
    		$this->obsFactura = $vars['observaciones'];
    		$this->resultados = false;
    		$this->serviciosFijos();
    		$this->serviciosAlmacenaje();
    		$this->serviciosAgrupados();
    		$this->serviciosNoAgrupados();
    		$this->serviciosDescuento();
    		$this->setDatosFormaPago();
    	} else {
    		return false;
    	}
    }
    /**
     * Recuperamos los datos de una factura ya generada Creada
     */
    private function datosFactura()
    {
    	$sql = "SELECT
			id_cliente AS idCliente,
			DATE_FORMAT(fecha, '%d-%m-%Y') AS fecha,
			codigo as factura,
			DATE_FORMAT(fecha_inicial, '%d-%m-%Y') AS fechaInicial,
			DATE_FORMAT(fecha_final, '%d-%m-%Y') AS fechaFinal,
			obs_alt AS observaciones,
    		fpago AS formaPago,
			obs_fpago AS obsFormaPago,	
			pedidoCliente
			FROM regfacturas
			WHERE id LIKE ?";
    	$resultados = Cni::consultaPreparada(
    		$sql,
    		array($this->idFactura),
    		PDO::FETCH_CLASS
    	);
    	foreach ($resultados as $resultado) {
    		$this->cliente = new Cliente($resultado->idCliente);
    		$this->fechaFactura = $resultado->fecha;
    		$this->numeroFactura = $resultado->factura;
    		$this->fechaInicialFactura = $resultado->fechaInicial;
    		$this->fechaFinalFactura = $resultado->fechaFinal;
    		$this->obsFactura = $resultado->observaciones;
    		$this->formaPago = $resultado->formaPago;
    		$this->obsFormaPago = $resultado->obsFormaPago;
    		$this->pedidoCliente = $resultado->pedidoCliente;
    	}
    	if ($this->duplicado) {
    		$this->nombreFactura = "FACTURA (DUPLICADO)";
    		$this->tituloFactura = "FACTURA<BR/>DUPLICADO";
    	} else {
    		$this->nombreFactura = "FACTURA";
    		$this->tituloFactura = "FACTURA";
    	}
    }
    /**
     * Consulta los datos del historico para comprobar si exsite o no
     * la factura si existe la procesa
     */
    private function datosHistorico()
    {
        $sql = "SELECT * 
                FROM historico
				WHERE factura 
                LIKE ?";
        $this->procesaConsultaServicios($sql, array($this->numeroFactura));
    }
    /**
     * Devuelve los servicios Fijos contratados por el cliente
     */
    private function serviciosFijos()
    {
    	$sql = "SELECT 
    			Servicio AS servicio,
    			unidades AS cantidad,
    			Imp_Euro AS unitario,
    			iva,
    			observaciones AS obs
 				FROM tarifa_cliente 
				WHERE ID_Cliente LIKE ? 
 				ORDER BY Imp_Euro DESC";
    	$this->fijos = true;
    	$this->procesaConsultaServicios($sql, array($this->cliente->idCliente));
    }
    private function getCamposFecha($almacen)
    {
        $campoFecha = new stdClass();
        // Caso almacenaje / servicios cont
        $campoFecha->inicial = ($almacen) ? 'inicio' : 'c.fecha';
        // Caso almacenaje / servicios cont
        $campoFecha->final = ($almacen) ? 'fin' : 'c.fecha';
        return $campoFecha;
    }
    /**
    * [consultaFecha description]
    * @param  boolean $almacen [description]
    * @return boolean|Array           [description]
    */
    private function consultaFecha($almacen = false)
	{
        $campoFecha = $this->getCamposFecha($almacen);
        $sql = false;
        $params = false;
        $diaFacturacion = $this->cliente->diaFacturacionCliente();
        if ($diaFacturacion) {
        	$diaFacturacion = $diaFacturacion ."-" .
    					Cni::verMes($this->fechaFactura) ."-".
    					Cni::verAnyo($this->fechaFactura);
        	// Datos entre dia Facturacion del mes anterior y el de factura
            $sql = " AND 
                    ".$campoFecha->inicial." > 
                    DATE_SUB( 
                        STR_TO_DATE(?, '%d-%m-%Y'), 
                        INTERVAL 1 MONTH
                    ) AND 
                    ".$campoFecha->final." <= 
                    STR_TO_DATE(?, '%d-%m-%Y') ";
            $params = array($diaFacturacion, $diaFacturacion);
        } else {
            if ($this->fechaFinalFactura == '00-00-0000') {
                if ($this->fechaInicialFactura == '00-00-0000') {
                	// Facturacion Normal Mes
                    $sql = "
                        AND MONTH(".$campoFecha->inicial.") 
                        LIKE MONTH( STR_TO_DATE(?, '%d-%m-%Y') )
                        AND YEAR(".$campoFecha->inicial.") 
                        LIKE YEAR( STR_TO_DATE(?, '%d-%m-%Y') ) ";
                    $params = array($this->fechaFactura, $this->fechaFactura);
                } else {
                	// Facturacion puntual dia unico
                    $sql = " 
                    AND ".$campoFecha->inicial." LIKE 
                    STR_TO_DATE(?, '%d-%m-%Y') ";
                    $params = array($this->fechaInicialFactura);
                }
            } else {
                if ($this->fechaInicialFactura != '00-00-0000') {
                    $sql = " 
                        AND ".$campoFecha->inicial."
                        >= STR_TO_DATE(?, '%d-%m-%Y') 
                        AND ".$campoFecha->final."
                        <= STR_TO_DATE(?, '%d-%m-%Y')
                        ";
                    $params = array(
                        $this->fechaInicialFactura,
                        $this->fechaFinalFactura
                    );
                }
            }
        }
        if ($sql && $params) {
		    return array('sql' => $sql, 'params' => $params);
        } else {
            return false;
        }
	}
    /**
     * Consulta si el servicio es o no agrupado
     * @param  boolean $agrupado [description]
     * @return String            [description]
     */
	private function consultaAgrupado($agrupado = false)
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
			array($this->cliente->idCliente),
			PDO::FETCH_CLASS
		);
		if (Cni::totalDatosConsulta() > 0) {
			foreach ($resultados as $resultado) {
				$noAgrupados[] = $resultado->nombre;
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
	 * Procesa la consulta de Servicios y genera las lineas de la factura
	 * 
	 * @param string $sql
	 * @param array $params
	 */
	private function procesaConsultaServicios($sql, $params)
	{
		$resultados = Cni::consultaPreparada(
			$sql,
			$params,
			PDO::FETCH_CLASS
		);
		if (Cni::totalDatosConsulta() > 0 ) {
			$this->resultados = $resultados;
			$this->procesaFactura();
		}
	}
    /**
     * Comprueba los almacenajes contratados por el cliente
     */
    private function serviciosAlmacenaje()
    {
    	$sql = "SELECT 
    			'Bultos Almacenados' AS servicio,
    			(a.bultos * DATEDIFF(a.fin,a.inicio)) AS cantidad,
    			s.PrecioEuro AS unitario, 
     			s.iva AS iva,
     			CONCAT(
    				'del',
    				' ',
    				DATE_FORMAT(a.inicio, '%d-%m-%Y'),
    				' ',
    				'al',
    				' ',
    				DATE_FORMAT(a.fin, '%d-%m-%Y')
    			) AS obs
     			FROM z_almacen AS a, servicios2 as s
     			WHERE a.cliente LIKE ?
     			AND s.Nombre like '%Almacenaje%' ";
        $filtroFecha = $this->consultaFecha(true);
        $sql .= $filtroFecha['sql'];
        $params = array_merge(
        	array($this->cliente->idCliente),
        	$filtroFecha['params']
        );
    	$this->procesaConsultaServicios($sql, $params);
    }
    /**
     * Comprueba los servicios no Agrupados
     */
    private function serviciosNoAgrupados()
    {
    	$sql = "SELECT
 		d.Servicio AS servicio,
 		d.Cantidad AS cantidad,
 		date_format(c.fecha,'%d-%m-%Y') AS fecha,
		d.PrecioUnidadEuros AS unitario,
 		d.ImporteEuro AS importe,
 		d.iva AS iva,
 		c.`Id Pedido` AS idPedido,
		d.observaciones AS obs
 		FROM `detalles consumo de servicios` as d
 		INNER JOIN `consumo de servicios` as c
		ON c.`Id Pedido` = d.`Id Pedido`
 		WHERE c.Cliente like ? ";
    	$filtroFecha = $this->consultaFecha();
    	$sql .= $filtroFecha['sql'];
    	$sql .= $this->consultaAgrupado(false);
    	$params = array_merge(
    		array($this->cliente->idCliente),
    		$filtroFecha['params']
    	);
    	$this->procesaConsultaServicios($sql, $params);
    }
    /**
     * Comprueba los servicios Agrupados
     */
    private function serviciosAgrupados()
    {
    	$sql = "SELECT
		d.Servicio AS servicio,
		sum(d.Cantidad) AS cantidad,
		date_format(c.fecha,'%d-%m-%Y') AS fecha,
		d.PrecioUnidadEuros AS unitario,
		sum(d.ImporteEuro) AS importe,
		d.iva AS iva,
		c.`Id Pedido` AS idPedido,
		d.observaciones AS obs
		FROM `detalles consumo de servicios` AS d
		INNER JOIN `consumo de servicios` AS c
		ON c.`Id Pedido` = d.`Id Pedido`
		WHERE c.Cliente LIKE ? ";
    	$filtroFecha = $this->consultaFecha();
    	$sql .= $filtroFecha['sql'];
    	$sql .= $this->consultaAgrupado(true);
    	$params = array_merge(
    		array($this->cliente->idCliente),
    		$filtroFecha['params']
    	);
    	$this->procesaConsultaServicios($sql, $params);
    }
    /**
     * Comprueba el descuento del cliente
     */
    private function serviciosDescuento()
    {
    	//TODO Aplicar el descuento
    	$sql = "SELECT razon 
    			FROM clientes 
    			WHERE id like ? 
    			AND razon NOT LIKE ''";
    	$resultados = Cni::consultaPreparada(
    		$sql,
    		array($this->cliente->idCliente),
    		PDO::FETCH_CLASS
    	);
    	if (Cni::totalDatosConsulta() > 0 ) {
    		foreach ($resultados as $resultado) {
    			$porcentaje = explode("%", $resultado->razon);
    			$importeDescuento = ($this->totalFijos * $porcentaje[0]) / 100;
    			$descuento = new stdClass();
    			$descuento->servicio = "Descuento del ".$porcentaje[0]."%";
    			$descuento->cantidad = 1;
    			$descuento->unitario = $importeDescuento;
    			$descuento->importe = $importeDescuento;
    			$descuento->iva = IVA;
    			$descuento->obs = "";
    		}
    		$this->totalDescuento = $importeDescuento;
    		$this->resultados = $descuento;
    		$this->procesaFactura();
    	}
    }
    /**
     * Consulta los datos de Pago y los establece
     * @deprecated Usar datos de la clase cliente
     */
    private function setDatosFormaPago()
    {
		if (is_null($this->formaPago)) {
	    	$sql = "SELECT
					fpago AS formaPago,
					cc AS obsFormaPago,
					dpago AS pedidoCliente
			    	FROM facturacion
					WHERE idemp LIKE ?";
	    	$resultados = Cni::consultaPreparada(
	    		$sql,
	    		array($this->cliente->idCliente),
	    		PDO::FETCH_CLASS
	    	);
	    	foreach ($resultados as $resultado) {
	    		$this->formaPago = $resultado->formaPago;
	    		$this->obsFormaPago = $resultado->obsFormaPago;
	    		$this->pedidoCliente = $resultado->pedidoCliente;
	    	}
	    	$pagoCC = array("Cheque","Contado","Tarjeta credito","Liquidación");
	    	$pagoNCC = array("Cheque");
	    	if (!in_array($this->formaPago, $pagoCC)) {
	    		$this->formapago = "Cuenta: ". $this->formaPago;
	    	} elseif (in_array($this->formaPago, $pagoNCC)) {
	    		$this->obsFormaPago = "Vencimiento: ". $this->obsFormaPago;
    		}
		}
    }
    /**
     * Agrega los datos al historico si no estan agregados
     * 
     * @return boolean
     */
    private function agregaHistorico()
    {
        $sql = "INSERT 
                INTO historico 
                (factura, servicio, cantidad, unitario, iva, obs)
				VALUES (?, ?, ?, ?, ?, ?)";
        $params = array(
                $this->numeroFactura,
                $this->servicio,
                $this->cantidad,
                $this->unitario,
                $this->iva,
                $this->obs
                );
        Cni::consultaPreparada($sql, $params);
        return true;
    }
    /**
     * Agrega la factura al registro de facturas
     */
    private function agregaRegFacturas()
    {
    	//TODO Agrega los datos de la factura a regfacturas
    }
    /**
     * Devuelve la linea de la factura y si no esta en historico
     * y no es una prueba lo agrega al historico
     * 
     * @return string
     */
    private function lineaFactura()
    {
        $importe = $this->unitario * $this->cantidad;
        $total = Cni::totalconIva($importe, $this->iva);
        $html = "
 		<tr>
		<td>".$this->servicio." ".$this->obs."</td>
		<td>".Cni::formateaNumero($this->cantidad)."</td>
		<td>".Cni::formateaNumero($this->unitario, true)."</td>
		<td>".Cni::formateaNumero($importe, true)."</td>
		<td>".Cni::formateaNumero($this->iva)."%</td>
		<td>".Cni::formateaNumero($total, true)."</td>
		</tr>";
        $this->totalBruto += $importe;
        $this->totalCantidad += $this->cantidad;
        $this->totalGlobal += $total;
        $this->totalFijos += ($this->fijos) ? $importe : 0;
        $this->numeroLinea ++;
        if (!$this->historico && !$this->prueba) {
            $this->agregaHistorico();
        }
        return $html;
    }
    /**
     * Procesa las lineas de la factura
     * 
     * @return unknown
     */
    private function procesaFactura()
    {
    	if ($this->resultados) {
    		foreach ($this->resultados as $resultado) {
            	$this->servicio = ucfirst(trim($resultado->servicio));
            	$this->cantidad = $resultado->cantidad;
            	$this->unitario = $resultado->unitario;
           		$this->iva = $resultado->iva;
            	$this->obs = $resultado->obs;
            	$this->html .= $this->lineaFactura();
        	}
    	}
    	$this->fijos = false;
        return true;
    }
    /**
     * Compensacion del diseño para el llenado del A4
     * 
     * @return [type] [description]
     */
    private function compensacionEspacio()
    {
        $coeficiente = 432 - ( $this->numeroLinea - 1 ) * 18;
        if ($coeficiente >= 1) {
            $this->html .= "<tr>
                <td height='".$coeficiente."px'>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>";
        }
        return true;
    }
    /**
     * Devuelve la factura ya generada
     * 
     * @return string
     */
    public function presentaFactura()
    {
    	$this->html .= $this->compensacionEspacio();
    	return $this->html;
    }
}
 