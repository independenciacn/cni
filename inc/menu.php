<?php
/**
* menu File Doc Comment
*
* Genera el menu de la aplicacion
*
* PHP Version 5.2.6
*
* @category rapido
* @package  CniRapido
* @author   Ruben Lacasa Mas <ruben@ensenalia.com>
* @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ CC BY-NC-ND 3.0
* @version  GIT: Id$ In development. Very stable.
* @link     https://github.com/independenciacn/cni
*/
require_once 'variables.php';
require_once 'classes/Cni.php';
require_once 'classes/Connection.php';
$cni = new CNI();
$cni->checkSession();
$tabla = "";
if (isset($_SESSION['usuario'])) {
    $con = new Connection();
    $sql = "Select id, nombre, imagen from menus";
    $resultados = $con->consulta($sql, array(), PDO::FETCH_CLASS);
    $tabla ="<div id='menu_general'>";
    $tabla .= "<table width='100%'><tr>";
    foreach ($resultados as $resultado) {
        switch ($resultado->id) {
            case 7:
                $tabla .="<th><a href='javascript:datos(1)'>
                    <img src='".$resultado->imagen."' alt='".$resultado->nombre."' width='32'/>
                    <p />".$resultado->nombre."</a></th>";
                break;
            case 8:
                $tabla .="<th><a href='javascript:datos(2)'>
                    <img src='".$resultado->imagen."' alt='".$resultado->nombre."' width='32'/>
                    <p />".$resultado->nombre."</a></th>";
                break;
            case 9:
                $tabla .="<th><a href='javascript:datos(3)'>
                    <img src='".$resultado->imagen."' alt='".$resultado->nombre."' width='32' />
                    <p />".$resultado->nombre."</a></th>";
                break;
            default:
                $tabla .= "<th><a href='javascript:menu(".$resultado->id.")'>
                    <img src='".$resultado->imagen."' alt='".$resultado->nombre."' width='32'/>
                    <p/>".$resultado->nombre."</a></th>";
                break;
        }
    }
    $tabla .="<th><a href='inc/logout.php'>
    <img src='imagenes/salir.png' width='32' alt='Salir'><p/>Salir<a></th>";
    $tabla .= "</tr></table>";
    $tabla .= "<div id='principal'></div>";
    $tabla .= "</div>";
}
echo $tabla;
