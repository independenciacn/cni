<?php
require_once '../inc/configuracion.php';
if ( !isset( $_SESSION['usuario'] ) && !isset( $_POST ) ) {
	notFound();
}
sanitize($_POST);
var_dump($_POST);
$respuesta = "";
if ( isset( $_POST['factura'] ) ) {
	$respuesta = "
	<form name='parametrosFactura' id='parametrosFactura' method='post' action=''>
	<fieldset>
		<legend>Facturación ". $_POST['factura'] ." de " . $_POST['cliente'] . "</legend>
	<h3>Datos generales de la Factura</h3>
	<input type='hidden' id='tipo' value='". $_POST['factura'] ."' />
	<label for='fecha_factura'>Fecha Factura:</label>
	<input type='text' class='fecha'
	id='fecha_factura' name='fecha_factura' size = '10' value='".date('d-m-Y')."'/>
	<label for='codigo'>Numero Factura:</label>
	<input type='text' id='codigo' value='".ultimo_codigo()."'  size='6'/>
	<label for='observaciones'>Observaciones:</label>
	<input type='text' id='observaciones' name='observaciones' size='60' />";
	if( $_POST['factura'] == 'puntual') {
		$respuesta .= "
		<h3>Datos especificos facturación puntual</h3>
		<label for='fecha_inicial_factura'>Fecha a Facturar:</label>
		<input type='text' class='fecha' id='fecha_inicial_factura' 
		name='fecha_inicial_factura' size = '10' value='--'/>
		<label for='fecha_final_factura'>Fecha fin Rango:</label>
		<input type='text' class='fecha' id='fecha_final_factura' 
		name='fecha_final_factura' size = '10' value='--'/>";
	}
}
$respuesta .= <<<EON
	<br/>
	<input type='button' onclick='generar_excel()' value='>Informe Gestion'/>
	<input type='button' onclick='genera_factura_prueba()' value='>Generar Proforma' />
	<input type='button'  onclick='genera_factura()' value='>Generar Factura' />
	</fieldset></form>
	<script>
	$('.fecha').datepicker({dateFormat:"dd-mm-yy"});
	</script>
EON;
echo $respuesta;
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