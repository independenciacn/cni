<?php
/**
 * Parametros_factura File Doc Comment
 *
 * Fichero que controla las funciones de gestion de los parametros de factura
 *
 * PHP Version 5.2.6
 *
 * @category Parametros_factura
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
	case 0:$respuesta=form_parametros_factura($_POST);break;
	case 1:$respuesta=establecer_fecha($_POST);break;
	case 2:$respuesta=agregar_agrupado($_POST);break;
	case 3:$respuesta=quitar_agrupado($_POST);break;
}
echo $respuesta;
/**
 * Genera el formulario de los parametros de factura
 * 
 * @param array $vars
 * @return string $cadena
 */
function form_parametros_factura($vars)
{
	//formulario de los parametros
	$cliente = $vars['cliente'];
	$cadena = "<input type='button' class='boton_cerrar' 
	onclick='cerrar_parametros_factura()' value='Cerrar' />";
	$cadena .= "<p/><b>ATENCION:Por defecto NO se Agrupan Franqueo,
	Consumo Tel&eacute;fono, Material de oficina, Secretariado y Ajuste. 
	Los intervalos de facturaci&oacute;n son por mes</b>";
	//$cadena .= "<form id='parametros_facturacion' onsubmit='alta_datos(); return false' />";
	//$cadena .= "<input type='hidden' name='idemp' id='idemp' value='".$vars[cliente]."' />"
	$cadena .= "<p/>Fecha Facturacion:";
	$cadena .= "<input type='text' id='fecha_facturacion' size='2' />&nbsp;"; 
	$cadena .= "<input type='button' onclick='establecer_fecha(".$cliente.")' 
	value='Establecer Fecha' />"; //caso de sony y el otro
	$cadena .= "<p/>Servicio:"; //Hay 4 fijos para todos
	$cadena .= listado_servicios();
	$cadena .= "<input type='button' onclick='agrupar_servicio(".$cliente.")' 
	value='Desagrupar servicio' />"; //caso de sony y el otro
	$cadena .= "<p/><b><u>Parametros Aplicados</u></b>";
	$cadena .= "<p>Ciclo de Factura:".fecha_factura($cliente);
	$cadena .= "<p>Servicios NO Agrupados:".servicios_agrupados($cliente);
	return $cadena;
}
/**
 * Devuelve el listado de los servicios
 * 
 * @return string
 */
function listado_servicios()
{
	global $con;
	$sql = "Select id,Nombre from servicios2 order by Nombre";
	$consulta = mysql_query($sql,$con);
	$cadena = "<select name='servicio' id='servicio'>";
	$cadena .= "<option value='0'>--Servicio--</option>";
	while(true == ($resultado = mysql_fetch_array($consulta))) {
		$cadena .= "<option value='".$resultado[0]."'>".$resultado[1]."</option>";
	}
	$cadena .= "</select>";
	return $cadena;
}
/**
 * Establece la fecha de facturacion
 * 
 * @param integer $cliente
 * @return string $cadena
 */
function fecha_factura($cliente)
{
	global $con;
	$sql = "Select * from agrupa_factura 
	where concepto like 'dia' and idemp like ".$cliente;
	$consulta = mysql_query($sql,$con);
	if (mysql_numrows($consulta)!=0) {
		$resultado = mysql_fetch_array($consulta);
		if ($resultado[3] == "") {
			$cadena = "<b>Mes Natural</b>";
		} else {
		    $cadena = "<b> Dia ".$resultado[3]." de cada mes</b>";
	    }
	} else {
		$cadena = "<b>Mes Natural</b>";
	}
	return $cadena;
}
/**
 * Muestra los servicios agrupados
 * 
 * @param unknown_type $cliente
 * @return string $cadena
 */
function servicios_agrupados($cliente)
{
	global $con;
	$cadena = "";
	$sql ="Select a.id ,s.Nombre from agrupa_factura as a 
	join servicios2 as s on a.valor = s.id 
	where a.concepto like 'servicio' and a.idemp like ".$cliente;
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)==0) {
		$cadena ="<b>No hay servicios</b>";
	} else {
		while(true == ($resultado = mysql_fetch_array($consulta))) {
			$cadena .= "<p/><b>".$resultado[1]."</b>-
			<a href='javascript:quitar_agrupado(".$resultado[0].",".$cliente.")'>
			[X] Quitar</a>";
		}
	}
	return $cadena;
}
/**
 * Establece la fecha de facturacion
 * 
 * @param array $vars
 * @return string
 */
function establecer_fecha($vars)
{
	global $con;
    $sql = "Select * from agrupa_factura where concepto like 'dia' 
	and idemp like ".$vars['cliente'];
	$consulta = mysql_query($sql,$con);
	if (mysql_numrows($consulta)==0) {
		$sql = "insert into agrupa_factura (idemp,concepto,valor) 
	    values (".$vars['cliente'].",'dia','".$vars['dia']."')";
	} else {
		$resultado = mysql_fetch_array($consulta);
		$sql = "Update agrupa_factura set 
		valor = '".$vars['dia']."' where id like ".$resultado[0];
	}
	if( mysql_query($sql,$con) ) {
		return "<div class='success'>Parametros Actualizados</div>";
	} else {
		return "<div class='error'>Error: Parametros no actualizados</div>";
	}
}
/**
 * Agrega el agrupado
 * 
 * @param array $vars
 * @return boolean
 */
function agregar_agrupado($vars)
{
	global $con;
	$sql = "Insert into agrupa_factura (idemp,concepto,valor) 
	values (".$vars['cliente'].",'servicio','".$vars['servicio']."')";
	if(mysql_query($sql,$con)) {
		return true;
	} else {
		return false;
	}
}
/**
 * Quita el agrupado
 * 
 * @param array $vars
 * @return boolean
 */
function quitar_agrupado($vars)
{
	global $con;
	$sql = "Delete from agrupa_factura where id like ".$vars['id'];
    if(mysql_query($sql,$con)) {
		return true;
	} else {
		return false;
	}
}