<?php 
/**
 * Variables File Doc Comment
 * 
 * Variables usadas en la aplicacion
 * 
 * PHP Version 5.2.6
 * 
 * @category Index
 * @package  cni
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */

ini_set( 'mysql.default_host', 'localhost' );
ini_set( 'mysql.default_user', 'cni' );
ini_set( 'mysql.default_password', 'inc' );
$dbname = "centro";
$con = mysql_pconnect() or die ( mysql_error() );
if ( !mysql_set_charset( 'utf8', $con ) ) {
    die( mysql_error() );
}
if ( !mysql_select_db( $dbname, $con ) ) {
    die( mysql_error() );
}
// Constantes 
DEFINE( "OK", "imagenes/clean.png" ); //imagen en el mensaje de correcto
DEFINE( "NOK", "imagenes/error.png" ); //imagen en el mensaje de fallo
DEFINE( "SISTEMA", "*nix" );
// Funciones Genericas
/**
 * Devuelve el dia y el mes en formato Español
 * 
 * @param string $stamp
 * @return string
 */
function diaYmes( $stamp )
{
    $fecha = explode( "-", $stamp );
    return $fecha[2]."-".$fecha[1];
}
/**
 * Cambia el formato de fecha de un estandar a otro
 * 
 * @param string $stamp
 * @return string
 */
function cambiaf( $stamp )
{
    $fecha = explode( "-", $stamp );
    return $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0];
}
/**
 * Devuelve el dia y mes invertidos para la ordenacion
 * 
 * @param string $stamp
 * @return string
 */
function invierte( $stamp )
{
    $fecha = explode( "-", $stamp );
    return $fecha[1]."-".$fecha[0];
}
/**
 * Establece la clase de la tabla
 * 
 * @param integer $k
 * @return string $clase
 */
function clase( $k )
{
    $clase = "impar";
    if ( $k%2 == 0 ) {
        $clase = "par";
    }
    return $clase;
}
