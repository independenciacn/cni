<!-- Pagina principal de asigacion de servicios. Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007-2008-->
<html>
<head><title>Servicios</title>
<link REL="stylesheet" TYPE="text/css" href="../estilo/cni.css">
<link href="../estilo/calendario.css" rel="stylesheet" type="text/css"></link>
<script src="js/ajax.js" type="text/javascript"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/lang/calendar-es.js"></script>
<script type="text/javascript" src="../js/calendar-setup.js"></script>
<script src='js/prototype.js' type="text/javascript"></script>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
</head>
<?php 
//fichero rapido, para añadir rapidamente servicios a clientes 
//en el mes actual
//funciones
function clientes($cliente)
{
	include("../inc/variables.php");
	$sql = "Select Id,Nombre from clientes where `Estado_de_cliente` like '-1' or `Estado_de_cliente` like 'on' order by Nombre";
	$consulta = mysql_db_query($dbname,$sql,$con);
	while($resultado = mysql_fetch_array($consulta))
	{
		if($cliente == $resultado[0])
		$seleccionado = "selected";
		else
		$seleccionado = "";
		$texto .= "<option ".$seleccionado." value='".$resultado[0]."'>".$resultado[1]."</option>";
	}
	return $texto;
}
//funcion para mostrar las facturas por meses, ser marca por defecto el mes en el que estamos

//Funcion de la seleccion de meses para ver los servicios asignados ese mes
function seleccion_meses($mes)
{
if(isset($mes))
$mes = $mes;
else
$mes = date("m");

$meses = Array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	$cadena .= "<select name='meses' id='meses'>";
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
//Fin funciones auxiliares en la principal
?>
<body>
<form name='seleccion_cliente' id='seleccion_cliente'>
<table  class='tabla'>
<tr><td aling='left' valing='top' colspan='4'>
<input type='button' class='boton' onclick='window.close()' value='[X] Cerrar' />
</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<th valing='top'>
<input type='hidden' id='id_cliente' name='id_cliente' />
<img src='../iconos/personal.png' alt='cliente' />&nbsp;Cliente:</th>
<td><input type='text' name='cliente' id='cliente' autocomplete='off' onkeyup='busca_cliente()' size='60'/></td>
<th><img src='../iconos/date.png' alt='Mes' />&nbsp;Mes:</th><td><? echo seleccion_meses($mes); ?></td>
<td><select id='anyo'>
<? for ($i=2007;$i<=date(Y)+2;$i++)
{
	if(date(Y)==$i)
    echo "<option selected value='".$i."'>".$i."</option>";
    else
    echo "<option value='".$i."'>".$i."</option>";
}
?>
</select></td>
<td><input type='button' class='ver_servicios' onclick='ver_servicios_contratados()' value='Ver Servicios' /></td>
<td><input type='reset' class='limpiar' value='Limpiar' /></td>

</tr>
<tr>
<td colspan='2'>
	<input type='button' onclick='cliente_rango(0)' value='>Facturacion Mensual' />
	<input type='button' onclick='cliente_rango(1)' value='>Facturacion Puntual' />
	<input type='button' onclick='gestion_facturas(0)' value='>Gesti&oacute;n Facturas'/>
	<input type='button' onclick='oculta_parametros()' value='>Ocultar Ventana' />
	<input type='button' onclick='gestion_facturas(1)' value='>Listar todas las facturas' /></td>
</tr></table>
</form>
<div id='parametros_facturacion'></div>
<div id='listado_clientes'></div>
<div id='tabla'></div>
<div id='observa'></div>
<div id='modificar'></div>
<div id='debug'></div>
</body>
</html>