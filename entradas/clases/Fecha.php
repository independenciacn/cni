<?php
/**
 * Fecha File Doc Comment
 * 
 * Fichero de clase Fecha, controla las fechas
 * 
 * PHP Version 5.2.6
 * 
 * @category Fecha
 * @package  cni/entradas/clases
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
/**
 * Fecha Class Doc Comment
 * 
 * @category Class
 * @package  cni/entradas/classes
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @version  Release: 1.0
 * @link     https://github.com/independenciacn/cni
 *
 */
class Fecha
{
    /**
     * Constructor: establece el timezone a Europe/Madrid
     */
    public function __construct ()
    {
        date_default_timezone_set( 'Europe/Madrid' );
    }
    /**
     * Funcion que devuelve el nombre de los meses
     * 
     * @return array $meses
     */
    public function getMeses ()
    {
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", 
        "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", 
        "Diciembre");
        return $meses;
    }
    /**
     * Funcion que devuelve un array con el nombre de los meses cortos
     * 
     * @return array $meses
     */
    public function getMesesCortos ()
    {
        $meses = array("", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", 
        "Ago", "Sep", "Oct", "Nov", "Dic");
        return $meses;
    }
    /**
     * Funcion Auxiliar que cambia la fecha del formato MySQl al 
     * castellano y viceversa
     * 
     * @param string $fecha
     * @return string
     */
    public function cambiaf ($fecha)
    {
        $dia = explode( "-", $fecha );
        return $dia[2] . "-" . $dia[1] . "-" . $dia[0];
    }
    /**
     * Funcion Auxiliar que devuelve el dia pasado por parametro
     * 
     * @param string $fecha
     * @return string
     */
    public function verDia ($fecha)
    {
        return date( "j", strtotime( $fecha ) );
    }
    /**
     * Funcion Auxiliar que devuelve el mes pasado por parametro
     * 
     * @param string $fecha
     * @return string
     */
    public function verMes ($fecha)
    {
        return date( "n", strtotime( $fecha ) );
    }
    /**
     * Funcion Auxiliar que devuelve el año pasado como parametro
     * 
     * @param string $fecha
     * @return string
     */
    public function verAnyo ($fecha)
    {
        return date( 'Y', strtotime( $fecha ) );
    }
}