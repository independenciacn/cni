<?php
require_once '../inc/variables.php';
/*
 * Chequeo de errores activado
 */
error_reporting(0);
/*
 * Recoge la opcion y la manda a su destino
 */
if(isset($_POST['opcion']))
{
	switch($_POST['opcion'])
	{
		case 0:$respuesta=formulario_despacho($_POST);break;
		case 1:$respuesta=cuca($_POST);break;
		case 2:$respuesta=dame_nombre_cliente($_POST);break;
		case 3:$respuesta=informacion_despacho($_POST);break;
		case 4:$respuesta=guarda_despacho($_POST);break;
		case 5:$respuesta=detalles_ocupacion($_POST);break;
		case 6:$respuesta=editar_ocupacion($_POST);break;
		case 7:$respuesta=actualiza_ocupacion($_POST);break;
		case 8:$respuesta=borra_ocupacion($_POST);break;
		case 9:$respuesta=guarda_obs($_POST);break;
		case 10:$respuesta=formulario_agenda($_POST);break;
		case 11:$respuesta=personalizacion($_POST);break;
		case 12:$respuesta=agrega_tarea($_POST);break;
		case 13:$respuesta=agregar_tarea_pendiente($_POST);break;
		case 14:$respuesta=actualiza_tarea_pendiente($_POST);break;
		case 15:$respuesta=cambia_estado_tarea($_POST);break;
		case 16:$respuesta=borra_tarea($_POST);break;
		case 17:$respuesta=actualiza_tarea($_POST);break;
		case 18:$respuesta=borra_tarea_interna($_POST);break;
		case 19:$respuesta=filtrado_tareas_pendientes($_POST);break;
		case 20:$respuesta=agrega_nota($_POST);break;
		case 21:$respuesta=actualiza_nota($_POST);break;
		case 22:$respuesta=borra_nota($_POST);break;
		case 23:$respuesta=actualiza_esta_tarea($_POST);break;
		case 24:$respuesta=tareas_no_realizadas();break;
	}
	echo $respuesta;
}
else
	echo "Error en los datos";

/*
 * Formulario general de los despachos
 */
function formulario_despacho($vars)
{
	global $con;
	if ( isset( $vars['cliente'] ) ) {
		$sql = "Select Nombre from clientes where id like ".$vars['cliente'];
		$consulta = mysql_query($sql,$con);
		if ( mysql_numrows($consulta)!=0 ) {
			$resultado = mysql_fetch_array($consulta);
			$cliente = $resultado[0];
		}
		if ( $vars['ocupacion']=='total') {
			$parcial="";
			$total ="checked='checked'";
		} else {
			$parcial= "checked='checked'";
			$total = "";
		}
	} else {	
		if ( $vars['otro']!="" ) {
			$cliente = "";
			if($vars['ocupacion']=='total') {
				$parcial="";
				$total ="checked='checked'";
			} else {
				$parcial= "checked='checked'";
				$total = "";
			}
		} else {
		    $cliente = "";
		    $parcial="checked='checked'";
		    $total="";
		}
	}
	//Comprobacion del tipo de cliente y sus datos
	
	$cadena ="<div class='boton_cerrar' onclick='cerrar_formulario_agenda()'>
	</div>";
	if ( isset($vars['tipo']) ) {
		$cadena .= "<form name='form_despachos' id='form_despachos' 
		method='post' action='' onsubmit='actualiza_ocupacion(); return false'>
		<input type='hidden' id='registro' name='registro' 
		value='".$vars['registro']."'/>";
		$id_cliente=$vars['cliente'];
	} else {
		$cadena .= "<form name='form_despachos' id='form_despachos' 
		method='post' action='' onsubmit='guarda_despacho(); return false'>";
		$id_cliente="";}
	if ( $vars['despacho'] == 23 ) {
		$muestra = "Sala de Juntas";
	} else {
		$muestra = "Despacho ".$vars['despacho'];
	}
	$cadena .="<div class='seccion'>$muestra<input type='hidden' name='despacho' id='despacho' readonly value='$vars[despacho]' /><input type='hidden' name='id_cliente' readonly id='id_cliente' value='$id_cliente'/></div><p/>";
	$cadena .="Cliente:<input type='text' name='cliente' id='cliente' autocomplete='off' onkeyup='busca_cliente()' size='24%' value='$cliente' />
	<div id='listado_clientes_agenda'></div>";
	$cadena .="Otro:&nbsp;&nbsp;&nbsp;<input type='text' name='no_cliente' id='no_cliente' size='24%' value='$vars[otro]'/><p/>";
	$cadena .="<input type='button' onclick='limpia_nombre_cliente()' value='Limpiar' /><p/>";
	//chequeo de parametros de fecha
	if(isset($vars[tipo]))
	{
		$finc=cambiaf($vars[finc]);
		$ffin=cambiaf($vars[ffin]);
		$hinc=$vars[hinc];
		$hfin=$vars[hfin];
	}
	else
	{
		if(isset($_POST[dia])) {
			$finc=$_POST[dia];
			$ffin=$_POST[dia];
		}
		else
		{	
			$finc="00-00-0000";
			$ffin="00-00-0000";
		}
		$hinc="00:00";
		$hfin="00:00";
		
	}
	
	$cadena .="<div class='seccion'>Fecha Entrada&nbsp;|&nbsp;Fecha Salida</div><br>
	<center><input type='text' name='finc' id='finc' size='10' value='$finc'/>
	<input type='button' class='calendario_agenda' id='trigger_finc' value='..'/>&nbsp;";
	
	$cadena .="<input type='text' id='ffin' name='ffin' size='10' value='$ffin'/>
	<input type='button' id='trigger_ffin' class='calendario_agenda' value='..'/></center><p/>";
	/*Repeticiones semanales*/
	$cadena.="Repetir todos los:<br>";
	$cadena.="L<input type='checkbox' name='repe_l' value='L'>";
	$cadena.="M<input type='checkbox' name='repe_m' value='M'>";
	$cadena.="X<input type='checkbox' name='repe_x' value='X'>";
	$cadena.="J<input type='checkbox' name='repe_j' value='J'>";
	$cadena.="V<input type='checkbox' name='repe_v' value='V'>";
	/*Fin repeticion*/
	$cadena .="<div class='seccion'>&nbsp;Entrada (hh:mm)&nbsp;|&nbsp;Salida (hh:mm)</div><br>
	<center><input type='text' name='hinc' size=5 value='$hinc'/>&nbsp;&nbsp;
	<input type='text' name='hfin' size=5 value='$hfin'/>";
	$cadena.="<br><div id='debug'></div>";
	if(isset($vars[tipo]))
		$cadena .="<p/><input type='submit' name='Actualizar' value='Actualizar' />&nbsp;&nbsp;&nbsp;";
	else
		$cadena .="<p/>&nbsp;<input type='submit' name='Aceptar' value='Aceptar' />&nbsp;&nbsp;&nbsp;";
	$cadena .="<input type='button' name='Cancelar' onclick='cerrar_formulario_despacho()' value='Cancelar' /></center></form>";
return $cadena;	
}

/*
 * FUNCION QUE MUESTA EL LISTADO DE CLIENTES EN EL BUSCADOR
 */
function cuca($vars)
{
	global $con;
	if($vars[texto] == "")
	
		$muestra = "";
	else
	{
		$vars[texto] = codifica($vars[texto]);
		$sql = "Select * from `clientes` where (Nombre like '%$vars[texto]%' or Contacto like '%$vars[texto]%') and `Estado_de_cliente` like '-1' order by Nombre ";
		$consulta = mysql_query($sql,$con);
		
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			$muestra .="<span class='lbl_clientes' onclick='marca(".$resultado[0].")' onmouseout='quitar_color(".$resultado[0].")' onmouseover='cambia_color(".$resultado[0].")'><p id='linea_".$resultado[0]."'>".traduce(eregi_replace($vars[texto],"<b><u>".strtoupper($vars[texto])."</u></b>",$resultado[1]))."</span></p>";
		}
	}
	return $muestra;
}

/*
 * DEVUELVE EL NOMBRE DEL CLIENTE
 */
function dame_nombre_cliente($vars)
{
	global $con;
	$sql = "Select * from `clientes` where id like $vars[cliente] ";
	$consulta = @mysql_query($sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	$cadena = $resultado[0].";".traduce($resultado[1]);
	return $cadena;
}
/*
 * FUNCIONES AUXILIARES
 */


/*
 * Cambio de formato de fecha, en ambos sentidos
 */
function cambiaf($stamp)
{
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}

/*
 * Funcion auxiliar, muestra el nombre del cliente
 */
function nombre_cliente($id)
{
	global $con;
	$sql="Select Nombre from clientes where id like $id";
	$consulta = @mysql_query($sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	return $resultado[Nombre];
}

/*
 * Quita los segundos en la visualizacion
 */
function quita_segundos($hora)
{
	$sin_sec=explode(":",$hora);
	$final = $sin_sec[0].":".$sin_sec[1];
	return $final;
}

/*
 * FIN AUXILIARES
 */
function comprueba_cliente_total($vars)
{
	global $con;
	$sql="Select * from z_sercont where idemp like '$vars[cliente]' and servicio like 'Codigo Negocio' and valor like '%$vars[despacho]'";
	$consulta = @mysql_query($sql,$con);
	if(@mysql_numrows($consulta)!=0)
	$respuesta = true;
	else
	$respuesta = false;
	return $respuesta;
}

/*
 * Comprueba si el clientes es parcial
 */
function comprueba_cliente_parcial($cliente)
{
	global $con;
	return "";
}

/*
 * Funcion que muestra informacion detallada del despacho seleccionado
 */
function informacion_despacho($vars)
{
	global $con;
	$cadena.="<div class='boton_cerrar' onclick='cerrar_informacion_despacho()'></div>";
	if($vars[tipo]==0)
	{
		$cliente[cliente]=$vars[despacho];
		$datos_cliente = explode(";",dame_nombre_cliente($cliente));
		$cadena.="<form id='ficha_cliente' method='post' action=''>";
		$cadena.="<div class='seccion'>Cliente:</div><p>".$datos_cliente[1]."</p>";
	//Codigo Negocio
		$cadena.=comunicaciones($datos_cliente[0],"Codigo Negocio");
	//Telefonos
		$cadena.=comunicaciones($datos_cliente[0],"Telefono");
	//Adsl
		$cadena.=comunicaciones($datos_cliente[0],"Adsl");
	//Fax
		$cadena.=comunicaciones($datos_cliente[0],"Fax");
	//Direcciones IP
		$cadena.=comunicaciones($datos_cliente[0],"Direccion IP");
	//Codigo Fotocopias
		$cadena.=comunicaciones($datos_cliente[0],"Codigo Fotocopias");
	//Codigo Fotocopias
		$cadena.=comunicaciones($datos_cliente[0],"Codigo Fotocopias Autoservicio");
	//Empleados de la empresa
		$cadena.=traduce(empleados($datos_cliente[0]));
		//Parte de las observaciones
		$sql = "Select id_cliente,obs,id,despacho,conformidad,repeticion,hinc,hfin,finc,ffin from agenda where id like $vars[id]";
		$consulta = @mysql_query($sql,$con);
		$resultado = @mysql_fetch_array($consulta);
		$cadena.="<div class='seccion'>Observaciones:</div><p>";
		$cadena.="<textarea id='obs' cols='23' rows='5'>".$resultado[1]."</textarea><p/>";
		//$cadena.="<input type='button' class='boton' onclick='guarda_obs(".$resultado[2].",".$resultado[0].",".$vars[tipo].")' value='Guardar' /><p/>";
		$cadena.="<label>Ficha Conformidad</label>";
		if($resultado[4]=="Si")
		{
			$valor_si="checked";
			$valor_no="";
		}
		else
		{
			$valor_si="";
			$valor_no="checked";
		}
		$cadena.="<input type='radio' id='conformidad' name='conformidad' value='Si' ".$valor_si.">Si";
		$cadena.="<input type='radio' id='conformidad' name='conformidad' value='No' ".$valor_no.">No";
		/*Dia de Inicio y fin*/
		$cadena.="<br>Dia Inicio:<input type='text' id='finc' size='10' value='".cambiaf($resultado[finc])."'>";
		$cadena.="<br>Dia Fin:&nbsp;&nbsp;&nbsp;<input type='text' id='ffin' size='10' value='".cambiaf($resultado[ffin])."'>";
		/*Hora de entrada y salida*/
		$cadena.="<br>Hora Inicio:&nbsp;";
		$cadena.="<input type='text' id='hinc' size='6' value='".quita_segundos($resultado[hinc])."'><br>";
		$cadena.="Hora Final:&nbsp;&nbsp;";
		$cadena.="<input type='text' id='hfin' size='6' value='".quita_segundos($resultado[hfin])."'>";
		/*Repeticion de dias*/
		$repetimos .=";".$resultado[repeticion];
		$dias_repe = explode(";",$repetimos);
		$cadena.="<br>Repetir todos los:<br>";
		$cadena.="L<input type='checkbox' id='repe_l' value='L' ";
		if(array_search("L",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		$cadena.="M<input type='checkbox' id='repe_m' value='M' ";
		if(array_search("M",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		$cadena.="X<input type='checkbox' id='repe_x' value='X' ";
		if(array_search("X",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		$cadena.="J<input type='checkbox' id='repe_j' value='J' ";
		if(array_search("J",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		$cadena.="V<input type='checkbox' id='repe_v' value='V' ";
		if(array_search("V",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		/*Fin repeticion de dias*/
		$cadena.="<br><center><input type='button' class='boton' onclick='guarda_obs(".$resultado[2].",".$resultado[0].",".$vars[tipo].")' value='Guardar' />&nbsp;";
		$cadena.="<input type='button' class='boton' onclick='borra_ocupacion(".$resultado[2].")' value='Borrar'></center><p/>";
		$cadena.="<input type='hidden' id='codigo_despacho' value='".$resultado[3]."'>";
	}
	else
	{
		$cadena.="<div class='seccion'>Cliente:$vars[despacho]</div><p>";
			$sql = "Select otro,obs,id,despacho,conformidad,finc,ffin,hinc,hfin from agenda where id like $vars[despacho]";
		$consulta = @mysql_query($sql,$con);
		$resultado = @mysql_fetch_array($consulta);
		$cadena.=$resultado[0]."</p>";
		
		$cadena.="<div class='seccion'>Observaciones:</div><p>";
		$cadena.="<textarea id='obs' cols='23' rows='5'>".$resultado[1]."</textarea><p/>";
		$cadena.="<label>Ficha Conformidad</label>";
		if($resultado[4]=="Si")
		{
			$valor_si="checked";
			$valor_no="";
		}
		else
		{
			$valor_si="";
			$valor_no="checked";
		}
		$cadena.="<input type='radio' id='conformidad' name='conformidad' value='Si' ".$valor_si.">Si";
		$cadena.="<input type='radio' id='conformidad' name='conformidad' value='No' ".$valor_no.">No";
		
		/*Dia de Inicio y fin*/
		$cadena.="<center><br>Dia Inicio:<input type='text' id='finc' size='10' value='".cambiaf($resultado[finc])."'>";
		$cadena.="<br>Dia Fin:&nbsp;&nbsp;&nbsp;<input type='text' id='ffin' size='10' value='".cambiaf($resultado[ffin])."'>";
		/*Hora de entrada y salida*/
		$cadena.="<br>Hora Inicio:&nbsp;";
		$cadena.="<input type='text' id='hinc' size='6' value='".quita_segundos($resultado[hinc])."'><br>";
		$cadena.="Hora Final:&nbsp;&nbsp;";
		$cadena.="<input type='text' id='hfin' size='6' value='".quita_segundos($resultado[hfin])."'>";
		$cadena.="<br>";
		/*Repeticion de dias*/
		$repetimos .=";".$resultado[repeticion];
		$dias_repe = explode(";",$repetimos);
		$cadena.="<br>Repetir todos los:<br>";
		$cadena.="L<input type='checkbox' id='repe_l' value='L' ";
		if(array_search("L",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		$cadena.="M<input type='checkbox' id='repe_m' value='M' ";
		if(array_search("M",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		$cadena.="X<input type='checkbox' id='repe_x' value='X' ";
		if(array_search("X",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		$cadena.="J<input type='checkbox' id='repe_j' value='J' ";
		if(array_search("J",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		$cadena.="V<input type='checkbox' id='repe_v' value='V' ";
		if(array_search("V",$dias_repe)) $cadena.="checked";
		$cadena.=" >";
		/*Fin repeticion de dias*/
		$cadena.="<br><center><input type='button' class='boton' onclick='guarda_obs(".$resultado[2].",".$resultado[2].",".$vars[tipo].")' value='Guardar' />&nbsp;";
		$cadena.="<input type='button' class='boton' onclick='borra_ocupacion(".$resultado[2].")' value='Borrar'></center><p/>";
		$cadena.="<input type='hidden' id='codigo_despacho' value='".$resultado[3]."'>";
		$cadena.="</center></form>";
	}		
	return $cadena;
}

/*
 * Guarda las observaciones y la conformidad de la ocupacion del despacho
 */
function guarda_obs($vars)
{
	if($vars[tipo]!=1)
	{
		if($_POST[repe_l]=="L")
			$repes.="L;";
		if($_POST[repe_m]=="M")
			$repes.="M;";
		if($_POST[repe_x]=="X")
			$repes.="X;";
		if($_POST[repe_j]=="J")
			$repes.="J;";
		if($_POST[repe_v]=="V")
			$repes.="V;";
		$finc=cambiaf($_POST[finc]);
		$ffin=cambiaf($_POST[ffin]);
		if($vars[conformidad]!="Si")
			$vars[conformidad]="No";
	//$sql = "Update agenda set obs='$vars[obs]',conformidad='$vars[conformidad]' ,repeticion ='$repes',hinc ='$vars[hinc]', hfin='$vars[hfin]',finc='$finc',ffin='$ffin' where id like $vars[id]";
	}
	else
	{
		if($vars[conformidad]!="Si")
			$vars[conformidad]="No";
		//$sql = "Update agenda set obs='$vars[obs]',conformidad='$vars[conformidad]' where id like $vars[id]";
	}
	$sql = "Update agenda set obs='$vars[obs]',conformidad='$vars[conformidad]' ,repeticion ='$repes',hinc ='$vars[hinc]', hfin='$vars[hfin]',finc='$finc',ffin='$ffin' where id like $vars[id]";

	global $con;
	if( mysql_query( $sql, $con ) )
		$cadena = "Datos Guardados".$sql;
	else
		$cadena = $sql;
	return $sql;
}

/*
 * Muestra las comunicaciones asociadas al cliente
 */
function comunicaciones($cliente,$seccion)
{
	global $con;
	$sql = "select * from z_sercont where idemp like $cliente and servicio like '$seccion'";
	$consulta = @mysql_query($sql,$con);
	//echo $sql;//Depuracion
	if(@mysql_num_rows($consulta)!=0)
	{
		$cadena.="<div class='seccion'>".$seccion.":</div>";
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			$cadena.="<p>".$resultado[valor];
			if($resultado[boca]!="")
				$cadena.="- EXT:".$resultado[boca];
			$cadena.="</p>";
		}
	}
	return $cadena;
}

/*
 * Muestra el nombre de los empleados asociados a la empresa
 */
function empleados($cliente)
{
	global $con;
	$sql = "Select * from pempresa where idemp like $cliente";
	$consulta=@mysql_query($sql,$con);
	//echo $sql;//Depuracion
	if(mysql_num_rows($consulta)!=0)
	{
		$cadena.="<div class='seccion'>Empleados:</div>";
		while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
		{
			$cadena.="<p>".$resultado[nombre]." ".$resultado[apellidos];
			if($resultado[email]!="")
			$cadena.="&nbsp;<a href=mailto:".$resultado[email]." alt=".$resultado[email]."><img src='../iconos/mail_generic.png' alt='Enviar e-mail'/></a>";
			$cadena.="</p>";
			return $cadena;
		}
	}
}

/*
 * Visualizamos lo detalles de ocupacion del despacho
 * vamos la pantalla de antes en grande al lateral
 */
function detalles_ocupacion($vars)
{
	global $con;
	$sql="Select * from agenda where despacho like '$vars[despacho]' and 
		(datediff(curdate(),finc)<=0 or datediff(curdate(),ffin)<=0)
		order by finc asc, hinc asc";
	$consulta = @mysql_query($sql,$con);
	$respuesta ="<div class='boton_cerrar_nuevo' onclick='cerrar_formulario_agenda()'></div>";
	if($vars[despacho] == 23)
		$muestra = "Sala de Juntas";
	else
		$muestra = "Despacho ".$vars[despacho];
	$respuesta.="<div class='cabezera_despacho'>".$muestra."
&nbsp;&nbsp;&nbsp;&nbsp;</div><input type='hidden' value='".$vars[despacho]."' id='codigo_despacho' />";
	if(@mysql_numrows($consulta)!=0)
	{
		while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
		{
		$respuesta.="<p/><center><table width='95%' cellspacing=0px>
		<tr><th align='left'>Cliente</th><th align='right'>
		<span class='mini_boton' onclick='editar_ocupacion($resultado[id])'>[*]</span>
		<span class='mini_boton' onclick='borra_ocupacion($resultado[id])'>[X]</span>
		</th></tr>";
		//CASO RESERVAS
		if($resultado[otro]!="")
			$respuesta.="<tr><td colspan='2'>".traduce($resultado[otro])."</td></tr>";
		else
			$respuesta.="<tr><td colspan='2'>".traduce(nombre_cliente($resultado[id_cliente]))."</td></tr>";
		$respuesta.="<tr><th align='left'>Fecha Inicio</th><th align='left'>Fecha Fin</th></tr>";
	$respuesta.="<tr><td>".cambiaf($resultado[finc])."</td><td>".cambiaf($resultado[ffin])."</td></tr>";
		$respuesta.="<tr><th align='left'>Hora Inicio</th><th align='left'>Hora Fin</th></tr>";
			if($resultado[hinc]=="00:00")
				$hinc="--";
			else
				$hinc=$resultado[hinc];
			if($resultado[hfin]=="00:00")
				$hfin="--";
			else
				$hfin=$resultado[hfin];
		
		$respuesta.="<tr><td>".quita_segundos($hinc)."</td><td>".quita_segundos($hfin)."</td></tr>";
		$respuesta.="</table></center><p/>";
		}
	}
	else
		$respuesta.="Este despacho no tiene programada ninguna ocupaci&oacute;n";
	return $respuesta;
}
/*
 * FUNCIONES CRUD
 */
 
/*
 * Almacena los datos de ocupacion del despacho
 * !!NO SE COMPRUEBA NADA DE NADA!!
 */
function guarda_despacho($vars)
{
	$repeat="";
	$repes = array("repe_l","repe_m","repe_x","repe_j","repe_v");
	foreach($repes as $repe)
	{
		if(isset($_POST[$repe]))
		$repeat.=$_POST[$repe].";";
	}
	global $con;
	//chequeo hora
	if($vars[hinc]==" ")
	$vars[hinc]=="00:00";
	if($vars[hfin]==" ")
	$vars[hfin]=="00:00";
	//Comprobamos si el campo OTROS esta vacio
	if(marmota($vars))//Chequeo de despacho,dia,hora
	{
		if($vars[no_cliente]!="")
			$sql = "Insert into agenda (otro,despacho,finc,ffin,hinc,hfin,repeticion)
	values ('$vars[no_cliente]','$vars[despacho]','".cambiaf($vars[finc])."',
	'".cambiaf($vars[ffin])."','$vars[hinc]','$vars[hfin]','$repeat')";
		else
			$sql = "Insert into agenda (id_cliente,despacho,finc,ffin,hinc,hfin,repeticion)
	values ('$vars[id_cliente]','$vars[despacho]','".cambiaf($vars[finc])."',
	'".cambiaf($vars[ffin])."','$vars[hinc]','$vars[hfin]','$repeat')";
		if( mysql_query($sql,$con) )
			$cadena.=datos_del_despacho($vars);
		else
			$cadena.=datos_del_despacho($vars);
	}
	else
		$cadena.="Despacho Ocupado";
	return $cadena;
}

/*
 * Tanto para ok como para ko generamos de nuevo los detalles del despacho
 */
function datos_del_despacho($vars) //Solo para parciales
{
	$cadena.="<div class='cabezera_despacho'>Despacho ".$vars[despacho]."
&nbsp;&nbsp;&nbsp;&nbsp;<span class='mini_boton' onclick='formulario_despacho($vars[despacho])'>[+]</span>";
	$cadena.="&nbsp;<span class='mini_boton' onclick='detalles_ocupacion($vars[despacho])'>[?]</span></div>";
	$cadena.="<input type='hidden' id='cliente_despacho_$vars[despacho]' value='' />";
	$cadena.=datos_despacho($vars[despacho]);
	$cadena.="</div>";
	return $cadena;
}

/*
 * Muestra los datos del despacho
 */
function datos_despacho($despacho) //!!!FUNCION REPETIDA
{
	global $con;
	$sql="Select * from agenda where despacho like '$despacho' and 
		(datediff(curdate(),finc)<=0 or datediff(curdate(),ffin)<=0)
		order by finc asc, hinc desc limit 2";
	$consulta = @mysql_query($sql,$con);
	if(@mysql_numrows($consulta)!=0)
	{
		$cadena.="<div class='despacho_parcial' height='100%'>";
		$i=0;
		while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
		{
			$i++;
			$cadena.=nombre_cliente($resultado[id_cliente])."<br/>";
			$cadena.=cambiaf($resultado[finc])." - ".cambiaf($resultado[ffin])."<p/>";
			$cadena.="<span class='mini_boton' style='background:#666699;' onclick='informacion_cliente($resultado[id_cliente])'>[+Info]</span><p/>";
		}
		$cadena.="</div>";
	}
	else
		$cadena.="";
	return $cadena;
}

/*
 * Editamos la ocupacion seleccionada en el formulario
 * en $vars solo viene el despacho,sacamos el cliente
 */
function editar_ocupacion($vars)
{
	global $con;
	$sql = "Select id_cliente,despacho,finc,ffin,hinc,hfin,tipo_ocupacion,otro from agenda where id like $vars[ocupacion]";
	$consulta = @mysql_query($sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	$param[cliente]=$resultado[0];
	$param[despacho]=$resultado[1];
	$param[finc]=$resultado[2];
	$param[ffin]=$resultado[3];
	$param[hinc]=$resultado[4];
	$param[hfin]=$resultado[5];
	///NUEVA FASE
	$param[tipo]='1';
	$param[ocupacion]=$resultado[6];
	$param[otro]=$resultado[7];
	$param[registro]=$vars[ocupacion];
	//FIN NUEVA FASE
	$cadena.=formulario_despacho($param);
	return $cadena;
}

/*
 * Actualiza la ocupacion del despacho
 */
function actualiza_ocupacion($vars)
{
	global $con;
	if($vars[id_cliente]!="")
	$sql = "Update agenda set id_cliente='$vars[id_cliente]',";
	else
	$sql = "Update agenda set ";
	$sql.="finc='".cambiaf($vars[finc])."',
	ffin='".cambiaf($vars[ffin])."',
	hinc='$vars[hinc]',
	hfin='$vars[hfin]' where id like '$vars[registro]'";
	if( mysql_query($sql,$con))
		$cadena.=datos_del_despacho($vars);
	else
		$cadena.=datos_del_despacho($vars);
	return $cadena."".$sql;
}

/*
 * Borra la ocupacion del despacho
 */
function borra_ocupacion($vars)
{
	global $con;
	$sql = "Delete from agenda where id like $vars[ocupacion]";
	if(mysql_query($sql,$con))
		$cadena.=datos_del_despacho($vars);
	else
		$cadena.=datos_del_despacho($vars);
	return $cadena;
}

/*
 * AGENDA INTERNA
 */
function formulario_agenda($vars)
{
	/*Lista colores pasteles
	 * 
	 */
	$repetido = array('N','D','S','M','A','O');
	$colores = array('white','PaleGoldenRod','Pink','LightSteelBlue','PaleVioletRed ');
	$color_es = array('Ninguno','Amarillo','Rosa','Gris','Violeta');
	if(isset($vars[tarea]))
	{
		global $con;
		//chequeamos si hay datos modificados 
		
		$sql="Select * from agenda_interna where id like '$vars[tarea]'";
		$consulta=@mysql_query($sql,$con);
		$resultado=@mysql_fetch_array($consulta);
		$titulo="Modificar Tarea";
		$descripcion=$resultado[descripcion];
		$dia=cambiaf($resultado[dia]);
		$hinc=quita_segundos($resultado[inicio]);
		$hfin=quita_segundos($resultado[fin]);
		$clave = array_search($resultado[repetir],$repetido);
		$repetir[$clave]="selected";
		$repeticion=$resultado[repeticion];
		$clave = array_search($resultado[color],$colores);
		
		//Si hemos modificado el valor de esta celda
		$sql = "Select * from agenda_interna_estado where id_tarea like $vars[tarea] and hour(hora) like hour('$hinc') and dia like '$resultado[dia]'";
		$consulta = @mysql_query($sql,$con);
		if(@mysql_numrows($consulta)!=0)
		{	
			$resultado = @mysql_fetch_array($consulta);
			$clave = array_search($resultado[color],$colores);
			$color[$clave]="selected";
		}
		else
			$color[$clave]="selected";
		/*Fin chequeo*/
		$botones.="<input type='hidden' name='dia_marc' id='dia_marc' value='".$vars[dia]."' />";
		$botones.="<input type='hidden' name='id_tarea' id='id_tarea' value='".$vars[tarea]."' />";
		$botones.="<input type='hidden' name='hora_marc' id='hora_marc' value='".$vars[hora]."' />";
		$botones .="<center><input type='button' name='boton' onclick='actualiza_tarea(".$vars[tarea].")' value='Actualizar Todas' />";
		$botones .="<input type='button' name='boton' onclick='actualiza_esta_tarea()' value='Actualiza solo Esta' />";
		$botones .="&nbsp;<input type='button' name='boton' onclick='borra_tarea_interna(".$vars[tarea].")' value='Borrar' /></center>";
	}
	else
	{
		$titulo="Nueva Tarea";
		$descripcion="Nueva Tarea";
		$dia=$_POST[dia];
		$hinc=$vars[hora].":00";
		$hfin=$vars[hora]+1;
		$hfin=$hfin.":00";
		$repetir=array();
		$color=array();
		$botones = "<center><input type='button' name='boton' onclick='agrega_tarea()' value='Guardar' /></center>";
	}
	$cadena ="<div class='boton_cerrar' onclick='cerrar_formulario_agenda()'></div>";
	$cadena.="<form name='tareas' id='tareas' method='post' action=''>";
	$cadena.="<div class='seccion'>".$titulo."</div>";
	$cadena.="<div id='estado_tarea'></div>";
	$cadena.="<p><label>Descripcion: </label>hola<textarea name='descripcion'> ".$descripcion."</textarea></p>";
	$cadena.="<p><label>Dia:</label><input type='text' name='dia' value='".$dia."' size='11'/></p>";
	$cadena.="<p>
	<label>Inicio: </label>
	<select name='inicio'>";
	for($i=8;$i<=21;$i++)
	{
		if($hinc == $i.":00")
			$sel = "selected";
		else
			$sel = "";
		$cadena.="<option ".$sel." value=".$i.":00>".$i.":00</option>";
	}
	$cadena.="</select>";
	$cadena.="<label> Fin: </label>
	<select name='fin'>";
	for($i=8;$i<=21;$i++)
	{
		if($hfin == $i.":00")
			$sel = "selected";
		else
			$sel = "";
		$cadena.="<option ".$sel." value=".$i.":00>".$i.":00</option>";
	}
	$cadena.="</select>";
	$cadena.="<p><label>Repetir: </label>";
	$cadena.="<select id='repetir' name='repetir' onchange='previa_tipo()'>
	<option ".$repetir[0]." value='N'>No</option>
	<option ".$repetir[1]." value='D'>Todos los dias</option>
	<option ".$repetir[2]." value='S'>Cada Semana</option>
	<option ".$repetir[3]." value='M'>Cada Mes</option>
	<option ".$repetir[4]." value='A'>Cada A&ntilde;o</option>
	<option ".$repetir[5]." value='O'>Personalizar...</option>
	</select></p>";
	//Personalizacion de Cita
	$cadena.="<div id='personalizar'></div>";
	$cadena.="<p><label>Color: </label>";
	$cadena.="<select id='color' name='color' onchange='previa_color()'>";
	for($i=0;$i<=count($color_es)-1;$i++)
	$cadena.="<option ".$color[$i]." value='".$colores[$i]."'>".$color_es[$i]."</option>";
	/*<option ".$color[1]." value='".$colores[1]."'>Rojo</option>
	<option ".$color[2]." value='".$colores[2]."'>Naranja</option>
	<option ".$color[3]." value='".$colores[3]."'>Violeta</option>
	<option ".$color[4]." value='".$colores[4]."'>Azul</option>
	<option ".$color[5]." value='".$colores[5]."'>Amarillo</option>
	<option ".$color[6]." value='".$colores[6]."'>Verde</option>*/
	$cadena.="</select><span id='previa_color'>&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
	".$botones."
	</form>";
	return $cadena;
}

/*
 * Si seleccionamos programacion personalizada
 */
function personalizacion($vars)
{
	if($vars[repetir]=='O')
	{
		$cadena.="L<input type='checkbox' name='L' />";
		$cadena.="M<input type='checkbox' name='M' />";
		$cadena.="X<input type='checkbox' name='X' />";
		$cadena.="J<input type='checkbox' name='J' />";
		$cadena.="V<input type='checkbox' name='V' /><br/>";
		$cadena.="Cada:<input type='text' name='frecuencia' id='frecuencia' value='1' size='2'/> Semanas";
	}
	else
		$cadena="";
	return $cadena;
}

/*
 * Agregamos la tarea a la base de datos
 */
function agrega_tarea($vars)
{
	global $con;
	//inicializo repeticion,frecuencia y dia
	$repeticion="";
	$frecuencia=1;
	$dia = cambiaf($vars[dia]);
	//tratamos si llega como repetir O
	if($vars[repetir]=='O')
	{
		$dias=array('L','M','X','J','V');
		for($i=0;$i<=4;$i++)
		{
			if(isset($vars[$dias[$i]]))
			$repeticion.=$dias[$i].";";
		}
	$frecuencia=$vars[frecuencia];
	}
	//SQL
	$sql="Insert into agenda_interna 
	(descripcion,dia,inicio,fin,repetir,repeticion,frecuencia,color)
	values
	('$vars[descripcion]','$dia','$vars[inicio]','$vars[fin]','$vars[repetir]','$repeticion','$frecuencia','$vars[color]')";
	if(mysql_query($sql,$con))
		return "Tarea agregada";
	else
		return "No se ha agregado la tarea";
	
}

/*
 * Agrega la tarea pendiente
 */
function agregar_tarea_pendiente($vars)
{
	$fecha = cambiaf($vars[vencimiento]);
	global $con;
	$sql = "Insert into tareas_pendientes (nombre,vencimiento,prioridad,asignada) 
	values ('$vars[nombre]','$fecha','$vars[prioridad]',$vars[asignada])";
	if(mysql_query($sql,$con))
		return "Tarea agregada";
	else
		return "No se ha agregado la tarea";
}

/*
 * Funcion que comprueba si el despacho esta ocupado en ese momento
 */
function marmota($vars)
{
	//Chequeo despacho,dia,hora
	$horas=array();
	$fecha=cambiaf($vars[finc]);
	global $con;
	$sql = "SELECT hinc,hfin
	FROM `agenda` WHERE finc='$fecha' and despacho like '$vars[despacho]'";
	$consulta = mysql_query($sql,$con);
	while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
	{
		$hora_a=explode(":",$resultado[hinc]);
		$hora_b=explode(":",$resultado[hfin]);
		$hora_aa = intval($hora_a[0].$hora_a[1]);
		$hora_bb = intval($hora_b[0].$hora_b[1]);
		for($i=$hora_aa;$i<=$hora_bb-1;$i++)
		{
			$horas[$i]=1;//horas ocupadas
		}
	}
	//formateamos las horas de introduccion de despacho
	$hora_c=explode(":",$vars[hinc]);
	$hora_d=explode(":",$vars[hfin]);
	$hora_cc=intval($hora_c[0].$hora_c[1]);
	$hora_dd=intval($hora_d[0].$hora_d[1]);
	$check=0;
	for($i=$hora_cc;$i<=$hora_dd-1;$i++)
	{
		if($horas[$i]==1)
		$check=1;
	}
	if($check==0)
		return TRUE;
	else
		return FALSE;
}

/*
 * Funcion que actualiza los datos de la tarea
 */
 function actualiza_tarea_pendiente($vars)
 {
 	global $con;
	$vencimiento = cambiaf($vars[vencimiento]);
	$sql ="Update tareas_pendientes set nombre = '$vars[nombre]',
	vencimiento='$vencimiento', prioridad='$vars[prioridad]',asignada ='$vars[asignada]' where id like
	$vars[tarea]";
	if(mysql_query($sql,$con))
		return "Tarea Modificada";
	else	
		return "No se ha modificado la tarea".$sql;
 }
 
 /*
  * Cambia el estado de la tarea de realizada a no
  */
 function cambia_estado_tarea($vars)
 {
 	global $con;
	if($vars[estado]=="on")
		$valor = "Si";
	else
		$valor = "No";
	$sql = "Update tareas_pendientes set realizada = '$valor' where id = '$vars[tarea]'";
	if(mysql_query($sql,$con))
		return "Tarea actualizada";
	else
		return "No se ha actualizado la tarea ".$sql;
 }
 
 /*
  * Borra la tarea
  */
 function borra_tarea($vars)
 {
 	global $con;
	$sql = "Delete from tareas_pendientes where id like $vars[tarea]";
	if(mysql_query($sql,$con))
		return "Tarea borrada";
	else
		return "No se ha borrado la tarea ".$sql;
 }
 /*
  * CONTINUACION AGENDA INTERNA
  */
 
 /*
  * Actualiza la tarea de la agenda interna
  */
 function actualiza_tarea($vars)
 {
 	//inicializo repeticion,frecuencia y dia
	$repeticion="";
	$frecuencia=1;
	$dia = cambiaf($vars[dia]);
	//tratamos si llega como repetir O
	if($vars[repetir]=='O')
	{
		
		$dias=array('L','M','X','J','V');
		for($i=0;$i<=4;$i++)
		{
			if(isset($vars[$dias[$i]]))
			$repeticion.=$dias[$i].";";
		}
	/*Da error de momento la inicializo a 0*/
	//$frecuencia=$vars[frecuencia];
	$frecuencia = 1;
	}
	global $con;
	if($vars[repetir]=='O')
		$sql = "Update agenda_interna set 
	descripcion = '$vars[descripcion]', dia = '$dia',
	inicio = '$vars[inicio]', fin = '$vars[fin]',
	frecuencia = '$frecuencia',
	color = '$vars[color]' where id = '$vars[tarea]'";
	else
		$sql = "Update agenda_interna set 
	descripcion = '$vars[descripcion]', dia = '$dia',
	inicio = '$vars[inicio]', fin = '$vars[fin]',
	repetir = '$vars[repetir]' ,
	repeticion = '$vars[repeticion]',
	frecuencia = '$frecuencia',
	color = '$vars[color]' where id = '$vars[tarea]'";
	$sql2 = $sql;
	if(mysql_query($sql,$con))
	{
		$sql = "Delete from agenda_interna_estado where id_tarea like $vars[tarea]";
		if(mysql_query($sql,$con))
			return "Tarea actualizada";
		else
			return "No se ha actualizado la tarea ".$sql;
	}
	else
		return "No se ha actualizado la tarea ".$sql;
 }
 /*
  * Actualiza solo esa tarea para que aparezca distinta
  */
 function actualiza_esta_tarea($vars)
 {
 	
	global $con;
	$sql = "Select id from agenda_interna_estado where dia like '$vars[dia]' and hour(hora) like hour('$vars[hora]') and id_tarea like $vars[tarea]";
	$consulta = @mysql_query($sql,$con);
	if(@mysql_numrows($consulta)!=0)
	{
		foreach($vars as $key => $var)
		$cadena.="[".$key."]=>".$var.";";
		$resultado = mysql_fetch_array($consulta);
		$sql = "Update agenda_interna_estado set color = '$vars[color]' where id_tarea like $vars[tarea] and dia like '$vars[dia]' and hour(hora) like hour('$vars[hora]')";
		if(mysql_query($sql,$con))
			return "Tarea Actualizada";
		else
			return "No se ha actualizado la tarea".$sql;
	}
	else
	{
		$sql = "Insert into agenda_interna_estado (dia,hora,color,id_tarea) values('$vars[dia]','$vars[hora]','$vars[color]','$vars[tarea]')";
		if(mysql_query($sql,$con))
			return "Tarea Actualizada";
		else
			return "No se ha actualizado la tarea".$sql;
	}
 }
 /*
  * Borra la tarea de la agenda interna
  */
 function borra_tarea_interna($vars)
 {
 	global $con;
	$sql = "Delete from agenda_interna where id like $vars[tarea]";
	if(mysql_query($sql,$con))
		return "Tarea Borrada";
	else
		return "No se ha borrado la tarea".$sql;
 }
 
 /*
  * Filtrado de presentacion de las tareas pendientes
  */
 function filtrado_tareas_pendientes($vars) {
	global $con;
	$i = 0;
	$tareas = array ("Normal", "Media", "Alta", "Muy Alta" );
	$opcion = array ("No", "Si" );
	$tipo = array ("pendientes", "realizadas" );
	// foreach($vars as $key -> $var)
	if (isset ( $vars ['asignada'] ))
		$sql = "Select * from tareas_pendientes where asignada like '$vars[asignada]' order by prioridad desc ,vencimiento asc";
	else if (isset ( $vars ['prioridad'] ))
		$sql = "Select * from tareas_pendientes where prioridad like '$vars[prioridad]' order by prioridad desc ,vencimiento asc";
	else if (isset ( $vars ['vencimiento'] ))
		$sql = "Select * from tareas_pendientes where vencimiento like '$vars[vencimiento]' order by prioridad desc ,vencimiento asc";
	
	$consulta = @mysql_query ( $sql, $con );
	if (@mysql_numrows ( $consulta ) != 0) {
		while ( true == ($resultado = mysql_fetch_array ( $consulta )) ) {
			if ($i ++ % 2)
				$clase = "lista_par";
			else
				$clase = "lista_impar";
				/*
			 * Colores de las prioridades
			 */
			if ($resultado ['realizada'] == "Si")
				$realizada = "checked";
			else
				$realizada = "";
			$color_tarea = array ("normal", "media", "alta", "muy_alta" );
			// $texto.="<div class='".$clase."'><span
			// class='fecha_tarea'>".cambiaf($resultado[vencimiento])."</span>&nbsp;&nbsp;<span
			// class='prioridad_".$color_tarea[$resultado[prioridad]]."'>&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;".nombre_emp($resultado[asignada])."&nbsp;&nbsp;<a
			// href='javascript:edita_tarea_pendiente(".$resultado[0].")'>".$resultado[nombre]."</a><span
			// class='check'><input type='checkbox' id='tarea_".$resultado[0]."'
			// onchange='cambia_estado_tarea(".$resultado[0].")' ".$realizada."
			// />&nbsp;|&nbsp;<img src='imagenes/borrar.png' alt='Borrar Tarea'
			// onclick='borra_tarea(".$resultado[0].")' /></span></div>";
			$texto .= "<div class='" . $clase . "'>
			<span class='fecha_tarea'>" . cambiaf ( $resultado [vencimiento] ) . "</span>
			&nbsp;&nbsp;<span class='prioridad_" . $color_tarea [$resultado [prioridad]] . "'>
			&nbsp;&nbsp;&nbsp;&nbsp;</span>
			&nbsp;&nbsp;&nbsp;" . nombre_emp ( $resultado [asignada] ) . "&nbsp;&nbsp;
			<a href='javascript:edita_tarea_pendiente(" . $resultado [0] . ")'>
			" . $resultado [nombre] . "</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<span align='right'>
			<input type='checkbox' id='tarea_" . $resultado [0] . "' 
			onchange='cambia_estado_tarea(" . $resultado [0] . ")' " . $realizada . " />
			&nbsp;|&nbsp;<img src='imagenes/borrar.png' alt='Borrar Tarea' 
			onclick='borra_tarea(" . $resultado [0] . ")' /></span></div>";
		
		}
	} else
		$texto .= "<div class='lista_impar'>No hay tareas " . $tipo [$j] . "</div>";
	return $texto;
}

/*
 * Muestra el nombre de la empresa
 */
function nombre_emp($id)
{
	global $con;
	$sql = "Select * from empleados where Id like $id";
	$consulta = @mysql_query($sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	return "<span class='fecha_tarea'>".$resultado[3]." ".$resultado[1].":</span>";
}
/*
 * Funcion que agrega la nota a la base de datos
 */
 function agrega_nota($vars)
 {
 	global $con;
	$fecha = cambiaf($vars[vencimiento]);
	$sql= "Insert into notas (fecha,nota) values ('$fecha','$vars[nota]')";
	if(mysql_query($sql,$con))
		$cadena = "Tarea agregada";
	else
		$cadena = "No se ha agregado la nota ".$sql;
	return $cadena;
 }
 /*
  * Funcion que actualiza la nota
  */
 function actualiza_nota($vars)
 {
 	global $con;
	$fecha = cambiaf($vars[vencimiento]);
	$sql = "Update notas set fecha='$fecha' ,nota='$vars[nota]' where id like $vars[id]";
	if(mysql_query($sql,$con))
		$cadena.="Nota modificada";
	else
		$cadena.="No se ha modificado la nota".$sql;
	return $cadena;
 }
 /*
  * Function que borra la nota
  */
function borra_nota($vars)
{
	global $con;
	$sql = "Delete from notas where id like $vars[nota]";
	if(mysql_query($sql,$con))
		$cadena.="Nota borrada";
	else
		$cadena.="No se ha borrado la nota".$sql;
	return $cadena;
}
/*
 * Alerta de las tareas no realizadas
 */
function tareas_no_realizadas()
{
	global $con;
	$sql="SELECT e.Nombre, e.Apell1, t.vencimiento, t.nombre
FROM `tareas_pendientes` AS t
JOIN empleados AS e ON e.id = t.asignada where t.vencimiento like curdate() and realizada like 'No'";
	$consulta = @mysql_query($sql,$con);
	if(@mysql_numrows($consulta)!=0)
	{
		$cadena.="Tareas con vencimiento hoy:\n";
		while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
		{
			$cadena.=$resultado[0]." ".$resultado[1].":".$resultado[3]."\n ";
		}
	}
	else
		$cadena="No hay tareas con vencimiento hoy";
	
	return $cadena;
}
?>