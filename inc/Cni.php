<?php
require_once 'CniDB.php';
/** 
 * @author ruben
 * 
 */
final class Cni
{
    private static $_con;
    private static $_query;
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
    /**
     * Ejecuta la consulta y devuelve los resultados
     *
     * @param string $sql
     * @param integer $type PDO::FETCH_BOTH, PDO::FETCH_ASSOC
     * @return resource
     */
    public static function consulta( $sql, $type = PDO::FETCH_BOTH )
    {
        try {
            self::$_con = CniDB::connect();
            self::$_query = self::$_con->query($sql, $type);
            return self::$_query;
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }
    /**
     * Devuelve el numero de datos afectados en la consulta
     * 
     * @return number
     */
    public static function totalDatosConsulta()
    {
        return self::$_query->rowCount();
    }
    /**
     * Devuelve los datos relativos al numero de columna pasado
     * 
     * @param array $columna
     */
    public static function datosColumna($columna)
    {
        return self::$_query->getColumnMeta($columna);
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
    
    public static function formateaCampo($valor, $tipo)
    {
        switch ($tipo) {
            case 'DOUBLE': $valor = self::formateaNumero($valor);
            break;
            case 'LONG': $valor = self::formateaNumero($valor);
            break;
            case 'DATE': $valor = self::cambiaFormatoFecha($valor);
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
    
    
}
