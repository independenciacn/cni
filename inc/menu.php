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