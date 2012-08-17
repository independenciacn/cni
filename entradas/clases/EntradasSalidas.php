<?php
/**
 * EntradasSalidas File Doc Comment
 * 
 * Fichero de la Clase EntradasSalidas
 * 
 * PHP Version 5.2.6
 * 
 * @category EntradasSalidas
 * @package  cni/entradas/clases
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/Cni.php';
/**
 * EntradasSalidas Class Doc Comment
 * 
 * @category Class
 * @package  cni/entradas/classes
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @version  Release: 1.0
 * @link     https://github.com/independenciacn/cni
 * @fixme	Revision y migracion a Clase general Cni::consulta
 *
 */
class EntradasSalidas
{
    /*
     * Variables de la Clase
     */
    public $fecha = null;
    public $anyos = null;
    public $anyoInicial = null;
    public $anyoFinal = null;
    public $anyoActual = null;
    public $anyoZero = 2008;
    private $_tipoVistas = array(
    		1 => 'acumulada',
    		2 => 'detallada',
    		3 => 'grafica'
    		);
    private $_tipoDatos = array(1 => 'clientes', 2 => 'servicios');
    private $_tipoVista = null;
    private $_tipoDato = null;
    private $_conn = null;
    private $_datos = null;
    private $_categoriasBaneadas = array(
    		'Clientes externos',
    		'Otros',
    		'Clientes domiciliación especial  + atencion telefonica',
    		'Clientes domiciliación integral + atencion telefonica'
    		);
    private $_serviciosOcupacion = array(
    	array('Nombre' => 'Sala de Juntas Jornada Completa', 'Tipo' => 'Sala',
    		'Tiempo' => 'Completa'),
    	array('Nombre' => 'Sala de Reuniones Jornada Completa',
    		'Tipo' => 'Despacho', 'Tiempo' => 'Completa'),
    	array('Nombre' => 'Despacho Jornada Completa ', 'Tipo' => 'Despacho',
    		'Tiempo' => 'Completa'),
    	array('Nombre' => 'Sala de Reuniones Media Jornada',
    		'Tipo' => 'Despacho','Tiempo' => 'Media'),
    	array('Nombre' => 'Sala de Juntas Media Jornada', 'Tipo' => 'Sala',
    		'Tiempo' => 'Media'),
   		array('Nombre' => 'Despacho Media Jornada ', 'Tipo' => 'Despacho',
    		'Tiempo' => 'Media')
    	);
    private $_horasDespacho = array('Sala de Reuniones (una hora)',
    	'Sala de Juntas (una hora)', 'Despacho (una hora)'
    	);
    private $_anyoCeroKeys = array(
    	'2' => array('entradas' => '128', 'salidas' => '103'),
    	'3' => array('entradas' => '95', 'salidas' => '54'),
    	'4' => array('entradas' => '63', 'salidas' => '40'),
    	'6' => array('entradas' => '29', 'salidas' => '18'),
    	'7' => array('entradas' => '1', 'salidas' => '0')
    	);
    private $_categoriasClientes = null;
    private $_movimientos = array();
    /**
     * Constructor sobreescribe al padre
     */
    public function __construct ()
    {
        $this->anyoActual = date( 'Y' );
        $this->anyoInicial = $this->anyoActual - 1;
        $this->anyoFinal = $this->anyoActual;
        $this->anyos = array($this->anyoInicial, $this->anyoFinal);
        $this->_categoriasClientes = $this->categorias();
    }
    /**
     * Establecemos el rango de años
     */
    public function setAnyos ()
    {
        $this->anyos = array();
        for ($i = $this->anyoInicial; $i <= $this->anyoFinal; $i ++) {
            $this->anyos[] = $i;
        }
    }
    /**
     * Establecemos el tipo de vista
     * 
     * @param string $vista
     */
    public function setTipoVista ($vista)
    {
        $this->_tipoVista = $this->_tipoVistas[$vista];
    }
    /**
     * Establecemos el tipo de dato
     * 
     * @param string $dato
     */
    public function setTipoDato ($dato)
    {
        $this->_tipoDato = $this->_tipoDatos[$dato];
    }
    /**
     * Chequeamos en el año inicial del calculo de salidas, 
     * si es 2008 no se cuentan las salidas de la 2 quincena de 2007
     * 
     * @return string
     */
    public function inicioSalidas ()
    {
        if ($this->anyoInicial > $this->anyoZero) {
            $salida = " salida >= '" . ($this->anyoInicial - 1) . "-12-16' ";
        } else {
            $salida = " YEAR(salida) >= " . $this->anyoZero . " ";
        }
        return $salida;
    }
    /**
     * Establecemos el titulo del listado
     * 
     * @return string $titulo
     */
    public function titulo ()
    {
        $titulo = ($this->_tipoDato == 'clientes') ?
         'Movimientos Clientes' :
         'Consumo Servicios';
        $titulo .= " " . ucfirst( $this->_tipoVista ) . " ";
        $titulo .= ($this->anyoInicial == $this->anyoFinal) ?
          " " .$this->anyoInicial :
          " " . $this->anyoInicial . "-" .$this->anyoFinal;
        return $titulo;
    }
    /**
     * Establecemos el listado de categorias que vamos a usar 
     * para generar las tablas
     * 
     * @return array $datos
     */
    public function categorias ()
    {
        $sql = "SELECT Nombre FROM `categorías clientes`";
        $this->_datos = Cni::consulta($sql, PDO::FETCH_ASSOC);
        foreach ($this->_datos as $key => $dato) {
            if (array_search(
            		$dato['Nombre'],
            		$this->_categoriasBaneadas
            		) === false ) {
                $datos[] = $dato;
            }
        }
        return $datos;
    }
    /**
     * Devuelve los valores de la categoria en el año 0
     * 
     * @param string $categoria
     * @param string $tipo
     * 
     * @return string | false
     */
    public function valoresCategoriasAnyoCero ($categoria, $tipo)
    {
        $result = 0;
        $sql = "SELECT Id
        		FROM `categorías clientes` 
        		WHERE Nombre LIKE '".$categoria."'";
        $this->_datos = Cni::consulta($sql, PDO::FETCH_ASSOC);
        foreach ($this->_datos as $dato) {
            if (array_key_exists( $dato['Id'], $this->_anyoCeroKeys )) {
                $result =  $this->_anyoCeroKeys[$dato['Id']][$tipo];
                break;
            }
        }
        return $result;
    }
    /**
     * Devuelve los movimientos totales por categorias en un año
     * 
     * @return array $finales
     */
    public function movimientos ()
    {
        $totales = $this->movimientosTotales();
        $entradas = $this->entradasTotales();
        $salidas = $this->salidasTotales();
        $acumuladoEntradas = $this->entradasTotales( true );
        $acumuladoSalidas = $this->salidasTotales( true );
        foreach ($this->_categoriasClientes as $categorias) {
            $finales[$categorias['Nombre']] = array(
            		'total' => 0,
            		'entradas' => 0,
            		'salidas' => 0,
            		'acentradas' => 0,
            		'acsalidas' => 0,
            		'diferencia' => 0
            		);
        }
        foreach ($totales as $total) {
            if (array_key_exists( $total['categoria'], $finales )) {
                $finales[$total['categoria']]['total'] = $total['total'];
            }
        }
        foreach ($entradas as $total) {
            if (array_key_exists( $total['categoria'], $finales )) {
                $finales[$total['categoria']]['entradas'] = $total['total'];
            }
        }
        foreach ($salidas as $total) {
            if (array_key_exists( $total['categoria'], $finales )) {
                $finales[$total['categoria']]['salidas'] = $total['total'];
            }
        }
        foreach ($acumuladoEntradas as $total) {
            if (array_key_exists( $total['categoria'], $finales )) {
                $finales[$total['categoria']]['acentradas'] = $total['total'];
                $finales[$total['categoria']]['acentradas'] +=
                	$this->valoresCategoriasAnyoCero(
                		$total['categoria'], 'entradas'
                			);
                $finales[$total['categoria']]['diferencia'] +=
                	$finales[$total['categoria']]['acentradas'];
            }
        }
        foreach ($acumuladoSalidas as $total) {
            if (array_key_exists( $total['categoria'], $finales )) {
                $finales[$total['categoria']]['acsalidas'] -= $total['total'];
                $finales[$total['categoria']]['acsalidas'] -=
                	$this->valoresCategoriasAnyoCero(
                		$total['categoria'], 'salidas'
                			);
                $finales[$total['categoria']]['diferencia'] +=
                	$finales[$total['categoria']]['acsalidas'];
            }
        }
        return $finales;
    }
    /**
     * Funcion General de las ocupaciones Puntuales
     * Si se especifica rango como total devolvera los acumulados si no
     * sera el rango especificado
     * 
     * @param string $anyo
     */
    public function ocupacionPuntual ($anyo = false)
    {
        $ocupaciones = array('salaCompleta' => 0, 'salaMedia' => 0,
        'despachoCompleta' => 0, 'despachoMedia' => 0);
        foreach ($this->_serviciosOcupacion as $key => $servicio) {
            $datos = $this->ocupacionesPuntuales( $servicio['Nombre'], $anyo );
            foreach ($datos as $dato) {
            	if (isset( $dato['Total'] )) {
                	if ($servicio['Tipo'] == 'Sala') {
                    	if ($servicio['Tiempo'] == 'Media') {
                        	$ocupaciones['salaMedia'] += $dato['Total'];
                    	}
                    	if ($servicio['Tiempo'] == 'Completa') {
                        	$ocupaciones['salaCompleta'] += $dato['Total'];
                    	}
                	}
                	if ($servicio['Tipo'] == 'Despacho') {
                    	if ($servicio['Tiempo'] == 'Media') {
                        	$ocupaciones['despachoMedia'] += $dato['Total'];
                    	}
                    	if ($servicio['Tiempo'] == 'Completa') {
                        	$ocupaciones['despachoCompleta'] += $dato['Total'];
                    	}
                	}
            	}
            }
        }
        return $ocupaciones;
    }
    /**
     * Funcion que devuelve las horas de Despachos por clientes externos
     * 
     * @param string $anyo
     * @return string
     */
    public function horasDespachoSala ($anyo = false)
    {
        $horas = 0;
        foreach ($this->_horasDespacho as $servicio) {
            $datos = $this->ocupacionesPuntuales( $servicio, $anyo );
            foreach ($datos as $dato) {
            	if (isset( $dato['Total'] )) {
                	$horas += $dato['Total'];
            	}
            }
        }
        return $horas;
    }
    /**
     * Funcion de presentacion de los datos de Ocupacion puntual y Horas
     * 
     * @param string $mes
     * @param string $anyo
     * @param string $tipo
     * @param string $tiempo
     * @param string $anyoFinal
     * @return array
     */
    public function detallesOcupacionHoras ($mes, $anyo, $tipo,
    		$tiempo, $anyoFinal = null)
    {
        $filtro = "";
        if ($tipo == 'Horas') {
            foreach ($this->_horasDespacho as $servicio) {
                $filtro .= " d.servicio LIKE '{$servicio}' OR";
            }
        } else {
            foreach ($this->_serviciosOcupacion as $servicio) {
                if ($servicio['Tipo'] == $tipo
                		&& $servicio['Tiempo'] == $tiempo) {
                    $filtro .= " d.servicio LIKE '{$servicio['Nombre']}' OR";
                }
            }
        }
        if ($anyoFinal != null && $mes == 100) {
            $filtroAnyo = "YEAR(c.fecha) >= '{$anyo}' 
             AND YEAR(c.fecha) <= '{$anyoFinal}' ";
        } else {
            $filtroAnyo = "YEAR(c.fecha) LIKE '{$anyo}'
             AND MONTH(c.fecha) LIKE '{$mes}' ";
        }
        $filtro = substr( $filtro, 0, strlen( $filtro ) - 2 );
        $sql = "SELECT d.Servicio as Servicio, l.Nombre, c.fecha 
         FROM `detalles consumo de servicios` AS d 
         INNER JOIN `consumo de servicios` as c 
         ON d.`Id Pedido` = c.`Id Pedido` " . "INNER JOIN `clientes` AS l 
         ON c.`cliente` = l.id " . "WHERE ({$filtro}) AND ({$filtroAnyo})
         ORDER BY c.fecha;";
        return Cni::consulta($sql, PDO::FETCH_ASSOC);
    }
    /**
     * Calcula los movimientos totales por categoria en un rango de años
     * 
     * @return array;
     */
    public function movimientosTotales ()
    {
        $sql = "SELECT categoria, count(categoria) as total
         FROM `centro`.`entradas_salidas`
         WHERE ((YEAR(entrada) >= {$this->anyoInicial} 
         AND YEAR(entrada) <= {$this->anyoFinal} )
         OR ( {$this->inicioSalidas()} 
         AND salida <= '{$this->anyoFinal}-12-15')) GROUP by categoria;";
        return Cni::consulta($sql, PDO::FETCH_ASSOC);
    }
    /**
     * Calcula las ocupaciones puntuales
     * 
     * @param string $servicio
     * @param boolean $anyo
     * @return array
     */
    public function ocupacionesPuntuales ($servicio, $anyo = false)
    {
        if ($anyo == false) {
            $anyo = $this->anyoInicial;
        } else {
            $anyo = $this->anyoZero;
        }
        $sql = "SELECT d.Servicio as Servicio, count(d.servicio) AS Total 
         FROM `detalles consumo de servicios` AS d 
         INNER JOIN `consumo de servicios` as c 
         ON d.`Id Pedido` = c.`Id Pedido` INNER JOIN `clientes` AS l 
         ON c.`cliente` = l.id " . "WHERE d.servicio like '{$servicio}'
         AND (YEAR(c.fecha) >= {$anyo}
         AND YEAR(c.fecha) <= {$this->anyoFinal}) GROUP BY d.Servicio
         ORDER BY d.Servicio; ";
//         $this->_conn = new Sql();
//         $this->_conn->consulta( $sql );
        return Cni::consulta($sql, PDO::FETCH_ASSOC);
    }
    /**
     * Cuenta los servicios por meses
     * 
     * @param string $servicio
     * @param boolean $todos
     * @return array $arrayFinal
     */
    public function cuentaServiciosPorMes ($servicio, $todos = false)
    {
        if (! $todos) {
            $filtro = " l.categoria LIKE 'clientes externos' AND ";
        } else {
            $filtro = "";
        }
        $sql = "SELECT MONTH(c.fecha) AS mes, 
         COUNT(MONTH(c.fecha)) AS total, YEAR(c.fecha) AS anyo 
         FROM `detalles consumo de servicios` AS d 
         INNER JOIN `consumo de servicios` as c 
         ON d.`Id Pedido` = c.`Id Pedido` INNER JOIN `clientes` AS l 
         ON c.`cliente` = l.id WHERE {$filtro}
         d.servicio like '{$servicio}' 
         AND (YEAR(c.fecha) >= {$this->anyoInicial} 
         AND YEAR(c.fecha) <= {$this->anyoFinal}) 
         GROUP BY MONTH(c.fecha), YEAR(c.fecha) ORDER BY c.fecha";
//         $this->_conn = new Sql();
//         $this->_conn->consulta( $sql );
        $arrayFinal = array_fill( 0, $this->diferencia(), 0 );
        foreach (Cni::consulta($sql, PDO::FETCH_ASSOC) as $dato) {
            $key = $dato['mes'];
            if ($dato['anyo'] > $this->anyoInicial) {
                $key = $dato['mes'] + 11;
            } else {
                $key = $dato['mes'] - 1;
            }
            $arrayFinal[$key] = $dato['total'];
        }
        return $arrayFinal;
    }
    /**
     * Funcion auxiliar que calcula totales
     * 
     * @param string $sql
     * @return array $finales
     */
    public function auxiliarTotales ($sql)
    {
        foreach ($this->categorias() as $categoria) {
            $finales[] = array('categoria' => $categoria['Nombre'],
            'total' => 0);
        }
//         $this->_conn = new Sql();
//         $this->_conn->consulta( $sql );
        $original = Cni::consulta($sql, PDO::FETCH_ASSOC);
        $total = count( $finales );
        for ($i = 0; $i < $total; $i ++) {
            foreach ($original as $dato) {
                if ($dato['categoria'] == $finales[$i]['categoria']) {
                    $finales[$i]['total'] = $dato['total'];
                }
            }
        }
        return $finales;
    }
    /**
     * Calcula las entradas totales por categoria en un rango de años 
     * Y las acumuladas
     * 
     * @param boolean $anyo
     * @return array
     */
    public function entradasTotales ($anyo = false)
    {
        if ($anyo == false) {
            $anyo = $this->anyoInicial;
        } else {
            $anyo = $this->anyoZero;
        }
        $sql = "SELECT categoria, count(categoria) as total 
         FROM `centro`.`entradas_salidas` WHERE (YEAR(entrada) >= {$anyo} 
         AND YEAR(entrada) <= {$this->anyoFinal} ) GROUP BY categoria;";
        return $this->auxiliarTotales( $sql );
    }
    /**
     * Calcula las salidas totales por categoria en un rango de años
     * 
     * @param boolean $anyo
     * @return array
     */
    public function salidasTotales ($anyo = false)
    {
        if ($anyo == false) {
            $anyo = $this->inicioSalidas();
        } else {
            $anyo = "YEAR(salida) >= {$this->anyoZero}";
        }
        $sql = "SELECT categoria, count(categoria) as total 
         FROM `centro`.`entradas_salidas` WHERE ( {$anyo} 
         AND salida <= '{$this->anyoFinal}-12-15') GROUP by categoria;";
        return $this->auxiliarTotales( $sql );
    }
    /**
     * Devuelve un array con los detalles de los movimientos realizados
     * 
     * @param string $tipo
     * @param string $categoria
     */
    public function detallesMovimientos ($tipo, $categoria)
    {
        $sql = "SELECT c.Nombre, e.entrada, e.salida, e.categoria 
         FROM `centro`.`entradas_salidas` as e 
         INNER JOIN `centro`.`clientes` as c ON c.Id = e.idemp ";
        if ($tipo == "entrada") {
            $sql .= "WHERE (YEAR({$tipo}) >= {$this->anyoInicial}
             AND YEAR({$tipo}) <= {$this->anyoFinal}) ";
        } else {
            $sql .= "WHERE ({$this->inicioSalidas()} 
             AND {$tipo} <= '{$this->anyoFinal}-12-15') ";
        }
        $sql .= "AND e.categoria LIKE '" . $categoria  . "'
         ORDER by e.{$tipo};";
//         $this->_conn = new Sql();
//         $this->_conn->consulta( $sql );
//         return $this->_conn->datos();
		return Cni::consulta($sql, PDO::FETCH_ASSOC);
    }
    /**
     * Muestra los servicios externos consumidos en un rango de años.
     * Si $contado es TRUE los cuenta, si no los muestra
     * 
     * @param boolean $contado
     * @return array
     */
    public function serviciosExternos ($contado = false)
    {
        if ($contado == true) {
            $cuenta = " count(d.servicio) as Total ";
            $grupo = "GROUP BY d.Servicio ORDER BY d.Servicio ";
        } else {
            $cuenta = " c.fecha ";
            $grupo = "ORDER BY c.fecha";
        }
        $sql = "SELECT d.Servicio as Servicio, {$cuenta}
         FROM `detalles consumo de servicios` AS d 
         INNER JOIN `consumo de servicios` AS c 
         ON d.`Id Pedido` = c.`Id Pedido` INNER JOIN `clientes` AS l 
         ON c.`cliente` = l.id WHERE l.categoria lIKE 'clientes externos' 
         AND (YEAR(c.fecha) >= {$this->anyoInicial} 
         AND YEAR(c.fecha) <= {$this->anyoFinal}) {$grupo};";
//         $this->_conn = new Sql();
//         $this->_conn->consulta( $sql );
//         return $this->_conn->datos();
		return Cni::consulta($sql, PDO::FETCH_ASSOC);
    }
    /**
     * Funcion que muestra los detalles del servicio externo consumido
     * 
     * @param string $servicio
     * @param string $mes
     * @param string $anyo
     * @return Array
     * 
     */
    public function detallesServiciosExternos (
    	$servicio,
    	$mes = false,
    	$anyo = false
    ) {
        if ($mes && $anyo) {
            $nexo = "AND (MONTH(c.fecha) LIKE ? 
             AND YEAR(c.fecha) LIKE ?) ";
            $params = array($servicio, $mes, $anyo);
        } else {
            $nexo = "AND (YEAR(c.fecha) >= ? 
             AND YEAR(c.fecha) <= ?) ";
            $params = array($servicio, $this->anyoInicial, $this->anyoFinal);
        }
        $sql = "SELECT l.Nombre, c.fecha 
         FROM `detalles consumo de servicios` AS d 
         INNER JOIN `consumo de servicios` AS c 
         ON d.`Id Pedido` = c.`Id Pedido` " . "INNER JOIN `clientes` AS l 
         ON c.`cliente` = l.id WHERE l.categoria LIKE 'clientes externos' 
         AND d.servicio like ? ".$nexo." ORDER BY c.fecha;";
        return Cni::consultaPreparada($sql, $params, PDO::FETCH_ASSOC);
    }
    /**
     * Funcion a la que se le pasa por parametro el tipo 'entrada' 'salida' 
     * y la categoria, y devuelve el numero total de entradas o salidas por mes
     * en el rango de años
     *
     * @param string $tipo
     * @param string $categoria
     * @return array
     */
    public function totalesPorMeses ($tipo, $categoria)
    {
        if ($tipo == 'entrada') {
            $sql = "SELECT MONTH({$tipo}) AS mes, 
             COUNT(MONTH({$tipo})) AS total, YEAR({$tipo}) AS anyo
             FROM `centro`.`entradas_salidas` 
             WHERE (YEAR({$tipo}) >= {$this->anyoInicial} 
             AND YEAR({$tipo}) <= {$this->anyoFinal}) 
             AND categoria LIKE '{$categoria}' 
             GROUP BY MONTH({$tipo}), YEAR({$tipo}) ORDER BY {$tipo} ;";
        }
        if ($tipo == 'salida') {
            $sql = "SELECT {$tipo} FROM `centro`.`entradas_salidas` AS e 
             WHERE ({$this->inicioSalidas()} 
             AND {$tipo} <= '{$this->anyoFinal}-12-15') 
             AND categoria LIKE '{$categoria}' ORDER BY {$tipo}  ;";
        }
//         $this->_conn = new Sql();
//         $this->_conn->consulta( $sql );
//         return $this->_conn->datos();
        return Cni::consulta($sql, PDO::FETCH_ASSOC);
    }
    /**
     * Funcion a la que se le pasa como parametro la categoria de cliente, si es
     * entrada o salida, el mes y el año. Devuelve el detalle de los movimientos
     * 
     * @param string $categoria
     * @param string $tipo
     * @param string $mes
     * @param string $anyo
     * @return array 
     */
    function detalleClienteMes ($categoria, $tipo, $mes, $anyo)
    {
        $sql = "SELECT c.Nombre, e.entrada, e.salida, e.categoria
         FROM `centro`.`entradas_salidas` AS e 
         INNER JOIN `centro`.`clientes` AS c ON c.Id = e.idemp";
        $anyoAnterior = $anyo - 1;
        $mesAnterior = $mes - 1;
        if ($tipo == 'entrada') {
            $sql .= " WHERE (MONTH(e.{$tipo}) LIKE {$mes}
             AND YEAR(e.{$tipo}) LIKE {$anyo})";
        } else {
            if ($anyo != $this->anyoZero) {
                if ($mes == '1') {
                    $sql .= " WHERE ( e.{$tipo} >= '{$anyoAnterior}-12-16' ";
                } else {
                    $sql .= " WHERE ( e.{$tipo} >= '{$anyo}-{$mesAnterior}-16' ";
                }
            } else {
                if ($mes == '1') {
                    $sql .= " WHERE ( e.{$tipo} >= '{$anyo}-{$mes}-01' ";
                } else {
                    $sql .= " WHERE ( e.{$tipo} >= '{$anyo}-{$mesAnterior}-16' ";
                }
            }
            $sql .= "AND e.{$tipo} <= '{$anyo}-{$mes}-15') ";
        }
        $sql .= "AND e.categoria LIKE '{$categoria}' ORDER BY e.{$tipo};";
//         $this->_conn = new Sql();
//         $this->_conn->consulta( $sql );
//         return $this->_conn->datos();
        return Cni::consulta($sql, PDO::FETCH_ASSOC);
    }
    /**
     * Funcion Auxiliar que nos devuelve cuantos meses 
     * hay que visualizar en la grafica
     * 
     * @return string
     */
    public function diferencia ()
    {
        return ($this->anyoFinal - $this->anyoInicial + 1) * 12;
    }
    /**
     * Funcion que devuelve un array con los nombres cortos 
     * de los meses del rango seleccionado
     * 
     * @return array $meses
     */
    public function mesesRango ()
    {
        $meses = array("");
        $j = 0;
        $total = $this->diferencia();
        for ($i = 0; $i < $total; $i ++) {
            $meses[$i] = Cni::$mesesCortos[$j + 1];
            $j ++;
            if ($j == 12 && $i != 0) {
                $j = 0;
            }
        }
        return $meses;
    }
    /**
     * Funcion que devuelve el array de las entradas de categorias
     * por meses en el rango indicado
     * 
     * @param string $categoria
     * @return array $arrayEntradas
     */
    public function arrayEntradasClientes ($categoria)
    {
        $totalesEntradas = $this->totalesPorMeses( 'entrada', $categoria );
        $arrayEntradas = array_fill( 0, $this->diferencia(), 0 );
        $vuelta = $this->anyoInicial;
        $multi = 0;
        foreach ($totalesEntradas as $totales) {
        	if ($vuelta != $totales['anyo']) {
        		$multi += 12;
        		$vuelta = $totales['anyo'];
        	}
        	$posicion = $totales['mes'] + $multi - 1;
        	$arrayEntradas[$posicion] = $totales['total'];
        }
        return $arrayEntradas;
    }

    /**
     * Funcion que devuelve el array de las salidas de categorias
     * por meses en el rango indicado
     * 
     * @param string $categoria
     * @return array $arraySalidas
     */
    public function arraySalidasClientes ($categoria)
    {
        $totalesSalidas = $this->totalesPorMeses( 'salida', $categoria );
        $arraySalidas = array_fill( 0, $this->diferencia(), 0 );
        $vuelta = $this->anyoInicial;
        $multi = 0;
        foreach ($totalesSalidas as $totales) {
        	if ($vuelta < Cni::verAnyo( $totales['salida'] )) {
        		$vuelta = Cni::verAnyo( $totales['salida'] );
        		$multi += 12;
        	}
        	if (Cni::verDia( $totales['salida'] ) >= 16) {
        		if (Cni::verAnyo( $totales['salida'] ) <
        				$vuelta) {
        			$posicion = Cni::verMes(
        					$totales['salida'] ) - 12 + $multi;
        		} else {
        			$posicion = Cni::verMes(
        					$totales['salida'] ) + $multi;
        		}
        	} else {
        		$posicion = Cni::verMes(
        				$totales['salida'] ) + $multi - 1;
        	}
        	$arraySalidas[$posicion] += 1;
        }
        return $arraySalidas;
    }
    /**
     * Funcion que muestra la tabla de Acumulado de Clientes 
     * en el rango especificado
     * 
     * @return string $html
     */
    public function listadoAcumuladoClientes ()
    {
        $html = <<<EOD
        <div class="serviciosAcumulados">
        <table class="listaacumulada">
	    <tr>
			<td>&nbsp;</td>
			<th colspan="2">Anual</th>
			<th colspan="3">Acumulado</th>
		</tr>
		<tr>
			<th class="acumulada">Categoria</th>
			<th class="datosacumulados">Entradas</th>
			<th class="datosacumulados">Salidas</th>
			<th class="datosacumulados">Entradas</th>
		    <th class="datosacumulados">Salidas</th>
			<th class="datosacumulados">Diferencia</th>
		</tr>
EOD;
        foreach ($this->movimientos() as $key => $movimiento) {
            $entrada = urlencode(
            "entrada#{$key}#{$this->anyoInicial}#{$this->anyoFinal}" );
            $salida = urlencode(
            "salida#{$key}#{$this->anyoInicial}#{$this->anyoFinal}" );
            $html .= <<<EOD
            <tr class="ui-widget-content">
				<td >{$key}</td>
				<td class="totales numero celdaServicio" id="{$entrada}">
                    {$movimiento['entradas']} 
               </td>
				<td class="totales numero celdaServicio" id="{$salida}">
                    {$movimiento['salidas']}
               </td>
				<td class="numero">
                    {$movimiento['acentradas']}
                </td>
				<td class="numero">
                    {$movimiento['acsalidas']}
                </td>
				<td class="numero">
                    {$movimiento['diferencia']}
                </td>
            </tr>
EOD;
        }
        /*Nueva seccion de las ocupaciones*/
        $ocupacionRango = $this->ocupacionPuntual();
        $ocupacionAcumulada = $this->ocupacionPuntual( true );
        $idSalaCompleta = urlencode( "Sala#Completa#100" );
        $idSalaMedia = urlencode( "Sala#Media#100" );
        $idDespachoCompleta = urlencode( "Despacho#Completa#100" );
        $idDespachoMedia = urlencode( "Despacho#Media#100" );
        $idHorasOcupacion = urlencode( "Horas#Completa#100" );
        $html .= <<<EOD
		<tr class="ui-widget-content">
			<th class="acumulada">Tipo de Ocupación</th>
			<th class="datosacumulados">Completa</th>
			<th class="datosacumulados">Media</th>
			<th class="datosacumulados">Completa</th>
			<th class="datosacumulados">Media</th>
			<th class="datosacumulados">&nbsp;</th>
		</tr>
		<tr class="ui-widget-content">
			<td>Ocupacion Puntual Salas</td>
			<td class="puntual numero celdaServicio" id="{$idSalaCompleta}">
                {$ocupacionRango['salaCompleta']}
            </td>
			<td class="puntual numero celdaServicio" id="{$idSalaMedia}">
                {$ocupacionRango['salaMedia']}
            </td>
			<td class="numero">
				{$ocupacionAcumulada['salaCompleta']}
			</td>
			<td class="numero">
				{$ocupacionAcumulada['salaMedia']}
			</td>
			<td class="numero">
				&nbsp;
			</td>
		</tr>
		<tr class="ui-widget-content">
			<td>Ocupacion Puntual Despachos</td>
			<td class="puntual numero celdaServicio" id="{$idDespachoCompleta}">
                {$ocupacionRango['despachoCompleta']}
            </td>
			<td class="puntual numero celdaServicio" id="{$idDespachoMedia}">
                {$ocupacionRango['despachoMedia']}
            </td>
			<td class="numero">
				{$ocupacionAcumulada['despachoCompleta']}
			</td>
			<td class="numero">
				{$ocupacionAcumulada['despachoMedia']}
			</td>
			<td class="numero">
				&nbsp;
			</td>
		</tr>
EOD;
        /*Nueva seccion de despacho salas horas*/
        $horasDespachoSala = $this->horasDespachoSala();
        $horasDespachoSalaAcumuladas = $this->horasDespachoSala( true );
        $html .= <<<EOD
        <tr>
        	<th class="acumulada">Despacho/Sala Horas</th>
        	<th class="datosacumulados" colspan="2">D/S Horas</th>
        	<th class="datosacumulados" colspan="2">D/S horas</th>
        	<th class="datosacumulados">&nbsp;</th>
        </tr>
        <tr class="ui-widget-content">
        	<td>Servicios Horas</td>
        	<td class="puntual numero celdaServicio" colspan="2" id="{$idHorasOcupacion}">
        	    {$horasDespachoSala}
        	</td>
        	<td class="numero" colspan="2">
        	    {$horasDespachoSalaAcumuladas}
        	</td>
        	<td>
        	    &nbsp;
        	</td>
        </tr>	
EOD;
        /*Fin de la seccion de las ocupaciones y despachos salas/horas*/
        $html .= <<<EOD
        </table>
        </div>
        
        <div id="servicioDetallado" class="serviciosAcumulados"></div>
        <script type="text/javascript">
        $('.totales').click(function(){ 
        	$('.numero').removeClass('ui-widget-header');
        	$(this).addClass('ui-widget-header');
        	
        	$.post('procesa.php',{datos:this.id},function(data){ 
        		$('#servicioDetallado').html(data);
        	});
    		
        	$('.totales').ajaxStop(function(){
            	var alto = $('#servicioDetallado').height() + $('.serviciosAcumulados').height() + 50;
            	$('#resultado').height(alto)
    		});
    	});
    	$('.puntual').click(function(){
    		$('.numero').removeClass('ui-widget-header');
        	$(this).addClass('ui-widget-header');
        	
        	$.post('procesa.php',{ocupacion:this.id,inicial:$('#inicio').val(),fin:$('#fin').val()},function(data){ 
        		$('#servicioDetallado').html(data);
        	});
    		
        	$('.puntual').ajaxStop(function(){
            	var alto = $('#servicioDetallado').height() + $('.serviciosAcumulados').height() + 50;
            	$('#resultado').height(alto)
    		});
    	});
        </script>
EOD;
        return $html;
    }
    /**
     * Funcion que muestra el listado de Acumulado de Servicios
     */
    public function listadoAcumuladoServicios ()
    {
        $servicios = array();
        foreach ($this->serviciosExternos() as $detalles) {
            if (! array_key_exists( $detalles["Servicio"], $servicios )) {
                $servicios[$detalles["Servicio"]] = 1;
            } else {
                $servicios[$detalles["Servicio"]] ++;
            }
        }
        ksort( $servicios );
        $html = <<<EOD
        <div class="serviciosAcumulados">
        <table class="listadetallada">
		<tr>
			<th class="acumulada">Servicio</th>
			<th class="datosacumulados">Consumos</th>
		</tr>
EOD;
        foreach ($servicios as $key => $servicio) {
            $tituloServicio = ucwords( strtolower( $key ) );
            $idServicio = urlencode( 
            "servicios#{$key}#{$this->anyoInicial}#{$this->anyoFinal}" );
            $html .= <<<EOD
            <tr class="ui-widget-content celdaServicio totales" id="{$idServicio}">
           		<td>{$tituloServicio}</td>
            	<td class="numero">{$servicio}</td>
            </tr>
EOD;
        }
        $html .= <<<EOD
        </table>
        </div>
        
        <div id="servicioDetallado" class="serviciosAcumulados"></div>
        <script type="text/javascript">
        $('.totales').click(function(){ 
        	$('.celdaServicio').removeClass('ui-widget-header');
        	$(this).addClass('ui-widget-header');
        	
        	$.post('procesa.php',{datos:this.id},function(data){ 
        		$('#servicioDetallado').html(data); 
        	});
        	
    		$('.totales').ajaxStop(function(){
            	var alto = $('#servicioDetallado').height() + 50;
            	$('#resultado').height(alto)
    		});
    	});
        </script>
EOD;
        return $html;
    }
    /**
     * Funcion que genera los enlaces a las secciones de 
     * categorias en la visualizacion
     */
    public function enlacesCategorias ()
    {
        $html = "<div class='listaenlaces'>";
        foreach ($this->categorias() as $categoria) {
            $enlaceCategoria = urlencode( $categoria['Nombre'] );
            $nombreCategoria = $categoria['Nombre'];
            $html .= "<a class='enlacedetallada' href='#{$enlaceCategoria}'>{$nombreCategoria}</a>";
        }
        $html .= "<a class='enlacedetallada' href='#puntualSalas'>Ocupación Puntual Salas</a>";
        $html .= "<a class='enlacedetallada' href='#puntualDespachos'>Ocupación Puntual Despachos</a>";
        $html .= "<a class='enlacedetallada' href='#despachosSalaHoras'>Despacho/Sala Horas</a>";
        $html .= "</div>";
        return $html;
    }
    /**
     * Funcion que muestra la grafica de Clientes 
     */
    public function graficaClientes ()
    {
        $html = $this->enlacesCategorias() . "<br/><br/>";
        foreach ($this->categorias() as $categoria) {
            $nombreCategoria = urlencode( $categoria['Nombre'] );
            $url = urlencode( 
            "grafico#{$nombreCategoria}#{$this->anyoInicial}#{$this->anyoFinal}" );
            $html .= "<a name='{$nombreCategoria}'>
            <img src = 'graph.php?datos={$url}' alt='Generando Grafica...' />
            </a>";
            $html .= "<a class='enlacedetallada' href='#arriba'>Ir arriba</a>";
            $html .= "<br/><br/>";
        }
        return $html;
    }
    /**
     * Funcion que muestra la grafica de Servicios
     */
    public function graficaServicios ()
    {
        $grafico = urlencode(
        "grafico#{$this->anyoInicial}#{$this->anyoFinal}" );
        return "<img src = 'graphservicios.php?datos={$grafico}' alt='Grafica Servicios' />";
    }
    /**
     * Funcion que muestra el listado detallado de los clientes
     */
    public function listadoDetalladoClientes ()
    {
        $html = "";
        $movimientos = $this->movimientos();
        $html .= $this->enlacesCategorias();
        foreach ($this->categorias() as $categoria) {
            $diferencia = array();
            $nombreCategoria = $categoria["Nombre"];
            $enlaceCategoria = urlencode( $nombreCategoria );
            $idCategoria = ucfirst( strtolower( $nombreCategoria ) );
            $movimiento = $movimientos[$nombreCategoria];
            $entradasInicial = $movimiento['acentradas'] -
             $movimiento['entradas'];
            $salidasInicial = $movimiento['acsalidas'] + $movimiento['salidas'];
            $arrayEntradas = $this->arrayEntradasClientes( $nombreCategoria );
            $arraySalidas = $this->arraySalidasClientes( $nombreCategoria );
            // Inicio Tabla
            $html .= "<h4><a name='{$enlaceCategoria}'>{$nombreCategoria}</a></h4>";
            $html .= "<table class='listadetallada'>";
            $html .= "<tr><th >&nbsp;</th>";
            foreach ($this->mesesRango() as $mes) {
                $html .= "<th class='datosdetallados'>{$mes}</th>";
            }
            $html .= "</tr>";
            // Entradas
            $html .= '<tr class="ui-widget-content">
            <td class="seccionlistadetallada">Entradas</td>';
            foreach ($arrayEntradas as $key => $entrada) {
                if ($key >= 12) {
                    $anyo = $this->anyoFinal;
                    $month = $key - 11;
                } else {
                    $anyo = $this->anyoInicial;
                    $month = $key + 1;
                }
                $html .= "<td id='{$nombreCategoria}#entrada#{$month}#{$anyo}' class='consumo'>{$entrada}</td>";
            }
            $html .= "</tr>";
            // Salidas
            $html .= "<tr class='ui-widget-content'>
            <td class='seccionlistadetallada'>Salidas</td>";
            foreach ($arraySalidas as $key => $salida) {
                if ($key >= 12) {
                    $anyo = $this->anyoFinal;
                    $month = $key - 11;
                } else {
                    $anyo = $this->anyoInicial;
                    $month = $key + 1;
                }
                $html .= "<td id='{$nombreCategoria}#salida#{$month}#{$anyo}' class='consumo'>{$salida}</td>";
            }
            $html .= "</tr>";
            // Entradas Acumuladas
            $html .= "<tr class='ui-widget-content'>
            <td class='seccionlistadetallada'>AC Entradas</td>";
            $i = 0;
            foreach ($arrayEntradas as $entrada) {
                $entradasInicial += $entrada;
                $diferencia[$i ++] = $entradasInicial;
                $html .= "<td>{$entradasInicial}</td>";
            }
            $html .= "</tr>";
            // Salidas Acumuladas
            $html .= "<tr class='ui-widget-content'>
            <td class='seccionlistadetallada'>AC Salidas</td>";
            $i = 0;
            foreach ($arraySalidas as $salida) {
                $salidasInicial -= $salida;
                $diferencia[$i ++] += $salidasInicial;
                $html .= "<td>{$salidasInicial}</td>";
            }
            $html .= "</tr>";
            // Diferencia
            $html .= "<tr class='ui-widget-content'>
            <td class='seccionlistadetallada'>Diferencia</td>";
            foreach ($diferencia as $valor) {
                $html .= "<td>{$valor}</td>";
            }
            $html .= "</tr>";
            $html .= "</table>";
            $html .= "<a class='enlacedetallada' href='#arriba'>Ir arriba</a>";
            $html .= "<br/><br/>";
            unset( $diferencia );
        }
        // Listado detallado de Ocupacion Puntual y despachos horas
        $html .= $this->listadoDetalladoOcupacion();
        $html .= <<<EOD
        <div id="dialog">
        	<div id="subcarga">
        		<img src='../estilo/custom-theme/images/ajax-loader.gif' alt='Cargando' />
        	</div>
        </div>
        <script type="text/javascript">
       		$('.consumo').click(function(){ 
       			$('.consumo').removeClass('ui-widget-header');
       			$('.ocupacion').removeClass('ui-widget-header');
 				$(this).addClass('ui-widget-header'); 
				var datos = this.id.split('#'); 
				var titulo = datos[1].toUpperCase() + 'S ' + datos[0] + ' ' + datos[2] +'-'+datos[3];
				$('#dialog').html('');
				$('#dialog').dialog({ autoOpen:false, title: titulo, width: 600 });
				$.post('procesa.php',{cliente:this.id},function(data){ 
					$('#dialog').html(data);
    			}); 
				$('.consumo').ajaxStop(function(){ $('#dialog').dialog('open');});	 
			});
        </script>
EOD;
        return $html;
    }
    /**
     * Funcion que muestra el listado detallado de consumo de servicios,
     * como categorias
     * 
     * @return string
     */
    public function listadoDetalladoServicios ()
    {
        foreach ($this->serviciosExternos() as $servicio) {
            $consumos[$servicio['Servicio']] = array_fill( 0, 
            $this->diferencia(), 0 );
            $fechas[$servicio['Servicio']] = array_fill( 0, 
            $this->diferencia(), 0 );
        }
        foreach ($this->serviciosExternos() as $servicio) {
            $nombreServicio = $servicio['Servicio'];
            $mesServicio = Cni::verMes( $servicio['fecha'] );
            $anyoServicio = Cni::verAnyo( $servicio['fecha'] );
            if ($this->anyoInicial < $anyoServicio) {
                $valor = 11;
            } else {
                $valor = - 1;
            }
            $consumos[$nombreServicio][$mesServicio + $valor] += 1;
            $fechas[$nombreServicio][$mesServicio + $valor] = "{$mesServicio}#{$anyoServicio}";
        }
        $html = "<table class='listadetallada'><tr><th>Servicio</th>";
        foreach ($this->mesesRango() as $mes) {
            $html .= "<th class='datosdetalladosservicios'>{$mes}</th>";
        }
        $html .= "<th class='datosdetalladosservicios'>Total</th>";
        $html .= "</tr>";
        $j = 0;
        foreach ($this->serviciosExternos( true ) as $servicio) {
            $nombreServicio = $servicio['Servicio'];
            $tituloServicio = ucfirst( strtolower( $nombreServicio ) );
            $html .= "<tr id='{$j}' class='ui-widget-content celda'>
            <td class='seccionlistadetallada servicio{$j} celdaServicio'>{$tituloServicio}</td>";
            $total = 0;
            foreach ($this->mesesRango() as $key => $mes) {
                $total += $consumos[$nombreServicio][$key];
                $html .= "<td id='servicio#{$tituloServicio}#{$fechas[$nombreServicio][$key]}' 
                class='servicio{$j} celdaServicio consumo'>{$consumos[$nombreServicio][$key]}
                </td>";
            }
            $html .= "<td class='servicio{$j} celdaServicio'>{$total}</td>";
            $html .= "</tr>";
            $j ++;
        }
        $html .= "</table>";
        $html .= "<div id='dialog'><div id='subcarga'>
        <img src='../estilo/custom-theme/images/ajax-loader.gif' alt='Cargando'></img></div></div>";
        $html .= <<<EOD
        <script type="text/javascript">
        	$('.celda').click(function(){ 
        		$('.celdaServicio').removeClass('ui-widget-header');
        		$('.servicio'+this.id).addClass('ui-widget-header'); 
    		});
        	$('.consumo').click(function(){
				var datos = this.id.split('#'); 
				var titulo = decodeURI(datos[1]) + ' ' + datos[2] +'-'+datos[3];
				$('#dialog').html('');
				$('#dialog').dialog({ autoOpen: false, title: titulo, width: 600}); 			 
				$.post('procesa.php',{servicio:this.id},function(data){ 
					$('#dialog').html(data);
    			}); 
		 		$('.consumo').ajaxStop(function(){ $('#dialog').dialog('open');});
			});
        </script>
EOD;
        return $html;
    }
    /**
     * Funcion que devuelve el listado detallado de ocupaciones
     * Puntuales y despachos hora
     * 
     * @return string
     */
    function listadoDetalladoOcupacion ()
    {
        /*
          * Llenado de Salas Despachos Media y Completa
          */
        foreach ($this->_serviciosOcupacion as $servicio) {
            if ($servicio['Tipo'] == 'Sala') {
                if ($servicio['Tiempo'] == 'Media') {
                    $serviciosOcupacionSalaMedia[$servicio['Nombre']] = $this->cuentaServiciosPorMes( 
                    $servicio['Nombre'], true );
                }
                if ($servicio['Tiempo'] == 'Completa') {
                    $serviciosOcupacionSalaCompleta[$servicio['Nombre']] = $this->cuentaServiciosPorMes( 
                    $servicio['Nombre'], true );
                }
            }
            if ($servicio['Tipo'] == 'Despacho') {
                if ($servicio['Tiempo'] == 'Media') {
                    $serviciosOcupacionDespachoMedia[$servicio['Nombre']] = $this->cuentaServiciosPorMes( 
                    $servicio['Nombre'], true );
                }
                if ($servicio['Tiempo'] == 'Completa') {
                    $serviciosOcupacionDespachoCompleta[$servicio['Nombre']] = $this->cuentaServiciosPorMes( 
                    $servicio['Nombre'], true );
                }
            }
        }
        $datosOcupacionSalaMedia = array_fill( 0, $this->diferencia(), 0 );
        foreach ($serviciosOcupacionSalaMedia as $servicio) {
            foreach ($servicio as $key => $dato) {
                $datosOcupacionSalaMedia[$key] += $dato;
            }
        }
        $datosOcupacionSalaCompleta = array_fill( 0, $this->diferencia(), 0 );
        foreach ($serviciosOcupacionSalaCompleta as $servicio) {
            foreach ($servicio as $key => $dato) {
                $datosOcupacionSalaCompleta[$key] += $dato;
            }
        }
        $datosOcupacionDespachoMedia = array_fill( 0, $this->diferencia(), 0 );
        foreach ($serviciosOcupacionDespachoMedia as $servicio) {
            foreach ($servicio as $key => $dato) {
                $datosOcupacionDespachoMedia[$key] += $dato;
            }
        }
        $datosOcupacionDespachoCompleta = array_fill( 0, $this->diferencia(), 
        0 );
        foreach ($serviciosOcupacionDespachoCompleta as $servicio) {
            foreach ($servicio as $key => $dato) {
                $datosOcupacionDespachoCompleta[$key] += $dato;
            }
        }
        // Llenado datos despachos Hora
        foreach ($this->_horasDespacho as $servicio) {
            $horasDespacho[$servicio] = $this->cuentaServiciosPorMes( 
            $servicio );
        }
        $datosHorasDespacho = array_fill( 0, $this->diferencia(), 0 );
        foreach ($horasDespacho as $servicio) {
            foreach ($servicio as $key => $dato) {
                $datosHorasDespacho[$key] += $dato;
            }
        }
        // Ocupacion Puntual Salas
        $totalSalaCompleta = array_sum( $datosOcupacionSalaCompleta );
        $totalSalaMedia = array_sum( $datosOcupacionSalaMedia );
        $totalDespachoCompleta = array_sum( $datosOcupacionDespachoCompleta );
        $totalDespachoMedia = array_sum( $datosOcupacionDespachoMedia );
        $totalSalas = $totalSalaCompleta + $totalSalaMedia;
        $totalDespachos = $totalDespachoCompleta + $totalDespachoMedia;
        $html = "<h4><a name='puntualSalas'>Ocupación Puntual Salas</a></h4>";
        $html .= "<table class='listadetallada'>
       <tr><th>&nbsp;</th>";
        foreach ($this->mesesRango() as $mes) {
            $html .= "<th class='datosdetalladosservicios'>{$mes}</th>";
        }
        $html .= "<th class='datosdetalladosservicios'>Total</th>";
        $html .= "</tr>";
        $html .= "<tr class='ui-widget-content celda'><td>Jornada Completa</td>";
        foreach ($datosOcupacionSalaCompleta as $key => $dato) {
            $html .= "<td class='ocupacion' id='Sala#Completa#{$key}'>{$dato}</td>";
        }
        $html .= "<td>{$totalSalaCompleta}</td>";
        $html .= "<tr class='ui-widget-content celda'><td>Media Jornada</td>";
        foreach ($datosOcupacionSalaMedia as $key => $dato) {
            $html .= "<td class='ocupacion' id='Sala#Media#{$key}'>{$dato}</td>";
        }
        $html .= "<td>{$totalSalaMedia}</td>";
        $html .= "</tr>";
        $html .= "<tr class='ui-widget-content celda'><td>Total</td>";
        foreach ($this->mesesRango() as $key => $mes) {
            $totalMes = $datosOcupacionSalaCompleta[$key] +
             $datosOcupacionSalaMedia[$key];
            $html .= "<td>{$totalMes}</td>";
        }
        $html .= "<td>{$totalSalas}</td>";
        $html .= "</tr>";
        $html .= "</table>";
        $html .= "<a class='enlacedetallada' href='#arriba'>Ir arriba</a>";
        $html .= "<br/><br/>";
        /*
        * Ocupacion Puntual Despachos
        */
        $html .= "<h4><a name='puntualDespachos'>Ocupación Puntual Despachos</a></h4>";
        $html .= "<table class='listadetallada'>
       <tr><th>&nbsp;</th>";
        foreach ($this->mesesRango() as $mes) {
            $html .= "<th class='datosdetalladosservicios'>{$mes}</th>";
        }
        $html .= "<th class='datosdetalladosservicios'>Total</th>";
        $html .= "</tr>";
        $html .= "<tr class='ui-widget-content celda'><td>Jornada Completa</td>";
        foreach ($datosOcupacionDespachoCompleta as $key => $dato) {
            $html .= "<td class='ocupacion' id='Despacho#Completa#{$key}'>{$dato}</td>";
        }
        $html .= "<td>{$totalDespachoCompleta}</td>";
        $html .= "<tr class='ui-widget-content celda'><td>Media Jornada</td>";
        foreach ($datosOcupacionDespachoMedia as $key => $dato) {
            $html .= "<td class='ocupacion' id='Despacho#Media#{$key}'>{$dato}</td>";
        }
        $html .= "<td>{$totalDespachoMedia}</td>";
        $html .= "</tr>";
        $html .= "<tr class='ui-widget-content celda'><td>Total</td>";
        foreach ($this->mesesRango() as $key => $mes) {
            $totalMes = $datosOcupacionDespachoCompleta[$key] +
             $datosOcupacionDespachoMedia[$key];
            $html .= "<td>{$totalMes}</td>";
        }
        $html .= "<td>{$totalDespachos}</td>";
        $html .= "</tr>";
        $html .= "</table>";
        $html .= "<a class='enlacedetallada' href='#arriba'>Ir arriba</a>";
        $html .= "<br/><br/>";
        /*
        * Despachos/Sala Horas
        */
        $totalHorasDespacho = array_sum( $datosHorasDespacho );
        $html .= "<h4><a name='despachosSalaHoras'>Despacho/Sala Horas</a></h4>";
        $html .= "<table class='listadetallada'>
       <tr><th>&nbsp;</th>";
        foreach ($this->mesesRango() as $mes) {
            $html .= "<th class='datosdetalladosservicios'>{$mes}</th>";
        }
        $html .= "<th class='datosdetalladosservicios'>Total</th>";
        $html .= "</tr>";
        $html .= "<tr class='ui-widget-content celda'><td>Horas</td>";
        foreach ($datosHorasDespacho as $key => $dato) {
            $html .= "<td class='ocupacion' id='Horas#Completa#{$key}'>{$dato}</td>";
        }
        $html .= "<td>{$totalHorasDespacho}</td>";
        $html .= "</tr>";
        $html .= "</table>";
        $html .= "<a class='enlacedetallada' href='#arriba'>Ir arriba</a>";
        $html .= "<br/><br/>";
        $html .= <<<EOD
	   <script type="text/javascript">
	  		$('.ocupacion').click(function(){
  
        		$('.ocupacion').removeClass('ui-widget-header');
        		$('.consumo').removeClass('ui-widget-header');
        		$(this).addClass('ui-widget-header'); 
    		
				$('#dialog').html('');
				$('#dialog').dialog({ autoOpen: false, width: 600}); 			 
				$.post('procesa.php',{ocupacion:this.id,inicial:$('#inicio').val(),fin:$('#fin').val()},function(data){ 
					$('#dialog').html(data);
    			}); 
		 		$('.ocupacion').ajaxStop(function(){ $('#dialog').dialog('open');});
			});
	   
	   </script>
       
EOD;
        return $html;
    }
}