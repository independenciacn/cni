<?php
/**
 * word.php File Doc Comment
 * 
 * Genera la version imprimible de los resultados generados NO USADO BORRAR
 * 
 * PHP Version 5.2.6
 * 
 * @category servicont
 * @package  cni/servicont
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
session_start();
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
Cni::chequeaSesion();
$html = "Acceso denegado";
if ( isset($_SESSION['usuario']) ) {
	$sql = $_SESSION['metagenerator'];
	$empresa = $_SESSION['metaempresa'];
	$mostrada = $_SESSION['metafecha'];
	$sersel = $_SESSION['metaservicio'];
	$agrupado = $_SESSION['metagrupado'];
	$resultados = Cni::consulta($sql);
	$servicios = 0;
	$totalAcumulado = 0;
	$html = "
	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<th colspan=7>
			Servicios contratados por ".$empresa." - 
			Periodo ".$mostrada." - Servicio: ".$sersel."
		</th>
	</tr>
	<tr>
		<th align='left'>Fecha</th>
		<th align='left'>Servicio</th>
		<th align='left'>Cantidad</th>
		<th align='left'>Precio unidad</th>
		<th align='left'>Subtotal</th>
		<th align='left'>Iva</th>
		<th align='left'>Total</th>
	</tr>
	";
	foreach ($resultados as $key => $resultado) {
		if ( $agrupado == 1 ) {
			$fecha = "Agrupado";
		} else {
			$fecha = Cni::cambiaFormatoFecha($resultado[2]);
		}
		$total = Cni::totalconIva($resultado[4], $resultado[5]);
		$totalAcumulado = $totalAcumulado + $total;
		$unitario = round($resultado[3], 2);
		$subtotal = round($resultado[4], 2);
		if ( $resultado[7] != '' ) {
			$observa = "<div>".$resultado[7]."</div>";
		} else { 
			$observa = "";
		}
		$html .= "
		<tr>
		    <td align='left' valign='top'>
		        ".$fecha."
		    </td>
		    <td align='left' valign='top'>
		        ".$resultado[0]." ".$observa."
		    </td>
		    <td align='left' valign='top'>
		        ".$resultado[1]."
		    </td>
		    <td align='left' valign='top'>
		        ".Cni::formateaNumero($unitario, true)."
		    </td>
		    <td align='left' valign='top'>
		        ".Cni::formateaNumero($subtotal, true)."
		    </td>
		    <td align='left' valign='top'>
		        ".$resultado[5]."
		    </td>
		    <td align='left' valign='top'>
		        ".Cni::formateaNumero($total, true)."
		    </td>
		</tr>";
		$servicios++;
		$toserv = $toserv+$resultado[1];
	}
	$html .= "
	    <tr>
	        <th>Totales</th>
	        <th align='left'>Servicios: ".$servicios."</th>
	        <th align='left'>Cantidad: ".$toserv."</th>
	        <th></th>
	        <th></th>
	        <th></th>
	        <th align='left'>".Cni::formateaNumero($totalAcumulado, true)."</th>
	    </tr>";
	$html .= "</table>";
}
header("Content-type:  application/msword");
header("Content-Disposition: attachment; filename=word.doc");
echo $html;