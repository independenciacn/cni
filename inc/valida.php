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
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
require_once 'variables.php';
require_once 'classes/Connection.php';
$params = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
checkSession();
//sanitize($_POST);
$location = "Location:../index.php?error=1";
if (isset($params['usuario']) && isset($params['passwd'])) {
    $con = new Connection();
    $sql = "SELECT 1 from usuarios
    WHERE nick like '".$params['usuario']."' 
    AND contra like sha1('".$params['passwd']."')";
    $result = $con->consulta($sql);
    if (count($result) == 1) {
        // TODO OK
        $_SESSION['usuario'] = $params['usuario'];
        $location = "Location:../index.php";
    }
}
header($location);
exit(0);
