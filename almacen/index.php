<?php
/* TODO: Almacenaje: Seleccionas un cliente y sale el almacenaje de años anteriores.
 * Programarse para qwu se vea solo el año en curso y especificar el iva al 18%
 *
 *
 */
function clientes($cliente)
{
	include("../inc/variables.php");
	$sql = "Select Id,Nombre from clientes where `Estado_de_cliente` like '-1' or `Estado_de_cliente` like 'on' order by Nombre";
	$consulta = mysql_query($sql,$con);
	while(true == ($resultado = mysql_fetch_array($consulta)))
	{
		if($cliente == $resultado[0])
		$seleccionado = "selected";
		else
		$seleccionado = "";
		$texto .= "<option ".$seleccionado." value='".$resultado[0]."'>".$resultado[1]."</option>";
	}
	return $texto;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="pragma" content="no-cache" />
<script src="../js/prototype.js" type="text/javascript"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/lang/calendar-es.js"></script>
<script type="text/javascript" src="../js/calendar-setup.js"></script>
<script src="js/aplicacion.js" type="text/javascript"></script>
<link href="../estilo/cni.css" rel="stylesheet" type="text/css"></link>
<link href="../estilo/calendario.css" rel="stylesheet" type="text/css"></link>
<title>Gesti&oacute;n Almacenaje</title>
</head>
<body>
<div class='formulario'>
	<br/>
	Seleccione Cliente: <select id='cliente' onchange='abreform()'>
	<option value='0'>--Seleccione cliente--</option>
	<? echo clientes($cliente); ?>
	</select>
	<span class='boton' onclick='abre()'>[R]Recargar Cliente</span>
	<span class='boton' onclick='window.close()'>[X]Cerrar</span>
	<br/>
	<div id='formulario_almacen'></div>
</div>

<div id='tabla_datos'></div>
<div id='consulta'></div>
<div id='observa'></div>
<div id='modificar'></div>
</body>
</html>