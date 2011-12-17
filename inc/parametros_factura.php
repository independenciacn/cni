<?php
/**
 * Gestion de los parametros de Factura
 * 
 * Muestra el formulario de Parametros y gestiona sus acciones
 * 
 * PHP Version 5.2.10
 * 
 * @category Parametros_factura
 * @package  cni/inc/
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @todo	 Modificar el estilo del formulario para buena apariencia
 */
require_once 'configuracion.php';
$html = "";
var_dump( $_REQUEST );
if ( isset( $_POST['opcion'] ) ) {
	sanitize( $_POST );
	$funciones = array(
		'formularioParametros',
		'establecerFecha',
		'agregarAgrupado',
		'quitarAgrupado'
	);
	if ( array_key_exists( $_POST['opcion'], $funciones ) ) {
		$html = $funciones[$_POST['opcion']]($_POST);
	}
}
echo $html;
/**
 * Formulario de los parametros de factura
 * 
 * @param array $vars
 * @return string $html
 */
function formularioParametros( $vars )
{
	$cliente = $vars['cliente'];
	$html = "
	<div class='showgrid'>
		<div class='span-11 left'><h3>Parametros de Factura</h3></div>
		<div class='span-2 right last'>
			<input type='button' class='boton_cerrar' 
			onclick='cerrar_parametros_factura()' value='Cerrar' />
		</div>
	</div>
	<div class='span-14 last'>
		<h4>ATENCION:Por defecto NO se Agrupan Franqueo,
	Consumo Teléfono,Material de oficina,Secretariado y Ajuste. 
	Los intervalos de facturación son por mes</h4>
	</div>
	<p><label for='fecha_facturacion'>Fecha Facturacion:</label>
	<input type='text' id='fecha_facturacion' size='2' />
	<input type='button' onclick='establecer_fecha(" . $vars['cliente'] . ")' 
	 value='Establecer Fecha' /></p>
	<p>" . selectServicios() . "</p>
	<p><input type='button' onclick='agrupar_servicio(" . $vars['cliente'] . ")' 
	 value='Desagrupar servicio' /></p>
	<p><strong><u>Parametros Aplicados</u></strong</p>
	<p><strong>Ciclo de Factura:</strong><br/>" 
	. fechaFactura( $vars['cliente'] ) . "</p>
	<p><strong>Servicios NO Agrupados:</strong><br/>"
	. serviciosAgrupados( $vars['cliente'] ) ."</p>";
	return $html;
}
/**
 * Genera un Select con el listado de servicios
 * 
 * @return string $html
 */
function selectServicios()
{
	$sql = "Select id, Nombre from servicios2 order by Nombre";
	$resultados = consultaGenerica( $sql );
	$html = "
	<label for='servicio'>Servicio:</label>
	<select name='servicio' id='servicio'>
	<option value='0'>--Servicio--</option>";
	foreach ( $resultados as $resultado ) {
		$html .= 
		"<option value='" . $resultado[0] . "'>" . $resultado[1] . "</option>";
	}
	$html .= "</select>";
	return $html;
}
/**
 * Devuelve la fecha de factura si tiene especificada una
 * 
 * @param integer $cliente
 * @return string $html
 */
function fechaFactura( $cliente )
{
	$html = "Mes Natural";
	$sql = "Select * from agrupa_factura where 
	concepto like 'dia' and idemp like " . $cliente;
	$resultado = consultaUnica( $sql );
	if ( ( count( $resultado ) > 0 ) && ( $resultado[3] != "" ) ) {
		$html = "Dia ".$resultado[3]." de cada mes";
	}
	return $html;
}
/**
 * Devuelve el listado de servicios agrupados si hay
 * 
 * @param integer $cliente
 * @return string $html
 */
function serviciosAgrupados( $cliente )
{
	$html = "<p><strong>No hay servicios</strong></p>";
	$j = 0;
	$sql ="Select a.id ,s.Nombre from agrupa_factura as a 
	join servicios2 as s on a.valor = s.id 
	where a.concepto like 'servicio' and a.idemp like " . $cliente;
	$resultados = consultaGenerica( $sql );
	if ( count( $resultados ) > 0 ) {
		$html = "";
		foreach( $resultados as $resultado ) {
			$html .= 
			"<div class='". clase($j++) ."'><strong>" . $resultado[1] . "</strong> - 
			<a href='javascript:quitar_agrupado(" . $resultado[0] . ", " . $cliente .")'>
			[X] Quitar</a></div>";
		}
	}
	return $html;
}
/**
 * Establece la fecha de agrupacion de factura o la actualiza
 * 
 * @param array $vars
 * @return boolean
 */
function establecerFecha( $vars )
{
	$sql = "Select * from agrupa_factura 
	where concepto like 'dia' and idemp like " . $vars['cliente'];
	$resultado = consultaUnica( $sql );
	if ( count( $resultado ) == 0 ) {
		$sql = "insert into agrupa_factura (idemp,concepto,valor) 
		values (" . $vars['cliente'] . ",'dia','" . $vars['dia'] ."')";
	} else {
		$sql = "Update agrupa_factura set valor = '" . $vars['dia'] ."' 
		where id like " . $resultado[0];
	}
	return ejecutaConsulta( $sql );
}
/**
 * Agrega el concepto agrupado
 * 
 * @param array $vars
 * @return boolean
 */
function agregarAgrupado( $vars )
{
	$sql = "Insert into agrupa_factura (idemp,concepto,valor) 
	values (" . $vars['cliente'] .",'servicio','" . $vars['servicio'] ."')";
	return ejecutaConsulta( $sql );
}
/**
 * Quita el concepto agrupado
 * 
 * @param array $vars
 * @return boolean
 */
function quitarAgrupado( $vars )
{
	$sql = "Delete from agrupa_factura where id like " . $vars['id'];
	return ejecutaConsulta( $sql );
}