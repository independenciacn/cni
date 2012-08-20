<?php
require_once 'Cni.php';
require_once 'Cliente.php';
require_once 'OpcionesPago.php';
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
class Facturas
{
    public $numeroFactura = null;
    public $nombreFactura = null;
    public $tituloFactura = null;
    public $fechaFactura = null;
    public $fechaInicialFactura = '00-00-0000';
    public $fechaFinalFactura = '00-00-0000';
    public $obsFactura = null;
    public $OpcionesPago = null;
    public $idFactura = null;
    public $duplicado = false;
    public $servicio = null;
    public $cantidad = null;
    public $unitario = null;
    public $iva = null;
    public $obs = null;
    public $cliente = null;
    public $diaFacturacionCliente = false;
    public $resultados = false;
    public $totalGlobal = 0;
    public $totalBruto = 0;
    public $totalCantidad = 0;
    public $totalDescuento = 0;
    public $totalFijos = 0;
    public $html = "";
    private $fijos = false;
    public $historico = false;
    private $prueba = false;
    private $numeroLinea = 0;

    /**
     * Constructor Clase, si se le pasa factura la coge del historico
     * si se le pasa duplicado genera duplicado
     *
     * @param Integer $factura
     * @param Boolean $duplicado
     */
    public function __construct($factura = null, $duplicado = false)
    {
        if (!is_null($factura)) {
            $this->duplicado = $duplicado; //Recoge si es un duplicado
            $this->historico = true; //Establece que cogera los datos de his
            $this->idFactura = $factura; //Establece el numero de factura
            $this->datosFactura(); //Carga los datos de la factura
            $this->datosHistorico(); //Carga los datos del historico
        }
    }

    /**
     * Carga los datos de la Factura
     */
    private function datosFactura()
    {
        $sql = "SELECT
			id_cliente AS idCliente,
			DATE_FORMAT(fecha, '%d-%m-%Y') AS fecha,
			codigo as factura,
			DATE_FORMAT(fecha_inicial, '%d-%m-%Y') AS fechaInicial,
			DATE_FORMAT(fecha_final, '%d-%m-%Y') AS fechaFinal
			FROM regfacturas
			WHERE id LIKE ?";
        $resultados = Cni::consultaPreparada(
            $sql,
            array($this->idFactura),
            PDO::FETCH_CLASS
        );
        if (!empty($resultados)) {
            foreach ($resultados as $resultado) {
                $this->cliente = new Cliente($resultado->idCliente);
                $this->OpcionesPago = new OpcionesPago(null, $this->idFactura);
                $this->fechaFactura = $resultado->fecha;
                $this->numeroFactura = $resultado->factura;
                $this->fechaInicialFactura = $resultado->fechaInicial;
                $this->fechaFinalFactura = $resultado->fechaFinal;
                $this->obsFactura = $resultado->observaciones;
            }
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
        if (Cni::totalDatosConsulta() > 0) {
            $this->setResultados($resultados);
            $this->procesaFactura();
        }
    }

    /**
     * Procesa las lineas de la factura
     *
     * @return bool
     */
    private function procesaFactura()
    {
        if (!($this->getResultados())) {
            foreach ($this->getResultados() as $resultado) {
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
		<td>" . $this->servicio . " " . $this->obs . "</td>
		<td>" . Cni::formateaNumero($this->cantidad) . "</td>
		<td>" . Cni::formateaNumero($this->unitario, true) . "</td>
		<td>" . Cni::formateaNumero($importe, true) . "</td>
		<td>" . Cni::formateaNumero($this->iva) . "%</td>
		<td>" . Cni::formateaNumero($total, true) . "</td>
		</tr>";
        $this->totalBruto += $importe;
        $this->totalCantidad += $this->cantidad;
        $this->totalGlobal += $total;
        $this->totalFijos += ($this->fijos) ? $importe : 0;
        $this->numeroLinea++;
        if (!$this->historico && !$this->prueba) {
            $this->agregaHistorico();
        }
        return $html;
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
     * Parte de la generacion de factura desde Cero
     * Generamos la factura en el caso de que no este creada
     *
     * @param Array $vars
     * @return bool
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
            $this->OpcionesPago = new OpcionesPago($vars['cliente']);
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
            $this->agregaRegFacturas();
            return true;
        } else {
            return false;
        }
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
     * Comprueba el descuento del cliente
     */
    private function serviciosDescuento()
    {
        //TODO Aplicar el descuento
        if ($this->cliente->razon != '') {
            $porcentaje = explode("%", $this->cliente->razon);
            $importeDescuento = ($this->totalFijos * $porcentaje[0]) / 100;
            $descuento = new StdClass();
            $descuento->servicio = "Descuento del " . $porcentaje[0] . "%";
            $descuento->cantidad = 1;
            $descuento->unitario = $importeDescuento;
            $descuento->importe = $importeDescuento;
            $descuento->iva = IVA;
            $descuento->obs = "";
            $this->totalDescuento = $importeDescuento;
            $this->setResultados($descuento);
            $this->procesaFactura();
        }
    }

    /**
     * @param $almacen
     * @return stdClass
     */
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
        $diaFacturacion = $this->cliente->diaFacturacion;
        if ($diaFacturacion) {
            $diaFacturacion .= "-" .
                Cni::verMes($this->fechaFactura) . "-" .
                Cni::verAnyo($this->fechaFactura);
            // Datos entre dia Facturacion del mes anterior y el de factura
            $sql = " AND 
                    " . $campoFecha->inicial . " >
                    DATE_SUB( 
                        STR_TO_DATE(?, '%d-%m-%Y'), 
                        INTERVAL 1 MONTH
                    ) AND 
                    " . $campoFecha->final . " <=
                    STR_TO_DATE(?, '%d-%m-%Y') ";
            $params = array($diaFacturacion, $diaFacturacion);
        } else {
            if ($this->fechaFinalFactura == '00-00-0000') {
                if ($this->fechaInicialFactura == '00-00-0000') {
                    // Facturacion Normal Mes
                    $sql = "
                        AND MONTH(" . $campoFecha->inicial . ")
                        LIKE MONTH( STR_TO_DATE(?, '%d-%m-%Y') )
                        AND YEAR(" . $campoFecha->inicial . ")
                        LIKE YEAR( STR_TO_DATE(?, '%d-%m-%Y') ) ";
                    $params = array($this->fechaFactura, $this->fechaFactura);
                } else {
                    // Facturacion puntual dia unico
                    $sql = " 
                    AND " . $campoFecha->inicial . " LIKE
                    STR_TO_DATE(?, '%d-%m-%Y') ";
                    $params = array($this->fechaInicialFactura);
                }
            } else {
                if ($this->fechaInicialFactura != '00-00-0000') {
                    $sql = " 
                        AND " . $campoFecha->inicial . "
                        >= STR_TO_DATE(?, '%d-%m-%Y') 
                        AND " . $campoFecha->final . "
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
            "Franqueo", "Consumo Tel%fono",
            "Material de oficina", "Secretariado", "Ajuste");
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
            $cadena .= " d.Servicio " . $like . " '" . $noAgrupado .
                "' " . $union . " ";
        }
        $cadena = substr($cadena, 0, strlen($cadena) - (strlen($union) + 1));
        $cadena .= ") " . $groupBy . "
			ORDER BY d.ImporteEuro DESC , d.Servicio ASC";
        return $cadena;
    }


    /**
     * Agrega la factura al registro de facturas
     */
    private function agregaRegFacturas()
    {
        //TODO Revisar campos donde se inserta
        $sql = "Insert into regfacturas (
				id_cliente,
				codigo,
				fecha,
				iva,
				importe,
				obs_alt,
				mes,
				ano,
				fpago,
				obs_fpago,
				pedidoCliente,
				fecha_inicial,
				fecha_final
				) values (
				?, ?, STR_TO_DATE(?, '%d-%m-%Y'), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = array(
            $this->cliente->idCliente,
            $this->numeroFactura,
            $this->fechaFactura,
            $this->totalGlobal - $this->totalBruto,
            $this->totalGlobal,
            $this->obs,
            Cni::verMes($this->fechaFactura),
            Cni::verAnyo($this->fechaFactura),
            $this->OpcionesPago->fpago,
            $this->OpcionesPago->obsFormaPago,
            $this->OpcionesPago->pedidoCliente,
            $this->fechaInicialFactura,
            $this->fechaFinalFactura
        );
        return Cni::consultaPreparada($sql, $params);
    }


    /**
     * Compensacion del diseño para el llenado del A4
     *
     * @return bool
     */
    private function compensacionEspacio()
    {
        $coeficiente = 432 - ($this->numeroLinea - 1) * 18;
        if ($coeficiente >= 1) {
            $this->html .= "<tr>
                <td height='" . $coeficiente . "px'>&nbsp;</td>
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

    /**
     * @param bool | Object $resultados
     */
    public function setResultados($resultados)
    {
        $this->resultados = $resultados;
    }

    /**
     * @return bool | Object
     */
    public function getResultados()
    {
        return $this->resultados;
    }
}
 