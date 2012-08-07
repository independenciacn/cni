<?php
require_once 'Cni.php';
require_once 'Cliente.php';
class Facturas
{
    public $numeroFactura = null;
    public $idCliente = null;
    public $nombreFactura = null;
    public $ficheroFactura = null;
    public $tituloFactura = null;
    public $fechaFactura = null;
    public $fechaDeFactura = null;
    public $fechaInicialFactura = '00-00-0000';
    public $fechaFinalFactura = '00-00-0000';
    public $diaFacturacion = false;
    public $obsFactura = null;
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
    public $html = "";
    private $historico = false;
    private $prueba = false;
    /**
     * 
     * @param unknown_type $factura
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
     * Generamos la factura en el caso que no halla sido creada
     * @param array $vars
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
    		$this->diaFacturacion();
    		$this->idCliente = $vars['cliente'];
    		$this->fechaFactura = $vars['fechaFactura'];
    		$this->numeroFactura = $vars['codigo'];
    		$this->fechaInicialFactura = $vars['fechaInicialFactura'];
    		$this->fechaFinalFactura = $vars['fechaFinalFactura'];
    		$this->obsFactura = $vars['observaciones'];
    		$this->cliente = new Cliente($this->idCliente);
    		$this->fechaDeFactura = $this->fechaDeFactura();
    		$this->resultados = false;
    		$this->serviciosFijos();
    		$this->serviciosAlmacenaje();
    		$this->serviciosAgrupados();
    		$this->serviciosNoAgrupados();
    		$this->serviciosDescuento();
    	} else {
    		die('Son necesarios los parametros');
    	}
    }
    /**
     * Devuelve en modo presentacion la fecha de la factura
     * 
     * @return string
     */
    private function fechaDeFactura()
    {
    	return Cni::verDia($this->fechaFactura) .
    	" de ". Cni::$meses[Cni::verMes($this->fechaFactura)] .
    	" de " . Cni::verAnyo($this->fechaFactura);
    }
    /**
     * Recuperamos los datos de una factura ya generada
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
			pedidoCliente
			FROM regfacturas
			WHERE id LIKE ?";
    	$resultados = Cni::consultaPreparada(
    			$sql,
    			array($this->idFactura),
    			PDO::FETCH_CLASS
    			);
    	foreach ($resultados as $resultado) {
    		$this->idCliente = $resultado->idCliente;
    		$this->fechaFactura = $resultado->fecha;
    		$this->numeroFactura = $resultado->factura;
    		$this->fechaInicialFactura = $resultado->fechaInicial;
    		$this->fechaFinalFactura = $resultado->fechaFinal;
    		$this->obsFactura = $resultado->observaciones;
    		$this->pedidoCliente = $resultado->pedidoCliente;
    	}
    	$this->cliente = new Cliente($this->idCliente);
    	$this->fechaDeFactura = $this->fechaDeFactura();
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
    	$this->procesaConsultaServicios($sql, array($this->idCliente));
    }
    /**
     * Comprueba si el cliente tiene dia de facturacion
     */
    private function diaFacturacion()
    {
    	$sql = "SELECT *
				FROM agrupa_factura
				WHERE concepto LIKE 'dia'
				AND idemp LIKE ?
				AND valor NOT LIKE '' " ;
    	$resultados = Cni::consultaPreparada(
    			$sql,
    			array($this->idCliente),
    			PDO::FETCH_CLASS
    	);
    	if (Cni::totalDatosConsulta() > 0 ) {
    		foreach ($resultados as $resultado) {
    			$this->diaFacturacion = $resultado->valor ."-" .
    					Cni::verMes($this->fechaFactura) ."-".
    					Cni::verAnyo($this->fechaFactura);
    		}
    	}
    }
    private function consultaFecha()
	{
		$sql = "";
		/**
		 * Si hay fecha inicial y final se factura en el rango
		 */
		if ($this->fechaInicialFactura != '00-00-0000') {
			if ($this->fechaFinalFactura != '00-00-0000') {
				$sql = " 
					AND c.fecha
					>= STR_TO_DATE(?, '%d-%m-%Y') 
					AND c.fecha
					<= STR_TO_DATE(?, '%d-%m-%Y')
					";
				$params = array(
					$this->fechaInicialFactura,
					$this->fechaFinalFactura
					);
			} else {
				/**
			 	 * Si esta la inicial se factura sol del dia
			 	 */
				$sql = " 
					AND c.fecha LIKE 
					STR_TO_DATE(?, '%d-%m-%Y') ";
				$params = array($this->fechaInicialFactura);
			}
		} else {
			/**
			 * Si el cliente tiene dia de facturacion se calcula lo consumido
			 * en ese periodo
			 */
			if ($this->diaFacturacion) {
				$sql = "
    				AND
    				c.fecha > DATE_SUB(
    					STR_TO_DATE(?, '%d-%m-%Y'),
    					INTERVAL 1 MONTH
    					)
    				AND
    				c.fecha <= STR_TO_DATE(?, '%d-%m-%Y')";
				$params = array($this->diaFacturacion, $this->diaFacturacion);
			} else {
				/**
				 * En cualquier otro caso
				 */
				$sql = "
					AND MONTH(c.fecha) 
    				LIKE MONTH( STR_TO_DATE(?, '%d-%m-%Y') )
					AND YEAR(c.fecha) 
    				LIKE YEAR( STR_TO_DATE(?, '%d-%m-%Y') ) ";
				$params = array($this->fechaFactura, $this->fechaFactura);
			}
		}
		return array('sql' => $sql, 'params' => $params);
	}
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
			array($this->idCliente),
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
     			WHERE a.cliente LIKE 28
     			AND s.Nombre like '%Almacenaje%' ";
    	if (($this->fechaInicialFactura == '00-00-0000')
    			&& ($this->fechaFinalFactura == '00-00-0000')) {
    		if ($this->diaFacturacion) {
    			$sql .= " 
    				AND	
    				inicio >= DATE_SUB(
    					STR_TO_DATE(?, '%d-%m-%Y'),
    					INTERVAL 1 MONTH
    					) 
    				AND
    				fin <= STR_TO_DATE('?', '%d-%m-%Y')";
    			$params = array($this->diaFacturacion, $this->diaFacturacion);
    		} else {
    			$sql .= "
    				AND MONTH(fin) 
    				LIKE MONTH( STR_TO_DATE(?, '%d-%m-%Y') )
					AND YEAR(fin) 
    				LIKE YEAR( STR_TO_DATE(?, '%d-%m-%Y') ) ";
    			$params = array($this->fechaFactura, $this->fechaFactura);
    		}
    	} else {
    		if (($this->fechaInicialFactura != '00-00-0000')
    			&& ($this->fechaFinalFactura != '00-00-0000')) {
    			$sql .= "
    				AND MONTH(fin) 
    				LIKE MONTH( STR_TO_DATE(?, '%d-%m-%Y') )
					AND YEAR(fin) 
    				LIKE YEAR( STR_TO_DATE(?, '%d-%m-%Y') ) ";
    			$params = array(
    					$this->fechaFinalFactura,
    					$this->fechaFinalFactura
    					);
    		} else {
    			$sql .= "AND fin <= STR_TO_DATE(?, %d-%m-%Y)";
    			$params = array($this->fechaFinalFactura);
    		}
    	}
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
		d.observaciones AS observaciones
 		FROM `detalles consumo de servicios` as d
 		INNER JOIN `consumo de servicios` as c
		ON c.`Id Pedido` = d.`Id Pedido`
 		WHERE c.Cliente like ? ";
    	$filtroFecha = $this->consultaFecha();
    	$sql .= $filtroFecha['sql'];
    	$sql .= $this->consultaAgrupado(false);
    	$params = array_merge(array($this->idCliente), $filtroFecha['params']);
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
		d.observaciones AS observaciones
		FROM `detalles consumo de servicios` AS d
		INNER JOIN `consumo de servicios` AS c
		ON c.`Id Pedido` = d.`Id Pedido`
		WHERE c.Cliente LIKE ? ";
    	$filtroFecha = $this->consultaFecha();
    	$sql .= $filtroFecha['sql'];
    	$sql .= $this->consultaAgrupado(true);
    	$params = array_merge(array($this->idCliente), $filtroFecha['params']);
    	$this->procesaConsultaServicios($sql, $params);
    }
    /**
     * Comprueba el descuento del cliente
     */
    private function serviciosDescuento()
    {
    	//TODO
    	$sql = "SELECT razon FROM clientes WHERE id like ? AND razon NOT LIKE ''";
    	$resultados = Cni::consultaPreparada(
    			$sql,
    			array($this->idCliente),
    			PDO::FETCH_CLASS
    	);
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
    	//TODO
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
		</td>
		</tr>";
        $this->totalBruto += $importe;
        $this->totalCantidad += $this->cantidad;
        $this->totalGlobal += $total;
        if (!$this->historico && !$this->prueba) {
            $this->agregaHistorico();
        }
        return $html;
    }
    /**
     * Genera la linea de los totales para la presentacion en pantalla
     * 
     * @return string
     */
    private function lineaTotales()
    {
    	$html = "<tfoot>
    		<tr>
    		<th></th>
    		<th>" .Cni::formateaNumero($this->totalCantidad, false) ."</th>
    		<th></th>
    		<th>" .Cni::formateaNumero($this->totalBruto, true) ."</th>
    		<th></th>
    		<th>" .Cni::formateaNumero($this->totalGlobal, true)."</th>
    		</tr>
    		</tfoot>";
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
    }
    /**
     * Devuelve la factura ya generada
     * 
     * @return string
     */
    public function presentaFactura()
    {
    	$this->html .= $this->lineaTotales();
    	return $this->html;
    }
}