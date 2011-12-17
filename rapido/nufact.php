<?php 
//nufact.php. FIchero para la creacion de los nuevos parametros de factura.Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007
require_once '../inc/configuracion.php';
$respuesta = "";
if ( isset( $_POST['opcion'] ) ) {
	sanitize($_POST);
	switch( $_POST['opcion']) {
		case 0: $respuesta = cabezera( "mensual", $_POST ) . "" . generales() . 
			"" . opciones(0) . "" . botones() . "</table>";
		break;
		case 1: $respuesta = cabezera( "puntual", $_POST ) ."". generales() . 
			"" . opciones(1) . "" . botones() . "</table>";
		break;
	}
}
echo $respuesta;
//Funciones auxiliares**************************************************/
function cabezera($valor,$vars)
{
	$cadena = "<table width='100%' class='tabla'>";
	$cadena .= "
		<tr>
			<th>Facturación ". $valor ." de " . nombreCliente($vars)."</th>
		</tr>";
	return $cadena;
	
}
function botones()
{
	$cadena = "<tr><td align='left'><input type='button' onclick='generar_excel()' value='>Informe Gestion'/>";
	$cadena .="<input type='button' onclick='genera_factura_prueba()' value='>Generar Proforma' />";
	$cadena .="<input type='button'  onclick='genera_factura()' value='>Generar Factura' /></td></tr>";
	return $cadena;
}

/**
 * Devuelve el nombre del cliente de la base de datos - PARA AUXILIARES
 * @param unknown_type $vars
 */
function nombreCliente( $vars )
{
	$cadena = "Debe seleccionar un cliente";
	$sql = "Select Nombre from clientes where id like " . $vars['cliente'];
	$resultado = consultaUnica( $sql );
	if ( count( $resultado ) > 0) {
		$cadena = $resultado[0];
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
	$cadena = "";
	if($tipo == 1)
	{
		$cadena .= "<tr><th>Datos especificos facturaci&oacute;n puntual</th></tr>";
		$cadena .= "<tr><td>Fecha a Facturar:<input type='text' id='fecha_inicial_factura' name='fecha_inicial_factura' size = '10' value='--'/>";
		$cadena .= "&nbsp;&nbsp;<button TYPE='button' class='calendario' id='f_trigger_fecha_inicial_factura'></button>";
		$cadena .= "&nbsp;Fecha fin Rango:<input type='text' id='fecha_final_factura' name='fecha_final_factura' size = '10' value='--'/>";
		$cadena .= "&nbsp;&nbsp;<button TYPE='button' class='calendario' id='f_trigger_fecha_final_factura'></button></td></tr>";
	}
	$cadena .= "<input type='hidden' id='tipo' value='".$tipo."' />";
	return $cadena;
}
/**
 * Genera el ultimo Codigo
 */
function ultimo_codigo()
{
	$codigo = 2003;
	$sql = "select codigo from regfacturas 
	where codigo != 0 order by codigo desc limit 1 offset 0";
	$resultado = consultaUnica( $sql );
	if ( count( $resultado ) > 0 ) {
		$codigo = $resultado[0] + 1;
	}
	return $codigo;
}
?>