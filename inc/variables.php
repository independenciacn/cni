<?php //aqui pondremos las diferencias de variables entre windows mac y linux
//nombre de la base de datos
// Establecemos el nivel de error de la aplicacion
/*error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('html_errors', 1);*/
// Establecemos la zona horaria 
date_default_timezone_set('Europe/Madrid'); 
// Base de datos
$con = mysql_connect ("localhost","cni","inc") or die (mysql_error());
mysql_set_charset('utf8', $con);
//mara mac y linux -- DEPRECATED - Por compatibilidad
$dbname = "centro"; 
//Establecemos la base de datos por defecto -- FINAL
mysql_select_db($dbname, $con);
// Constantes
define("OK", "imagenes/clean.png");//imagen en el mensaje de correcto
define("NOK","imagenes/error.png"); //imagen en el mensaje de fallo
//define("SISTEMA","*nix");
define("SISTEMA","windows");
//$sql = "SET NAMES 'utf8'";
//$sql = "SET NAMES 'latin1'";
//$consulta = mysql_query($sql,$con);
//Funciones Auxiliares
//Funcion para poner en la cabezera de los fichero para evitar el 
//headers already sent

function checkSession(){
    if ( session_id() != null ){
        session_regenerate_id();
    } else {
        session_start();
    }
}
/* Funciones Auxiliares */

function clase($k)
{
    if($k%2==0)
        $clase = "par";
    else
        $clase = "impar";
    return $clase;
}
/* Funciones Por compatibilidad */
function traduce($texto)
{
    return $texto;
}
function codifica($texto)
{
    return $texto;
}