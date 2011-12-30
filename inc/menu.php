<?php 
/**
 * Validacion File Doc Comment
 *
 * Solo genera el menu - Refractorizar el nombre a menu.php
 *
 * PHP Version 5.2.6
 *
 * @category Validacion
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
require_once 'variables.php';
checkSession();
/**
 * Genera el menu de la aplicacion
 * 
 * @return string $tabla
 */
function menu()
{
	global $con;
	$sql = "Select * from menus";
	//$consulta = mysql_query($sql,$con);
	$consulta = mysql_query($sql, $con);
	$tabla = "<table width='100%'><tr>";
	while (true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
		switch ($resultado[0]) {
		    case 7:	
		        $tabla .="<th><a href='javascript:datos(1)'>
			        <img src='".$resultado[3]."' alt='".$resultado[1]."' width='32'/>
				    <p />".$resultado[1]."</a></th>";
		    break;
		    case 8: 
		        $tabla .="<th><a href='javascript:datos(2)'>
				    <img src='".$resultado[3]."' alt='".$resultado[1]."' width='32'/>
				    <p />".$resultado[1]."</a></th>";
		    break;
		    case 9: 
		        $tabla .="<th><a href='javascript:datos(3)'>
				    <img src='".$resultado[3]."' alt='".$resultado[1]."' width='32' />
				    <p />".$resultado[1]."</a></th>";
		    break;
		    default:	
		        $tabla .= "<th><a href='javascript:menu(".$resultado[0].")'>
				    <img src='".$resultado[3]."' alt='".$resultado[1]."' width='32'/>
				    <p/>".$resultado[1]."</a></th>";
		    break;
		}	
	}
	$tabla .="<th><a href='inc/logout.php'>
	<img src='imagenes/salir.png' width='32' alt='Salir'><p/>Salir<a></th>";
	$tabla .= "</tr></table><div id='principal'></div>";
	return $tabla;
}