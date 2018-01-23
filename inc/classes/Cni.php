<?php
/**
* Cni File Doc Comment
*
* Clases generales de la aplicación
*
* PHP Version 5.2.6
*
* @category Classes
* @package  CniClasses
* @author   Ruben Lacasa Mas <ruben@ensenalia.com>
* @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ CC BY-NC-ND 3.0
* @version  GIT: Id$ In development. Very stable.
* @link     https://github.com/independenciacn/cni
*/
/**
 * Clase de funciones generales de la aplicación class
 */
class CNI
{
    public function __construct()
    {
        // Por no hacerla statica o para posible herencia de Connection
    }
    /**
     * Devuelve el nombre del mes en base al numero
     * @param int $numMes numero del mes
     * @return string Nombre del mes
     */
    public function getMes($numMes)
    {
        $meses = array(
            1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Septiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre"
        );
        return (array_key_exists($numMes, $meses)) ? $meses[$numMes]: "";
        return $mes;
    }
    /**
     * Devuelve la fecha con el nombre del mes
     * @param string $fecha Fecha a formatear
     * @param string $format Formato de la fecha enviada
     * @return string Fecha ya convertida
     */
    public function getFechaConNombreMes($fecha, $format = 'Y-m-d')
    {
        $date = new DateTime($fecha, new DateTimeZone('Europe/Madrid'));
    
    //    $date = date_create_from_format($format, $fecha, new DateTimeZone('Europe/Madrid'));
        return $date->format('d'). " de ". $this->getMes($date->format('n')). " de ". $date->format('Y');
    }
}
