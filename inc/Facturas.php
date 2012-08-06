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
        }
        $this->datosHistorico();
    }
    public function generacionFactura($vars)
    {
    	$this->idCliente = $vars['cliente'];
    	$this->fechaFactura = $vars['fechaFactura'];
    	$this->numeroFactura = $vars['codigo'];
    	$this->fechaInicialFactura = $vars['fechaInicialFactura'];
    	$this->fechaFinalFactura = $vars['fechaFinalFactura'];
    	$this->obsFactura = $vars['observaciones'];
    	$this->cliente = new Cliente($this->idCliente);
    	$this->fechaDeFactura = $this->fechaDeFactura();
    	if (isset($vars['prueba'])) {
    		$this->nombreFactura = "FACTURA (PROFORMA)";
    		$this->tituloFactura = "FACTURA<BR/>PROFORMA";
    		$this->prueba = true;
    	} else {
    		$this->nombreFactura = "FACTURA";
    		$this->tituloFactura = "FACTURA";
    	}
    }
    private function fechaDeFactura()
    {
    	return Cni::verDia($this->fechaFactura) .
    	" de ". Cni::$meses[Cni::verMes($this->fechaFactura)] .
    	" de " . Cni::verAnyo($this->fechaFactura);
    }
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
     * Consulta los datos del historico
     */
    private function datosHistorico()
    {
        $sql = "SELECT * 
                FROM historico
				WHERE factura 
                LIKE ?";
        $resultados = Cni::consultaPreparada(
                $sql,
                array($this->numeroFactura),
                PDO::FETCH_CLASS
        );
        if (Cni::totalDatosConsulta() > 0 ) {
            $this->resultados = $resultados;
            $this->procesaFactura();
        } else {
            $this->resultados = false;
            $this->serviciosFijos();
            $this->serviciosAlmacenaje();
            $this->serviciosAgrupados();
            $this->serviciosNoAgrupados();
            $this->serviciosDescuento();
        }
    }
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
    	$this->resultados = Cni::consultaPreparada(
    			$sql,
    			array($this->idCliente),
    			PDO::FETCH_CLASS
    			);
    	$this->procesaFactura();
    }
    private function serviciosAlmacenaje()
    {
    	$sqlFinal = "SELECT 
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
     			AND s.Nombre like '%Almacenaje%'";
    	/**
    	 * Todo
    	 */
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
    private function serviciosNoAgrupados()
    {
    }
    private function serviciosAgrupados()
    {
    }
    private function serviciosDescuento()
    {
    }
    /**
     * Agrega los datos al historico
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
    	foreach ($this->resultados as $resultado) {
            $this->servicio = ucfirst(trim($resultado->servicio));
            $this->cantidad = $resultado->cantidad;
            $this->unitario = $resultado->unitario;
            $this->iva = $resultado->iva;
            $this->obs = $resultado->obs;
            $this->html .= $this->lineaFactura();
        }
    }
    public function presentaFactura()
    {
    	$this->html .= $this->lineaTotales();
    	return $this->html;
    }
}