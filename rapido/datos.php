<? //datos.php  !!REVISION ENLACES ID PEDIDO E ID DE DETALLES
//Fichero datos.php (muestra los datos del cliente el en mes actual). Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007 
setlocale(LC_NUMERIC, 'es_ES');
switch($_POST[opcion])
{
	case 1:$cadena = cuca($_POST);break;
	case 2:$cadena = dame_nombre_cliente($_POST);break;
	case 3:$cadena = ver_servicios_contratados($_POST);break;
	case 4:$cadena = borra_servicio_contratado($_POST);break;
	case 5:$cadena = frm_modificacion_servicio($_POST);break;
	case 6:$cadena = modificacion_servicio($_POST);break;
	case 7:$cadena = frm_alta_servicio($_POST);break;
	case 8:$cadena = valor_del_servicio($_POST);break;
	case 9:$cadena = agrega_el_servicio($_POST);break;
	case 10:$cadena = ventana_observaciones($_POST);break;
	case 11:$cadena = listado_facturas($_POST);break;
	case 13:$cadena = borrar_factura($_POST);break;
	case 14:$cadena = filtros($_POST);break;
	case 15:$cadena = casos($_POST);break;
}
echo $cadena;

//FUNCIONES AUXILIARES********************************************************************************
//Funcion que muestra el importe total a pagar de almacenaje si el cliente tiene algo almacenado
function traduce($texto)
{
//en algunos casos
	if(SISTEMA == "windows")
		$bien = utf8_encode($texto); //para windows
	else
		$bien = $texto;//para sistemas *nix
	return $bien;
}
//***********************************************************************************************/
//a la hora de agregarlo lo codifica
function codifica($texto)
{
	if(SISTEMA == "windows")
		$bien = utf8_decode($texto); //para windows
	else
		$bien = $texto;//para sistemas *nix
	return $bien;
}
//***********************************************************************************************/
//Cambia la fecha a formato sql
function cambiaf($stamp) //funcion del cambio de fecha
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	if($fecha == "--")
	$fecha = "0000-00-00";
	return $fecha;
}
function dame_el_mes($mes)
{
	switch($mes)
	{
		case 1: $marcado = "Enero";breaK;
		case 2: $marcado = "Febrero";breaK;
		case 3: $marcado = "Marzo";breaK;
		case 4: $marcado = "Abril";breaK;
		case 5: $marcado = "Mayo";breaK;
		case 6: $marcado = "Junio";breaK;
		case 7: $marcado = "Julio";breaK;
		case 8: $marcado = "Agosto";breaK;
		case 9: $marcado = "Septiembre";breaK;
		case 10: $marcado = "Octubre";breaK;
		case 11: $marcado = "Noviembre";breaK;
		case 12: $marcado = "Diciembre";breaK;
	}
	return $marcado;
}
/**************************************************************************************************/
//calculoa lo que hay en el almacen y si hay que cobrarlo !!!!CAMBIAR MES ACTUAL POR MES SELLECCIONADO
function almacenaje($cliente,$mes,$anyo) //No creo que sea necesaria
{
	include("../inc/variables.php");
    $sql = "Select datediff('$anyo-$mes-01','2010-07-01')";
    $consulta = mysql_db_query($dbname,$sql,$con);
    $diff = $resultado = mysql_fetch_array($consulta);
    if($diff[0]>=0)
    {
        $sql = "select PrecioEuro, iva from servicios2 where nombre like '%Almacenaje%'";
        $consulta = mysql_db_query($dbname,$sql,$con);
        $par_almacenaje = mysql_fetch_array($consulta);
    }
    else
        $par_almacenaje = array('PrecioEuro'=>'0.70','iva'=>'16');

    //Fin del calculo de los parametros del almacenaje dependiendo del valor que tiene en servicios
	$sql = "Select bultos, datediff(fin,inicio) from z_almacen where cliente like $cliente and month(fin) like $mes and year(fin) like $anyo";
	$consulta = mysql_db_query($dbname,$sql,$con);
	while ($resultado = mysql_fetch_array($consulta))
	{
		$j++;
		if($j%2==0)
			$color = "class='impar'";
		else
			$color = "class='par'";
        /*Modificar ivas aqui*/
		$subtotal = $resultado[0]*$resultado[1]*$par_almacenaje['PrecioEuro'];
		$total = round($subtotal + ($subtotal * $par_almacenaje['iva'])/100,2);
        /*Modificar ivas*/
		$cadena .="<tr ".$color.">
		<td>".$resultado[1]." Dias</td>
		<td>&nbsp;</td><td>&nbsp;</td>
		<td>&nbsp;Almacenaje</td>
		<td>".$resultado[0]."</td>
		<td>".$par_almacenaje['PrecioEuro']."&euro; Bultos Dia</td>
		<td>".$subtotal."&euro;</td>
		<td>".$par_almacenaje['iva']."</td>
		<td>".number_format($total,2,",",".")."&euro;</td>
		</tr>";
		$totales = $totales + $total;
		$subtotales = $subtotales + $subtotal;
	}
	$final = array($cadena,$subtotales,$totales);
return $final;
}
//Listado de servicios disponibles, creo que de aqui desaparecera
function servicios()
{
	include("../inc/variables.php");
	$sql = "Select id,Nombre from servicios2 where `Estado_de_servicio` like '-1' or `Estado_de_servicio` like 'on' order by Nombre";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$texto = "<select name='servicios' id='servicios' onchange='dame_el_valor()'>";
	$texto.="<option value=0>--Seleccione Servicio--</option>";
	while($resultado = mysql_fetch_array($consulta))
		$texto .= "<option value='".$resultado[0]."'>".traduce($resultado[1])."</option>";
	$texto .= "</select>";
	return $texto;
}
//FUNCIONES GENERALES NUEVAS************************************************************************/
//Funcion que busca y muestra el nombre del cliente*************************************************/
function cuca($vars)
{
	include("../inc/variables.php");
	if($vars[texto] == "")
	
		$muestra = "";
	else
	{
		$vars[texto] = codifica($vars[texto]);
		$sql = "Select * from `clientes` where (Nombre like '%$vars[texto]%' or Contacto like '%$vars[texto]%') and `Estado_de_cliente` like '-1' order by Nombre ";
		$consulta = mysql_db_query($dbname,$sql,$con);
		$muestra .="<ul>";
		while($resultado = mysql_fetch_array($consulta))
		{
			$muestra .="<li><span class='lbl_clientes' onclick='marca(".$resultado[0].")' onmouseout='quitar_color(".$resultado[0].")' onmouseover='cambia_color(".$resultado[0].")'><p id='linea_".$resultado[0]."'>".traduce(eregi_replace($vars[texto],"<b><u>".strtoupper($vars[texto])."</u></b>",$resultado[1]))."</p></span></li>";
		}
		$muestra .="</ul>";
	}
	return $muestra;
}

//Funcion que devuelve el nombre de cliente y lo pone en el campo de texto*********************************/
function dame_nombre_cliente($vars)
{
	include("../inc/variables.php");
	$sql = "Select * from `clientes` where id like $vars[cliente] ";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$cadena = $resultado[0].";".traduce($resultado[1]);
	return $cadena;
}

//Funcion que muestra los datos de los servicios contratados del cliente en el mes seleccionado***************/
function ver_servicios_contratados($vars)
{
	include("../inc/variables.php");
	
	if($vars[mes] <= 9)
		$mes_buscado = "0".$vars[mes];
	else
		$mes_buscado = $vars[mes];
	$sql = "Select d.Servicio, d.Cantidad, date_format(c.fecha,'%d-%m-%Y') as fecha, 
	d.PrecioUnidadEuros, d.ImporteEuro, d.iva, c.`Id Pedido` ,d.Observaciones, d.Id 
	from `detalles consumo de servicios` as d join `consumo de servicios` as c on c.`Id Pedido` = d.`Id Pedido` 
	where c.Cliente like $vars[cliente] and ($vars[anyo] 
	like date_format(c.fecha,'%Y') and '$mes_buscado' like date_format(c.fecha,'%m')) order by c.fecha asc"; 
	//echo $sql;
	$consulta = mysql_db_query($dbname,$sql,$con);
	$cadena .= "<span class='agregar' onclick='ver_frm_agregar_servicio($vars[cliente])'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Agregar Servicio</span><div id='form_agregar'></div>";
	$cadena .= "<table class='tabla' width='100%'>";
	$cadena .= "<tr><th colspan=9>";
	$cadena .= "<tr><th>Fecha</th><th>&nbsp;</th><th>&nbsp;</th><th>Servicio</th><th>Cantidad</th><th>Precio Unidad</th><th>Importe</th><th>Iva</th><th>Total</th></tr>";
//almacenaje
	//echo $sql;
	$almacenaje = almacenaje($vars[cliente],$mes_buscado,$vars[anyo]);
	$cadena .= $almacenaje[0];
	$subtotal = $almacenaje[1];
	$total = $almacenaje[2];
//fin del almacenaje

	while ($resultado=mysql_fetch_array($consulta))
	{
		$subtotal = ($resultado[4]+($resultado[4]*$resultado[5])/100);	
//acumulados
		$total = $subtotal + $total;
		$cantidad = $resultado[1] + $cantidad;
//fin acumulados
		$j++;
		if($j%2==0)
			{$color = "class = 'par'";$botoncico1 = "boton_borrar_par";$botoncico2 = "boton_editar_par";}
		else
			{$color = "class = 'impar'";$botoncico1 = "boton_borrar_impar";$botoncico2 = "boton_editar_impar";}
		$modificar ="<input type='button' class='".$botoncico2."' onclick='modificar(".$resultado[8].")' />";
		$cadena.= "<tr><td ".$color.">".$resultado[2]."</td>
		<td ".$color." align='center'>
		<input type='button' class='".$botoncico1."' onclick='borra(".$resultado[8].")' /></td>
		<td ".$color." align='center'>".$modificar."</td>
		<td ".$color.">&nbsp;".traduce($resultado[0])." ".$resultado[7]."</td>
		<td ".$color.">&nbsp;".$resultado[1]."</td>
		<td ".$color.">&nbsp;".number_format($resultado[3],2,",",".")."&euro;</td>
		<td ".$color.">&nbsp;".number_format($resultado[4],2,",",".")."&euro;</td>
		<td ".$color.">&nbsp;".$resultado[5]."</td>
		<td ".$color.">&nbsp;".number_format($subtotal,2,",",".")."&euro;</td></tr>";
	}
	$cadena.= "<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;".$cantidad."</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>".number_format($total,2,",",".")."&euro;</th>";
	$cadena.= "</table>";
return $cadena;
}

//Borrado del servicio contratado***************************************************************************
function borra_servicio_contratado($vars)
{
	include("../inc/variables.php");
	$sql = "Select `Id Pedido` from `detalles consumo de servicios` where `Id` like $vars[servicio]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado  = mysql_fetch_array($consulta);
	$sql = "Delete from `consumo de servicios` where `Id Pedido` like $resultado[0]";
	if($consulta = mysql_db_query($dbname,$sql,$con))
	{
		$sql = "Delete from `detalles consumo de servicios` where `Id` like $vars[servicio]";
		$consulta = mysql_db_query($dbname,$sql,$con);
		return true;
	}
	else
		return false;
}

//formulario de modificacion de servicios*******************************************************************
function frm_modificacion_servicio($vars)
{
	include("../inc/variables.php");
	$sql = "Select * from `detalles consumo de servicios` where `Id` like $vars[servicio]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$cliente = $_GET[cmodi];
	$modi[0] = $_GET[modi];//Id Pedido
	$modi[1] = $resultado[5];//Cantidad
	$modi[2] = round($resultado[7],2);//Precio Unidad Euros
	$modi[3] = $resultado[9];//Importe Euros
	$modi[3] = round($modi[3],2);
	$modi[4] = $resultado[10];//Iva
	$modi[5] = $resultado[11];//observaciones
	$total = ($modi[3]+($modi[3]*$modi[4]/100))*$modi[1];//importe total
	$total = round($total,2);
	$cadena .= "
	<input type='button' class='boton_cerrar' onclick='cierra_frm_modificacion()' value='Cerrar'/>
	<form id='modificacion' name='modificacion' method='post' onSubmit='modif(".$resultado[0]."); return false'>
	<table cellpadding='1px' cellspacing='1px' width='100%'>
	<tr>
	<th align='center' colspan='6'> Servicio:".traduce($resultado[Servicio])." </th>
	</tr>
	<tr>
	<th align='left'>Precio:</th>
	<td><input type='text' id='precio' name='precio' onkeyup='recalcula_modificacion()' value='".round($resultado[PrecioUnidadEuros],2)."' size='8'/>&euro;</td>
	<th align='left'>Cantidad:</th>
	<td><input type='text' id='cantidad' name='cantidad'  onkeyup='recalcula_modificacion()' value='".$resultado[Cantidad]."' size='3'/></td>
	<th align='left'>IVA:</th>
	<td><input type='text' id='iva'  name='iva'onkeyup='recalcula_modificacion()' value='".$resultado[iva]."' size='5'/></td>
	</tr>
	<tr>
	<th align='left'>Importe:</th>
	<td><span id='importe'>".$modi[3]."</span>&euro</td>
	<th align='left'>Total:</th>
	<td><span id='total'>".$total."</span>&euro;</td>
	<td></td>
	<td></td>
	</tr>
	<tr>
	<th valign='top' align='left'>Observaciones:</th>
	<td colspan='3'><textarea id='observacion' name='observacion' cols='40' rows='2' >".$resultado[11]."</textarea></td>
	<th colspan='2'><input class='boton_actualizar' type='submit' name='Modificar' value='Modificar' /></th></tr>
	</table>
	</form>";
	return $cadena;
}
//PASA LOS DATOS PARA MODIFICAR EN LA BASE*****************************************************************/
function modificacion_servicio($vars)
{
	include("../inc/variables.php");
	$subtotal = $vars[cantidad] * $vars[precio];
	$subtotal = round($subtotal,2);
	$sql = "Update `detalles consumo de servicios` set `Cantidad` = '$vars[cantidad]',
			`PrecioUnidadEuros` = '$vars[precio]',
			`ImporteEuro` = '$subtotal',
			`iva` = '$vars[iva]',
			`observaciones` = '$vars[observacion]' where `Id` like '$vars[servicio]'";
	if($consulta = mysql_db_query($dbname,$sql,$con))
		return "<img src='".OK."' alt='Servicio Agregado' width='64'/> Servicio Actualizado&nbsp;&nbsp;<p/>";
	else
		return "<img src='".NOK."' alt='ERROR' width='64'/> ERROR&nbsp;&nbsp;<p/>".$sql;
}
//Formulario de Agregar servicio al cliente****************************************************************/
function frm_alta_servicio($vars)
{
	include("../inc/variables.php");
	$cadena ="<br/><form id='frm_alta' class='formulario'  method='post' onSubmit='agrega_servicio(); return false'>
	<table width='100%' class='tabla'>
	<tr>
	<th>Fecha:</th><td colspan='2'><input type='text' name='fecha' size='10' value='".date("d-m-Y")."' />
	<th>Servicio:</th><td colspan='2'>".servicios()."</td>
	</tr><tr>
	<th>Precio:</th><td><input type='text' name='precio' id='precio' size='8' value='".round($resultado[0],2)."' onChange='recalcula()' tabindex='2'/>&euro;</td>
	<th>Cantidad:</th><td><input type='text' name='cantidad' id='cantidad' size='3' value='1' onkeyup='recalcula()' tabindex='3'/></td>
	<th>IVA:</th><td><input type='text' name='iva' id='iva' size='3' value='".$resultado[1]."' onkeyup='recalcula()' tabindex='4'/></td>
	</tr><tr>
	<th>Importe:</th><td colspan='2'><span id='importe'>".round($resultado[0],2)."</span>&euro;</td>
	<th>Total:</th><td colspan='2'><span id='total'>".$totals."</span>&euro;</td>
	</tr>
	<tr>
	<th colspan='2'>Observaciones:</th>
	<td colspan='2'><textarea id='observacion' name='observacion' rows='1' cols='50' tabindex='5'></textarea></td>
	<th colspan='2'><input type='submit' class='agregar' id='agregar' accesskey='a' value='Agregar' tabindex='6'/></th>
	</tr>
	</table>
	";
	return $cadena;
}
//Funcion que devuelve el precio y el iva del servicio seleccionado****************************************/
function valor_del_servicio($vars)
{
	include("../inc/variables.php");
	$sql = "Select PrecioEuro,iva from servicios2 where id like $vars[servicio]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	return round($resultado[0],2).";".$resultado[1];
}
//AGREGAMOS EL SERVICIO AGAIN*****************************************/
function agrega_el_servicio($vars)
{
	include("../inc/variables.php");
	//parametros que llegan, cliente,fecha,servicio,precio,cantidad,iva,observaciones
	//el servicio DEBE almacenarse como su valor en texto
	$sql = "Select Nombre from servicios2 where id like $vars[servicios]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$servicio = $resultado[0];
	$fecha = cambiaf($vars[fecha]);
	$subtotal = $vars[precio]*$vars[cantidad];
	$sql = "Insert into `consumo de servicios` (`Cliente`,`Fecha`) values ('$vars[cliente]','$fecha')";
	if($consulta = mysql_db_query($dbname,$sql,$con))
	{
		$sql = "Insert into `detalles consumo de servicios` (`Id Pedido`,`Servicio`,`Cantidad`,`PrecioUnidadEuros`,`ImporteEuro`,`iva`,`observaciones` )
	values (LAST_INSERT_ID(),'$servicio','$vars[cantidad]','$vars[precio]','$subtotal','$vars[iva]','$vars[observacion]')";
		if($consulta = mysql_db_query($dbname,$sql,$con))
			return "<img src='".OK."' alt='Servicio Modificado' width='64'/> Servicio Modificado&nbsp;&nbsp;<p/>".$sql;
		else
			return "<img src='".NOK."' alt='ERROR' width='64'/> ERROR&nbsp;&nbsp;<p/>".$sql;
	}
	else
			return "<img src='".NOK."' alt='ERROR' width='64'/> ERROR&nbsp;&nbsp;<p/>".$sql;
}
//Visualizacion de la ventana de observaciones*******************************************************************/
function ventana_observaciones($vars)
{
	include("../inc/variables.php");
	$sql = "Select observaciones from `detalles consumo de servicios` where `Id Pedido` like $vars[servicio]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$cadena = "<input type='button' class='boton_cerrar' onclick='cierra_ventana_observaciones()' value='Cerrar' /><br/>
	".$resultado[0];
	return $cadena;
}
//Para borrar una factura seleccionada**********************************************/
function borrar_factura($vars)
{
	include("../inc/variables.php");
	$sql = "Select codigo from regfacturas where id like $vars[factura]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$codigo = $resultado[0];
	$sql = "Delete from regfacturas where id like $vars[factura]";
	if($consulta = mysql_db_query($dbname,$sql,$con))
	{
		$sql = "Delete from historico where factura like $codigo";
		$consulta = mysql_db_query($dbname,$sql,$con);
		$cadena = "Factura Borrada<p/>";
	}
	else
	$cadena = "No se ha borrado la factura<p/>";
	$cadena .= listado_facturas($vars);
return $cadena;
}
//DESDE AQUI ESTA TODO EMPANTANADO CASI NA************************************************/
//Funciones, La cabezera con los cuadros de texto son fijos
//Cambia el listado
//Funcion principal,solocabezera
function listado_facturas($vars)
{
	//Las vars marcaran que hay ordenado y que no
	$cadena =cabezera_pantalla(0,0,0,0,1);//Version de Evaluacion
	if($vars[tipo]==1)
		$sql = "Select r.id as id,r.codigo as codigo,r.fecha as fecha,r.importe as importe ,r.obs_alt as obs_alt, 
	c.Nombre as nombre from regfacturas as r join clientes as c on r.id_cliente like c.id order by r.fecha desc";
	else
		$sql = "Select r.id as id,r.codigo as codigo,r.fecha as fecha,r.importe as importe ,r.obs_alt as obs_alt, 
	c.Nombre as nombre from regfacturas as r join clientes as c on r.id_cliente like c.id where r.id_cliente like '$vars[cliente]' order by r.fecha desc";
	$cadena .="<div id='tabla_resultados'>";
	$cadena .=dibuja_pantalla($sql,0,0,0,0);
	$cadena .= "</div>";
	return $cadena;
}
/********************************************************************************************/
//Medidas Fijas cliente='285px,factura='50px,70px,70px,100px'
function cabezera_pantalla($marca_cliente,$marca_factura,$marca_fecha,$marca_importe,$tipo)
{
	$cadena .= "<table width='100%' class='tabla'><tr><th colspan='6'>Listado de Facturas</th></tr>";
	$cadena .= "<tr><th width='280px'>";
	$cadena .= "<input type='text' id='filtro_0' autocomplete='off' onkeyup='filtro(0)' size='50'/>";
	$cadena .= "</th><th width='50px'>";
	$cadena .= "<input type='text' id='filtro_1' autocomplete='off' onkeyup='filtro(1)' size='6'/>";
	$cadena .= "</th><th width='70px'>";
	$cadena .= "<input type='text' id='filtro_2' autocomplete='off' onkeyup='filtro(2)' size='10'/>";
	$cadena .= "</th><th width='70px' >";
	$cadena .= "<input type='text' id='filtro_3' autocomplete='off' onkeyup='filtro(3)' size='10'/>";
	$cadena .= "</th><th width='100px' >&nbsp;</th><th >&nbsp;</th></tr>";
	//Cabezeras con filtro
	//Valores de las marcas
	//0=asc y 1=desc
	$cadena .= "<tr><th>";
	$cadena .= "<a href='javascript:sort(0)' />Cliente</a>";
	$cadena .= "</th>";
	$cadena .= "<th>";
	$cadena .= "<a href='javascript:sort(1)' />Factura</a>";
	$cadena .= "</th>";
	$cadena .= "<th>";
	$cadena .= "<a href='javascript:sort(2)' />Fecha</a>";
	$cadena .= "</th>";
	$cadena .= "<th>";
	$cadena .= "<a href='javascript:sort(3)' />Importe</a>";
	$cadena .= "</th>";
	$cadena .= "<th>Observacion</th><th>&nbsp;</th></tr></table>";
	return $cadena;
	
}
//PARTE PRINCIPAL DE LA GESTION DE FACTURACION*****************************************/
//Muestra la seleccion x cliente o x mes y deja imprimirlas desde aqui
function casos($vars)
{
		//habra que filtrar los sorts aqui
		switch($vars[seccion])
		{
			case 0:$orden = " order by c.Nombre ";
			if($vars[marca_cliente]==0)
			{
				$orden .= " asc ";
				$marca_cliente=1;
				$marca_factura=0;
				$marca_fecha=0;
				$marca_importe=0;
			}
			else
			{
				$orden .= " desc ";
				$marca_cliente=0;
				$marca_factura=0;
				$marca_fecha=0;
				$marca_importe=0;
			};break;
			case 1:$orden = " order by r.codigo ";
			if($vars[marca_factura]==0)
			{
				$orden .= " asc ";
				$marca_cliente=0;
				$marca_factura=1;
				$marca_fecha=0;
				$marca_importe=0;
			}
			else
			{
				$orden .= " desc ";
				$marca_cliente=0;
				$marca_factura=0;
				$marca_fecha=0;
				$marca_importe=0;
			};break;
			case 2:$orden = " order by r.fecha ";
			if($vars[marca_fecha]==0)
			{
				$orden .= " asc ";
				$marca_cliente=0;
				$marca_factura=0;
				$marca_fecha=1;
				$marca_importe=0;
			}
			else
			{
				$orden .= " desc ";
				$marca_cliente=0;
				$marca_factura=0;
				$marca_fecha=0;
				$marca_importe=0;
			};break;
			case 3:$orden = " order by r.importe ";
			if($vars[marca_importe]==0)
			{
				$orden .= " asc ";
				$marca_cliente=0;
				$marca_factura=0;
				$marca_fecha=0;
				$marca_importe=1;
			}
			else
			{
				$orden .= " desc ";
				$marca_cliente=0;
				$marca_factura=0;
				$marca_fecha=0;
				$marca_importe=0;
			};break;
			default:$orden = " order by r.codigo";break;
		}
		$sql = "Select r.id as id,r.codigo as codigo,r.fecha as fecha,r.importe as importe ,r.obs_alt as obs_alt, 
c.Nombre as nombre from regfacturas as r join clientes as c on r.id_cliente like c.id ";
		$sql .= $orden;
		//echo $sql;
		$pantalla .= dibuja_pantalla($sql,$marca_cliente,$marca_factura,$marca_fecha,$marca_importe);
	return $pantalla;
}


//FILTROS de texto
function filtros($vars)
{
	switch($vars[filtro])
	{
		case 0:$cacho=" where c.Nombre like '".$vars[texto]."%'";break;
		case 1:$cacho=" where r.codigo like '".$vars[texto]."%'";break;
		case 2:$fecha = cambiab($vars[texto]);$cacho=" where r.fecha like '".$fecha."%'";break;
		case 3:$cacho=" where r.importe like '".$vars[texto]."%'";break;
	}
	$sql = "Select r.id as id,r.codigo as codigo,r.fecha as fecha,r.importe as importe ,r.obs_alt as obs_alt, 
c.Nombre as nombre from regfacturas as r join clientes as c on r.id_cliente like c.id";
	$sql .= $cacho;
	$pantalla .= dibuja_pantalla($sql,0,0,0,0);
	return $pantalla;
}

function cambiab($stamp) //funcion del cambio de fecha especial para el filtro
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	if($fdia[2]=='')
		$fdia[2]='%';
	if($fdia[1]=='')
		$fdia[1]='%';
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}
//Aqui llega la sentencia la ejecuta y la visualiza.
function dibuja_pantalla($sql,$marca_cliente,$marca_factura,$marca_fecha,$marca_importe)
{
	include("../inc/variables.php");
	//Ordenes
	$cadena .="
	<input type='hidden' id='marca_cliente' value='".$marca_cliente."' />
	<input type='hidden' id='marca_factura' value='".$marca_factura."' />
	<input type='hidden' id='marca_fecha' value='".$marca_fecha."' />
	<input type='hidden' id='marca_importe' value='".$marca_importe."' />";
	
	$cadena .="<table width='100%' class='tabla'>";
	$consulta = mysql_db_query($dbname,$sql,$con);
	if (mysql_numrows($consulta)!=0)
	{
		$k=0;
		while ($resultado = mysql_fetch_array($consulta))
		{
			if($resultado[codigo] == 0)
				$codigo = intval($resultado[id]) + 99;
			else
				$codigo = $resultado[codigo];
				
				$cadena .="<tr>
				<td class='".clase($k)."' width='280px'>".traduce($resultado[nombre])."</td>
				<td class='".clase($k)."' width='50px'>". $codigo."</td>
				<td class='".clase($k)."' width='70px'>".cambiaf($resultado[fecha])."</td>
				<td class='".clase($k)."' width='70px'>".number_format($resultado[importe],2,',','.')."</td>
				<td class='".clase($k)."' width='100px'>&nbsp;".$resultado[obs_alt]."</td>
				<td class='".clase($k)."'> 
				
<input class='boton' type='button' onclick='borrar_factura(".$resultado[id].")' value='Borrar' />
<input class='boton' type='button' onclick='ver_factura(".$resultado[id].")' value='Factura' /> 
<input class='boton' type='button' onclick='duplicado_factura(".$resultado[id].")' value='Duplicado' />
<input class='boton' type='button' onclick='genera_recibo(".$resultado[id].")' value='Recibo' />
<input class='boton' type='button' onclick='window.open(\"facturapdf.php?factura=".$codigo."\",\"_blank\")' value='PDF' />
<input class='boton' type='button' onclick='window.open(\"facturapdf.php?factura=".$codigo."&dup=1\",\"_blank\")' value='Duplicado PDF' />
<input type='checkbox' name='code' id='code' value='".$codigo."' />
<p/>
<div id='modificaciones_".$resultado[id]."'></td></tr>";
		$k++;
		}
	}
else
	$cadena .= "<tr><th colspan='5'>No hay facturas</th></tr>";
$cadena .= "</table>
<div class='linea_checks'>
<span class='boton' onclick='check_all()'>Marcar Todos</span>
<span class='boton' onclick='uncheck_all()'>Desmarcar Todos</span>
<span class='boton' onclick='guarda_check_pdf(0)'>Guardar seleccionados como PDF</span>
<span class='boton' onclick='envia_check_pdf(0)'>Enviar PDF's por email</span>
<span class='boton' onclick='guarda_check_pdf(1)'>Guardar seleccionados como Duplicados PDF</span>
<span class='boton' onclick='envia_check_pdf(1)'>Enviar Duplicados PDF's por email</span>

</div>
</div><div id='linea_generacion'></div>";
return $cadena;
}
 function clase($k)
{
	if($k%2==0)
		$clase = "par";
	else
		$clase = "impar";
return $clase;
}
?>
