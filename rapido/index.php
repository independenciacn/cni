<?php require_once '../inc/variables.php'; 
/**
 * Crea el option de clientes
 * 
 * @param integer $cliente
 * @return string
 */
function clientes( $cliente = null )
{
	global $con;
	$sql = "Select Id,Nombre from clientes
	where `Estado_de_cliente` like '-1'
	or `Estado_de_cliente` like 'on' order by Nombre";
	$consulta = mysql_query($sql,$con);
	while(true == ($resultado = mysql_fetch_array($consulta))) {
		$seleccionado = ( $cliente == $resultado[0]) ? "selected" : "";
		$texto .= "<option ".$seleccionado." value='".$resultado[0]."'>"
		. $resultado[1] . "</option>";
	}
	return $texto;
}
/**
 * Muestra el select de los meses
 * 
 * @param integer $mes
 */
function seleccion_meses($mes = null)
{
	if( $mes == null ) {
		$mes = date("m");
	}

	$meses = Array("","Enero","Febrero","Marzo","Abril","Mayo","Junio",
			"Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	$cadena = "<select name='meses' id='meses'>";
	$cadena .= "<option value='0'>--Mes--</option>";
	for($i=1;$i<=12;$i++)
	{
		if($mes == $i)
			$marcado = "selected";
		else
			$marcado = "";
		$cadena .= "<option value='".$i."' ".$marcado.">".$meses[$i]."</option>";
	}
	$cadena .= "</select>";
	echo $cadena;
}
?>
<!-- Pagina principal de asigacion de servicios. Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007-2008-->
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link REL="stylesheet" TYPE="text/css" href="../estilo/cni.css">
<link href="../estilo/calendario.css" rel="stylesheet" type="text/css"></link>
<script type="text/javascript"src='../js/prototype.js'></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/lang/calendar-es.js"></script>
<script type="text/javascript" src="../js/calendar-setup.js"></script>
<script type="text/javascript"src="js/ajax.js" ></script>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<title>Servicios - <?php echo APLICACION; ?> - <?php echo VERSION; ?></title>
</head>
<body>
<form name='seleccion_cliente' id='seleccion_cliente'>
<table class='tabla'>
<tr>
	<td align='left' valign='top' colspan='4'>
		<input type='button' class='boton' onclick='window.close()' value='[X] Cerrar' />
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
			 onkeyup='busca_cliente()' size='60'/>
	</td>
	<th>
		<img src='../iconos/date.png' alt='Mes' />&nbsp;Mes:
	</th>
	<td>
		<? echo seleccion_meses(); ?>
	</td>
	<td>
		<select id='anyo'>
<?php 
		for ($i=2007;$i<=date('Y')+2;$i++){
			$selected = ( date('Y') == $i ) ? "selected":"";
			echo "<option ".$selected." value='".$i."'>".$i."</option>";
		}
?>
		</select>
	</td>
	<td>
		<input type='button' class='ver_servicios' 
			onclick='ver_servicios_contratados()' value='Ver Servicios' />
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
<div id='parametros_facturacion'></div>
<br/>
<div id='listado_clientes'></div>
<div id='tabla'></div>
<div id='observa'></div>
<div id='modificar'></div>
<div id='debug'></div>
</body>
</html>