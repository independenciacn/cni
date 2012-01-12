<?php 
//nufact.php. FIchero para la creacion de los nuevos parametros de factura.Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007
require_once '../inc/variables.php';
if(isset($_POST)) {
	switch($_POST['opcion']) {
		case 0:
		       $respuesta = cabezera("mensual",$_POST)."".
		               generales()."".opciones(0)."".botones()."</table>";
		break;
		case 1:
		       $respuesta = cabezera("puntual",$_POST)."".
		               generales()."".opciones(1)."".botones()."</table>";
		break;
	}
	echo $respuesta;
}
else {
	echo "<div class='error'>Error</div>";
}
//Funciones auxiliares**************************************************/
function cabezera($valor,$vars)
{
	$cadena = "<table width='100%' class='tabla'>";
	$cadena .= "<tr><th>Facturaci&oacute;n ".$valor." de ".
	    nombre_cliente($vars)."</th></tr>";
	return $cadena;
	
}
function botones()
{
	$cadena = "<tr><td align='left'>
	    <input type='button' onclick='generar_excel()' value='>Informe Gestion'/>";
	$cadena .="<input type='button' 
	    onclick='genera_factura_prueba()' value='>Generar Proforma' />";
	$cadena .="<input type='button'  
	    onclick='genera_factura()' value='>Generar Factura' /></td></tr>";
	return $cadena;
}
//Obtiene el nombre del cliente de base
function nombre_cliente($vars)
{
	global $con;
	$sql = "Select Nombre from clientes where id like ".$vars['cliente'];
	$consulta = mysql_query($sql,$con);
	if (mysql_numrows($consulta)!=0) {
	    $resultado = mysql_fetch_array($consulta);
	    $cadena = $resultado[0];
	} else {
	    $cadena = "Debe seleccionar un cliente";
	}
	return $cadena;
}
function generales()
{
	$fecha = date("d-m-Y");
	$cadena = "<tr><th>Datos generales de la Factura</th></tr>";
	$cadena .= "<tr><td>&nbsp;Fecha Factura:<input type='text' id='fecha_factura' name='fecha_factura' size = '10' value='".date('d-m-Y')."'/>";
	$cadena .= "&nbsp;&nbsp;<button TYPE='button' class='calendario' id='f_trigger_fecha_factura'></button>";
	$cadena .= "&nbsp;Numero Factura:<input type='text' id='codigo' value='".ultimo_codigo()."'  size='6'/>";
	$cadena .= "&nbsp;Observaciones:<input type='text' id='observaciones' name='observaciones' size='60' /></td></tr>";
	return $cadena;
}
function opciones($tipo)
{
	if($tipo == 1)
	{
	$cadena = "<tr><th>Datos especificos facturaci&oacute;n puntual</th></tr>";
	$cadena .= "<tr><td>Fecha a Facturar:<input type='text' id='fecha_inicial_factura' name='fecha_inicial_factura' size = '10' value='--'/>";
	$cadena .= "&nbsp;&nbsp;<button TYPE='button' class='calendario' id='f_trigger_fecha_inicial_factura'></button>";
	$cadena .= "&nbsp;Fecha fin Rango:<input type='text' id='fecha_final_factura' name='fecha_final_factura' size = '10' value='--'/>";
	$cadena .= "&nbsp;&nbsp;<button TYPE='button' class='calendario' id='f_trigger_fecha_final_factura'></button></td></tr>";
	}
	else {
	    $cadena ="";
	}
	$cadena .= "<input type='hidden' id='tipo' value='".$tipo."' />";
	return $cadena;
}
function ultimo_codigo()
{
	global $con;
	$sql = "select codigo from regfacturas where codigo != 0 order by codigo desc limit 1 offset 0";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0)
		{
		$resultado = mysql_fetch_array($consulta);
		$codigo = $resultado[0] + 1;
		}
	else
		$codigo = 2003;
	return $codigo;
}
?>