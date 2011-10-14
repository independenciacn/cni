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
session_start();
switch ($_POST['opcion'])
{
    case 0: $respuesta = valida( $_POST );
    break;
}
echo $respuesta;
/**
 * Funcion que valida a los usuarios
 * 
 * @param array $vars
 */
function valida ( $vars )
{
    include 'variables.php';
    $password = sha1( mysql_real_escape_string( $vars['passwd'], $con ) );
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
/**
 * Generamos el menu de la aplicacion
 */
function menu()
{
    include 'variables.php';
    $sql = "Select * from menus";
    $consulta = mysql_query( $sql, $con );
    $tabla = "<table width='100%'><tr>";
    while ( true == ($resultado = mysql_fetch_array( $consulta ) )) {
        switch ($resultado[0]) {
            case 7:
                $tabla .= "<th><a href='javascript:datos(1)'>
							<img src='" .
                 $resultado[3] . "' alt='" . $resultado[1] . "' width='32'/>
							<p />" . $resultado[1] . "</a></th>";
                break;
            case 8:
                $tabla .= "<th><a href='javascript:datos(2)'>
							<img src='" .
                 $resultado[3] . "' alt='" . $resultado[1] . "' width='32'/>
							<p />" . $resultado[1] . "</a></th>";
                break;
            case 9:
                $tabla .= "<th><a href='javascript:datos(3)'>
							<img src='" .
                 $resultado[3] . "' alt='" . $resultado[1] . "' width='32' />
							<p />" . $resultado[1] . "</a></th>";
                break;
            default:
                $tabla .= "<th><a href='javascript:menu(" . $resultado[0] . ")'>
							<img src='" .
                 $resultado[3] . "' alt='" . $resultado[1] . "' width='32'/>
							<p/>" . $resultado[1] . "</a></th>";
                break;
        }
    }
    $tabla .= "<th><a href='inc/logout.php'>
    <img src='imagenes/salir.png' width='32' alt='Salir'><p/>Salir<a></th>
    </tr></table><div id='principal'></div>";
    return $tabla;
}
