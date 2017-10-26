<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Contratos</title></head>
<link REL="stylesheet" TYPE="text/css" href="../cni.css">
<script src="prototype.js" type="text/javascript"></script>
<script src="contratos.js" type="text/javascript"></script>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<body>
<?php require_once("../conexion.php");
//header("Content-type:  application/msword");
//header("Content-Disposition: attachment; filename=contrato.doc");
function clientes($cliente)
{
	include("../conexion.php");
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
function contratos($cliente)
{
	include("../conexion.php");
	$sql = "Select * from z_contratos";
	$consulta = mysql_query($sql,$con);
	while(true == ($resultado = mysql_fetch_array($consulta)))
	{
		$texto .= "<option ".$seleccionado." value='".$resultado[0]."'>".$resultado[1]."</option>";	
	}
	return $texto;
}
?>
<form name='tcontare' method='post' action='#'>
Cliente:<select id='cliente'>
<option value='0'>--Cliente--</option>
<? echo clientes(); ?>
</select>
Contrato:<select id='contrato' onchange='datos_necesarios()'>
<option value='0'>--Tipo de Contrato--</option>
<? echo contratos(); ?>
</select>
<div id='contratos_base'><u>Listado de contratos base</u>
<ul>
<li><a href='anexo.php' target="_blank">Anexo</a></li>
<li><a href='Basica.php' target="_blank">Basica</a></li>
<li><a href='despachos.php' target="_blank">Despachos</a></li>
<li><a href='especial.php' target="_blank">Especial</a></li>
<li><a href='integral.php' target="_blank">Integral</a></li>
<li><a href='telefonica.php' target="_blank">Telefonica</a></li>
</ul>
</div>
<div id='datos'></div>
<div id='enlace'></div>
</form>
</body>
</html>