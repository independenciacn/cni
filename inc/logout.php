<?php
/**
 * Logout File Doc Comment
 * 
 * Cerramos la sesion y volvemos a la pagina de acceso
 * 
 * PHP Version 5.2.6
 * 
 * @category Logout
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
    session_start();
    session_destroy();
    header( "Location:../index.php?exit=0" );
    exit;
?>
