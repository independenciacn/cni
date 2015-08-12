<?php 
/**
 * Datins File Doc Comment
 *
 * Generacion de datos de interes para la aplicacion, avisos, 
 * cumpleaños, estadisticas, etc, lo vamos a controlar por 
 * opcion -> funcion si no es un kaos
 *
 * PHP Version 5.2.6
 *
 * @category Bavanzada
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
require_once 'variables.php';
checkSession();
if (isset($_POST)) {
    sanitize($_POST);
}
switch($_POST['dato'])
{
	case 1:$datos = avisos();break;
	//case 2:$datos = cumples();break;
	case 3:$datos = busqueda_avanzada();break; 
}
echo $datos;
/**
 * Cambia el formato de fecha
 * 
 * @deprecated
 * @param string $stamp
 * @return string
 */
function cambiaf($stamp) //funcion del cambio de fecha
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2];//."-".$fdia[1]; //quito el a�o no interesa para el cumple."-".$fdia[0];
	return $fecha;
}
/**
 * Cambia el formato de fecha en el otro sentido
 * 
 * @deprecated
 * @param string $stamp
 * @return string
 */
function cambiaf2($stamp) //funcion del cambio de fecha
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]; //quito el a�o no interesa para el cumple."-".$fdia[0];
	return $fecha;
}
/**
 * Genera el formulario de avisos
 * 
 * @return string
 */
function avisos()
{
//que queremos avisar principalmente fin de contratos en el dia y en el mes tanto de clientes como
//de proveedores. De donde se coge ese dato, de la tabla facturacion
/*AUDITAREMOS campos finicio, duracion, valores de duracion dias-espacio es decir 1-H, 1-D, 1-S, 1-M, 1-A 
Clientes (facturacion)
1.- Fecha inicio + duracion
2.- Dia de Pago - Si es hoy el dia del mes de pago
Proveedores (z_facturacion)
1.- Fecha inicio + duracion
2.- Dia de Pago - Si es hoy el dia del mes de pago
*/
    global $con;
    $cadena = "";
    $hnocump = 0;
    $ssid = session_id();
    $cadena .="<table class='tabla'>";
    $cadena .= "<tr><th colspan='2' align='left'><span class='boton' 
    onclick='cierralo()' onkeypress='cierralo()'>[X] Cerrar</span></td></tr>";
    $cadena .= "<tr><th colspan='2'>AVISOS</th></tr>";
//Clientes FInalizan HOY
    $cadena .= "<tr><th Colspan='2'>Hoy finalizan contrato</th></tr>";
    $sql = "SELECT facturacion.id, 
	    facturacion.idemp, 
	    facturacion.finicio, 
	    facturacion.duracion, 
	    facturacion.renovacion, 
	    clientes.Nombre
        FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
        WHERE date_format(renovacion,'%d %c %y') 
        LIKE date_format(curdate(),'%d %c %y')";
    $consulta = mysql_query($sql,$con);
    $total = mysql_numrows($consulta);
	if ($total >= 1){
		while(true == ($resultado = mysql_fetch_array($consulta))){
			$cadena .="<tr><td>
			<a href='javascript:muestra(".$resultado[1].")'>"
			.$resultado[5]."</a></td></tr>";
		}
	} else {
	    $hnocump++;
    }
//$cadena .= "</table>";
//return $cadena;
//Clientes que finalizan contrato este mes
//Clientes FInalizan este mes
//$cadena .= "<table>";
    $cadena .= "<tr><th>Dia</th><th>Finalizan contrato este mes</th></tr>";
    $sql = "SELECT facturacion.id, 
	    facturacion.idemp, 
	    facturacion.finicio, 
	    facturacion.duracion, 
	    facturacion.renovacion, 
	    clientes.Nombre
        FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
        WHERE month(renovacion) LIKE month(curdate()) 
        and year(renovacion) like year(curdate()) order by renovacion asc";
    $consulta = mysql_query($sql,$con);
    $total = mysql_numrows($consulta);
	if ($total >= 1) {
		while(true == ($resultado = mysql_fetch_array($consulta))) {
			$cadena .="<tr><td>".cambiaf($resultado[4])."</td>
			<td><a href='javascript:muestra(".$resultado[1].")'>"
			.$resultado[5]."</a></td></tr>";
		}
	} else {
	    $hnocump++;
	}
//$cadena .= "</table>";
//Clientes que finalizan contrato dentro de los proximos 60 dias
//Clientes FInalizan este mes
//$cadena .= "<table>";
    $cadena .= "<tr><th>Dia</th>
    <th>Finalizan contrato en los proximos 60 dias</th></tr>";
    $sql = "SELECT facturacion.id, 
	    facturacion.idemp, 
	    facturacion.finicio, 
	    facturacion.duracion, 
	    facturacion.renovacion, 
	    clientes.Nombre
        FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
        WHERE (CURDATE() <= renovacion) 
        and (DATE_ADD(CURDATE(),INTERVAL 60 DAY)) >= renovacion 
        order by Month(renovacion) asc, DAY(renovacion) asc";
    $consulta = mysql_query($sql,$con);
    $total = mysql_numrows($consulta);
	if ($total >= 1) {
		while(true == ($resultado = mysql_fetch_array($consulta))) {
			$cadena .="<tr><td>".cambiaf2($resultado[4])."</td>
			<td><a href='javascript:muestra(".$resultado[1].")'>"
			.$resultado[5]."</a></td></tr>";
		}
	} else {
	    $hnocump++;
	}
    $cadena .= "</table>";
    return $cadena;
}
/**
 * Formulario de busqueda avanzada
 * 
 * @return string
 */
function busqueda_avanzada()
{
	$cadena ="";
	$cadena .="<form id='busqueda_avanzada' 
	onsubmit='busqueda_avanzada(); return false' >";
	$cadena .="<table class='tabla'>";
	$cadena .="<tr><th aling='left'>
	<span class='boton' onclick='cierralo()' onkeypress='cierralo()'>
	[X] Cerrar</span></th><th>Busqueda Avanzada</td></tr>";
	
	//$cadena .="<p/>Por:<p/><hr>";
	//$cadena .="Razon social:<input type='checkbox' name='razon' />";
	//$cadena .="Nombre Comercial:<input type='checkbox' name='comercial' />";
	//$cadena .="Nombre del Empleado:<input type='checkbox' name='empleado' /><p/>";
	//$cadena .="Otros Nombres:<input type='checkbox' name='onombre' />";
	//$cadena .="Telefono:<input type='checkbox' name='telefono' />";
	//$cadena .="Email:<input type='checkbox' name='email' />";
	$cadena .="<tr><th colspan='2'><input type='text' name='texto' size='40'/>
	<input type='submit' name='Buscar' value='Buscar' /></th></tr>";
	$cadena .="<tr><td colspan='2'>
	<div id='resultados_busqueda_avanzada'></div></td></tr>";
	$cadena .="</table>";
	$cadena .= "</form>";
	return $cadena;
}

?>