<?php
//Este codigo ha sido desarrollado en su totalidad por Ruben Lacasa Mas ruben@sbarrat.org 2006
/**
 * @deprecated - Este fichero no es necesario ya comentarlo entero comprobar
 * y borrar
 */
/*
if ($ssid == session_id())//comprobacion de seguridad
{
include("conexion.php");

function cambiaf($stamp) //funcion del cambio de fecha
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}
//No son funciones pero como si lo fueran
/*************************************************************************/
/*
if(isset($_POST['alta']))//alta en la base de datos
{
$tabla = $_POST['tabla'];
$id = $_POST['id'];
$alias = "Select * from alias where tabla like '$tabla' and mostrar like 'Si' and tipo != 'fijo' order by orden";
$consulta = mysql_db_query($dbname,$alias,$con);
$i=0;
while ($resultado = mysql_fetch_array($consulta))
{
	$nombrecampos[$i] = $resultado[2];//array con el nombre original de los campos en la base
	$valor = $resultado[8];//nombre del campo posteado
	if ($resultado[4] == 'checkbox')
		if($_POST[$valor]=='on')
		$valores[$i] = '-1';
		else
		$valores[$i] = 0;
	else
	//caso especial del date
	if($resultado[4] == 'date')
	{
		$valores[$i] = cambiaf($_POST[$valor]);
		if ($valores[$i] == '--')
		$valores[$i] = "0000-00-00";
	}
	else 
	$valores[$i] = $_POST[$valor];//volcado del campo posteado en el array de valores
	$i++;
}
$sql = "Insert into ".$tabla."  (";
for($b=1;$b<=$i-1;$b++)
{
	$sql .= "`".$nombrecampos[$b]."`";
	if($b !=$i-1)
	$sql .= ", ";
	else
	$sql .= ") values ("; 
}
for ($a=1;$a<=$i-1;$a++)
{
	$sql .= "'".$valores[$a]."' ";
	if($a !=$i-1 )
	$sql.= ",";
	else
	$sql .= ")";
}
//echo $sql; //para depuracion.

if($consulta = mysql_db_query($dbname,$sql,$con))
$_SESSION['accion'] = "<span id='avisok'>Servicio dado de alta</span>";
else
$_SESSION['accion'] = "<span id='avisnok'>No se ha dado de alta el servicio</span>";
}	
/*************************************************************************/
/*
if(isset($_POST['modificar']))//modificacion
{
$tabla = $_POST['tabla'];
$id = $_POST['id'];
$idemp = $_GET['emp'];//aqui se coge el valor del idemp
$registro = $_GET['reg'];//valor del registro del subformulario
$alias = "Select * from alias where tabla like '$tabla' and mostrar like 'Si' and tipo != 'fijo' order by orden";
$consulta = mysql_db_query($dbname,$alias,$con);
$i=0;
while ($resultado = mysql_fetch_array($consulta))
{
	$nombrecampos[$i] = $resultado[2];//array con el nombre original de los campos en la base
	$valor = $resultado[8];//nombre del campo posteado
	
	//caso especial del checkbox
	if ($resultado[4] == 'checkbox')
		if($_POST[$valor]=='on')
		$valores[$i] = '-1';
		else
		$valores[$i] = 0;
	else
	//caso especial del date
	if($resultado[4] == 'date')
		{
		$valores[$i] = cambiaf($_POST[$valor]);
		if ($valores[$i] == '--')
		$valores[$i] = "0000-00-00";
		}
	else 	
	$valores[$i] = $_POST[$valor];//volcado del campo posteado en el array de valores
	$i++;
}
$sql = "Update ".$tabla." set ";
for ($a=1;$a<=$i-1;$a++)
{
	
	$sql .= "`".$nombrecampos[$a] ."`='".$valores[$a]."' ";
	if($a !=$i-1 )
	$sql.= ",";
}
//si esta el valor sub se coge el idemp
if((isset($_GET['sub'])) && ($_GET['sub']!=1))
	{
	if(isset($_GET['reg']))
	$sql .= "where id like $registro";
	else
	$sql .= " where idemp like $idemp";
	}
else
$sql .= " where id like $valores[0]";
//echo $sql; //Solo para depuracion
if($consulta = mysql_db_query($dbname,$sql,$con))
$_SESSION['accion'] = "<span id='avisok'>Actualizacion Realizada</span>";
else
$_SESSION['accion'] = "<span id='avisnok'>No se han actualizado los datos</span>";
}
/*************************************************************************/
/*
if(isset($_POST['borrar']))//borrado, deshabilitado
{
$tabla = $_POST['tabla'];
$id = $_POST['id'];
//caso facturacion se coge idemp
if (($_GET['sub']==2) || ($_GET['sub']==8))//Solo para este caso, necesito un control adicional para facturacion
$sql = "Delete from ".$tabla." where idemp like ".$id."";
else
$sql = "Delete from ".$tabla." where id like ".$id."";
//echo $sql;//Solo para depuracion
if($consulta = mysql_db_query($dbname,$sql,$con))
$_SESSION['accion'] =  "<span id='avisok'>Registro Borrado</span>";
else
$_SESSION['accion'] = "<span id='avisnok'>No se ha borrado el registro</span>";
}
/*************************************************************************/
/*
if(isset($_POST['limpiar']))//limpiar formulario
{
	unset($_SESSION['busqueda']);
	unset($_SESSION['pagbus']);
}
if(isset($_POST['todos']))
{
	unset($_SESSION['busqueda']);
	unset($_SESSION['pagbus']);
}
/***********************************************************/
/*
function generatabla ($nombretabla,$categoria,$ssid,$pagina,$columnas)
{
include("conexion.php");
//$columnas=4;
$nomcamp ="Select * from alias where tabla like '$nombretabla' and mostrar like 'Si' order by orden";
$comcamp = mysql_db_query($dbname,$nomcamp,$con);
$totcamp = mysql_numrows($comcamp);
$i=0;
while ($rnomcamp = mysql_fetch_array($comcamp))
{
	$nombrec[$i]=$rnomcamp[3];//se vuelca el nombre de los campos a mostrar en nombrec
	$nombrea[$i]=$rnomcamp[2];//nombre antiguo que buscamos
	$tipocampo[$i]=$rnomcamp[4];//tipo de campo
	$nombretab[$i]=$rnomcamp[9];//nombre de la tabla dependiente
	$i++;
}
//consulta con limites, solo tenemos que seleccionar los 5 campos que queremos mostrar
$sservic = "Select "; //consulta base
//elegimos solo los que queremos mostar
for ($d=0;$d<=$i-1;$d++)
{
	$sservic .= " `".$nombrea[$d]."`";
	if($d != $i-1)
	$sservic .= ",";
	else
	$sservic .= " ";
}
$sservic .= " from $nombretabla "; //fin cadena base
//aqui podemos poner lo de busqueda
if(isset($_POST['buscar']))//opciones de busqueda
{
$cadena = $_POST['cadena'];
$sservic .= " where ";
	for ($e=1;$e<=$i-1;$e++)
	{
		if($e != 1)
		$sservic .= " or ";
		$sservic .= "`".$nombrea[$e]."` like '%$cadena%' ";
	}
$_SESSION['busqueda']=$sservic;
$_SESSION['pagbus']=$_GET['cat'];
}
if ($_SESSION['pagbus'] != $_GET['cat'])
{
	unset($_SESSION['busqueda']);
	unset($_SESSION['pagbus']);
}
else
$sservic = $_SESSION['busqueda'];

//fin parte cadena busqueda
$cservic = mysql_db_query($dbname,$sservic,$con);//volcado de totales
$totreg = mysql_numrows($cservic);
$numpag = ($totreg-($totreg % 10))/10;
$limite = ($pagina * 10) - 10;
$sservic .= " order by 2 limit $limite,10 ";

//consulta con limite
$cservic = mysql_db_query($dbname,$sservic,$con);//consulta con limite
//aqui tendria que poner el contador de resultados
$muestra = "<table id='".$nombretabla."' width='100%' cellspacing='0'>";//inicio de la tabla
//formulario de busqueda
$muestra .= "<tr><td colspan='".$columnas."' align='left'>";
$muestra .= "<form name='busqueda' id='busqueda' action='principal.php?id=".$ssid."&amp;cat=".$categoria."' method='POST'>";
$muestra .= "<img src='color/apps/xmag.png' width='16' alt='buscar'/>";
$muestra .= "<input type='text' name='cadena' size='30'/>";
$muestra .= "<input type='submit' class='boton' name='buscar' value='Buscar' />";
$muestra .= "<input type='submit' class='boton' name='todos' value='Ver Todos' />";
$muestra .= "</form></td></tr>";
$muestra .= "<tr>";//inicio de la linea de cabezera
for ($i=1;$i<=$columnas;$i++)//solo mostramos 5 o 6 campos en el listado
{
	$muestra .="<th valign='bottom' align='left'><span class='etiqueta'>".$nombrec[$i]."</span><br /></th>";
}
$muestra .="</tr>";//fin de la cabezera
while ($rservic = mysql_fetch_array($cservic))
{
	$linea++;
	if($rservic[0]==$_GET['emp'])
	{
	$colorlinea ='#B9C0FF';
	}
	else
	{
	if ($linea%2 == 0)
	$colorlinea='#DDE0FF';
	else
	$colorlinea = '#bbE0FF';
	}
	
	//$muestra .= "<tr bgcolor=".$colorlinea.">";
	/*Parte del desvio*/
/*
	$desvio = desvio($rservic[0]); //cambia el color de linea si esta marcado el desvio
	if($desvio == -1)
	$muestra .= "<tr bgcolor='#FCD393'>";
	else
	$muestra .= "<tr bgcolor=".$colorlinea.">";
	//si no hay desvio no sale nada
	for ($i=1;$i<=$columnas;$i++)//me sobra un campo
	{
		$valorcam = $rservic[$i];
	$muestra .= "<td><a href='principal.php?id=".$ssid."&amp;cat=".$categoria."&amp;emp=".$rservic[0]."'>".$valorcam."</a></td>";
	}
	$muestra .= "</tr>";
	$arrayids[$linea]=$rservic[0];
	
}
$muestra .="</table><table width='100%' id='cuentapag'><tr><td align='left' valign='middle'>";
//anterior y siguente
if(isset($_GET['emp']))
{
//recorremos la array 
//foreach ($arrayids as $numero)
//echo $numero;
for($f=0;$f<=$linea;$f++)
{
	if ($arrayids[$f] == $_GET['emp'])
	{
		$anterior = $arrayids[$f-1];
		$posterior = $arrayids[$f+1];
		if ($anterior =="")
		{
		$mensaje="Primer Registro";
		$anterior == $_GET['emp'];
		$cheqant = 1;
		}
		else
		$cheqant = 0;
		if ($posterior =="")
		{
		$mensaje="Ultimo Registro";
		$posterior == $_GET['emp'];
		$cheqpos = 1;
		}
		else 
		$cheqpos = 0;
	}
	else $mensaje = "";
}
if ($cheqant != 1)
$muestra .="<a href='principal.php?id=".$ssid."&amp;cat=".$categoria."&amp;emp=".$anterior ."'><img src='color/anterior.png' alt='anterior' /></a>";
if ($cheqpos != 1)
$muestra .="<a href='principal.php?id=".$ssid."&amp;cat=".$categoria."&amp;emp=".$posterior ."'><img src='color/siguente.png' alt='siguente' /></a>";
$muestra .=" ".$mensaje." </td>";
}
$muestra .="<td align='right'><b>Registros: del ".($limite+1)." al ".($limite+10)." de ".$totreg[0]." pagina: ";
for($c=1;$c<=$numpag+1;$c++)
{
	if ($c == $pagina)
	$marcada = "<font color='red'>";
	else
	$marcada = "<font color='blue'>";
	$muestra.= "<a href='principal.php?id=".$ssid."&amp;cat=".$categoria."&amp;pag=".$c."'><u>".$marcada."".$c."</font></u>&nbsp;</a>";
}
$muestra .="</b></td></tr>";
$muestra .= "</table>";
return $muestra;
}
/***********************************************************************************/
/*
function generaformulario($nombretabla,$categoria,$sel,$ssid)
{
	//Funcion para generar formularios
	//$nombretabla es la tabla
	//$categoria es la categoria del menu
	
	//$sel sera cuando seleccione alguno se pasara el id para mostrar sus datos
	//en el caso del subformulario vamos a hacer que sel sea la reg
	if (isset($_GET[reg]))
	$sel = $_GET['reg'];
	//listo
	include("conexion.php");
	//cargamos los alias de la tabla
	$alias = "Select * from alias where tabla like '$nombretabla' and mostrar like 'Si' order by orden";
	$comcamp = mysql_db_query($dbname,$alias,$con);
	//nombre de los campos, con pasar el numero muestra los datos de cualquiera
	$totcamp = mysql_numrows($comcamp);//numero total de campos
	$i=0;
	while ($rnomcamp = mysql_fetch_array($comcamp))
	{
	$nombrea[$i]=$rnomcamp[2];//nombre original en la tabla
	$nombrec[$i]=$rnomcamp[3];//nombres de los campos
	$nombret[$i]=$rnomcamp[4];//carga el tipo de campo
	$nombretam[$i]=$rnomcamp[5];//carga el tama�o de campo
	$nombretipo[$i]=$rnomcamp[7];//carga el tipo de enlace de campo
	$nombrevar[$i]=$rnomcamp[8];//carga el nombre de la variable para el post
	$nombredepende[$i]=$rnomcamp[9];
	$campodepende[$i]=$rnomcamp[10];
	$tablatex[$i] = $rnomcamp[11];//en el caso de los fijos carga la tabla texto
	$campotex[$i] = $rnomcamp[12];//carga el valor del campo texto
	$tablavar[$i] = $rnomcamp[13];//carga la tabla donde esta la variable
	$campovar[$i] = $rnomcamp[14];//carga el valor de la variable
	$i++;
	}
	//dise�o del formulario, el name sera el nombre del campo
	if ($sel != "")
	{
		$sql = "Select ";
		//elegimos solo los que queremos mostar
		for ($d=0;$d<=$i-1;$d++)
		{
			if($nombrea[$d]!='')
			{
				$sql .= " `".$nombrea[$d]."`";
				if($d != $i-1)
				$sql .= ",";
				else
				$sql .= " ";
			}
		}
		
		//parte de los subformularios
		/****SOLO para este caso vamos a poner que si la sub es 2 salga idemp*******/	
/*
		if(($_GET['sub']==2) || ($_GET['sub']==8))/* !!!!!!SOLO PARA ESTE CASO cambiarlo cuando se pueda*/
/*		
        $sql .= " from `$nombretabla` where idemp like $sel";
		else
		$sql .= " from `$nombretabla` where id like $sel";
		//echo $sql;//depuracion
		$consulta = mysql_query($sql,$con);
		$resultado = mysql_fetch_array($consulta);
	}
	
	else
	{
	for($b=0;$b<=$totcamp;$b++)
	$resultado[$b]="";
	}
	//aqui el impas del subgenerator de la muerte
	$botoncitos = "Select * from submenus where menu like '$categoria'";
	$cbotoncitos = mysql_db_query($dbname,$botoncitos,$con);
	$totsub = mysql_numrows($cbotoncitos);
	if ($totsub != 0) //hay submenus se generan los botoncitos
	{
		$muestra .="<tr><td colspan='4'>";
		$muestra .="<table><tr>";
		while($rbotoncitos = mysql_fetch_array($cbotoncitos))
		{
			if ($_GET['sub']==$rbotoncitos[0])
			$bgcolor="bgcolor = '#B9C0FF';";
			else
			$bgcolor = "";
			$muestra .="<td ".$bgcolor."><span class='boton'><a href='principal.php?id=".$ssid."&amp;cat=".$categoria."&amp;emp=".$_GET[emp]."&amp;sub=".$rbotoncitos[0]."'><u>".$rbotoncitos[2]."".$c."</font></u>&nbsp;</a><span class='boton'></td>";	
		}
		//boton de los servicios contratados, !Solo en clientes!
		if($categoria == 1) //solo para los clientes
		{
		$muestra.="<td ".$bgcolor."><input class='boton' type=submit onclick=popUp('servicont/index.php?id=".$ssid."&amp;emp=".$_GET[emp]."') value = 'Estadisticas Servicios' /></td>";
		$muestra.="<SCRIPT LANGUAGE=\"JavaScript\">
		function popUp(URL) {
		day = new Date();
		id = day.getTime();
		eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=900,height=700');\");
		}
		</SCRIPT>";
		$muestra.="<td ".$bgcolor."><input class='boton' type=submit onclick=popUp('rapido/index.php') value = 'Asignacion de Servicios' /></td>";
		$muestra.="<SCRIPT LANGUAGE=\"JavaScript\">
		function popUp(URL) {
		day = new Date();
		id = day.getTime();
		eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=900,height=600');\");
		}
		</SCRIPT>";
		$muestra.="<td ".$bgcolor."><input class='boton' type=submit onclick=popUp('almacen/index.php') value = 'Almacenaje' /></td>";
		$muestra.="<SCRIPT LANGUAGE=\"JavaScript\">
		function popUp(URL) {
		day = new Date();
		id = day.getTime();
		eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=900,height=600');\");
		}
		</SCRIPT>";
		$muestra.="<td ".$bgcolor."><input class='boton' type=submit onclick=popUp('contratos/index.php') value = 'Contratos' /></td>";
		$muestra.="<SCRIPT LANGUAGE=\"JavaScript\">
		function popUp(URL) {
		day = new Date();
		id = day.getTime();
		eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=900,height=600');\");
		}
		</SCRIPT>";
		}
		if(isset($_SESSION['accion']))
		{
		$muestra .="<td>".$_SESSION['accion']."</td>";
		unset($_SESSION['accion']);
		}
		$muestra .="</tr></table></td></tr>";
	}
	if(isset($_GET['sub']))
		$completa .= "&amp;sub=".$_GET['sub'];
	else $completa .= "";
	if(isset($_GET['reg']))
		$completa .= "&amp;reg=".$_GET['reg'];
	else 
		$completa .= "";
	$muestra .= "<center><form name='formgen' method='post' action=principal.php?id=".$ssid."&cat=".$categoria."&emp=".$_GET['emp']."".$completa .">";//formulario generico nombre campo nombre valor
	$muestra .="<table>";
	
//dise�o 3 columnas cada 3 salto
$numcol = 2;
$cuentacol = 0;	
	for ($a=0;$a<=$totcamp-1;$a++)
	{
		//segun el tipo de campo lo dise�amos de una manera u otra
		//variable $nombret, $nombretam, $nombretipo
		switch($nombret[$a])//damos formato al campo de introduccion de datos
		{
			case "hidden": { if ((isset($_GET['sub'])) && ($_GET['sub']!= 1))
								if(isset($_GET['reg']))
									{
										if($nombrevar[$a]=='id')
										$idemp = $_GET['reg'];
										else
										$idemp = $_GET['emp'];
									}
									
								else
									$idemp = $_GET['emp'];
							else
							$idemp = $resultado[$a];
				$formato = "<input type='hidden' size = '".$nombretam[$a]."' name='".$nombrevar[$a]."' value='".$idemp."' />";
			}break;
			case "text": $formato = "<input type='text' size = '".$nombretam[$a]."' name='".$nombrevar[$a]."' value='".$resultado[$a]."' />";break;
			case "textarea": $formato = "<textarea rows = '".$nombretam[$a]."' cols ='50' name='".$nombrevar[$a]."'>".$resultado[$a]."</textarea>";break;
			case "checkbox": 
							{//solo para este caso -1 checkeado
							if ($resultado[$a]!= 0)
							$chequeado = 'checked';
							else
							$chequeado = ''; 
							$formato = "<input type='checkbox' name='".$nombrevar[$a]."' ".$chequeado." />";
							}break;
			case "select": 
							{//hay que hacer una consulta a la tabla dependiente de los valores
							$tabla = $nombredepende[$a];
							$seleccion = "Select * from `$tabla` order by 2";
							$cseleccion = mysql_db_query($dbname,$seleccion,$con);
							$formato ="<select name='".$nombrevar[$a]."'>";
							$formato .="<option value='0'>-::".$nombrec[$a].":-</option>";
							while ($rseleccion = mysql_fetch_array($cseleccion))
								{
								if ($rseleccion[1]== $resultado[$a])
									$marcado = 'selected';
								else 
									$marcado = "";
								$formato .= "<option ".$marcado." value='".$rseleccion[1]."'>".$rseleccion[1]."</option>";
								}
							$formato .= "</select>";
							}break;
			case "date": {
				
				
				//$formato ="<form action='#' method='get'>";
				$formato = "<input type='text' id='f_date_b' size = '".$nombretam[$a]."' name='".$nombrevar[$a]."' value='".cambiaf($resultado[$a])."' />";
				$formato .= "<button type='reset' id='f_trigger_b'>...</button>";
				//$formato .="</form>";
				$formato .="<script type='text/javascript'>
    			Calendar.setup({
        		inputField     :    'f_date_b',      // id of the input field
        		ifFormat       :    '%d-%m-%Y',       // format of the input field
        		showsTime      :    true,            // will display a time selector
        		button         :    'f_trigger_b',   // trigger for the calendar (button ID)
        		singleClick    :    false,           // double-click mode
        		step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    			});
				</script>";			
			}
				break;
			
			case "join": {//campo que en la tabla es un int y que coge su valor de otra tabla su valor es el id de la tabla que depende
						$tabla = $nombredepende[$a];
						$join = "Select * from `$tabla` where id like $resultado[$a]";
						$cjoin = mysql_db_query($dbname,$join,$con);
						$rjoin = mysql_fetch_array($cjoin);
						$formato = "<input type='text' size = '".$nombretam[$a]."' name='".$nombrevar[$a]."' value='".$rjoin[1]."' />";}
						break;
			case "fijo":{//este campo viene de una tabla en la cual esta representado por un int y tenemos que sacar su nombre
						$consulta = "SELECT t.$campotex[$a] FROM `$tablatex[$a]` AS t JOIN $tablavar[$a] AS c ON t.$campotex[$a] = c.$campovar[$a]
WHERE c.id LIKE $_GET[emp] ";
//$consulta = "SELECT t.* FROM `$tablatex[$a]` AS t JOIN $tablavar[$a] AS c ON t.`Nombre` = c.$campovar[$a]
//WHERE c.id LIKE $_GET[emp] ";
//echo $consulta;
						$cjoin = mysql_db_query($dbname,$consulta,$con);
						$rjoin = mysql_fetch_array($cjoin);
						$formato = "<span id='fijo'>".$rjoin[0]."</span>";}break;
		}
		
		
		switch($nombretipo[$a])//generamos el enlace de conexion o bien a web o envio de correo
		{
			case "web":$formato .="<a href='http://".$resultado[$a]."' target='_blank'><img src='color/apps/browser.png' width='12' alt='Abrir Web'/></a>";break;
			case "mail":$formato .="<a href='mailto:".$resultado[$a]."'><img src='color/apps/email.png' width='12' alt='Enviar Correo'/></a>";break;
		}
		//parte del dise�o
		if($cuentacol == $numcol)
		{
		$muestra.="</tr>";
		$cuentacol = 0;
		}
		if($cuentacol == 0)
		$muestra .="<tr>";
		$cuentacol++;
		//Caso de los hidden
		if ($nombret[$a]=="hidden") //los hidden no tiene que contar en la muestra de formulario
		//$muestra .="<th align='right' valign='top'></th><td align='left' valign='top'>".$formato."</td>";
		{
			$muestra .= $formato;
			$cuentacol--;
		}
		else
		$muestra .="<th align='right' valign='top'><label>".$nombrec[$a]."</label></th><td align='left' valign='top'>".$formato."</td>";
	}
	$numcol = $numcol * 2;
	$muestra .= "<tr><th colspan='$numcol'>";
	$muestra .= "<input type='hidden' name='tabla' value='".$nombretabla."' />";
	//para el paso de valores pasamos como un hidden el nombre de la tabla
	//***NUEVO CONTROL-> otra vez para la tabla de facturacion para no insertar duplicados
	if ($_GET['sub']==2)//categoria de facturacion una tabla unico registro
	{
	//buscamos si existe el registro
	$existe = "Select * from `$nombretabla` where idemp like $idemp";
	$cexiste = mysql_db_query($dbname,$existe,$con);
	$rexiste = mysql_numrows($cexiste);
		if ($rexiste == 0)
			$muestra .= "<input class='boton' type='submit' name='alta' value='Alta Registro' />";
		else 
			$muestra .= "<input class='boton' type='submit' name='modificar' value='Modificar Registro' />";
	}
	else
	{
	$muestra .= "<input class='boton' type='submit' name='alta' value='Alta Registro' />";
	$muestra .= "<input class='boton' type='submit' name='modificar' value='Modificar Registro' />";
	}
	$muestra .= "<input class='boton' type='submit' name='borrar' value='Borrar Registro' />";
	$muestra .= "<input class='boton' type='submit' name='limpiar' value='Limpiar formulario' />";
	$muestra .= "</table></form></center>";
	//Parte del script del calendario
	return $muestra;
}
function generasubtabla($nombretabla,$categoria,$ssid,$columnas,$depende,$deel)//nuevas funciones las dependientes ie subtabla
{//funcionamiento de subtabla muestra todas sin busqueda las direcciones, 
include("conexion.php");
//$columnas=4;
$nomcamp ="Select * from alias where tabla like '$nombretabla' and mostrar like 'Si' order by orden";
$comcamp = mysql_db_query($dbname,$nomcamp,$con);
$totcamp = mysql_numrows($comcamp);
$i=0;
while ($rnomcamp = mysql_fetch_array($comcamp))
{
	if($rnomcamp[3]!=$deel)
	{
	$nombrec[$i]=$rnomcamp[3];//se vuelca el nombre de los campos a mostrar en nombrec
	$nombrea[$i]=$rnomcamp[2];//nombre antiguo que buscamos
	$tipocampo[$i]=$rnomcamp[4];//tipo de campo
	$nombretab[$i]=$rnomcamp[9];//nombre de la tabla dependiente
	$i++;
	}
}
//consulta con limites, solo tenemos que seleccionar los 5 campos que queremos mostrar
$sservic = "Select "; //consulta base
//elegimos solo los que queremos mostar
for ($d=0;$d<=$i-1;$d++)
{
	$sservic .= " `".$nombrea[$d]."`";
	if($d != $i-1)
	$sservic .= ",";
	else
	$sservic .= " ";
}
$sservic .= " from $nombretabla where `$deel` like $depende"; //fin cadena base subbusqueda
//echo $sservic; //solo para depuracion
//fin parte cadena busqueda

$cservic = mysql_db_query($dbname,$sservic,$con);//volcado de totales
$cservic = mysql_db_query($dbname,$sservic,$con);//consulta con limite
//aqui tendria que poner el contador de resultados
$muestra = "<table id='".$nombretabla."' width='100%' cellspacing='0'>";//inicio de la tabla
$muestra .= "<tr>";//inicio de la linea de cabezera
for ($i=1;$i<=$columnas;$i++)//solo mostramos 5 o 6 campos en el listado
{
	$muestra .="<th valign='bottom' align='left'>".$nombrec[$i]."<br /></th>";
}
$muestra .="</tr>";//fin de la cabezera
while ($rservic = mysql_fetch_array($cservic))
{
	$linea++;
	if($rservic[0]==$_GET['reg'])
	{
	$colorlinea ='#B9C0FF';
	}
	else
	{
	if ($linea%2 == 0)
	$colorlinea='#DDE0FF';
	else
	$colorlinea = '#bbE0FF';
	}
	$muestra .= "<tr bgcolor=".$colorlinea.">";
	for ($i=1;$i<=$columnas;$i++)//me sobra un campo
	{
		if($tipocampo[$i]=="select")//tipo de campo
		{
		$busco = "Select * from `$nombretab[$i]` where id like '$rservic[$i]'";
		//echo $busco;
		$cbusco = mysql_db_query($dbname,$busco,$con);
		$rbusco = mysql_fetch_array($cbusco);
		$valorcam = $rbusco[1];
		}
		else
		$valorcam = $rservic[$i];
	$muestra .= "<td><a href='principal.php?id=".$ssid."&amp;cat=".$categoria."&amp;emp=".$_GET[emp]."&amp;sub=".$_GET[sub]."&amp;reg=".$rservic[0]."'>".$valorcam."</a></td>";
	}
	$muestra .= "</tr>";
	$arrayids[$linea]=$rservic[0];
	
}
$muestra .="</table>";
	
return $muestra;	
}
function desvio($empresa) //funcion que dice si hay desvio o no
{
	include("conexion.php");
	$sql = "Select Desvio from clientes where id like $empresa";
	$consulta = mysql_query($sql,$con);
	$resultado = mysql_fetch_array($consulta);
	return $resultado[0];
}

}

*/