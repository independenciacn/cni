<?php
/**
 * Variables File Doc Comment
 *
 * Funciones y variables requeridas por las funciones de la aplicacion
 *
 * PHP Version 5.2.6
 *
 * @category Inc
 * @package  CniInc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ CC BY-NC-ND 3.0
 * @version  GIT: $Id$ In development. Very stable.
 * @link     https://github.com/independenciacn/cni
 */

error_reporting(0);
$localhost = 'localhost';
if (getenv('MYSQL_HOSTNAME')) {
    $localhost = getenv('MYSQL_HOSTNAME');
}
/**
 * Establecemos la zona horaria
 */
date_default_timezone_set('Europe/Madrid');
/**
 * Version de la aplicación
 *
 * @var string
 */
define('VERSION', "2.0e");
/**
 * Titulo de la aplicación
 *
 * @var string
 */
define('APLICACION', 'Aplicación Gestión Independencia Centro Negocios');
/**
 * Iva Generico a utilizar en la aplicación
 *
 * @var integer
 */
define('IVA', 18);
/**
 * Precio Generico del almacenaje
 *
 * @var integer
 */
define('ALMACENAJE', 0.70);
define('FORMA_PAGO', 'Transferencia');
define('NUMERO_CUENTA', 'ES88 0049 2833 91 2116206154');

/**
 * Conexión a la base de datos
 *
 * @var resource
 * @deprecated
 */
$con = mysql_connect($localhost, "cni", "inc") or die(mysql_error());
mysql_set_charset('utf8', $con);
/**
 * Nombre de la tabla
 *
 * @deprecated - establecerlo dentro de la funcion mysql_select_db
 * @var string
 */
$dbname = "centro";
mysql_select_db($dbname, $con);
/**
 * Imagen en el mensaje de correcto
 *
 * @deprecated - Estan siendo retiradas de donde aparecian
 * @var unknown_type
 */
define("OK", "imagenes/clean.png");
/**
 * Imagen en el mensaje de error
 * @deprecated - Estan siendo retiradas de donde aparecian
 * @var unknown_type
 */
define("NOK", "imagenes/error.png");
//define("SISTEMA","*nix");
/**
 * Define el sistema operativo donde va a trabajar la aplicacion
 *
 * @var unknown_type
 */
define("SISTEMA", "windows");
setlocale(LC_ALL, 'es_ES');
setlocale(LC_NUMERIC, 'es_ES');
/**
 * Devuelve el precio formateado con 2 decimales separados por , miles . y
 * el simbolo del Euro;
 * @param integer $number
 * @deprecated Integrar en Cni
 */
function formatoDinero($number)
{
    if (SISTEMA == "windows") {
        $number = number_format($number, 2, ',', '.')."&euro;";
    } else {
        $number = money_format('%n', $number);
    }
    return $number;
}
/**
 * Devuelve el numero formateado con 2 decimales separados por , y miles .
 * @param unknown_type $number
 * @deprecated Integrar en Cni
 */
function formatoNoDinero($number)
{
    $number = number_format($number, 2, ',', '.');
    return $number;
}
/**
 * Chequea si la sesion se ha iniciado
 * @deprecated Integrar en Cni
 */
function checkSession()
{
    if (session_id() != null) {
        session_regenerate_id();
    } else {
        session_start();
    }
}
/**
 * Devuelve el tipo de clase css que sera el campo
 *
 * @param integer $k
 * @return string
 * @deprecated Utilizar forma directa
 */
function clase($k)
{
    $clase = ( $k%2 == 0)? 'par': 'impar';
    return $clase;
}
/**
 * Se le puede pasar como parametro un array o una string y la sanea
 *
 * @param mixed $vars
 * @deprecated Utilizar el filter_input
 */
function sanitize(&$vars)
{
    global $con;
    if (is_array($vars)) {
        foreach ($vars as &$var) {
            mysql_real_escape_string($var, $con);
        }
    } elseif (is_string($vars)) {
        mysql_real_escape_string($vars, $con);
    }
}
/**
 * Convierte el texto a utf8
 *
 * @deprecated
 * @param string $texto
 * @return string $texto
 */
function traduce($texto)
{
    return $texto;
}
/**
 * Traduce el texto de utf8
 *
 * @deprecated
 * @param string $texto
 * @return string $texto
 */
function codifica($texto)
{
    return $texto;
}
