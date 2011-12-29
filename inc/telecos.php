<?php
/**
 * Telecos File Doc Comment
 *
 * Fichero que genera el formulario y gestion de datos de las telecomunicaciones
 *
 * PHP Version 5.2.6
 *
 * @category Telecos
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
require_once 'variables.php';
checkSession();
sanitize($_POST);
switch($_POST['opcion'])
{
	case 0:$devuelve = campo_valores($_POST);break;
	case 1:$devuelve = chequea_valores($_POST);break;
	case 2:$devuelve = genera_extensiones($_POST);break;
}
echo $devuelve;
/**
 * Genera el formulario segun el tipo de campo que pide se 
 * muestra una cosa u otra
 * 
 * @param array $vars
 */ 
function campo_valores($vars)
{
	switch($vars['campo'])
	{
		case "Adsl":$devuelve =campo_texto();break;
		case "Telefono":$devuelve =campo_texto();break;
		case "Fax":$devuelve =campo_texto();break;
		case "Codigo Fotocopias":$devuelve =campo_texto();break;
		case "Codigo Fotocopias Autoservicio":$devuelve =campo_texto();break;
		case "Direccion IP":$devuelve = genera_ips();break;
		case "Codigo Negocio":$devuelve = campo_texto();break;
		case "Extension":$devuelve = filtro_extensiones();break;
		default:$devuelve = "Seleccione un valor";break;
	}
    return $devuelve;
}
/**
 * Formulario que genera el listado de las ip - rango del 172.26.0.1 -254
 * 
 * @return string $tabla
 */ 
function genera_ips()
{
//1.- Consultamos cuales estan libre y despues mostramos las ocupadas
	global $con;
	$sql = "Select valor from z_sercont where servicio like 'Direccion IP'";
	$consulta = mysql_query($sql,$con);
	$totaloc = mysql_numrows($consulta);
	$i=0;
	while(true == ($resultado = mysql_fetch_array($consulta)))
	{
		$ocupadas[$i++] = $resultado[0];
	}
	sort($ocupadas); //ordenamos la array
	reset($ocupadas); //liberamos la array
	$tabla = "<select id='valor' name='valor'>
			<option value='0'>--Seleccione IP--</option>";
	for($j=1;$j<=254;$j++) {
		$ip = "172.26.0.".$j;
		if(!in_array($ip,$ocupadas,false)){
		    $tabla .= "<option value='172.26.0.".$j."'>172.26.0.".$j."</option>";
	    }
	}
	$tabla .= "</select>";
	return $tabla;			
}
/**
 * Formulario que genera el listado de las ip - rango del 172.26.0.1 - 254
 * este es cuadro de texto que comprueba si existe el que le pasemos
 * 
 * @return string $tabla
 */
function campo_texto()
{
	$tabla = "<input id='valor' name='valor' type='text' 
	size='10' onkeyup='chequea_valor()' />";
	return $tabla;
}
/**
 * La extension se genera con el despacho y luego la extension
 * Quedando de tal manera DDEE pe. 0101, 2001
 * 
 * @return string $tabla
 */
function filtro_extensiones()
{
	$cadena="<select id='despacho' onchange='ver_extensiones()'>";
	$cadena.="<option value='0'>--Seleccione Despacho--</option>";
	for ($i=1;$i<=36;$i++)
	{
		if($i==23)
		$cadena.="<option value='".$i."'>Sala Juntas</option>";
		else
		$cadena.="<option value='".$i."'>Despacho ".$i."</option>";
	}
	$cadena.="</select>";
	$cadena.="<div id='s_extensiones'></div>";
	return $cadena;
}
/**
 * Genera las extensiones
 * 
 * @param array $vars
 * @return string $tabla
 */
function genera_extensiones($vars)
{
	global $con;
	if($vars['despacho']!=0){
		if($vars['despacho']<=9) {
			$despacho = "0".$vars['despacho'];
		}
		else {
			$despacho = $vars['despacho'];
		}
	    $sql = "Select valor from z_sercont 
	        where servicio like 'Extension' and valor like '".$despacho."%'";
	    $consulta = mysql_query($sql,$con);
	    $totaloc = mysql_numrows($consulta);
	    $i=0;
	    if(mysql_numrows($consulta)!=0) {
		    while(true == ($resultado = mysql_fetch_array($consulta))){
			    $ocupadas[$i++] = $resultado[0];
		    }
	    }
	    else {
		    $ocupadas = array();
	    }
	    sort($ocupadas); //ordenamos la array
	    reset($ocupadas); //liberamos la array
	    $tabla = "<select id='valor' name='valor'>
			    <option value='0'>--Seleccione Extension--</option>";
	    if($despacho == 23) {
				$tabla .="<option value='003'>003</option>";
	    } else {
	        for($j=1;$j<=4;$j++) {
			    $ip =$despacho.$j;
			    if(!in_array($ip,$ocupadas,false)) {
			        $tabla .= "<option value='".$ip."'>".$ip."</option>";
	            }
	        }
	    }
	    $tabla .= "</select>";
	}
	else {
		$cadena="Seleccione despacho";
	}
	return $tabla;		
}

/**
 * Funciones de chequeo - Busca valores genericos
 * @param array $vars
 * @return string
 */
function chequea_valores($vars)
{
	global $con;
	$sql = "Select * from z_sercont where servicio like '".$vars['campo']."' 
	and valor like '".$vars['valor']."'";
	$consulta = mysql_query($sql,$con);
	$total = mysql_numrows($consulta);
	$tabla = ($total == 0) ?  "#00ff00" : "#ff0000";
	return $tabla;
}
