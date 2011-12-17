<?php //datos.php  !!REVISION ENLACES ID PEDIDO E ID DE DETALLES
//Fichero datos.php (muestra los datos del cliente el en mes actual). Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007 
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
$html = "";
if ( isset( $_POST['opcion'] ) ) {
	sanitize( $_POST );
	switch($_POST['opcion'])
	{
		case 1:$html = cuca( $_POST );break;
		case 2:$html = dameNombreCliente($_POST);break;
		case 3:$html = verServiciosContratados($_POST);break;
		case 4:$html = borra_servicio_contratado($_POST);break;
		case 5:$html = frm_modificacion_servicio($_POST);break;
		case 6:$html = modificacion_servicio($_POST);break;
		case 7:$html = frmAltaServicio( $_POST );break;
		case 8:$html = valor_del_servicio($_POST);break;
		case 9:$html = agrega_el_servicio($_POST);break;
		case 10:$html = ventana_observaciones($_POST);break;
		case 11:$html = listado_facturas($_POST);break;
		case 13:$html = borrar_factura($_POST);break;
		case 14:$html = filtros($_POST);break;
		case 15:$html = casos($_POST);break;
	}
}
echo $html;

//FUNCIONES AUXILIARES********************************************************************************

function dame_el_mes($mes)
{
	global $meses;
	return $meses[$mes];
}
/**************************************************************************************************/
//calculoa lo que hay en el almacen y si hay que cobrarlo !!!!CAMBIAR MES ACTUAL POR MES SELLECCIONADO
function almacenaje( $cliente, $mes, $anyo ) //No creo que sea necesaria
{
	global $con;
	$j = 0;
	$subtotales = 0;
	$cadena = "";
	$totales = 0;
    $sql = "Select datediff('$anyo-$mes-01','2010-07-01')";
    $consulta = mysql_query( $sql, $con );
    $diff = $resultado = mysql_fetch_array($consulta);
    if ( $diff[0] >= 0 ) {
        $sql = "select PrecioEuro, iva from servicios2 
        where nombre like '%Almacenaje%'";
        $consulta = mysql_query( $sql, $con );
        $par_almacenaje = mysql_fetch_array($consulta);
    } else {
        $par_almacenaje = array('PrecioEuro'=>'0.70','iva'=>'16');
    }
    //Fin del calculo de los parametros del almacenaje dependiendo del valor que tiene en servicios
	$sql = "Select bultos, datediff(fin,inicio) from z_almacen 
	where cliente like " . $cliente . " and month(fin) like " . $mes . " 
	and year(fin) like " . $anyo;
	//var_dump( $sql );
	$consulta = mysql_query( $sql, $con );
	while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
        /*Modificar ivas aqui*/
		$subtotal = $resultado[0]*$resultado[1]*$par_almacenaje['PrecioEuro'];
		$total = round($subtotal + ($subtotal * $par_almacenaje['iva'])/100,2);
        /*Modificar ivas*/
		$cadena .="<tr class='".clase($j++)."'>
		<td>".$resultado[1]." Dias</td>
		<td>&nbsp;</td><td>&nbsp;</td>
		<td>&nbsp;Almacenaje</td>
		<td>".$resultado[0]."</td>
		<td>". precioFormateado( $par_almacenaje['PrecioEuro'] ) . " Bultos Dia</td>
		<td>". precioFormateado( $subtotal ) . "</td>
		<td>".$par_almacenaje['iva']."</td>
		<td>". precioFormateado( $total ) . "</td>
		</tr>";
		$totales = $totales + $total;
		$subtotales = $subtotales + $subtotal;
	}
	$final = array( $cadena, $subtotales, $totales );
return $final;
}
//Listado de servicios disponibles, creo que de aqui desaparecera
function servicios()
{
	
	$sql = "Select id, Nombre from servicios2 
	where `Estado_de_servicio` like '-1' or `Estado_de_servicio` 
	like 'on' order by Nombre";
	$consulta = mysql_query( $sql, $con );
	$html = "<select name='servicios' id='servicios' onchange='dame_el_valor()'>";
	$html.="<option value=0>--Seleccione Servicio--</option>";
	while( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
		$html .= "<option value='" . $resultado[0]."'>".$resultado[1]."</option>";
	}
	$html .= "</select>";
	return $html;
}
//FUNCIONES GENERALES NUEVAS************************************************************************/
//Funcion que busca y muestra el nombre del cliente*************************************************/
function cuca( $vars )
{
	global $con;
	$html = "";
	if( $vars['texto'] != "") {
		$sql = "Select * from `clientes` where 
		(Nombre like '%" . $vars['texto'] . "%' or Contacto 
		like '%" . $vars['texto'] ."%') and `Estado_de_cliente` 
		like '-1' order by Nombre ";
		$resultados = consultaGenerica( $sql );
		if (count($resultados) > 0) {
			$html .="<ul>";
			foreach ( $resultados as $resultado ) {
			    $html .="
			    <li>
			    	<span class='lbl_clientes' 
			    		onclick = 'marca(" . $resultado[0] . ")' 
			    		onmouseout='quitar_color(" . $resultado[0] . ")' 
			    		onmouseover='cambia_color(" . $resultado[0] . ")' >
			    	<p id='linea_".$resultado[0]."'>" . 
			    		preg_replace(
			    			'#' . $vars['texto'] .'#',
			    			"<b><u>" . strtoupper( $vars['texto'] ) . "</u></b>",
			    			$resultado[1]
			    		) . 
			    	"</p>
			    	</span>
			    </li>";
		    }
		    $html .="</ul>";
		} 
	}
	return $html;
}

/**
 * Funcion que devuelve el nombre de cliente y lo pone en el campo de texto
 *
 * @param array $vars
 * @return string
 */
function dameNombreCliente( $vars )
{
	global $con;
	$sql = "Select Nombre from `clientes` 
	where Id like " . $vars['cliente'] ;
	$resultado = consultaUnica( $sql );
	return $resultado[0];	
}

//Funcion que muestra los datos de los servicios contratados del cliente en el mes seleccionado***************/
function verServiciosContratados( $vars )
{
    $j = 0;
	$cantidad = 0;
	$html = "";
	
	$sql = "Select d.Servicio, d.Cantidad, date_format(c.fecha,'%d-%m-%Y') 
	as fecha, d.PrecioUnidadEuros, d.ImporteEuro, d.iva, c.`Id Pedido`,
	d.Observaciones, d.Id 
	from `detalles consumo de servicios` as d 
	join `consumo de servicios` as c on c.`Id Pedido` = d.`Id Pedido` 
	where c.Cliente like " . $vars['idCliente'] ." and (" . $vars['anyo'] . " 
	like date_format(c.fecha,'%Y') and '" . $vars['mes'] . "' 
	like date_format(c.fecha,'%c')) order by c.fecha asc"; 
	$resultados = consultaGenerica($sql);
	$html .="<input id='agregarServicios' type='button' 
	value='Agregar Servicios' />";
	/*$html .= "<span class='agregar' 
	onclick='ver_frm_agregar_servicio(" . $vars['idCliente'] . ")'>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Agregar Servicio</span>*/
	$html .= "<div id='frmAgregar'></div>";
	$html .= "<table>";
	$html .= "<thead>";
	$html .= "<tr><th>Fecha</th><th>&nbsp;</th><th>&nbsp;</th>
	<th>Servicio</th><th>Cantidad</th><th>Precio Unidad</th>
	<th>Importe</th><th>Iva</th><th>Total</th></tr></thead>";
//almacenaje
	//echo $sql;
	$almacenaje = almacenaje( $vars['idCliente'], $vars['mes'], $vars['anyo'] );
	$html .= $almacenaje[0];
	$subtotal = $almacenaje[1];
	$total = $almacenaje[2];
//fin del almacenaje
    $html .= "<tbody>";
	foreach( $resultados as $resultado ) {
		$subtotal = ($resultado[4]+($resultado[4]*$resultado[5])/100);	
//acumulados
		$total = $subtotal + $total;
		$cantidad = $resultado[1] + $cantidad;
//fin acumulados
		$j++;
		$modificar ="<input type='button' class='boton_editar_".clase( $j )."' 
		onclick='modificar(".$resultado[8].")' />";
		$html.= "<tr><td class=' ". clase( $j ) ."'>".$resultado[2]."</td>
		<td class=' ". clase( $j ) ."' align='center'>
		<input type='button' class='boton_borrar_". clase( $j ) . "' 
		onclick='borra(".$resultado[8].")' /></td>
		<td class=' ". clase( $j ) ."' align='center'>".$modificar."</td>
		<td class=' ". clase( $j ) ."'>&nbsp;".$resultado[0]." ".$resultado[7]."</td>
		<td class=' ". clase( $j ) ."'>&nbsp;".$resultado[1]."</td>
		<td class=' ". clase( $j ) ."'>&nbsp;" . precioFormateado( $resultado[3] ) . "</td>
		<td class=' ". clase( $j ) ."'>&nbsp;" . precioFormateado( $resultado[4] ) . "</td>
		<td class=' ". clase( $j ) ."'>&nbsp;".$resultado[5]."</td>
		<td class=' ". clase( $j ) ."'>&nbsp;" . precioFormateado( $subtotal ) . "</td></tr>";
	}
	$html .= "</tbody>";
	$html.= "<thead><tr><th colspan='4'>&nbsp;</th>
	<th>&nbsp;".$cantidad."</th><th colspan='3'>&nbsp;</th>
	<th>".precioFormateado( $total ) ."</th></thead>";
	$html.= "</table>";
	$html.= <<<EOD
    <script>
    $("#agregarServicios").click(function(){
        var url='formularioServicios.php';
	    var pars='cliente='+ $("#idCliente").val();
	    var div='frmAgregar';
	    procesaAjax(url, pars, div, 'Cargando Formulario', false, false );
    });
	</script>
EOD;
    return $html;
}

//Borrado del servicio contratado***************************************************************************
function borra_servicio_contratado($vars)
{
	include("../inc/variables.php");
	$sql = "Select `Id Pedido` from `detalles consumo de servicios` where `Id` like $vars[servicio]";
	$consulta = mysql_query( $sql, $con );
	$resultado  = mysql_fetch_array($consulta);
	$sql = "Delete from `consumo de servicios` where `Id Pedido` like $resultado[0]";
	if($consulta = mysql_query( $sql, $con ))
	{
		$sql = "Delete from `detalles consumo de servicios` where `Id` like $vars[servicio]";
		$consulta = mysql_query( $sql, $con );
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
	$consulta = mysql_query( $sql, $con );
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
	<th align='center' colspan='6'> Servicio:".$resultado[Servicio]." </th>
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
	if($consulta = mysql_query( $sql, $con ))
		return "<img src='".OK."' alt='Servicio Agregado' width='64'/> Servicio Actualizado&nbsp;&nbsp;<p/>";
	else
		return "<img src='".NOK."' alt='ERROR' width='64'/> ERROR&nbsp;&nbsp;<p/>".$sql;
}
//Formulario de Agregar servicio al cliente****************************************************************/
/*function frmAltaServicio( $vars )
{
	//onSubmit='agrega_servicio(); return false'>
    $html ="
	<form class='inline' id='frmAlta' name='frmAlta' method='post' action='' >
	<fieldset>
	    <legend>Alta de Servicio</legend>
	   <p>
	        <label for='fecha'>Fecha:</label>
	        <input type='text' name='fecha' id='fecha' />
	    
	        <label for='servicio'>Servicio:</label>
	        ". servicios()."
	    
	        <label for='precio'>Precio:</label>
	        <input type='text' name='precio' id='precio' />
	    
	        <label for='cantidad'>Cantidad:</label>
	        <input type='text' name='cantidad' id='cantidad' size='3' />
	   
	        <label for='iva'>Iva:</label>
	        <input type='text' name='iva' id='iva' size='3' />
	    
	        <label>Importe:</label>
	        <span id='importe'></span>
	    
	        <label>Total:</label>
	        <span id='total'></span>
	    </p>
	    <p>
	        <label for='observacion'>Observacion:</label><br/>
	        <textarea id='observacion' name='observacion' rows='1' cols='50'>
	        </textarea>
	    </p>
	    <input type='submit' value='Agregar Servicio' />
	</fieldset> 
	</form>";
	/*<table width='100%' class='tabla'>
	<tr>
	<th>Fecha:</th><td colspan='2'><input type='text' name='fecha' size='10' 
	value='".date("d-m-Y")."' />
	<th>Servicio:</th><td colspan='2'>".servicios()."</td>
	</tr><tr>
	<th>Precio:</th><td>
	<input type='text' name='precio' id='precio' size='8' 
	value='".precioFormateado( $resultado[0] )."' onChange='recalcula()' tabindex='2'/></td>
	<th>Cantidad:</th><td>
	<input type='text' name='cantidad' id='cantidad' size='3' value='1' 
	onkeyup='recalcula()' tabindex='3'/></td>
	<th>IVA:</th>
	<td><input type='text' name='iva' id='iva' size='3' 
	value='".$resultado[1]."' onkeyup='recalcula()' tabindex='4'/></td>
	</tr><tr>
	<th>Importe:</th><td colspan='2'>
	<span id='importe'>" . precioFormateado( $resultado[0] ) . "</span></td>
	<th>Total:</th><td colspan='2'><span id='total'>".$totals."</span></td>
	</tr>
	<tr>
	<th colspan='2'>Observaciones:</th>
	<td colspan='2'><textarea id='observacion' name='observacion' rows='1' cols='50' tabindex='5'></textarea></td>
	<th colspan='2'><input type='submit' class='agregar' id='agregar' accesskey='a' value='Agregar' tabindex='6'/></th>
	</tr>
	</table>
	";
	return $html;
}*/
//Funcion que devuelve el precio y el iva del servicio seleccionado****************************************/
function valor_del_servicio($vars)
{
	include("../inc/variables.php");
	$sql = "Select PrecioEuro,iva from servicios2 where id like $vars[servicio]";
	$consulta = mysql_query( $sql, $con );
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
	$consulta = mysql_query( $sql, $con );
	$resultado = mysql_fetch_array($consulta);
	$servicio = $resultado[0];
	$fecha = cambiaf($vars[fecha]);
	$subtotal = $vars[precio]*$vars[cantidad];
	$sql = "Insert into `consumo de servicios` (`Cliente`,`Fecha`) values ('$vars[cliente]','$fecha')";
	if($consulta = mysql_query( $sql, $con ))
	{
		$sql = "Insert into `detalles consumo de servicios` (`Id Pedido`,`Servicio`,`Cantidad`,`PrecioUnidadEuros`,`ImporteEuro`,`iva`,`observaciones` )
	values (LAST_INSERT_ID(),'$servicio','$vars[cantidad]','$vars[precio]','$subtotal','$vars[iva]','$vars[observacion]')";
		if($consulta = mysql_query( $sql, $con ))
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
	$consulta = mysql_query( $sql, $con );
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
	$consulta = mysql_query( $sql, $con );
	$resultado = mysql_fetch_array($consulta);
	$codigo = $resultado[0];
	$sql = "Delete from regfacturas where id like $vars[factura]";
	if($consulta = mysql_query( $sql, $con ))
	{
		$sql = "Delete from historico where factura like $codigo";
		$consulta = mysql_query( $sql, $con );
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
	if($vars['tipo']==1)
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
	$cadena = "";
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
function dibuja_pantalla( $sql, $marca_cliente, $marca_factura, $marca_fecha, $marca_importe )
{
	global $con;
	//Ordenes
	$k = 0;
	$cadena ="
	<input type='hidden' id='marca_cliente' value='".$marca_cliente."' />
	<input type='hidden' id='marca_factura' value='".$marca_factura."' />
	<input type='hidden' id='marca_fecha' value='".$marca_fecha."' />
	<input type='hidden' id='marca_importe' value='".$marca_importe."' />";
	$cadena .="<table width='100%' class='tabla'>";
	$resultados = consultaGenerica($sql);
	if ( count( $resultados ) > 0) {
		foreach ( $resultados as $resultado ) {
			$codigo = ( $resultado['codigo'] == 0) ? intval($resultado['id']) + 99 : $resultado['codigo'];
			$cadena .="<tr>
			<td class='" . clase( $k ) . "' width='280px'>
				" . $resultado['nombre'] . "</td>
			<td class='" . clase( $k ) . "' width='50px'>
				" . $codigo . "</td>
			<td class='" . clase( $k ) . "' width='70px'>
				" . cambiaf( $resultado['fecha'] ) . "</td>
			<td class='" . clase( $k ) . "' width='70px'>
				" . precioFormateado( $resultado['importe'] ) . "</td>
			<td class='". clase( $k ) ."' width='100px'>
				&nbsp;" . $resultado['obs_alt'] . "</td>
			<td class='" . clase( $k ) . "'> 
			<input class='boton' type='button' 
				onclick='borrar_factura(" . $resultado['id'] . ")' value='Borrar' />
			<input class='boton' type='button' 
				onclick='ver_factura(" . $resultado['id'] . ")' value='Factura' /> 
			<input class='boton' type='button' 
				onclick='duplicado_factura(" . $resultado['id'] . ")' value='Duplicado' />
			<input class='boton' type='button' 
				onclick='genera_recibo(" . $resultado['id'] . ")' value='Recibo' />
			<input class='boton' type='button' 
				onclick='window.open(\"facturapdf.php?factura=" . $codigo . "\",\"_blank\")' value='PDF' />
			<input class='boton' type='button' 
				onclick='window.open(\"facturapdf.php?factura=" . $codigo . "&dup=1\",\"_blank\")' value='Duplicado PDF' />
			<input type='checkbox' name='code' id='code' value='" . $codigo . "' />
			<p/>
			<div id='modificaciones_" . $resultado['id'] . "'></td></tr>";
			$k++;
		}
	} else {
		$cadena .= "<tr><th colspan='5'>No hay facturas</th></tr>";
	}
	$cadena .= "</table>
	<div class='linea_checks'>
	<span class='boton' onclick='check_all()'>Marcar Todos</span>
	<span class='boton' onclick='uncheck_all()'>Desmarcar Todos</span>
	<span class='boton' onclick='guarda_check_pdf(0)'>Guardar seleccionados como PDF</span>	
	<span class='boton' onclick='envia_check_pdf(0)'>Enviar PDF's por email</span>
	<span class='boton' onclick='guarda_check_pdf(1)'>Guardar seleccionados como Duplicados PDF</span>
	<span class='boton' onclick='envia_check_pdf(1)'>Enviar Duplicados PDF's por email</span>
	</div>
	</div>
	<div id='linea_generacion'></div>";
	return $cadena;
}

?>
