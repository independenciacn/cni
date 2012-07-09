<?php
require_once '../inc/variables.php';
checkSession();
/**
 * Cambiaf cambia el formato de la fecha de uno a otro
 * 
 * @deprecated
 * @param unknown_type $stamp
 * @return string
 */
function cambiaf($stamp) {
		$fdia = explode("-",$stamp);
		$fdia2 = explode(" ",$fdia[2]);
		$fecha = $fdia2[0]."-".$fdia[1]."-".$fdia[0];
		return $fecha;
}
	
if ( isset($_SESSION['titulo']) ) {
		$sql = $_SESSION['sqlQuery'];
		$consulta = mysql_query($sql,$con);
		$totalCampos = mysql_num_fields($consulta);
		$totalCeldas = mysql_numrows($consulta);
		$mensaje = "";
		$j=0;
		$cadena = "
		<table class='tabla' width='100%'>
		    <tr>
		        <th colspan='".$totalCampos."'>
		            ".$_SESSION['titulo']."
		        </th>
		    </tr>";
		if ( $totalCeldas >= 10000 ) {
		    $mensaje = "Demasiados Resultados. Filtre mas"; 
		    $cadena.="<tr><th colspan='".$totalCampos."'>".$mensaje."</th></tr>";
		} elseif ( $totalCeldas == 0 ) {
		    $mensaje = "No hay Resultados";
		    $cadena.="<tr><th colspan='".$totalCampos."'>".$mensaje."</th></tr>";
		} else {
		    $cadena.="<tr>";
		    for ( $i = 0; $i < $totalCampos; $i ++ ) {
				$cadena.= "<th>".mysql_field_name($consulta,$i)."</th>";
		    }
			$cadena.="</tr>";
			while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
				$clase = ( $j++ % 2 == 0) ? "par" : "impar";
				$cadena."<tr>";
				for ( $i = 0; $i < $totalCampos; $i++ ) {
					switch ( mysql_field_type( $consulta, $i ) ) {
						case "string":
						    $campo = $resultado[$i];
						    break;
						case "real":
						    $campo = number_format($resultado[$i],2,',','.');
							$tot[$i]=$tot[$i]+$resultado[$i];
						    break;
						case "date":
						    $campo = cambiaf($resultado[$i]);
						    break;
						default:
						    $campo = $resultado[$i];
							$tot[$i] ="";
					        break;
					}
					$cadena.="<td class='".$clase."'>".$campo."</td>";
				}
				$cadena.="</tr>";
			}
			$cadena.="<tr>";
			for ( $i = 0; $i < $totalCampos; $i++ ) {
				switch ( mysql_field_type( $consulta, $i ) ) {
					case "string":
					    $cadena.="<th></th>";
					    break;
					case "real":
					    $cadena.="<th>".number_format($tot[$i],2,',','.')."</th>";
					    break;
					default:
					    $cadena.="<th></th>";
					    break;
				}
			}
		}
		$cadena.="</tr>";
		$cadena.="</table>";
		$cadena.="<div id='titulo'>Total Resultados: ".$totalCeldas."</div>";
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="estilo/print.css" rel="stylesheet" type="text/css"></link>
<title>Aplicacion Gestion Independencia Centro Negocios </title>
<body>
	<span class='volver' onclick='window.history.back()'>&larr; Volver</span>
	<?php echo $cadena; ?>
</body>
</html>