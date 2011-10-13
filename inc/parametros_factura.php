<?
switch($_POST[opcion])
{
	case 0:$respuesta=form_parametros_factura($_POST);break;
	case 1:$respuesta=establecer_fecha($_POST);break;
	case 2:$respuesta=agregar_agrupado($_POST);break;
	case 3:$respuesta=quitar_agrupado($_POST);break;
}
echo $respuesta;

function form_parametros_factura($vars)
{
	//formulario de los parametros
	$cliente = $vars[cliente];
	$cadena .= "<input type='button' class='boton_cerrar' onclick='cerrar_parametros_factura()' value='Cerrar' />";
	$cadena .= "<p/><b>ATENCION:Por defecto NO se Agrupan Franqueo,Consumo Tel&eacute;fono,Material de oficina,Secretariado y Ajuste. Los intervalos de facturaci&oacute;n son por mes</b>";
	
	//$cadena .= "<form id='parametros_facturacion' onsubmit='alta_datos(); return false' />";
	//$cadena .= "<input type='hidden' name='idemp' id='idemp' value='".$vars[cliente]."' />"
	$cadena .= "<p/>Fecha Facturacion:";
	$cadena .= "<input type='text' id='fecha_facturacion' size='2' />&nbsp;"; 
	$cadena .= "<input type='button' onclick='establecer_fecha(".$cliente.")' value='Establecer Fecha' />"; //caso de sony y el otro
	$cadena .= "<p/>Servicio:"; //Hay 4 fijos para todos
	$cadena .= listado_servicios();
	$cadena .= "<input type='button' onclick='agrupar_servicio(".$cliente.")' value='Desagrupar servicio' />"; //caso de sony y el otro
	$cadena .= "<p/><b><u>Parametros Aplicados</u></b>";
	$cadena .= "<p>Ciclo de Factura:".fecha_factura($cliente);
	$cadena .= "<p>Servicios NO Agrupados:".servicios_agrupados($cliente);
	return $cadena;
}
function listado_servicios()
{
	include("variables.php");
	$sql = "Select id,Nombre from servicios2 order by Nombre";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$cadena = "<select name='servicio' id='servicio'>";
	$cadena .= "<option value='0'>--Servicio--</option>";
	while($resultado=mysql_fetch_array($consulta))
	{
		$cadena .= "<option value='".$resultado[0]."'>".utf8_encode($resultado[1])."</option>";
	}
	$cadena .= "</select>";
	return $cadena;
}
function fecha_factura($cliente)
{
	include("variables.php");
	$sql = "Select * from agrupa_factura where concepto like 'dia' and idemp like $cliente";
	$consulta = mysql_db_query($dbname,$sql,$con);
	if (mysql_numrows($consulta)!=0)
	{
		$resultado = mysql_fetch_array($consulta);
		if ($resultado[3] == "")
			$cadena = "<b>Mes Natural</b>";
		else
		$cadena = "<b> Dia ".$resultado[3]." de cada mes</b>";
	}
	else
		$cadena = "<b>Mes Natural</b>";
	return $cadena;
}
function servicios_agrupados($cliente)
{
	include("variables.php");
	$sql ="Select a.id ,s.Nombre from agrupa_factura as a join servicios2 as s on a.valor = s.id where a.concepto like 'servicio' and a.idemp like $cliente";
	$consulta = mysql_db_query($dbname,$sql,$con);
	if(mysql_numrows($consulta)==0)
		$cadena ="<b>No hay servicios</b>";
	else
		{
			while($resultado = mysql_fetch_array($consulta))
			$cadena .= "<p/><b>".utf8_encode($resultado[1])."</b>-<a href='javascript:quitar_agrupado(".$resultado[0].",$cliente)'>[X] Quitar</a>";
		}
	return $cadena;
}

function establecer_fecha($vars)
{
	include("variables.php");
	$sql = "Select * from agrupa_factura where concepto like 'dia' and idemp like $vars[cliente]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	if (mysql_numrows($consulta)==0)
		$sql = "insert into agrupa_factura (idemp,concepto,valor) values ($vars[cliente],'dia','$vars[dia]')";
	else
		{
			$resultado = mysql_fetch_array($consulta);
			$sql = "Update agrupa_factura set valor = '$vars[dia]' where id like $resultado[0]";
		}
	if($consulta = mysql_db_query($dbname,$sql,$con))
		return $sql;
	else
		return $sql;
}
function agregar_agrupado($vars)
{
	include("variables.php");
	$sql = "Insert into agrupa_factura (idemp,concepto,valor) values ($vars[cliente],'servicio','$vars[servicio]')";
	if($consulta = mysql_db_query($dbname,$sql,$con))
		return true;
	else
		return false;
}
function quitar_agrupado($vars)
{
	include("variables.php");
	$sql = "Delete from agrupa_factura where id like $vars[id]";
	if($consulta = mysql_db_query($dbname,$sql,$con))
		return true;
	else
		return false;
}
?>