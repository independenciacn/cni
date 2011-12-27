<?php
/**
 * Valida File Doc Comment
 *
 * Fichero encargado de la validacion de usuarios
 *
 * PHP Version 5.2.6
 *
 * @category Valida
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once 'variables.php';
checkSession();
if ( isset( $_POST['usuario'] ) && isset( $_POST['passwd']) ){
    $usuario = mysql_real_escape_string($_POST['usuario'], $con);
    $passwd = mysql_real_escape_string($_POST['passwd'], $con);
    $sql = "SELECT 1 from usuarios
    WHERE nick like '".$usuario."' 
    AND contra like sha1('".$passwd."')";
    $consulta = mysql_query( $sql, $con );
    if ( mysql_num_rows($consulta) == 1 ) {
        // TODO OK
        $_SESSION['usuario'] = $usuario;
        header("Location:../index.php");
        exit(0);
    } else {
        // KO
        header("Location:../index.php?error=1");
        exit(0);
    }
} else {
    // KO
    header("Location:../index.php?error=1");
    exit(0);
}