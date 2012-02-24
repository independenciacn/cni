<?php
/**
 * Datos_gestion File Doc Comment
 *
 * Fichero que controla las funciones del apartado de gestion de la aplicacion
 *
 * PHP Version 5.2.6
 *
 * @category Valida
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
require_once 'variables.php';
checkSession();
if (isset($_POST)) {
    sanitize($_POST);
}
if (isset($_POST['opcion']))
{
	switch($_POST['opcion'])
	{
		case 0:$respuesta = listado_copias();break;
		case 1:$respuesta = haz_backup();break;
		case 2:$respuesta = restaura($_POST['archivo']);break;
		case 3:$respuesta = borra_backup($_POST['archivo']);break;
		case 4:$respuesta = revisa_tablas();break;
		case 5:$respuesta = repara_tablas();break;
		case 6:$respuesta = optimiza_tablas();break;
		case 7:$respuesta = listado_categorias($_POST);break;
		case 8:$respuesta = detalles_categoria($_POST);break;
		case 9:$respuesta = actualiza_categoria($_POST);break;
		case 10:$respuesta = listado_telefonos();break;
		case 11:$respuesta = formulario_telefonos();break;
		case 12:$respuesta = frm_agrega_telefono($_POST);break;
		case 13:$respuesta = rarita();break;
		case 14:$respuesta = listado_personalizado($_POST);break;
		case 15:$respuesta = borra_telefono_asignado($_POST);break;
		case 16:$respuesta = edita_telefono_asignado($_POST);break;
		case 17:$respuesta = actualiza_telefono_asignado($_POST);break;
		case 18:$respuesta = frmNuevaPass();break;
		case 19:$respuesta = actNuevaPass($_POST);break;
	}
	echo $respuesta;
}
/**
 * Realiza la copia de seguridad
 * 
 * @return string
 */
function haz_backup()
{
	$stamp = date("dmyHis");
	//$ruta = "/Applications/MAMP/Library/bin/";//para mac
	$ruta = 'C:\AppServ\MySQL\bin\\';//para windows
	exec($ruta.'mysqldump.exe --opt --user=cni --password=inc centro > ../copias/copia'.$stamp.'.sql');//windows
	//exec($ruta.'mysqldump --opt --user=cni --password=inc centro > ../copias/copia'.$stamp.'.sql');
	$nombre_copia = "<div class='success'>Copia Realizada</div>";
	return $nombre_copia;
}
/**
 * Restaura la copia de seguridad
 * 
 * @param string $archivo
 * @return string
 */
function restaura($archivo)
{
	//$ruta = "/Applications/MAMP/Library/bin/"; //para mac
	//$rutadir = "/Applications/MAMP/htdocs/cni/nueva/copias/".$archivo; //para mac
	$ruta = 'C:\AppServ\MySQL\bin\\';//para windows
	$rutadir = 'C:\AppServ\www\cni\copias\\'.$archivo; //para windows
	exec($ruta."mysql.exe --user=cni --password=inc centro < ".$rutadir);
	//exec($ruta."mysql --user=cni --password=inc centro < ".$rutadir);

	$nombre_copia = "<div class='success'>Copia Restaurada</div>";
	//return $ruta.
	return $nombre_copia;
}
/**
 * Lista las copias de seguridad
 * 
 * @return string
 */
function listado_copias()
{
	//$ruta = '/Applications/MAMP/htdocs/cni/nueva/copias'; //para mac
	$i = 0;
    $ruta = 'C:\AppServ\www\cni\copias\\'; //para windows
	$cadena =  "<table class='tabla'><tr><th colspan='3'>
	Listado de Copias Realizadas</th></tr>";
	if (true == ($gestor = opendir($ruta))) {
   		while (false !== ($archivo = readdir($gestor))) {
       		if ($archivo != "." && $archivo != ".." && $archivo != ".DS_Store") {
           		$i++;
		   		$clase = clase($i);
				//vamos a tratar el nombre para que salga de otra manera
		   		//el formato de fichero es copiaddmmaahhmmss.sql
		   		$nombre = substr($archivo,5,2)."/".substr($archivo,7,2)."/".
		   		substr($archivo,9,2)."-".substr($archivo,11,2).":".
		   		substr($archivo,13,2).":".substr($archivo,15,2);
		   		$cadena .="<tr><td class='".$clase."'>".$i."</td>
		   		<td class='".$clase."'>".$nombre."</td><td>
		   		<span class='boton' onclick=restaurar_backup('".$archivo."')>
		   		&nbsp;&nbsp;[R]Restaurar&nbsp;&nbsp;</span>
		   		<span class='boton' onclick=borrar_backup('".$archivo."')>
		   		&nbsp;&nbsp;[B]Borrar&nbsp;&nbsp;</span>";
       		}
   		}
   		closedir($gestor);
   		$cadena .= "</table>";
	}
    return $cadena;
}
/**
 * Borra la copia de seguridad
 * @param unknown_type $archivo
 */
function borra_backup( $archivo )
{
	//$ruta = '/Applications/MAMP/htdocs/cni/nueva/copias/'.$archivo; //Para mac
	$ruta = 'C:\AppServ\www\cni\copias\\'.$archivo; //para windows

	//$comando = "rm"; //para mac y linux
	$comando = "del"; //para windows
	exec($comando." ".$ruta);
	//return $comando." ".$ruta;
	return "<span class='avisok'>Copia Borrada</span>";
}
/**
 * Checkea el estado de las tablas
 * 
 * @return string
 */
function revisa_tablas()
{
	global $con;
	$sql = "show tables";
	$consulta = mysql_query($sql,$con);
	while(true == ($resultado = mysql_fetch_array($consulta)))
	{
		$sql2 = "check table `".$resultado[0]."`";
		$consulta2 = mysql_query($sql2,$con);
		$resultado2 = mysql_fetch_array($consulta2);
		$cadena .= "<br>Estado ".$resultado[0]." ->".$resultado2[3]; 
	}
	return $cadena;
}
/**
 * Reparacion de todas las tablas
 * 
 * @return string $cadena
 */
function repara_tablas()
{
	global $con;
	$sql = "show tables";
	$consulta = mysql_query($sql,$con);
	while(true == ($resultado = mysql_fetch_array($consulta))) {
		$sql2 = "repair table `".$resultado[0]."`";
		$consulta2 = mysql_query($sql2,$con);
		$resultado2 = mysql_fetch_array($consulta2);
		$cadena .= "<br>Estado ".$resultado[0]." ->".$resultado2[3]; 
	}
	return $cadena;
}
/**
 * Optimiza la tabla y devuelve el estado
 * 
 * @return string $cadena
 */
function optimiza_tablas()
{
	global $con;
	$sql = "show tables";
	$consulta = mysql_query($sql,$con);
	while(true == ($resultado = mysql_fetch_array($consulta))) {
		$sql2 = "optimize table `".$resultado[0]."`";
		$consulta2 = mysql_query($sql2,$con);
		$resultado2 = mysql_fetch_array($consulta2);
		$cadena .= "<br>Estado ".$resultado[0]." ->".$resultado2[3]; 
	}
	return $cadena;
}
/**
 * Devuelve el listado de las categorias de Servicios o de Clientes
 * 
 * @param array $vars
 * @return string $cadena
 */
function listado_categorias( $vars )
{
	global $con;;
	$tabla1="categoría servicios";
	$tabla2="categorías clientes";
	switch($vars['categoria'])
	{
		case 1: $listado=$tabla1;$sql = "SELECT * FROM `".$tabla1."` ";break;
		case 2: $listado=$tabla2;$sql = "SELECT * FROM `".$tabla2."` ";break;
	}
	$consulta = mysql_query($sql,$con);
	$cadena = "<input type='hidden' id='categoria' 
	value='".$vars['categoria']."' />";
	$cadena .= "<table class='tabla'>";
	$cadena .= "<tr><th colspan='3'>Listado de ".ucfirst($listado)."</th></tr>";
	$i=0;
	while(true == ($resultado = mysql_fetch_array($consulta)))
	{
		$i++;
		$clase = clase($i);
		$cadena .= "<tr class='".$clase."'>";
		$cadena .= "<td>".$resultado[1]."</td><td>".$resultado[2]."</td>";
		$cadena .= "<td><span class='boton' 
		onclick='editar_categoria(".$resultado[0].")'>Editar</span></td></tr>";
	}
	$cadena .= "</table><div id='detalles_categoria'></div>";
	return $cadena;
}
/**
 * Formulario de modificacion de Categorias
 * 
 * @todo Agregar la funcion de borrar y añadir
 * @param array $vars
 * @return string $cadena
 */
function detalles_categoria($vars)
{
	global $con;
	$tabla1="categoría servicios";
	$tabla2="categorías clientes";
	switch($vars['categoria'])
	{
		case 1: $sql = "SELECT * FROM `".$tabla1."` where Id 
		        like ".$vars['registro'];
		break;
		case 2: $sql = "SELECT * FROM `".$tabla2."` where Id 
		        like ".$vars['registro'];
		break; 
	}
	$consulta = mysql_query($sql,$con);
	$resultado = mysql_fetch_array($consulta);	
	$cadena = <<<EON
	<form class='frmGestion' id='formulario_categorias' 
	onsubmit='actualiza_categoria(); return false'>
	<fieldset>
	<legend>Modificacion Categorias</legend>
	<input type='hidden' id='categoria' 
	name='categoria' value='{$vars['categoria']}' />
	<input type='hidden' id='registro' 
	name='registro' value='{$vars['registro']}' />
	<label for='Nombre'>Categoria:</label> 
	<input type='text' name='Nombre' value='{$resultado[1]}' size='40'/><br/>
	<label for='descripcion'>Descripcion:</label>
	<textarea name='descripcion' cols='38'>{$resultado[2]}</textarea><br/>
	<input id='Actualizar' type='submit' name='Actualizar' 
	value='Actualizar' />
	<div id='resultadoActCategoria' class='reset'></div>
	</fieldset>
	</form>
EON;
	return $cadena;
	
}
/**
 * Actualiza los datos de la categoria nombre, y descripcion
 * 
 * @todo Agregar las funciones de borrado y insercion de categoria
 * @param array $vars
 * @return string
 */
function actualiza_categoria($vars)
{
	global $con;
	switch($vars['categoria'])
	{
		case 1: $tabla = "categoría servicios";break;
		case 2: $tabla = "categorías clientes";break;
	}
	if(isset($vars['Actualizar'])){
		$sql = "Update `".$tabla."` 
	    set `Nombre` = '".$vars['Nombre']."',  
	    `Descripción` = '".$vars['descripcion']."' 
	    WHERE id like ".$vars['registro'];
	}
	if(true == ($consulta = mysql_query($sql,$con))) {
		$mensaje = "<div class='success'>Categoría modificada</div>";
	}else {
		$mensaje = "<div class='error'>No se ha modificado la categoria</div>";
	}
	return $mensaje;
}
/**
 * Listado de los telefonos del centro
 * 
 * @return string $cadena
 */
function listado_telefonos()
{
	global $con;
	$sql = "Select c.Nombre, z.valor from clientes as c 
	join z_sercont as z on c.id like z.idemp 
	where servicio like 'Telefono' order by c.Nombre";
	$consulta = mysql_query($sql,$con);
	$cadena ="<input class='boton' value='[X] Cerrar' 
	onclick='cierra_listado_copias()' ><table><tr>";
	$columnas='4';
	for($i=1;$i<=$columnas;$i++) {
	    $cadena .="<th class='impar'>Cliente</th><th class='par'>Telefono</th>";
	}
	$cadena .="</tr><tr>";
	$i=0;
	while(true == ($resultado = mysql_fetch_array($consulta)))
	{
		if($i%$columnas == 0)
		$cadena .= "</tr><tr>";
		$cadena .= "<td class='impar'>".$resultado[0]."</td>
		<td class='par'>".$resultado[1]."</td>";
		$i++;
	}
	$cadena .= "</tr></table>";
	$cadena .= listado_ip();
	return $cadena ;
}
/**
 * Listado de ips del centro
 * 
 * @return string $cadena
 */
function listado_ip()
{
	global $con;
	$sql = "Select c.Nombre, z.valor from clientes as c join z_sercont 
	as z on c.id like z.idemp 
	where servicio like 'Direccion IP' order by c.Nombre";
	$consulta = mysql_query($sql,$con);
	$cadena ="<table><tr>";
	$columnas='4';
	for($i=1;$i<=$columnas;$i++) {
	    $cadena .="<th class='impar'>Cliente</th><th class='par'>Direccion IP</th>";
	}
	$cadena .="</tr><tr>";
	$i=0;
	while(true == ($resultado = mysql_fetch_array($consulta))) {
		if($i%$columnas == 0) {
		    $cadena .= "</tr><tr>";
		}
		$cadena .= "<td class='impar'>".$resultado[0]."</td>
		<td class='par'>".$resultado[1]."</td>";
		$i++;
	}
	$cadena .= "</tr></table>";
	return $cadena;
}
/**
 * Muestra el formulario para agregar los numeros de telefono del centro
 * Mostrara los que estan asignados, pudiendo desasignarlos y dejara agregar 
 * nuevo telefonos,
 * Modificar los existentes, y borrarlos o sea CRUD
 * 
 * @return string
 */
function formulario_telefonos()
{
	$cadena = "<form class='formulario' id='frm_agrega_telefono' 
	name='frm_agrega_telefono' onsubmit='agrega_telefono();return false' 
	method='post'>";
	$cadena .= "Telefono:<input type='text' name='numero_telefono' size='12'/>";
	$cadena .= "<input type='submit' class='boton' name='agregar' 
	value='[+]Agregar Telefono' />";
	$cadena .= "</form><div id='mensajes_estado'></div>";
	$cadena .= listado_telefonos_centro();
	return $cadena;	
}
/**
 * Devuelve el listado de telefonos del centro
 * Tres parametros, telefono, asignado, y a quien
 * Telipex sera la base de datos de telefonos donde estaran todos, se compara
 * con la de z_sercont para ver quien lo tiene
 * 
 * @return string $cadena
 */
function listado_telefonos_centro()
{
	global $con;
	$asignados_despacho_telefono=array();
	$asignados_despacho_adsl=array();
	$asignados_despacho_fax=array();
	$asignados_telefono=array();
	$asignados_adsl=array();
	$asignados_fax=array();
	$no_asignados=array();
	$sql = "select DISTINCT direccion from telipext where tipo like 'telefono'";
	$consulta = mysql_query($sql,$con);
	$asignados=array();
	$no_asignados=array();
	while(true == ($resultado = mysql_fetch_array($consulta)))
	{
		//Aqui comparo las de la base con las que tengo asignadas
		//el telefono en telipext esta siempre 976 12 34 56
		//en la base puede estar asi o no
		//quito los espacios en blanco
		$teljunto="";
		$telefono = explode(" ",$resultado['direccion']);
		foreach($telefono as $tele)
			$teljunto .= $tele;
		$sql2 = "Select * from z_sercont 
		where valor like '".$resultado['direccion']."%' 
		or valor like '".$teljunto."%' ";
		
		$consulta2 = mysql_query($sql2,$con);
		if(mysql_numrows($consulta2)>=1)
		{
			while(true == ($resultado2 = mysql_fetch_array($consulta2)))
			{
				$tipo = categoria_del_cliente($resultado2['idemp']);
				//echo "<p/>".$resultado[direccion]."-".$tipo."-".$resultado2[servicio];
				if($tipo == "OK")
				{
					switch($resultado2['servicio'])
					{
						case "Telefono":
						    $asignados_despacho_telefono[] 
						    = $resultado['direccion']."-".$resultado2['idemp'];
						    break;
						case "Adsl":
						    $asignados_despacho_adsl[] 
						    = $resultado['direccion']."-".$resultado2['idemp'];
						    break;
						case "Fax":
						    $asignados_despacho_fax[] 
						    = $resultado['direccion']."-".$resultado2['idemp'];
						    break;
					}
				}
				else
				{
					switch($resultado2['servicio'])
					{
						case "Telefono":
						    $asignados_telefono[] 
						    = $resultado['direccion']."-".$resultado2['idemp'];
						    break;
						case "Adsl":
						    $asignados_adsl[] 
						    = $resultado['direccion']."-".$resultado2['idemp'];
						    break;
						case "Fax":
						    $asignados_fax[] 
						    = $resultado['direccion']."-".$resultado2['idemp'];
						    break;
					}
				}
			}
		}
		else {
			$no_asignados[] = $resultado['direccion'];
		}
	}
	sort($asignados_despacho_telefono);
	sort($asignados_despacho_adsl);
	sort($asignados_despacho_fax);
	sort($asignados_telefono);
	sort($asignados_adsl);
	sort($asignados_fax);
	sort($no_asignados);
	//Nuevo diseño
	//$cadena.="<div id='tabla_asignacion_telefonos'>";
	//Telefonos de Despachos
	$cadena="<input class='boton' value='[X] Cerrar' 
	onclick='cierra_listado_copias()' ><br>
	<div class='tabla'><div class='listado_1'>Telefonos Despachos</div>";
	$i=0;
	foreach($asignados_despacho_telefono as $despacho_telefono) {
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		$clase = clase($i);
		$cadena .="<div class='".$clase."'>
		".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".
		nombre_cliente($asignacion[1])."</div>";
	}
	$cadena .="</div>";
	//Fin telefonos despachos
	//Telefonos Domiciliados
	$cadena.="<div class='tabla'>
	<div class='listado_2'>Telefonos Domiciliados</div>";
	$i=0;
	foreach($asignados_telefono as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		$clase = clase($i);
		$cadena .="<div class='".$clase."'>
		".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".
		nombre_cliente($asignacion[1])."</div>";
	}
	$cadena .="</div>";
	//Fin telefonos Domiciliados
	//Telefonos del centro
	$cadena.="<div class='tabla'>
	<div class='listado_3'>Telefonos del Centro</div>";
	$i=0;
	foreach($no_asignados as $no_asignado)
	{
		//listado de telefonos del centro
                /*
                 * TODO: Se tienen que poder agregar y modificar desde el programa
                 */
		$centro = array("976 30 11 82","976 79 43 60","976 79 43 61","976 79 43 62");
		$i++;
		$clase = clase($i);
		if(!in_array($no_asignado,$centro)) {
			$libres[]=$no_asignado;
		}else{
			$cadena .="<div class='".$clase."'>".
			$no_asignado."&nbsp;&nbsp;&nbsp;&nbsp;".
			descripcion_telefono($no_asignado)."</div>";
	    }
	}
	$cadena .="</div>";
	//Fin telefonos Domiciliados
	//telefonos Libres
	$cadena .= "<div class='tabla'><div class='listado_4'>Telefonos Libres</div>";
	$i=0;
	//if($opcion==0)
    //{
        if(is_array($libres)) {
            foreach($libres as $libre) {
                $i++;
                $clase = clase($i);
                $cadena .="<div class='".$clase."'>
                <img src='iconos/edittrash.png' alt='Borrar telefono' 
                onclick='javascript:borrar_telefono_asignado(\"".$libre."\")'>
                &nbsp;<img src='iconos/kate.png' alt='Editar telefono' 
                onclick='javascript:editar_telefono_asignado(\"".$libre."\")'>
                &nbsp;".$libre."&nbsp;&nbsp;&nbsp;&nbsp;
                <span id='edicion_".$libre."'>".
                descripcion_telefono($libre)."</span></div>";
            }
        }
    //}
	$cadena .="</div>";
	//Fin telefonos libres
	//Faxes de Despachos
	$cadena.="<div class='tabla'><div class='listado_1'>Faxes Despachos</div>";
	$i=0;
	foreach($asignados_despacho_fax as $despacho_telefono) {
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		$clase = clase($i);
		$cadena .="<div class='".$clase."'>".$asignacion[0]."
		&nbsp;&nbsp;&nbsp;&nbsp;". nombre_cliente($asignacion[1]) ."</div>";
	}
	$cadena .="</div>";
	//Fin telefonos despachos
	//Faxes Domiciliados
	$cadena.="<div class='tabla'><div class='listado_2'>Faxes Domiciliados</div>";
	$i=0;
	foreach($asignados_fax as $despacho_telefono) {
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		$clase = clase($i);
		$cadena .="<div class='".$clase."'>".$asignacion[0]."
		&nbsp;&nbsp;&nbsp;&nbsp;". nombre_cliente($asignacion[1]) ."</div>";
	}
	$cadena .="</div>";
	//Adsl de Despachos
	$cadena.="<div class='tabla'><div class='listado_1'>Adsl Despachos</div>";
	$i=0;
	foreach($asignados_despacho_adsl as $despacho_telefono) {
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		$clase = clase($i);
		$cadena .="<div class='".$clase."'>".$asignacion[0]."
		&nbsp;&nbsp;&nbsp;&nbsp;".nombre_cliente($asignacion[1])."</div>";
	}
	$cadena .="</div>";
	//Fin adsl despachos
	//Adsl Domiciliados
	$cadena.="<div class='tabla'><div class='listado_2'>Adsl Domiciliados</div>";
	$i=0;
	foreach($asignados_adsl as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		$clase = clase($i);
		$cadena .="<div class='".$clase."'>".$asignacion[0]."
		&nbsp;&nbsp;&nbsp;&nbsp;".nombre_cliente($asignacion[1])."</div>";
	}
	$cadena .="</div>";
	$cadena .="<div class='tabla'><div class='listado_2'>Listado de IP's</div>";
	$cadena .= consulta_de_ips();
	$cadena .="</div></div></div><p/><p/>";
	return $cadena;
}
/**
 * Devuelve el nombre del cliente
 * 
 * @param integer $id
 * @return Ambigous <>
 */
function nombre_cliente($id)
{
	global $con;
	$sql = "Select Nombre from clientes where id like ".$id;
	$consulta = mysql_query($sql,$con);
	$resultado = mysql_fetch_array($consulta);
	return $resultado[0];
}
/**
 * Devuelve ok si el cliente es de despachos si no ko
 * 
 * @param integer $id
 * @return string $valor
 */
function categoria_del_cliente($id)
{
	global $con;
	$sql = "Select Categoria from clientes where id like ".$id;
	$consulta = mysql_query($sql,$con);
	$resultado = mysql_fetch_array($consulta);
	switch($resultado[0])
	{
		case "Clientes despachos": 
		    $valor='OK';
		break;
		default: 
		    $valor = 'KO';
		break;
	}
	return $valor;
}
/**
 * Devuelve la descripcion del telefono
 * 
 * @param string $telefono
 * @return Ambigous <>
 */
function descripcion_telefono($telefono)
{
	global $con;
	$sql = "Select descripcion from telipext 
	where direccion like '".$telefono."'";
	$consulta = mysql_query($sql,$con);
	$resultado = mysql_fetch_array($consulta);
	return $resultado[0];
}
/**
 * Agrega el telefono
 * 
 * @param array $vars
 * @return string $texto
 */
function frm_agrega_telefono($vars)
{
	global $con;
	$sql="Insert into telipext (tipo,direccion,asignada) 
	values ('telefono','".$vars['numero_telefono']."','No')";
	if(mysql_query($sql,$con)) {
		$texto = "<div class='success'>Telefono Agregado</div>";
	} else {
		$texto = "<div class='error'>No se ha agregado el telefono</div>";
	}
	return $texto;
		
}
/**
 * Listado especial
 * 
 * @todo tarda mucho en generarse
 * @return string $cadena
 */
function rarita()
{
	global $con;
	$k=0;
	$sql = "SELECT DISTINCT(z.valor), c.Nombre, c.Categoria, f.observaciones 
	FROM `facturacion` 
	as f join clientes as c on c.id like f.idemp join z_sercont as z 
	on z.idemp like 
	c.id WHERE  Estado_de_cliente != 0 and 
	(c.Categoria like '%domiciliac%' or c.Categoria like '%despacho%' 
	or c.Categoria like 'Otros' or c.Categoria like '%Telefonica%' 
	or c.Categoria like '%oficina movil%') and
	z.servicio like 'Codigo Negocio' order by z.valor asc";
	$consulta = mysql_query($sql,$con);
	$cadena = "<input class='boton' value='[X] Cerrar' 
	onclick='cierra_listado_copias()' >
	<input type='button' class='boton' 
	onclick=window.open('inc/excel.php') value='Imprimir' />";
	$cadena .= "<table width='100%' class='tabla'>";
	$cadena .= "<tr><th>Codigo</th><th>Cliente</th>
	<th>Categoria</th><th>Observaciones</th></tr>";
	while (true == ($resultado = mysql_fetch_array($consulta))) {
	    $color = (preg_match("#despacho#i", $resultado[2])) ? "#69C" : "#F90";
		$cadena .= "<tr><td bgcolor='".$color."'>
		<font color='#fff' size='2'><b>".$resultado[0]."</b></font></td>
		<td class='".clase($k)."'>".$resultado[1]."</td>
		<td class='".clase($k)."'>".$resultado[2]."</td>
		<td class='".clase($k)."'>".$resultado[3]."</td></tr>";
		$k++;
	}
	$cadena .= "</table>";
	return $cadena;
}
/**
 * Funcion que genera el listado de ips asignadas y libres
 * 
 * @return string
 */
function consulta_de_ips()
{
	global $con;
	$sql = "Select c.Nombre,z.valor from z_sercont as z join clientes as c 
	on z.idemp like c.id where z.servicio like 'Direccion IP' order by z.valor";
	$consulta = mysql_query($sql,$con);
	while( true== ($resultado=mysql_fetch_array($consulta))) {
		$ipes=explode(".",$resultado['valor']);
		$ocupadas[intval($ipes[3])]=$resultado[0];
	}
	$j = 0;
	$k = 0;
	$cogidas = "";
	$no_cogidas = "";
	for($i=1;$i<=254;$i++) {
		$clase = clase($i);
		if( isset($ocupadas[$i]) && $ocupadas[$i]!="" ) {
			$j++;
			$clase = clase($j);
			$cogidas .= "<div class='".$clase."'>172.26.0.".$i
			."&nbsp;&nbsp;&nbsp;&nbsp;".$ocupadas[$i]."</div>";
		} else {	
			$k++;
			$clase = clase($k);
			$no_cogidas .="<div class='".$clase."'>172.26.0.".$i
			."&nbsp;&nbsp;&nbsp;&nbsp;</div>";
		}
	}
	return $cogidas."-".$no_cogidas;
}
/**
 * Genera un el listado personalizado
 * 
 * @param array $vars
 * @return string $cadena
 */
function listado_personalizado( $vars )
{
	global $con;
	$cadena ="";
	//buscamos el nombre de la categoria_del_cliente
	$tabla = "categorías clientes";
	//$sql = "SELECT * FROM `$tabla`";
	
	if ($vars['tipo']=='social') {
		$sql ="Select * from clientes where direccion not like '' 
		and Estado_de_cliente like '-1' order by Nombre";
	} else {
		if($vars['tipo']=='comercial') {
			$sql="Select * from clientes where dcomercial != '' 
			and Estado_de_cliente like '-1' order by Nombre";
		} else {
			if($vars['tipo']=='conserje') {
				$sql = "Select * from clientes where 
			    (categoria like '%domicili%' or categoria 
			    like '%despachos%' or categoria like '%tencion telefo%') 
			    and Estado_de_cliente like '-1' order by Nombre";
			} else {
				if($vars['tipo']=='independencia') {
					$sql ="Select * from clientes where direccion 
					like '%Independencia, 8 dpdo%' 
					and Estado_de_cliente like '-1' order by Nombre";
				} else {	
					$sql = "Select * from clientes as c join `".$tabla."` 
					as d on c.Categoria = d.Nombre 
					where d.id like ".$vars['tipo']." 
					and c.Estado_de_cliente like '-1' order by c.Nombre";
		        }
			}
		}
	}
	$consulta = mysql_query($sql,$con);
	$i=0;
	while(true == ($resultado = mysql_fetch_array($consulta))) {
		$clase='listado_'.clase($i);
		$i++;
		$cadena.="<div class='".$clase."'>".$i." ".$resultado[1]."
		<span class='direccion_esp'> ".$resultado['Direccion']."</span>
		</div>";
		//$i++;
	}
	$cadena .= "<div><input type='button' class='boton' 
	onclick=window.open('inc/excel.php?tipo=".$vars['tipo']."') 
	value='Imprimir' /></div>";
	return $cadena;
}
/**
 * Borra el telefono que esta libre
 * 
 * @params array $vars
 * @return string
 */
function borra_telefono_asignado( $vars )
{
	global $con;
	$sql="Delete from telipext where direccion like '".$vars['telefono']."'";
	if(mysql_query($sql,$con)) {
		return "<div class='success'>Telefono Borrado</div>";
	} else {
		return "<div class='error'>No se ha borrado el telefono</div>";
    }
}
/**
 * Edita la descripcion del telefono libre
 * 
 * @param array $vars
 * @return string $cadena
 */
 function edita_telefono_asignado( $vars )
 {
 	global $con;
	$sql = "SELECT * FROM `telipext` 
	WHERE direccion LIKE '".$vars['telefono']."'";
 	$consulta = mysql_query($sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$cadena="<input type='text' id='descripcion_".$vars['telefono']."' 
	value='".$resultado['descripcion']."'>
	<input type='hidden' id='identificador_".$vars['telefono']."' 
	value='".$resultado['id']."'>
	<input type='button' 
	onclick='actualiza_descripcion_telefono(\"".$vars['telefono']."\")' 
	value='Actualizar'>";
 	return $cadena;
 }
 /**
  * Actualiza la descripcion del telefono libre
  * 
  * @params array $vars
  * @return boolean
  */
 function actualiza_telefono_asignado( $vars )
 {
 	global $con;
	$sql = "Update `telipext` set descripcion = '".$vars['descripcion']."' 
	where id like ".$vars['id'];
	if (mysql_query($sql,$con) ) {
        return true;
	} else {
        return false;
	}
 }
/**
 * Formulario para establecer una nueva contraseña
 * 
 * @return string $html
 */
function frmNuevaPass() 
{
	$html = <<<EON
	<form class='frmGestion' name='nuevaPass' id='nuevaPass' 
	method='post' action=''>
	<fieldset>
	<legend>Modificacion Contraseña Acceso</legend>
	<label for='vieja'>Contraseña Anterior:</label>
	<input id='vieja' name='vieja' type='password' 
	title='Escriba la contraseña Actual' /><br/>
	<label for='nueva'>Contraseña Nueva:</label>
	<input id='nueva' name='nueva' type='password' 
	title='Escriba la Nueva Contraseña' /><br/>
	<input type='button' onclick='estableceNuevaPass()' 
	value='Establecer Nueva Contraseña' 
	title='Haga clic para establecer la Nueva Contraseña' />
	<div id='resultadoNuevaPass' class='reset'></div>
	</legend>
	</form>
EON;
	return $html;	
}
/**
 * Actualiza la constraseña de acceso a la aplicacion
 * 
 * @param array $vars
 * @return string $mensaje
 */
function actNuevaPass( $vars )
{
	global $con;
	$mensaje = "";
	$sql = "Select 1 
	from usuarios where nick like 'usuario' 
	and contra like sha1('". $vars['vieja']."')";
	$consulta = mysql_query($sql, $con);
	if ( mysql_numrows($consulta) == 0) {
		$mensaje = "<div class='error'>Contraseña Incorrecta</div>";
	} else {
		$sql = "Update usuarios 
		set contra = sha1('". $vars['nueva'] ."') 
		where nick like 'usuario'";
	 	if (mysql_query( $sql, $con ) ) {
	 		$mensaje = "<div class='success'>Contraseña modificada</div>";
	 	} else {
	 		$mensaje = "<div class='error'>No se ha modificado la contraseña</div>";
	 	}
	}
	return $mensaje;
}