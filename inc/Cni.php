<?php
/**
 * Cni File Doc Comment
 *
 * Clase y Metodos Estaticos de la Aplicacion
 *
 *
 * PHP Version 5.2.6
 *
 * @author  Ruben Lacasa <ruben@ensenalia.com>
 * @package cniEstable/inc
 * @license Creative Commons Atribución-NoComercial-SinDerivadas 3.0 Unported
 * @version 2.0e Estable
 * @link    https://github.com/sbarrat/cniEstable
 */
require_once 'CniDB.php';
/**
 * Cni Class Doc Comment
 *
 * Funciones estaticas de la aplicación
 *
 */
final class Cni
{
    private static $_con = null;
    private static $_query = null;
    private static $_type = PDO::FETCH_BOTH;
    public static $meses = array (
        1=>"Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
        );
    public static $mesesCortos = array (
    	1=>"Ene",
    	"Feb",
    	"Mar",
    	"Abr",
    	"May",
    	"Jun",
    	"Jul",
    	"Ago",
    	"Sep",
    	"Oct",
    	"Nov",
    	"Dic"
    	);
    public static $cambiosIva = array(
    		array(
    				'fecha' => '2010-07-01',
    				'ivaAnterior' => 16,
    				'ivaGenerico' => 18
    		),
    		array(
    				'fecha' => '2012-09-01',
    				'ivaAnterior' => 18,
    				'ivaGenerico' => 21
    		)
    );
    /**
     * Para una fecha en un formato y devuelve la fecha con el año y dia
     * cambiado de sitio MySql - Normal , Normal - MySql
     * 
     * @param string $fechaOriginal
     * @return string
     */
    public static function cambiaFormatoFecha($fechaOriginal)
    {
        //Dividimos la fecha de la hora si existe
        $partesStamp = explode(" ", $fechaOriginal);
        // La primera parte es la fecha, la segunda la hora
        $partesFecha = explode ( "-", $partesStamp[0] );
        // La fecha final
        return $partesFecha[2] ."-".$partesFecha[1]."-".$partesFecha[0];
    }
    /**
     * Funcion Auxiliar que devuelve el dia pasado por parametro
     * 
     * @param string $fecha
     * @return string
     */
    public static function verDia ($fecha)
    {
        return date( "j", strtotime( $fecha ) );
    }
    /**
     * Funcion Auxiliar que devuelve el mes pasado por parametro
     * 
     * @param string $fecha
     * @return string
     */
    public static function verMes ($fecha)
    {
        return date( "n", strtotime( $fecha ) );
    }
    /**
     * Funcion Auxiliar que devuelve el año pasado como parametro
     * 
     * @param string $fecha
     * @return string
     */
    public static function verAnyo ($fecha = false)
    {
        if ($fecha) {
            return date( 'Y', strtotime( $fecha ) );
        } else {
            return date( 'Y' );
        }
    }

    /**
     * Devuelve el importe con el iva
     *
     * @param number $importe
     * @param number $iva
     * @return number
     */
    public static function totalconIva($importe, $iva)
    {
        $total = ($importe * $iva/100) + $importe;
        return round($total, 2);
    }
    /**
     * Devuelve el numero formateado
     * @param number $numero
     * @param boolean $moneda si True devuelve como si fuera moneda
     * @return string
     */
    public static function formateaNumero($numero, $moneda = false)
    {
        $numero = number_format($numero, 2, ',', '.');
        $numero .= ( $moneda ) ? "&euro;" : "";
        return $numero;
    }
    public static function cambiaFormatoNumerico($numero)
    {
        $numero = str_replace('.', '', $numero);
        return str_replace(',', '.', $numero);
    }
    /**
     * Ejecuta la consulta y devuelve los resultados
     *
     * @param string $sql
     * @param integer $type PDO::FETCH_BOTH, PDO::FETCH_ASSOC
     * @return resource
     */
    public static function consulta($sql, $type = null)
    {
        try {
            if (!is_null($type)) {
                self::$_type = $type;
            }
            self::$_con = CniDB::connect();
            self::$_query = self::$_con->query($sql, self::$_type);
            return self::$_query;
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }
    /**
     * Ejecuta la consulta preparada, segura
     * 
     * @param string $sql
     * @param array $params
     * @param int $type PDO
     */
    public static function consultaPreparada($sql, $params, $type = null)
    {
        try {
            if (!is_null($type)) {
                self::$_type = $type;
            }
            self::$_con = CniDB::connect();
            self::$_query = self::$_con->prepare($sql);
            if (self::$_query->execute($params)) {
            	return self::$_query->fetchAll(self::$_type);
            } else {
            	return false;
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }
    /**
     * Devuelve el numero de datos afectados en la consulta
     * 
     * @return number|boolean
     */
    public static function totalDatosConsulta()
    {
        if ( !is_null(self::$_query) ) {
            return self::$_query->rowCount();
        } else {
            return false;
        }
    }
    /**
     * Devuelve el numero de columnas afectadas en la consulta
     * @return number|boolean
     */
    public static function totalColumnasConsulta()
    {
        if ( !is_null(self::$_query) ) {
            return self::$_query->columnCount();
        } else {
            return false;
        }
    }
    /**
     * Devuelve los datos relativos al numero de columna pasado
     * 
     * @param array $columna
     * @return string|boolean
     */
    public static function datosColumna($columna)
    {
        if ( !is_null(self::$_query) ) {
            return self::$_query->getColumnMeta($columna);
        } else {
            return false;
        }
    }
    /**
     * Chequea si la sesion se ha iniciado
     */
    public static function chequeaSesion()
    {
        if ( session_id() != null ) {
            session_regenerate_id();
        } else {
            session_start();
        }
    }
    /**
     * Formatea el campo dependiendo del tipo que sea en la base de datos
     * 
     * @param unknown_type $valor
     * @param string $tipo
     * @return string
     */
    public static function formateaCampo($valor, $tipo)
    {
        switch ($tipo) {
            case 'DOUBLE': $valor = self::formateaNumero($valor);
            break;
            case 'LONG': $valor = self::formateaNumero($valor);
            break;
        }
        return $valor;
    }
    /**
     * Devuelve el tipo de clase css que sera el campo
     *
     * @param integer $celda
     * @return string
     */
    public static function clase($celda)
    {
        return ( $celda % 2 == 0)? 'par': 'impar';
    }
    /**
     * Genera la tabla de resultados pasandole la consulta Sql
     * 
     * @param unknown_type $sql
     * @param unknown_type $titulo
     * @param unknown_type $subtitulo
     * @return string $tabla
     * @todo NO hacer que ejecute la consulta, sino que solo procese los datos
     */
    public static function generaTablaDatos($sql, $params, $titulo = null)
    {
        $type = PDO::FETCH_NUM;
    	if ($params) {
        	$resultados = self::consultaPreparada($sql, $params, $type);
        } else {
    		$resultados = self::consulta($sql, $type);
        }
        $totalResultados = self::totalDatosConsulta();
        $totalColumnas = self::totalColumnasConsulta();
        $tabla = "";
        $datosCabezera = "<tr>";
        $datosCuerpo = "";
        $totalColumna = array_fill(0, $totalColumnas, null);
        $cabezera = true;
        if ( $totalResultados > 0 && $totalResultados < 2000) {
	        foreach ($resultados as $resultado) {
		        $datosCuerpo .= "<tr>";
		        foreach ($resultado as $key => $var) {
		            $datosColumna = self::datosColumna($key);
		            if ( $cabezera) {
		                $datosCabezera .="<th>".$datosColumna['name']."</th>";
		            }
		            $datosCuerpo .= "<td>".
		                self::formateaCampo(
		                        $var,
		                        $datosColumna['native_type']
		                    )
		                ."</td>";
		            if ( is_numeric($var) ) {
		                $totalColumna[$key] = $totalColumna[$key] + $var;
		            }
		        }
		        $cabezera = false;
		        $datosCuerpo .= "</tr>";
		    }
        } else {
		    $datosCabezera .=
		    "<th>Total Resultados ".$totalResultados."</th>";
	    }
	    $datosCabezera .= "</tr>";
	    // Guardamos la tabla final
	    $tabla .= "
	        <table class='table table-striped table-condensed'>
	            <caption><strong>".$titulo."</strong></caption>
	            <thead>".$datosCabezera."</thead>
	            <tbody>".$datosCuerpo."</tbody>
	            <tfoot>".self::pieTabla($totalColumna)."</tfoot>
	        </table>";
	    return $tabla;
    }
    /**
     * Generamos el pie de la  tabla
     * @param unknown_type $totalColumna
     * @return string $datosPie
     */
    private static function pieTabla($totalColumna)
    {
        $datosPie = "<tr>";
        foreach ($totalColumna as $total) {
            $datosPie .= "<th>";
            $datosPie .= is_null($total) ? "" : self::formateaNumero($total);
            $datosPie .= "</th>";
        }
        $datosPie .= "</tr>";
        return $datosPie;
    }
    /**
     * [mensajeError description]
     * 
     * @param  [type] $mensaje [description]
     * 
     * @return [type]          [description]
     */
    public static function mensajeError($mensaje)
    {
        $html = "<span class='alert alert-danger'><strong>Error:</strong> ".
            $mensaje."</span>";
        return $html;
    }
    /**
     * [mensajeExito description]
     * 
     * @param  [type] $mensaje [description]
     * 
     * @return [type]          [description]
     */
    public static function mensajeExito($mensaje)
    {
        $html = "<span class='alert alert-success'>".$mensaje."</span>";
        return $html;
    }
}

