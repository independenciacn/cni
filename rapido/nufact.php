<?php 
/**
 * nufact.php File Doc Comment
 *
 * Fichero para la creacion de los nuevos parametros de factura
 * Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2012
 *
 * PHP Version 5.2.6
 *
 * @category rapido
 * @package  cni/rapido
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
require_once '../inc/Servicio.php';
require_once '../inc/Cliente.php';
Cni::chequeaSesion();
if (isset($_SESSION['usuario'])) {
	if (isset($_POST)) {
		echo generaFormulario($_POST);
	} else {
		echo "<div class='error'>Error</div>";
	}
} else {
	exit("Contenido no disponible");
}
/**
 * Gererna el formulario
 * @param unknown_type $vars
 * @return string
 */
function generaFormulario($vars)
{
	var_dump($vars);
	if ($vars['cliente'] == "") {
		exit('Debe especificar un cliente');
	}
	$cliente = new Cliente($vars['cliente']);
	$tipo = ($vars['opcion'] == 0) ? "mensual" : "puntual";
	$html = "
		<table width='100%' class='tabla'>
		<tr>
			<th>Facturación ".$tipo." de " . $cliente->nombre ."</th>
		</tr>	
		<tr>
			<th>Datos generales de la Factura</th>
		</tr>
		<tr>
		<td>
			Fecha Factura:
			<input type='text' id='fechaFactura' name='fechaFactura' 
				size = '10' value='".date('d-m-Y')."'/>
			&nbsp;&nbsp;
			<button TYPE='button' class='calendario' 
				id='f_trigger_fecha_factura'></button>
			&nbsp;&nbsp;
			Numero Factura:
			<input type='text' id='codigo' 
				value='".ultimoCodigo()."' size='6'/>
			&nbsp;
			Observaciones:
			<input type='text' id='observaciones' 
				name='observaciones' size='60' />
		</td>
		</tr>";
	$html .= opciones($vars['opcion']);
	$html .= "
		<tr>
		<td align='left'>
	    	<input type='button' onclick='generar_excel()' 
				value='>Informe Gestion'/>
			<input type='button' onclick='generaFactura(true)' 
				value='>Generar Proforma' />
			<input type='button' onclick='generaFactura(false)' 
				value='>Generar Factura' />
		</td>
		</tr>
		</table>";
	return $html;
}
/**
 * Devuelve las opciones de formulario
 * @param unknown_type $tipo
 * @return string
 */
function opciones($tipo)
{
	if ($tipo == 1) {
		$html = "
			<tr>
				<th>Datos especificos facturación puntual</th>
			</tr>
			<tr>
			<td>
				Fecha a Facturar:
				<input type='text' id='fechaInicialFactura' 
					name='fechaInicialFactura' size = '10' value='00-00-0000'/>
				&nbsp;&nbsp;
				<button TYPE='button' class='calendario' 
					id='f_trigger_fecha_inicial_factura'></button>
				&nbsp;Fecha fin Rango:
				<input type='text' id='fechaFinalFactura' 
					name='fechaFinalFactura' size = '10' value='00-00-0000'/>
				&nbsp;&nbsp;
				<button TYPE='button' class='calendario' 
					id='f_trigger_fecha_final_factura'></button>
			</td>
			</tr>";
	} else {
	    $html = "<input type='hidden' id='fechaInicialFactura' 
					name='fechaInicialFactura' size = '10' value='00-00-0000'/>
	    		<input type='hidden' id='fechaFinalFactura' 
					name='fechaFinalFactura' size = '10' value='00-00-0000'/>";
	}
	$html .= "<input type='hidden' id='tipo' value='".$tipo."' />";
	return $html;
}
/**
 * Devuelve el ultimo codigo insertado
 * 
 * @return number
 */
function ultimoCodigo()
{
	$sql = "SELECT codigo 
			FROM regfacturas 
			WHERE codigo != 0 
			ORDER BY codigo DESC 
			LIMIT 1 
			OFFSET 0";
	$resultados = Cni::consultaPreparada($sql, array(), PDO::FETCH_CLASS);
	if (Cni::totalDatosConsulta() > 0) {
		foreach ($resultados as $resultado) {
			$codigo = $resultado->codigo + 1;
		}
	} else {
		$codigo = 2003;
	}
	return $codigo;
}
 
