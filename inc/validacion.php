<?php
/**
 * Validacion File Doc Comment
 * 
 * Fichero encargado de la validacion de usuarios y generacion del menu
 * 
 * PHP Version 5.2.6
 * 
 * @category Validacion
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */ 
require_once 'variables.php';
if ( isset( $_POST['usuario'] )  && isset( $_POST['password'] ) ) {
    valida( $_POST );
    header( 'Location:../index.php' );
    exit;
}
/**
 * Funcion que valida a los usuarios
 * 
 * @param array $vars
 */
function valida ( $vars )
{
    global $con;
    $password = sha1( mysql_real_escape_string( $vars['password'], $con ) );
    $usuario = mysql_real_escape_string( $vars['usuario'], $con ); 
    $sql = "Select nick,contra FROM usuarios 
		where nick like '" .
        $usuario . "' and contra like '$password'";
    $consulta = mysql_query( $sql, $con );
    if (mysql_numrows( $consulta ) != 1) {
        $_SESSION['error'] = 1; 
    } else {
        $resultado = mysql_fetch_array( $consulta );
        if ( ( $usuario == $resultado['nick'] ) && 
                ( $password == $resultado['contra'] ) ) {
            $_SESSION['usuario'] = $usuario;
            unset( $_SESSION['error'] );  
        } else {
            $_SESSION['error'] = 1;
        }
    }
}

