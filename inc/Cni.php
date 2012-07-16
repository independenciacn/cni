<?php
require_once 'CniDB.php';
/** 
 * @author ruben
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
     * Establece el tipo de devolucion de datos
     * 
     * @param number|null $type
     */
    private static function setType ( $type ) 
    {
        if ( !is_null($type) ) {
            $self::$_type = $type;
        }
    }
    /**
     * Ejecuta la consulta y devuelve los resultados
     *
     * @param string $sql
     * @param integer $type PDO::FETCH_BOTH, PDO::FETCH_ASSOC
     * @return resource
     */
    public static function consulta( $sql, $type = null )
    {
        try {
            self::setType($type);
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
     * @param int $type
     */
    public static function consultaPreparada($sql, $params, $type = null )
    {
        try {
            self::setType($type);
            self::$_con = CniDB::connect();
            self::$_query = self::$_con->prepare($sql);
            self::$_query->execute($params);
            return self::$_query->fetchAll(self::$_type);
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
    /**
     * Genera la tabla de resultados pasandole la consulta Sql
     * 
     * @param unknown_type $sql
     * @param unknown_type $titulo
     * @param unknown_type $subtitulo
     * @return string $tabla
     */
    public static function generaTablaDatos($sql, $titulo = null)
    {
        $resultados = self::consulta($sql);
        $totalResultados = self::totalDatosConsulta();
        $totalColumnas = self::totalColumnasConsulta();
        $tabla = "";
        $datosCabezera = "<tr>";
        $datosCuerpo = "";
        $datosPie = "";
        $celda = 0;
        $totalColumna = array_fill(0, $totalColumnas - 1, null);
        $cabezera = true;
        if ( $totalResultados > 0 ) {
	        foreach ($resultados as $resultado) {
		        $datosCuerpo .= "<tr class='".self::clase($celda++)."'>";
		        foreach ($resultado as $key => $var) {
		            if ( $cabezera && !is_numeric($key) ) {
		                $datosCabezera .="<th>".$key."</th>";
		            }
		            if ( is_numeric($key) ) {
		                $datosColumna = self::datosColumna($key);
		                $datosCuerpo .="<td>".
		                    self::formateaCampo(
		                        $var, 
		                        $datosColumna['native_type']
		                        )
		                ."</td>";
		                if ( is_numeric($var) ) {
		                    $totalColumna[$key] = $totalColumna[$key] + $var;
		                }
		            }
		        }
		        $cabezera = false;
		        $datosCuerpo .= "</tr>";
		    }
        } else {
		    $datosCabezera .="<th>No Hay Resultados</th>";
	    }
	    $datosCabezera .= "</tr>";
	    // Ponemos los datos del pie de la tabla
	    $datosPie .= "<tr>";
	    for ($i = 0; $i < $totalColumnas; $i++) {
	        $datosPie .= "<th>";
	        if ( !is_null($totalColumna[$i]) ) {
	            $datosPie .= Cni::formateaNumero($totalColumna[$i]);
	        }
	        $datosPie .= "</th>";
	    }
	    $datosPie .= "</tr>";
	    // Guardamos la tabla final
	    $tabla .= "
	        <table class='tabla' width='100%'>
	            <caption>".$titulo."</caption>
	            <thead>".$datosCabezera."</thead>
	            <tbody>".$datosCuerpo."</tbody>
	            <tfoot>".$datosPie."</tfoot>
	        </table>";

	    return $tabla;
    }
    
    
}
