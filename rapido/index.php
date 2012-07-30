<?php
/**
 * index.php File Doc Comment
 *
 * Pagina principal de asigacion de servicios. 
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
$tituloGeneral = APLICACION. " - ". VERSION;
/**
 * Crea el option de clientes
 * 
 * @param integer $cliente
 * @return string
 */
/**
 * Muestra el select de los meses
 * 
 * @param integer $mes
 */
function seleccionMeses($mesMarcado = null)
{
	if ( $mesMarcado == null ) {
		$mesMarcado = date("m");
	}
	$html = "<select name='meses' id='meses'>";
	$html .= "<option value='0'>--Mes--</option>";
	foreach (Cni::$meses as $key => $mes) {
		$marcado = ($key == $mesMarcado) ? "selected" : "";
		$html .= "<option value='".$key."' ".$marcado.">".$mes."</option>";
	}
	$html .= "</select>";
	echo $html;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="pragma" content="no-cache">
	<link  href="../estilo/cni.css" rel="stylesheet"/>
	<link  href="../estilo/calendario.css"  rel="stylesheet"/>
	<script src='../js/prototype.js'></script>
	<script src="../js/calendar.js"></script>
	<script src="../js/lang/calendar-es.js"></script>
	<script src="../js/calendar-setup.js"></script>
	<script src="../js/NumberFormat154.js"></script>
	<script src="js/rapido.js" ></script>
	<title>Servicios - <?= $tituloGeneral ?></title>
</head>
<body>
<form name='seleccion_cliente' id='seleccion_cliente'>
	<table class='tabla'>
	<tr>
		<td align='left' valign='top' colspan='4'>
			<input type='button' class='boton' 
				onclick='window.close()' value='[X] Cerrar' />
		</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<th valign='top'>
			<input type='hidden' id='id_cliente' name='id_cliente' />
			<img src='../iconos/personal.png' alt='cliente' />&nbsp;Cliente:
		</th>
		<td>
			<input type='text' name='cliente' id='cliente' 
				 onkeyup='buscaCliente()' size='60'/>
		</td>
		<th>
			<img src='../iconos/date.png' alt='Mes' />&nbsp;Mes:
		</th>
		<td>
			<?= seleccionMeses() ?>
		</td>
		<td>
			<select id='anyo'>
<?php 
for ($i = 2007; $i <= date('Y') + 2; $i++) {
	$selected = ( date('Y') == $i ) ? "selected":"";
	echo "<option ".$selected." value='".$i."'>".$i."</option>";
}
?>
		</select>
	</td>
	<td>
		<input type='button' class='ver_servicios' 
			onclick='verServiciosContratados(false)' value='Ver Servicios' />
	</td>
	<td>
		<input type='reset' class='limpiar' value='Limpiar' />
	</td>
</tr>
<tr>
	<td colspan='2'>
		<input type='button' onclick='cliente_rango(0)' 
			value='>Facturacion Mensual' />
		<input type='button' onclick='cliente_rango(1)' 
			value='>Facturacion Puntual' />
		<input type='button' onclick='gestion_facturas(0)' 
			value='>Gesti&oacute;n Facturas'/>
		<input type='button' onclick='oculta_parametros()' 
			value='>Ocultar Ventana' />
		<input type='button' onclick='gestion_facturas(1)' 
			value='>Listar todas las facturas' />
	</td>
</tr>
</table>
</form>
<div id='precargaDatos'></div>
<div id='parametros_facturacion'></div>
<br/>
<div id='listado_clientes'></div>
<div id='tabla'></div>
<div id='observa'></div>
<div id='modificar'></div>
<div id='debug'></div>
</body>
</html>
<?php
 