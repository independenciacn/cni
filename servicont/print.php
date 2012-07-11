<?php
/**
 * print.php File Doc Comment
 *
 * Genera la tabla con los resultados recibidos
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
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
Cni::chequeaSesion();
$tabla = "";
if ( isset($_SESSION['titulo']) ) {
	$sql = $_SESSION['sqlQuery'];
	$resultados = Cni::consulta($sql);
	$totalResultados = Cni::totalDatosConsulta();
	$cabezera = true;
	$cabezeraTabla = "<tr>";
	$datosTabla = "";
	$pieTabla = "";
	$celda = 0;
	$numeroColumnas = 0;
	$totalColumna = array_fill(0, 10, null);
	if ( $totalResultados > 0 ) {
	    foreach ($resultados as $resultado) {
		    $datosTabla .= "<tr class='".Cni::clase($celda++)."'>";
		    foreach ($resultado as $key => $var) {
		        if (  $cabezera && !is_numeric($key) ) {
		            $cabezeraTabla .="<th>".$key."</th>";
		            $numeroColumnas++;
		        }
		        if ( is_numeric($key) ) {
		            $datosColumna = Cni::datosColumna($key);
		            $datosTabla .="<td>".
		                Cni::formateaCampo($var, $datosColumna['native_type'])
		                ."</td>";
		            if ( is_numeric($var) ) {
		                $totalColumna[$key] = $totalColumna[$key] + $var;    
		            }
		        }
		    }
		    $cabezera = false;
		    $datosTabla .= "</tr>";
		}
    } else {
		$cabezeraTabla .="<th>No Hay Resultados</th>";
	}
	$cabezeraTabla .= "</tr>";
	// Ponemos los datos del pie de la tabla
	$pieTabla .= "<tr>";
	for ($i = 0; $i < $numeroColumnas; $i++) {
	    $pieTabla .= "<th>";
	    if ( !is_null($totalColumna[$i]) ) {
	        $pieTabla .= Cni::formateaNumero($totalColumna[$i]);
	    }
	    $pieTabla .= "</th>";
	}
	$pieTabla .= "</tr>";
	// Guardamos la tabla final
	$tabla .= "
	    <table class='tabla' width='100%'>
	        <caption>".$_SESSION['titulo']."</caption>
	        <thead>".$cabezeraTabla."</thead>
	        <tbody>".$datosTabla."</tbody>
	        <tfoot>".$pieTabla."</tfoot>
	    </table>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="estilo/print.css" rel="stylesheet" type="text/css"></link>
<title>Aplicacion Gestion Independencia Centro Negocios </title>
<body>
	<span class='volver' onclick='window.history.back()'>&larr; Volver</span>
	<?php echo $tabla; ?>
</body>
</html>