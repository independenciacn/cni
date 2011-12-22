<?php //funciones de creacion dinamica de contenidos Hecha por Ruben Lacasa Mas en Marzo 2007 ruben@ensenalia.com
//***********************************************************************************************/
//switch(opcion) Recoge la opcion pasada por la funcion ajax y la redirecciona a la funcion asiganda php
//despues recoge el valor de la funcion en respuesta y la muestra por pantalla
//***********************************************************************************************/
switch($_POST[opcion])
{
	case 0:$respuesta = generador($_POST);break;
	case 1:$respuesta = cuca($_POST);break;
	case 2:$respuesta = formulario($_POST);break;
	case 3:$respuesta = subformulario($_POST);break;
	case 4:$respuesta = actualiza($_POST);break;
	case 5:$respuesta = nuevo($_POST);break;
	case 6:$respuesta = agrega_registro($_POST);break;
	case 7:$respuesta = borra_registro($_POST);break;
	case 8:$respuesta = frm_srv_fijos($_POST);break;
	case 9:$respuesta = cambia_los_otros($_POST);break;
	case 10:$respuesta = agrega_srv_fijo($_POST);break;
	case 11:$respuesta = borra_srv_fijo($_POST);break;
	case 12:$respuesta = actualiza_srv_fijo($_POST);break;
}
echo $respuesta;

//***********************************************************************************************/
//traduce(texto): cuando algo no se muestra bien este lo decodifica
//***********************************************************************************************/
function traduce($texto)
{
/*if(SISTEMA == "windows")
	$bien = utf8_encode($texto); //para windows
else*/
	$bien = $texto;//para sistemas *nix
return $bien;
}

//***********************************************************************************************/
//codifica(texto): inversa a traduce
//***********************************************************************************************/
function codifica($texto)
{
/*if(SISTEMA == "windows")
	$bien = utf8_decode($texto); //para windows
else*/
	$bien = $texto;//para sistemas *nix
return $bien;
}

//***********************************************************************************************/
//OPCION:0 GENERADOR de la pagina principal de gestion
//funcion generadora
//nuevo dise�o, en cabeza nos saldra un campo de texto que ira mostrando resultados segun lo que pongamos
//y el que seleccionemos nos dara sus datos mola no?
//cogemos la tabla perteneciente al menu
//***********************************************************************************************/
function generador($vars)
{
include("variables.php");
	//caso del generator
	if($vars[codigo] == 6)
	{
		$tabla .= "
		<div class='gestion_app'>
		Gesti&oacute;n de Base de Datos:
		<span class='boton' onclick='hacer_backup()'>&nbsp;&nbsp;[H]Hacer copia&nbsp;&nbsp;</span>
		<span class='boton' onclick='lista_backup()'>&nbsp;&nbsp;[L]Listado de Copias 		realizadas&nbsp;&nbsp;</span>
		<span class='boton' onclick='nuevaPass()'>Nueva Contraseña de Acceso</span>";
		/*<span class='boton' onclick='revisar_tablas()'>&nbsp;&nbsp;[V]Revisar Tablas&nbsp;&nbsp;</span>
		<span class='boton' onclick='reparar_tablas()'>&nbsp;&nbsp;[R]Reparar Tablas&nbsp;&nbsp;</span>
		<span class='boton' onclick='optimizar_tablas()'>&nbsp;&nbsp;[O]Optimizar Tablas&nbsp;&nbsp;</span>*/
		$tabla.="</div>
		<div class='gestion_app'>
		Datos Categorias:
		<span class='boton' onclick='categorias(1)'>Categorias Servicios</span>
		<span class='boton' onclick='categorias(2)'>Categorias Clientes</span>
		</div>
		
		<div class='gestion_app'>
		Telefonos Centro: 
		<span class='boton' onclick='formulario_telefonos()'>&nbsp;&nbsp;Gestion Telefonos Centro &nbsp;&nbsp;</span>
		</div>
		<div class='gestion_app'>
		Listado Despachos y Domiciliados:
		<input type='button' class='boton' onclick='consulta_especial()' value='Ver Listado Completo' />
		<p><label>Ver listado filtrado de:</label>
		".listado_categorias()."
		</p>
		</div>
		<div id='listado_copias'></div>
		<div id='estado_copia'></div>
		<div id='status_tablas'></div>
		</center>";
	}
	else
	{
		$sql = "Select pagina from menus where id like $vars[codigo]";
		$consulta = mysql_db_query($dbname,$sql,$con);
		$resultado = mysql_fetch_array($consulta);
		$tabla .= "<div id='botoneria'>";
		$tabla = "&nbsp;&nbsp;<span class='titulo_categoria'>Seleccione ".ucfirst($resultado[0]).":</span>";
		$tabla .= "<input type='hidden' id='tabla' value='".$resultado[0]."' />";
		$tabla .= "<input type='hidden' id='nuevo' value='".$vars[codigo]."' />";
		$tabla .= "<input type='text' id='texto' autocomplete='off' onkeyup='busca()'/>&nbsp;<input class='boton' type='submit' onclick='busca()' value='[M]Mostrar Busqueda'>";
		//Y los nuevos donde van matarile rile ron
		$tabla .= "&nbsp;<input class='boton' type='submit' onclick='nuevo($vars[codigo])' value='[+] Nuevo ".ucfirst($resultado[0])."'>";
		if($vars[codigo] == 1)//!!PEGOTEEEEEEEEEE
		{
				
			$tabla.="&nbsp;<input class='boton' type=submit onclick=popUp('servicont/index.php') value = 'Estadisticas Servicios' />";
			$tabla.="&nbsp;<input class='boton' type=submit onclick=popUp('rapido/index.php') value = 'Asignacion de Servicios' />";
			$tabla.="&nbsp;<input class='boton' type=submit onclick=popUp('almacen/index.php') value = 'Almacenaje' />";
			//$tabla.="&nbsp;<input class='boton' type=submit onclick=popUp('contratos/index.php') value = 'Contratos' />";
			$tabla.="&nbsp;<input class='boton' type=submit onclick=popUp('agenda/index.php') value = 'Agenda' />";
			$tabla.="&nbsp;<input class='boton' type=submit onclick=popUp('entradas2/index.php') value = 'Entradas' />";
			
		}
		$tabla .= "</div>";
		
	}
	return $tabla;
}

//***********************************************************************************************/
//cuca(array variables): funcion de buscador de cliente por Nombre y contacto
//***********************************************************************************************/
function cuca($vars)
{
	include("variables.php");
	if($vars[texto] == "")
	
		$muestra = "";
	else
	{
		$vars[texto] = codifica($vars[texto]);
		if ($vars[tabla] == 'clientes')
		$sql = "Select * from `$vars[tabla]` where Nombre like '%$vars[texto]%' or Contacto like '%$vars[texto]%' order by Nombre";
		else
		$sql = "Select * from `$vars[tabla]` where Nombre like '%$vars[texto]%' order by Nombre";
		$consulta = mysql_db_query($dbname,$sql,$con);
		$muestra .= "<input class='boton' type='button' onclick='cierra_frm_busca()' value='[X]Cerrar'>";
		while($resultado = mysql_fetch_array($consulta))
		{
			$i++;
			if($i%2 == 0)
				$clase = "pares";
			else
				$clase = "impares";
			$muestra .="<div class='".$clase."'><a href='javascript:muestra(". $resultado[0] .")' >".traduce(eregi_replace($vars[texto],"<span class='resalta'>".strtoupper($vars[texto])."</span>",$resultado[1]))."</a></div>";
		}
	}
return $muestra;
}
/*************************************************************************************************/
//color_cabezera($tabla,$vars) funcion que genera el color de la cabezera dependiendo del tipo de cliente;
//***********************************************************************************************/
function color_cabezera($tabla,$vars)
{
	switch($tabla)
	{
		case "clientes":switch(TRUE)
							{
								case(ereg("despacho",$vars[Categoria])):$color="#69C";break;
								case(ereg("domicili",$vars[Categoria])):$color="#F90";break;
								default:$color="#7d0063";break;
							};break;
		default:$color="#7d0063";break;
	}
	return $color;
}
//Devuelve el codigo de negocio
function codigo_negocio($idemp)
{
	include("variables.php");
	if(isset($idemp)&& $idemp!=NULL)
    {
    $sql = "Select * from z_sercont where idemp like $idemp and servicio like 'Codigo Negocio'";
	$consulta = mysql_db_query($dbname,$sql,$con);
	//echo $sql; //Linea de test
	if (mysql_numrows($consulta)>= 0)
	{
		$resultado =  mysql_fetch_array($consulta);
		$cadena = "<font size='6'>".$resultado[valor]."</font>";
	}
	else
		$cadena = "";
    }
    else $cadena = "";
	return $cadena;
}
//
//***********************************************************************************************/
//GENERACION DEL FORMULARIO DEPENDIENDO DE DONDE ESTAMOS solo agrega
//***********************************************************************************************/
function formulario($vars)
{
	include("variables.php");
	$sql = "Select * from `$vars[tabla]` where id like $vars[registro]";//raiz
	$consulta = mysql_db_query($dbname,$sql,$con);
	$numero_campos = mysql_num_fields($consulta); 
	$resultado = mysql_fetch_array($consulta);
	$cadena .= "
	<form id='formulario_actualizacion' action='#' method='post' onSubmit='actualiza_registro(); return false'>
	<input type='hidden' id='opcion' value='0' />
	<input type='hidden' id='idemp' value='".$resultado[0]."' />
	<table cellpadding=0px cellspacing=1px class='formulario'>";
	//cabezera nombre de empresa, desvio y activo y menu
	switch($vars[tabla])
	{
		case "clientes": $desvio .= desvio_activo($resultado[desvio],$resultado["Estado_de_cliente"],$resultado[extranet],$vars[registro]);
		//$code = codigo_negocio($resultado[Id]);
		break;
		default:$desvio .= "";//$code="";break;
	}
	$color_cabezera = color_cabezera($vars[tabla],$resultado);
	$cadena .= "<th height='24px' bgcolor='".$color_cabezera."' color='#fff' align='left' width='100px'><div id='edicion_actividad'></div>";
	$cadena .= $desvio."</th><th height='24px' align='left' bgcolor='".$color_cabezera."' colspan='2'><font size='4'>".traduce($resultado[Nombre])." ".codigo_negocio($resultado[Id])."</font><input type='hidden' name='nombre_tabla' id='nombre_tabla' value='".$vars[tabla]."' /><input type='hidden' name='numero_registro' id='numero_registro' value='".$resultado[0]."' /></th><th align='right' bgcolor='".$color_cabezera."'><input class='boton' onclick='cierra_el_formulario()' value='[X] Cerrar' ></th></tr>";

	//submenus
	$cadena .= submenus($vars);
	//Fin de los submenus
	//campo oculto con nombre de tabla
	for($i=1;$i<=$numero_campos-1;$i++)//si empiezo desde 1 me salto el id, pero no el idepm
	{
		if($j%2==0)
		$cadena .= "</tr><tr>";
		$j++;
		$cadena .= "<th align='left' valign='top' class='nombre_campo'>".traduce(nombre_campo(mysql_field_name($consulta,$i),$vars[tabla])) ."</th><td align='left' valign='top' class='valor_campo'>".tipo_campo(mysql_field_name($consulta,$i),$vars[tabla],$resultado[$i],'actualiza',$i) ."</td>";
	}
	$cadena .= "</tr>";
	//parte de la botoneria ya empezamos con los casos particulars
	//o actualizo o creo
	if(isset($vars[principal])) //de momento indicativo de subformulario
	{
		$cadena .= "<tr><th colspan='4' align='center'><input class='boton' type='submit'  value='[+] Agregar' />";
		$cadena .= "<input class='boton' type='reset'  value='[L] Limpiar formulario' /></th></tr>";
	}
	else
	{
		$cadena .= "<tr><th colspan='4' align='center'><p/><input class='boton' type='submit' value='[*]Actualizar Datos' tabindex='".$numero_campos."'/>";
		$cadena .= "<input type='button' class='boton' onclick='borrar_registro(".$resultado[0].")' value='[X]Borrar Datos' tabindex='".$numero_campos."'/></th></tr>";
	}
	$cadena .= "</table></form>";
	return $cadena;
}
//***********************************************************************************************/
//funcion nombre_campo(nombre_campo,nombre_tabla)
//***********************************************************************************************/
function nombre_campo($campo,$tabla)
{
	include("variables.php");
	$sql = "Select campof from alias where tabla like '$tabla' and `campoo` like '$campo'";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	return $resultado[0];
}

//***********************************************************************************************/
//funciton tipo_campo(nombre_campo,nombre_tabla,valor_campo,opcion,orden_en_la_lista)
//***********************************************************************************************/
function tipo_campo($campo,$tabla,$valor,$opcion,$orden)
{
	include("variables.php");
	$sql = "Select * from alias where tabla like '$tabla' and `campoo` like '$campo'";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	switch($resultado[tipo])
	{
		case "text": //caso rarito de z_sercont valor
		 			if (($tabla =='z_sercont') && ($resultado[campoo]=='valor'))
					$cadena ="<div id='tipo_teleco'><input type='text' size='".$resultado[size]."' id='".$resultado[variable]."' name='".$resultado[campoo]."' value='".traduce($valor)."' tabindex='".$i."' onkeyup='chequea_valor()'/></div>";
					else
					$cadena = "<input type='text' size='".$resultado[size]."' id='".$resultado[variable]."' name='".$resultado[campoo]."' value='".traduce($valor)."' tabindex='".$i."'/>";break;
		case "textarea":$cadena = "<textarea id='".$resultado[variable]."' name='".$resultado[campoo]."' rows='".$resultado[size]."' cols='46' tabindex='".$i."'>".traduce($valor)."</textarea>";break;
		case "checkbox": 	{
							if ($valor!= 0)
								$chequeado = 'checked';
							else
								$chequeado = ''; 
							$cadena = "<input  type='checkbox' id='".$resultado[variable]."' ".$chequeado." name='".$resultado[campoo]."' tabindex='".$i."'/>";
							}
							break;
		case "date": 	{
						$cadena = "<input type='text' id='".$resultado[variable]."' name='".$resultado[campoo]."' size = '".$resultado[size]."'  value='".cambiaf($valor)."' tabindex='".$i."'/>";
						$cadena .= "&nbsp;&nbsp;<button TYPE='button' class='calendario' id='f_trigger_".$resultado[variable]."' tabindex='".$i."'></button>";
						}
						break;
		case "select": 
							{//hay que hacer una consulta a la tabla dependiente de los valores
							$sql = "Select * from `$resultado[depende]` order by 2";
							$consulta = mysql_db_query($dbname,$sql,$con);
							if ($tabla =='z_sercont') //caso del z_sercont
								$cadena ="<select id='".$resultado[variable]."' name='".$resultado[campoo]."' tabindex='".$i."' onchange='muestra_campo()'>";
							else
								$cadena ="<select id='".$resultado[variable]."' name='".$resultado[campoo]."' tabindex='".$i."'>";
								$cadena .="<option value='0'>-::".traduce($resultado[campoo]).":-</option>";
							while ($resultado = mysql_fetch_array($consulta))
								{
								if (traduce($resultado[1]) == traduce($valor))
									$marcado = 'selected';
								else 
									$marcado = "";
								$cadena .= "<option ".$marcado." value='".traduce($resultado[1])."'>".traduce($resultado[1])."</option>";
								}
							$cadena .= "</select> ".traduce($valor);
							}break;					
		default: $cadena = $valor;break;
		
	}
	switch($resultado[enlace])//generamos el enlace de conexion o bien a web o envio de correo
		{
			case "web":$cadena .="<a href='http://".$valor."' target='_blank'><img src='iconos/package_network.png' width='14' alt='Abrir Web'/></a>";break;
			case "mail":$cadena .="<a href='mailto:".$valor."'><img src='iconos/mail_generic.png' width='14' alt='Enviar Correo'/></a>";break;
		}
	return $cadena;
}

//***********************************************************************************************/
//desvio_activo(valor_desvio,valor_estado): Funcion que muestra el pantalla si el cliente tiene activo el desvio y su estado como cliente
//***********************************************************************************************/
function desvio_activo($desvio,$estado,$extranet,$cliente)
{
	
	if($estado == 0) //Cliente activo o no
		$cadena = "<img src='imagenes/noactivo.gif' alt='Cliente Inactivo' width='24px'/>";
	else
		$cadena = "<img src='imagenes/activo.gif' alt='Cliente Activo' width='24px'/>";
		
	if($desvio == 0) //Desvio activo o no
		$cadena .= "<img src='imagenes/desvioi.gif' alt='Desvio Inactivo' width='24px'/>";
	else
		$cadena .= "<spam class='popup' onclick='ver_detalles(0,0,0,".$cliente.")'><img src='imagenes/nudesvioa.gif' alt='Desvio Activo' width='24px' /></spam>";
		
	if($extranet == 0)//Extranet activa o inactiva
		$cadena .= "<img src='imagenes/extraneti.gif' alt='Extranet Inactivo' width='24px'/>";
	else
		$cadena .= "<spam class='popup' onclick='ver_detalles(0,0,1,".$cliente.")'><img src='imagenes/extraneta.gif' alt='Extranet Activa' width='24px' /></spam>";
	return $cadena;	
}

//***********************************************************************************************/
//cambiaf(fecha): cambio de fecha de formato americano - espa�ol y viceversa 
//***********************************************************************************************/
function cambiaf($stamp)
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	if($fecha == "--")
		$fecha = "0000-00-00";
	return $fecha;
}

//***********************************************************************************************/
//submenus(nombre_tabla):Genera el submenu si la tabla pasada lo tiene si no no lo genera
//***********************************************************************************************/
function submenus($tabla)
{
	include("variables.php");
	$sql = "Select id from menus where pagina like '$tabla[tabla]'";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$sql = "Select * from submenus where menu like $resultado[0]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$cadena = "<tr><th colspan='4' width='100%'height='26px'><table><tr>";
	while ($resultado = mysql_fetch_array($consulta))
	{
		if($resultado[2] == "Principal")
		$cadena .= "<th><span class='boton' onclick='muestra(".$tabla[registro].")' >".traduce($resultado[2])."</span></th>";
		else
		$cadena .= "<th><span class='boton' onclick='submenu(".$resultado[0].")' >".traduce($resultado[2])."</span></th>";
	}
	$cadena .= "</tr></table></th></tr>";
	
	return $cadena;
}

//***********************************************************************************************/
//listado(array vars): Muestra el listado de la tabla que le hemos pasado dentro de la array
//El array $datos = array("tabla" => "$resultado[0]","registro" =>"$vars[registro]","principal"=>"$resultado[1]");
//***********************************************************************************************/
function listado($vars)
{

	include("variables.php");
	$sql = "Select * from `$vars[tabla]` where idemp like $vars[registro]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$totdatos = mysql_num_rows($consulta);
	$tot_columnas = mysql_num_fields($consulta);
	$cadena .= "<table class='listado'><tr>";
	for ($i=2;$i<=$tot_columnas-1;$i++)
		$cadena.="<th>".traduce(ucfirst(mysql_field_name($consulta,$i)))."</th>";
	$cadena .= "</tr>";
	if ($totdatos == 0)
		$cadena .= "<tr><td colspan = '".$tot_columnas."' align='center'>No hay registros</td></tr>";
	else
		while($resultado = mysql_fetch_array($consulta))
			{
			$cadena .= "<tr>";
			for ($i=2;$i<=$tot_columnas-1;$i++)
				$cadena .= "<td>".$resultado[$i]."</td>";
			$cadena .= "</tr>";
			}
	$cadena .= "</table>";	
	return $cadena;
}

//***********************************************************************************************/
//Comprobacion y cambio de valor en los campos check
//***********************************************************************************************/
function comprueba_check($tabla,$campo,$valor)
{
	include("variables.php");
	$sql = "Select tipo from alias where tabla like '$tabla' and campoo like '$campo'";
	//echo $campo.":".$valor."<br/>";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	switch($resultado[0])
	{
	case "checkbox":{
					if ($valor == 'on')
						$valor = -1;
					else
						$valor = 0;}break;
	case "date":$valor = cambiaf($valor);break;
	default:$valor=$valor;
	}
	//echo $campo.":".$valor;
	return $valor;
}
//***********************************************************************************************/
//actualizacion de registros
function actualiza($vars)
{
	//todos los valores estan serializados en el formulario 2 nos importan el nombre_tabla y el numero_registro
	//el resto pueden entrar en el bucle
	include("variables.php");
	$sql = "Select * from `$vars[nombre_tabla]`";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$totcamp = mysql_numfields($consulta); //total de campos
	$sql = "Update `$vars[nombre_tabla]` set ";
	for($i=1;$i<=$totcamp-1;$i++) //empezamos desde 1 para saltarnos el id
		{
		$sql .= " `".mysql_field_name($consulta,$i) ."` = '".codifica(comprueba_check($vars[nombre_tabla],mysql_field_name($consulta,$i),$vars[mysql_field_name($consulta,$i)]))."',";
		}
	$longitud = strlen($sql);
	$sql = substr($sql,0,$longitud-1); //eliminamos la , final
	$sql .= " where id like $vars[numero_registro]";
        //foreach($vars as $key => $valor)
	//$valores .= $key ."=>".$valor.";";
	//REASIGNACION DE SERVICIOS
	foreach($vars as $key => $variable)
	$cadenita.=$key."=>".$variable."<br>";
	if(($vars[nombre_tabla]=="clientes")&&(!isset($vars[Estado_de_cliente])))
	{
			//$cadenita.="Dentro";
			//Chequeo de tabla para asignacion directa por codigo de negocio
			$sql2 = "Select * from z_sercont where idemp like $vars[numero_registro]";
			//$cadenita.=$sql2;
			$consulta = @mysql_db_query($dbname,$sql2,$con);
			if(@mysql_numrows($consulta)!=0)
			{
				while($resultado = @mysql_fetch_array($consulta))
				{
					//tomamos valor del codigo de negocio
					if($resultado[servicio]=="Codigo Negocio")
					$cod_despacho = intval($resultado[valor]);
				}
				if($cod_despacho == 23)
				$code = "JUNTAS";
				else
				$code = $cod_despacho;
				$sql3 = "Select id from clientes where nombre like 'LIBRE $code'";
				$consulta = @mysql_db_query($dbname,$sql3,$con);
				$resultado = @mysql_fetch_array($consulta);
				$sql4 = "Update z_sercont set idemp=$resultado[0] where idemp like $vars[numero_registro]";
				$consulta = @mysql_db_query($dbname,$sql4,$con);
				$sql5 = "Delete from z_sercont where idemp like $resultado[0] and servicio like 'Codigo_Negocio'";
				$consulta = @mysql_db_query($dbname,$sql5,$con);
			}
	}

	if(mysql_db_query($dbname,$sql,$con))
		return "<img src='".OK."' alt='Registro Actualizado' width='24'/> Registro Actualizado &nbsp;&nbsp;<p/>".$sql5;
	else
		return "<img src='".NOK."' alt='ERROR' width='24'/> ERROR&nbsp;&nbsp;<p/> ".$sql;
}
//***********************************************************************************************/

//formulario de registro nuevo, aqui boton de agregar	
function nuevo($vars)
{
	include("variables.php");
	//pasamos el codigo necesito el nombre de tabla
	$sql = "Select pagina from menus where id like $vars[tabla]";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	//consulta vacia para nombre de las cabezeras de la tabla
	$sql = "Select * from `$resultado[0]`";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$numero_campos = mysql_num_fields($consulta);
	
	//se queda aqui es lo necesario para los nombres de campo
	$cadena .= "<form id='formulario_alta' action='#' onsubmit='agrega_registro(); return false'><table cellpadding=0px cellspacing=1px class='formulario'><tr>";
	$cadena .= "<input type='hidden' id='opcion' value='0' />";
	$cadena .= "<th align='left' bgcolor='#ccc' colspan='3'>".traduce($resultado[Nombre])."<input type='hidden' name='nombre_tabla' id='nombre_tabla' value='".$resultado[0]."' />
	</th></tr>";
	//Fin de los submenus
	for($i=1;$i<=$numero_campos-1;$i++)
	{
		if($j%2==0)
		$cadena .= "</tr><tr>";
		$j++;
		$cadena .= "<th align='right' valign='top' bgcolor='#7d0063'><font color='#ffffff'>".traduce(nombre_campo(mysql_field_name($consulta,$i),$resultado[0])) ."</font></th><td align='left' valign='top'>".tipo_campo(mysql_field_name($consulta,$i),$resultado[0],'','nuevo',$i) ."</td>";
	}
	$cadena .= "</tr>";
	//parte de la botoneria
	$cadena .= "<tr><th colspan='4' align='center'><input class='boton' type='submit'  name='boton_envio' value='Agregar' />";
	$cadena .= "&nbsp;<input class='boton' type='reset'  value='Limpiar formulario' /></th></tr>";
	$cadena .= "</table></form>";
	return $cadena;
}
//***********************************************************************************************/
//OPCION 6 Agregamos el regis, probaremos a poner la actualizacion de subformulario aqui a ver
//***********************************************************************************************/
/* NOTA MENTAL:
 * Tenemos ahora unos clientes que empiezan por el nombre LIBRE y que hacen
 * referencia a los despachos libres, estos clientes tienen asignadas unas 
 * caracteristicas la cuales son agregadas al cliente cuando se le asigna
 * el codigo de negocio que representa ese despacho, por lo tanto tengo que
 * chequear si se esta agregando a z_sercont y si el valor que se agrega
 * es codigo de negocio, en tal caso se le agregaran todos los datos que
 * tiene ese despacho
 */
function agrega_registro($vars)
{
	 include("variables.php");
	 $sql = "Select * from `$vars[nombre_tabla]`";
	 $campos = mysql_db_query($dbname,$sql,$con);
	 $total = mysql_num_fields($campos);
	 if($vars[boton_envio] == "Agregar")
	 {
		$sql = "Insert into `$vars[nombre_tabla]` ("; 
	//todo junto
		for($i=1;$i<=$total -1;$i++)
		{
			$sql .= "`".mysql_field_name($campos,$i)."`,";
			$sql2 .= "'".codifica(comprueba_check($vars[nombre_tabla],mysql_field_name($campos,$i),$vars[mysql_field_name($campos,$i)]))."',";
		}
	//quitamos la , del final y pongo parentesis
		$longitud = strlen($sql);
		$longitud2 = strlen($sql2);
		$sql = substr($sql,0,$longitud-1) .") values (".substr($sql2,0,$longitud2-1) .")";
	//ahora los valores
	//CASO DE CODIGO NEGOCIO
		if(($vars[nombre_tabla]== 'z_sercont') && ($vars[servicio] == 'Codigo Negocio'))
		{
		//chequeamos los valores del LIBRE
			$code = intval($vars[valor]);
			if ($code == 23)
				$code = "JUNTAS";
			$sql2 = "Select id from clientes where Nombre like 'LIBRE $code'";
			$consulta = @mysql_db_query($dbname,$sql2,$con);
			$resultado = @mysql_fetch_array($consulta);
			$code_cli = $resultado[0];
			$sql2 = "Select * from z_sercont where idemp like $resultado[0]";
			$consulta = @mysql_db_query($dbname,$sql2,$con);
			if(@mysql_numrows($consulta)!=0)
			{
				$sql3 = "Update z_sercont set idemp=$vars[idemp] where idemp like $code_cli";
				$consulta = @mysql_db_query($dbname,$sql3,$con);
			}
		}
	$tipo = "Agregado";
	}
	else
	{
		//Caso de la baja reasignamos sus datos al despacho
		$sql = "Update `$vars[nombre_tabla]` set ";
		for($i=1;$i<=$total-1;$i++) //empezamos desde 1 para saltarnos el id
		$sql .= " `".mysql_field_name($campos,$i) ."` = '".codifica(comprueba_check($vars[nombre_tabla],mysql_field_name($campos,$i),$vars[mysql_field_name($campos,$i)]))."',";
		$longitud = strlen($sql);
		$sql = substr($sql,0,$longitud-1); //eliminamos la , final
		if(($vars[nombre_tabla] == 'facturacion')||($vars[nombre_tabla] == 'z_facturacion')||($vars[nombre_tabla] == 'cfm') ||($vars[nombre_tabla] == 'tllamadas')) 
		$sql .= " where idemp like $vars[id]";
		else
		$sql .= " where id like $vars[id]";
		
		$tipo = "Actualizado";
		}
	
	if($consulta = mysql_db_query($dbname,$sql,$con))
	return "<img src='".OK."' alt='Registro ".$tipo."' width='24'/> Registro ".$tipo."&nbsp;&nbsp;<p/>".$test;
	else
		return "<img src='".NOK."' alt='ERROR' width='24'/> ERROR&nbsp;&nbsp;<p/>";
}
//***********************************************************************************************/
function borra_registro($vars)
{
	include("variables.php");
	$sql = "Delete from `$vars[tabla]` where id like $vars[registro]";
	if($consulta = mysql_db_query($dbname,$sql,$con))
		return "<img src='".OK."' alt='Registro Borrado' width='24'/> Registro Borrado&nbsp;&nbsp;<p/>";
	else
		return "<img src='".NOK."' alt='ERROR' width='24'/> ERROR&nbsp;&nbsp;<p/>".$sql;
}

//***********************************************************************************************/
//OPCION:3 GENERADOR del subformulario
//***********************************************************************************************/
function subformulario($vars) //opcion,codigo,registro, codigo = codigo de submenu, registro = cliente 
{
	include("variables.php");
	$sql = "Select s.pagina, m.pagina, s.listado,s.nombre from submenus as s join menus as m on s.menu = m.id where s.id like $vars[codigo]";
	//echo $sql;
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$tabla = Array("tabla"=>$resultado[1],"registro"=>$vars[registro]);
//2 casos de subformularios, proveedores y clientes
	if(isset($vars[tabla]))
	{
		switch($vars[tabla])
		{
			case "pproveedores":$busca = "Select c.id from proveedores as c join `$vars[tabla]` as t on c.id = t.idemp where t.id like $vars[registro]";break;
			default:$busca = "Select c.id from clientes as c join `$vars[tabla]` as t on c.id = t.idemp where t.id like $vars[registro]";break;
		}
		$analiza = mysql_db_query($dbname,$busca,$con);
		$y_el_ganador_es = mysql_fetch_array($analiza);
		$registro = $y_el_ganador_es[0];
		$tabla[registro]=$registro;
	}
	else
		$registro =$vars[registro];
	switch($resultado[1])
	{
		default: $sql = "Select Nombre from clientes where id like $registro";
            $code = codigo_negocio($registro);
            break;
		case "proveedores":
            $sql = "Select Nombre from proveedores where id like $vars[registro]";
            $code = "";
            break;
	}
	//$cadena .= $sql; Para depurar
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado2 = mysql_fetch_array($consulta);
	$cadena .= "<form id='formulario_alta' action='#' onsubmit='agrega_registro(); return false'>
<table cellpadding='0px' cellspacing='1px' class='formulario' ><tr>";
	$cadena .= "<th bgcolor='#7d0063' align='left'></th><th bgcolor='#7d0063' colspan = '3' bgcolor='#ccc' align='left'>";
	$cadena .= "<font size='4'>".traduce(ucfirst($resultado[3])) ." de ". traduce(ucfirst($resultado2[0]))." ".$code."</font>";
	$cadena .= "<input type='hidden' id='id' name='id' value='".$vars[registro]."' />";
	$cadena .= "<input type='hidden' id='idemp' name='idemp' value='".$registro."' /><input type='hidden' name='nombre_tabla' id='nombre_tabla' value='".$resultado[0]."' /></th><th><input class='boton' onclick='cierra_el_formulario()' value='[X] Cerrar' ></th></tr>";
	$cadena .= submenus($tabla);
	$formulario = "Select * from `$resultado[0]` where id like $vars[registro]";
	$listado = "Select * from `$resultado[0]` where idemp like $registro";
	//Caso de telecos
	if($resultado[0]=='z_sercont')
	$listado .= " order by servicio";
	switch($resultado[0])
	{
		case("facturacion"):{ $cadena .= subform($listado,$resultado[0],$registro,$vars[marcado]);
			$cadena .= "<input type='button' class='boton' value='Parametros Factura' onclick='parametros_factura($registro)' /><div id='parametros_factura'></div>";$cadena .= servicios_fijos($registro);}break;
		case("z_facturacion"):$cadena .= subform($listado,$resultado[0],$registro,$vars[marcado]);break;
		case("cfm"):$cadena.= subform($listado,$resultado[0],$registro,$vars[marcado]);break;
		case("tllamadas"):$cadena.= subform($listado,$resultado[0],$registro,$vars[marcado]);break;
		//case("pproveedores"):subform($form,$resultado[0],$registro,$vars[marcado]) ."".sublist($listado,$resultado[0]);break;
		default: $cadena .= subform($formulario,$resultado[0],$registro,$vars[marcado]) ."".sublist($listado,$resultado[0]);break;
	}
	$cadena .= "</table></form>";
	return $cadena;
}
//***********************************************************************************************/
function subform($sql,$tabla,$registro,$marcado)
{
	//$cadena = $sql;
	//if(isset($marcado))
	//$cadena .= "marcado";
	include("variables.php");
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$numero_campos = mysql_numfields($consulta);
	$numero_resultados = mysql_numrows($consulta);
	//necesitamos un filtrado mejor, aqui puede suceder 2 cosas que sea nuevo
	//o bien que sea una actualizacion, si es nuevo aparece el boton de nuevo
	//si es una actualizacion entonces aparece el valor de actualizar.
	//Actualizar en: facturacion y z_facturacion cuando ya hay un registro de esa empresa agregado
	//Actualizar en todas las demas cuando se ha seleccionado un registro
	//Nuevo: cuando no hay registro en facturacion y z_facturacion y siempre que se entra en las demas
	//por lo tanto 1ª a filtrar la tabla
	//$sql2 = "Select c.id from clientes as c join `$tabla` as t on c.id like ";
	switch($tabla)
	{
		case "facturacion": {$cadena_opcion = "<input type='hidden' id='opcion' value='2' >";$tipo = chequea_estado_tabla($tabla,$registro);}break;
		case "z_facturacion":{$cadena_opcion = "<input type='hidden' id='opcion' value='8' >";$tipo = chequea_estado_tabla($tabla,$registro);}break;
		case "cfm":{$cadena_opcion = "<input type='hidden' id='opcion' value='9' >";$tipo = chequea_estado_tabla($tabla,$registro);}break;
		case "tllamadas":{$cadena_opcion = "<input type='hidden' id='opcion' value='10' >";$tipo = chequea_estado_tabla($tabla,$registro);}break;
		default:{$cadena_opcion = "";if(isset($marcado))$tipo = "Actualizar"; else $tipo = "nuevo";}break;
	}
	for($i=2;$i<=$numero_campos-1;$i++)
		{
			if($tipo == "nuevo")
			{
				$cadena .="<tr><th align='left' valign='top' bgcolor='#7d0063'><font color='#ffffff'>".traduce(nombre_campo(mysql_field_name($consulta,$i),$tabla)) ."</font></th>";
				$cadena .="<td align='left' valign='top' width='100%' bgcolor='#eeeeee'>".tipo_campo(mysql_field_name($consulta,$i),$tabla,"",$tipo,$i)."</td></tr>";
				$boton = "<input type='submit' class='boton' name='boton_envio' value='Agregar'>";	
			}
			else
			{
				$cadena .="<tr><th align='left' valign='top' bgcolor='#7d0063'><font color='#ffffff'>".traduce(nombre_campo(mysql_field_name($consulta,$i),$tabla)) ."</font></th>";
				$cadena .="<td align='left' valign='top' width='100%' bgcolor='#eeeeee'>".tipo_campo(mysql_field_name($consulta,$i),$tabla,$resultado[$i],$tipo,$i)."</td></tr>";
				$boton = "<input class='boton' type='submit' class='boton' name='boton_envio' value='Actualizar'>";
			}
		}
	$cadena .= $cadena_opcion;
	$cadena .= "<tr><th colspan='2'>".$boton."</th></tr>";
	//$cadena .= $tipo; muestra el tipo
	return $cadena;
}

//***********************************************************************************************/
//CHEQUEA EL ESTADO SI hay registro sale actualizar no lo hay registro sale agregar*************/
//***********************************************************************************************/
function chequea_estado_tabla($tabla,$registro)
{
	include("variables.php");
	$sql = "Select * from `$tabla` where idemp like $registro";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$total = mysql_numrows($consulta);
	if ($total == 0)
	$tipo = "nuevo";
	else
	$tipo = "Actualizar";
	return $tipo;
}

//***********************************************************************************************/
//SUBLISTADO DENTRO DEL SUBFORMULARIO************************************************************/
//***********************************************************************************************/
function sublist($sql,$tabla)
{
	include("variables.php");
	//$cadena = $sql;
	//opcion en la que estamos
	$esecuele = "Select id from submenus where pagina like '$tabla'";
	$laconsulta = mysql_db_query($dbname,$esecuele,$con);
	$elresultado = mysql_fetch_array($laconsulta);
	$cadena .= "<tr><td colspan='2'><input type='hidden' id='opcion' value='".$elresultado[0]."' />";
	//echo $sql;
	$consulta = mysql_db_query($dbname,$sql,$con);
	$totcampos = mysql_num_fields($consulta);
	$cadena .= "<table width='100%' class='sublistado' cellspacing='0'><tr><th align='center' bgcolor='#7d0063'></th><th align='center' bgcolor='#7d0063'></th>";
	for($i=2;$i<=$totcampos-1;$i++)
	$cadena .= "<th align='center' bgcolor='#7d0063'><font color='#ffffff'>".ucfirst(mysql_field_name($consulta,$i))."</font></th>";
	$cadena .="</tr>";
	while ($resultado = mysql_fetch_array($consulta))
	{
		$j++;
		if($j%2==0)
			{$color = "par";$botoncico1 = "boton_borrar_par";$botoncico2 = "boton_editar_par";}
		else
			{$color = "impar";$botoncico1 = "boton_borrar_impar";$botoncico2 = "boton_editar_impar";}
		
		$cadena .= "<tr><td align='center' class='".$color."'>
		<input type='hidden' id='nombre_tabla' value='".$tabla."' />
		<input type='hidden' id='codigo' value='".$elresultado[0]."' />
		<input type='button' class='".$botoncico2."' onclick='muestra_registro(".$resultado[0].")' /></td>
		<td align='center' class='".$color."'>
		<input type='button' class='".$botoncico1."' onclick='borrar_registro(".$resultado[0].")' /></td>";
		for($i=2;$i<=$totcampos-1;$i++)
		$cadena .= "<td align='center' class='".$color."'>".ucfirst(traduce(comprueba_check($tabla,mysql_field_name($consulta,$i),$resultado[$i])))."</td>";
		$cadena .= "</tr>";
	}
	$cadena .= "</table></td></tr>";
	return $cadena;
}
//***********************************************************************************************/
//SERVICIOS FIJOS EN FACTURACION
//***********************************************************************************************/
function servicios_fijos($cliente)
{
	include("variables.php");
	$sql = "Select Id,ID_Cliente,Servicio,Imp_Euro,unidades,iva,observaciones from `tarifa_cliente` where `ID_Cliente` like $cliente";
	$consulta = @mysql_db_query($dbname,$sql,$con);
	$totcampos = @mysql_num_fields($consulta);
	$span = $totcampos-2;
	$cadena .= "<tr><td colspan='2'><table width='100%' class='sublistado' cellspacing='0'>";
	$cadena .= "<tr><th colspan='".$span."' bgcolor='#ccc'>Servicios Fijos Mensuales</th>";
	$cadena .= "<th align='center' bgcolor='#ccc'>
	<input type='button' class='agregar' onclick='frm_srv_fijo(".$cliente.")' /></th></tr>";
	$cadena .= "<tr><td colspan='4'><div id='frm_srv_fijos'></div></td></tr>";
	$cadena .= "<tr><th bgcolor='#7d0063'></th><th bgcolor='#7d0063'></th>";
	for($i=2;$i<=$totcampos-2;$i++)
	$cadena .= "<th align='center' bgcolor='#7d0063'><font color='#ffffff'>".ucfirst(mysql_field_name($consulta,$i))."</font></th>";
	$cadena .= "</tr>";
	while ($resultado = @mysql_fetch_array($consulta))
	{
		$j++;
		if($j%2==0)
			{$color = "par";$botoncico1 = "boton_borrar_par";$botoncico2 = "boton_editar_par";}
		else
			{$color = "impar";$botoncico1 = "boton_borrar_impar";$botoncico2 = "boton_editar_impar";}
		
		$cadena .= "<tr>";
		//borrado y edicion
		$cadena .= "<td align='center' class='".$color."'>
		<input type='button'  class='".$botoncico2."' onclick='muestra_srv_fijo(".$resultado[0].")' /></td>
		<td align='center' class='".$color."'>
		<input type='button' class='".$botoncico1."' onclick='borra_srv_fijo(".$resultado[0].")' /></td>";
		$cadena .= "<td class='".$color."'>".$resultado[Servicio]." ".$resultado[observaciones]."</td>";
		$cadena .= "<td class='".$color."' align='center'>".$resultado[Imp_Euro]."</td>";
		$cadena .= "<td class='".$color."' align='center'>".$resultado[unidades]."</td>";
		$cadena .= "<td class='".$color."' align='center'>".$resultado[iva]."</td>";
		$cadena .= "</tr>";
	}
	$cadena .= "</table></td></tr>";
	return $cadena;
}
//***********************************************************************************************/
function frm_srv_fijos($vars)
{
	include("variables.php");
	//Listado de servicios disponibles
	///AGTUNG, ALERTA, ATENCION !!!!TOMO COMO SERVICIOS A SERVICIOS2
	
	$sql = "Select id,Nombre from `servicios2` where `Estado_de_servicio` like '-1' order by Nombre";
	$consulta = @mysql_db_query($dbname,$sql,$con);
	//formulario
	if(isset($vars[cliente])) //si el parametro es cliente es nuevo si es id es modificacion
	{
		$cadena .= "</form><form id='frm_srv_fijos' name='frm_srv_fijos' action='#' onsubmit='agrega_srv_fijos(); return false'>";
		$cadena .= "<table id='tabla_srv_fijos' cellpadding='2px' cellspacing='2px'>
		<tr>
		<th>Servicio:</th><td><input type='hidden' id='id_Cliente' name='id_Cliente' value='".$vars[cliente]."' />
		<select id='servicio' name='servicio' onchange='cambia_los_otros()'>
		<option value='0'>--Servicio--</option>";
		while($resultado = @mysql_fetch_array($consulta))
		{
			$cadena .= "<option value='".traduce($resultado[1])."'>".traduce($resultado[1])."</option>";
		}
		$cadena .= "</select></td>";
		$cadena .= "<th>Importe:</th><td><input type='text' name='importe' id='importe' size='8'/>&euro;</td>";
		$cadena .= "<th>Unidades:</th><td><input type='text' name='unidades' id='unidades' size='2' value='1' /></td>";
		$cadena .= "<th>Iva:</th><td><input type='text' name='iva' id='iva' size='2'/></td></tr>";
		$cadena .= "<tr><th valign='top'>Observaciones:</th><td><textarea name='observaciones' id='observaciones' cols='30'></textarea></td>";
		$cadena .= "<td colspan='4' align='center'><input class='agregar' type='submit' name='agregar' value='Agregar' /></td></tr></table>";
	}
	else //se pasa el id de tarifa_cliente para modificar
	{
		$sql2 = "Select * from tarifa_cliente where id like $vars[id]";
		$consulta2 = @mysql_db_query($dbname,$sql2,$con);
		$resultado2 = @mysql_fetch_array($consulta2);
		$cadena .= "</form><form id='frm_srv_fijos' name='frm_srv_fijos' action='#' onsubmit='actualiza_srv_fijos(); return false'>";
		$cadena .= "<table id='tabla_srv_fijos' cellpadding='2px' cellspacing='2px'>
		<tr>
		<th>Servicio:</th><td><input type='hidden' id='id' name='id' value='".$resultado2[0]."' />
		<input type='hidden' id='id_Cliente' name='id_Cliente' value='".$resultado2[1]."' />
		<select id='servicio' name='servicio' onchange='cambia_los_otros()'>
		<option value='0'>--Servicio--</option>";
		while($resultado = @mysql_fetch_array($consulta))
		{
			if($resultado[1] == $resultado2[Servicio])
				$cadena .= "<option selected value='".traduce($resultado[1])."'>".traduce($resultado[1])."</option>";
			else
				$cadena .= "<option value='".traduce($resultado[1])."'>".traduce($resultado[1])."</option>";
		}
		$cadena .= "</select></td>";
		$cadena .= "<th>Importe:</th><td><input type='text' name='importe' id='importe' size='8'value='".$resultado2[Imp_Euro]."'/>&euro;</td>";
		$cadena .= "<th>Unidades:</th><td><input type='text' name='unidades' id='unidades' size='2' value='1' /></td>";
		$cadena .= "<th>Iva:</th><td><input type='text' name='iva' id='iva' size='2'value='".$resultado2[iva]."'/></td></tr>";
		$cadena .= "<tr><th valign='top'>Observaciones:</th><td><textarea name='observaciones' id='observaciones' cols='30'>".$resultado2[observaciones]."</textarea></td>";
		$cadena .= "<td colspan='4' align='center'><input type='submit' class='boton_actualizar' name='actualizar' value='Actualizar' /></td></tr></table>";
	}
	return $cadena;
}
//***********************************************************************************************/
function cambia_los_otros($vars)
{
	include("variables.php");
	$servicio = codifica($vars[servicio]);
	$sql = "Select PrecioEuro, iva from servicios2 where Nombre like '$servicio'";
	$consulta = @mysql_db_query($dbname,$sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	$cadena = $resultado[0].":".$resultado[1];
	return $cadena;
}
//***********************************************************************************************/
function agrega_srv_fijo($vars)
{
	include("variables.php");
	//recogida de variables y agregamos
	$sql = "Insert into tarifa_cliente (`ID_Cliente`,`Servicio`,`Imp_Euro`,`iva`,`unidades`,`observaciones`) values ('$vars[id_Cliente]','$vars[servicio]','$vars[importe]','$vars[iva]','$vars[unidades]','$vars[observaciones]')";
	if($consulta = @mysql_db_query($dbname,$sql,$con))
		return "<img src='".OK."' alt='Servicio Agregado' width='64'/> Servicio Agregado&nbsp;&nbsp;<p/>";
	else
		return "<img src='".NOK."' alt='ERROR' width='64'/> ERROR&nbsp;&nbsp;<p/>".$sql;
	
}
//***********************************************************************************************/
function borra_srv_fijo($vars)
{
	include("variables.php");
	$sql = "Delete from tarifa_cliente where id like $vars[id]";
	if($consulta = @mysql_db_query($dbname,$sql,$con))
		return "<img src='".OK."' alt='Servicio Borrado' width='64'/> Servicio Borrado&nbsp;&nbsp;<p/>";
	else
		return "<img src='".NOK."' alt='ERROR' width='64'/> ERROR&nbsp;&nbsp;<p/>";
}
//***********************************************************************************************/
function actualiza_srv_fijo($vars)
{
	include("variables.php");
	$sql = "Update `tarifa_cliente` set `Servicio`='$vars[servicio]', `Imp_Euro`='$vars[importe]', `iva`='$vars[iva]', `unidades`='$vars[unidades]',`observaciones`='$vars[observaciones]' where id like $vars[id]";
	if($consulta = @mysql_db_query($dbname,$sql,$con))
		return "<img src='".OK."' alt='Servicio Actualizado' width='64'/> Servicio Actualizado&nbsp;&nbsp;<p/>";
	else
		return "<img src='".NOK."' alt='ERROR' width='64'/> ERROR&nbsp;&nbsp;<p/>".$sql;
}
//***********************************************************************************************/
//Genera el listado de categorias de clientes para el select
function listado_categorias()
{
	include("variables.php");
	$tabla = utf8_decode("categorías clientes");
	$sql = "SELECT * FROM `$tabla`";
	$consulta = @mysql_db_query($dbname,$sql,$con);
	$cadena.="<select id='tipo_cliente' onchange='filtra_listado()'>
	<option value='0'>--Selecciona Tipo--</option>";
	while($resultado=@mysql_fetch_array($consulta))
	{
		$cadena.="<option value='$resultado[0]'>".traduce($resultado[1])."</option>";
	}
	$cadena.="<option value='social'>Con direccion Facturaci&oacute;n</option>";
	$cadena.="<option value='comercial'>Con direccion Contrato</option>";
	$cadena.="<option value='independencia'>Con direccion Independencia</option>";
	$cadena.="<option value='conserje'>Listado Conserje</option>";
	$cadena.="</select>";
	return $cadena;
}
?>
