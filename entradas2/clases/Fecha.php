<?php
class Fecha
{
    /*
	 * Constructor: establece el timezone a Europe/Madrid
	 */
    public function __construct ()
    {
        date_default_timezone_set('Europe/Madrid');
    }
    /*
	 * Funcion que devuelve el nombre de los meses
	 */
    public function get_meses ()
    {
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", 
        "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", 
        "Diciembre");
        return $meses;
    }
    /*
	 * Funcion que devuelve un array con el nombre de los meses cortos
	 */
    public function get_meses_cortos ()
    {
        $meses = array("", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", 
        "Ago", "Sep", "Oct", "Nov", "Dic");
        return $meses;
    }
    /*
	 * Funcion Auxiliar que cambia la fecha del formato MySQl al 
	 * castellano y viceversa
	 */
    public function cambiaf ($fecha)
    {
        $dia = explode("-", $fecha);
        return $dia[2] . "-" . $dia[1] . "-" . $dia[0];
    }
    /*
	 * Funcion Auxiliar que devuelve el dia pasado por parametro
	 */
    public function verDia ($fecha)
    {
        return date("j", strtotime($fecha));
    }
    /*
	 * Funcion Auxiliar que devuelve el mes pasado por parametro
	 */
    public function verMes ($fecha)
    {
        return date("n", strtotime($fecha));
    }
    /*
	 * Funcion Auxiliar que devuelve el año pasado como parametro
	 */
    public function verAnyo ($fecha)
    {
        return date('Y', strtotime($fecha));
    }
}