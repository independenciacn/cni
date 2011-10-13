<?php

if (isset($_POST[opcion]))
{
	switch($_POST[opcion])
	{
		case 0:$respuesta = listado_copias();break;
		case 1:$respuesta = haz_backup();break;
		case 2:$respuesta = restaura($_POST[archivo]);break;
		case 3:$respuesta = borra_backup($_POST[archivo]);break;
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
	}
	echo $respuesta;
}
/*************REALIZACION DE COPIAS************************************/
function haz_backup()
{
	$stamp = date("dmyHis");
	//$ruta = "/Applications/MAMP/Library/bin/";//para mac
	$ruta = "C:\AppServ\MySQL\bin\\";//para windows
	exec($ruta.'mysqldump.exe --opt --user=cni --password=inc centro > ../copias/copia'.$stamp.'.sql');//windows
	//exec($ruta.'mysqldump --opt --user=cni --password=inc centro > ../copias/copia'.$stamp.'.sql');
	$nombre_copia = "<span class='avisok'>Copia Realizada</span>";
	return $nombre_copia;
}
/*************RESTAURACION DE COPIAS************************************/
function restaura($archivo)
{
	//$ruta = "/Applications/MAMP/Library/bin/"; //para mac
	//$rutadir = "/Applications/MAMP/htdocs/cni/nueva/copias/".$archivo; //para mac
	$ruta = "C:\AppServ\MySQL\bin\\";//para windows
	$rutadir = "C:\AppServ\www\cni\copias\\".$archivo; //para windows
	exec($ruta."mysql.exe --user=cni --password=inc centro < ".$rutadir);
	//exec($ruta."mysql --user=cni --password=inc centro < ".$rutadir);

	$nombre_copia = "<span class='avisok'>Copia Restaurada</span>";
	//return $ruta.
	return $nombre_copia;
}
/*************LISTADO DE COPIAS************************************/
function listado_copias()
{
	//$ruta = '/Applications/MAMP/htdocs/cni/nueva/copias'; //para mac
	$ruta = "C:\AppServ\www\cni\copias\\"; //para windows
	$cadena =  "<table class='tabla'><tr><th colspan='3'>Listado de Copias Realizadas</th></tr>";
	if ($gestor = opendir($ruta)) 
	{
   		while (false !== ($archivo = readdir($gestor))) 
   		{
       		if ($archivo != "." && $archivo != ".." && $archivo != ".DS_Store") 
	   		{
           		$i++;
		   		if($i%2==0)
					$clase = "par";
				else
					$clase = "impar";
				//vamos a tratar el nombre para que salga de otra manera
		   		//el formato de fichero es copiaddmmaahhmmss.sql
		   		$nombre = substr($archivo,5,2)."/".substr($archivo,7,2)."/".substr($archivo,9,2)."-".substr($archivo,11,2).":".substr($archivo,13,2).":".substr($archivo,15,2);
		   		$cadena .="<tr><td class='".$clase."'>".$i."</td><td class='".$clase."'>".$nombre."</td><td>
		   		<span class='boton' onclick=restaurar_backup('".$archivo."')>&nbsp;&nbsp;[R]Restaurar&nbsp;&nbsp;</span>
		   		<span class='boton' onclick=borrar_backup('".$archivo."')>&nbsp;&nbsp;[B]Borrar&nbsp;&nbsp;</span>";
       		}
   		}
   		closedir($gestor);
   		$cadena .= "</table>";
	}
return $cadena;
}
/*************BORRADO DE COPIAS************************************/
function borra_backup($archivo)
{
	//$ruta = '/Applications/MAMP/htdocs/cni/nueva/copias/'.$archivo; //Para mac
	$ruta = "C:\AppServ\www\cni\copias\\".$archivo; //para windows

	//$comando = "rm"; //para mac y linux
	$comando = "del"; //para windows
	exec($comando." ".$ruta);
	//return $comando." ".$ruta;
	return "<span class='avisok'>Copia Borrada</span>";
}
/*************REVISION DE TODAS LAS TABLAS************************************/
function revisa_tablas()
{
	include("variables.php");
	$sql = "show tables";
	$consulta = mysql_db_query($dbname,$sql,$con);
	while($resultado = mysql_fetch_array($consulta))
	{
		$sql2 = "check table `$resultado[0]`";
		$consulta2 = mysql_db_query($dbname,$sql2,$con);
		$resultado2 = mysql_fetch_array($consulta2);
		$cadena .= "<br>Estado ".$resultado[0]." ->".$resultado2[3]; 
	}
	return $cadena;
}
/*************REVISION DE TODAS LAS TABLAS************************************/
function repara_tablas()
{
	include("variables.php");
	$sql = "show tables";
	$consulta = mysql_db_query($dbname,$sql,$con);
	while($resultado = mysql_fetch_array($consulta))
	{
		$sql2 = "repair table `$resultado[0]`";
		$consulta2 = mysql_db_query($dbname,$sql2,$con);
		$resultado2 = mysql_fetch_array($consulta2);
		$cadena .= "<br>Estado ".$resultado[0]." ->".$resultado2[3]; 
	}
	return $cadena;
}
/*************REVISION DE TODAS LAS TABLAS************************************/
function optimiza_tablas()
{
	include("variables.php");
	$sql = "show tables";
	$consulta = mysql_db_query($dbname,$sql,$con);
	while($resultado = mysql_fetch_array($consulta))
	{
		$sql2 = "optimize table `$resultado[0]`";
		$consulta2 = mysql_db_query($dbname,$sql2,$con);
		$resultado2 = mysql_fetch_array($consulta2);
		$cadena .= "<br>Estado ".$resultado[0]." ->".$resultado2[3]; 
	}
	return $cadena;
}
/******************LISTADO CATEGORIAS*******************************************/
function listado_categorias($vars)
{
	include("variables.php");
	$tabla1=utf8_decode("categoría servicios");
	$tabla2=utf8_decode("categorías clientes");
	switch($vars[categoria])
	{
		case 1: $listado=$tabla1;$sql = "SELECT * FROM `".$tabla1."` ";break;
		case 2: $listado=$tabla2;$sql = "SELECT * FROM `".$tabla2."` ";break;
	}
	$consulta = mysql_db_query($dbname,$sql,$con);
	$cadena .= "<input type='hidden' id='categoria' value='".$vars[categoria]."' />";
	
	$cadena .= "<table class='tabla'>";
	$cadena .= "<tr><th colspan='3'>Listado de ".utf8_encode(ucfirst($listado))."</th></tr>";
	$i=0;
	while($resultado = mysql_fetch_array($consulta))
	{
		$i++;
		if($i%2==0)
		$clase='par';
		else
		$clase = 'impar';
		$cadena .= "<tr class='".$clase."'>";
		$cadena .= "<td>".traduce($resultado[1])."</td><td>".traduce($resultado[2])."</td>";
		$cadena .= "<td><span class='boton' onclick='editar_categoria(".$resultado[0].")'>Editar</span></td></tr>";
	}
	$cadena .= "</table><div id='detalles_categoria'></div>";
	return $cadena;
}
/*****************************************************************************/
function detalles_categoria($vars)
{
	include("variables.php");
	$tabla1=utf8_decode("categoría servicios");
	$tabla2=utf8_decode("categorías clientes");
	switch($vars[categoria])
	{
		case 1: $sql = "SELECT * FROM `".$tabla1."` where Id like $vars[registro]";break;
		case 2: $sql = "SELECT * FROM `".$tabla2."` where Id like $vars[registro]";break; 
	}
        
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$cadena .= "<form id='formulario_categorias' onsubmit='actualiza_categoria(); return false'>";
	$cadena .= "<input type='hidden' id='categoria' name='categoria' value='".$vars[categoria]."' />";
	$cadena .= "<input type='hidden' id='registro' name='registro' value='".$vars[registro]."' />";
	$cadena .= "Categoria: <input type='text' name='Nombre' value='".traduce($resultado[1])."' size='40'/>";
	$cadena .= "<br/>Descripcion: <textarea name='descripcion' cols='35'>".traduce($resultado[2])."</textarea>";
	$cadena .= "<br/><input type='submit' name='Actualizar' value='Actualizar' class='boton' />";
/*
 * FIXME: NO DEJA AGREGAR CATEGORIAS
 */
//$cadena .= "<input type='submit' name='boton_envio' value='Borrar' class='boton' />";
	//$cadena .= "<input type='reset' value='Limpiar' class='boton'/>";
	//$cadena .= "<input type='submit' name='boton_envio' value='Agregar' class='boton' />";
	$cadena .= "</form>";
	return $cadena;
	
}
/*****************************************************************************/
function actualiza_categoria($vars)
{
	include("variables.php");
	/*$tabla1=utf8_decode("categoría servicios");
	$tabla2=utf8_decode("categorías clientes");*/
	switch($vars[categoria])
	{
		case 1: $tabla = utf8_decode("categoría servicios");break;
		case 2: $tabla = utf8_decode("categorías clientes");break;
	}
	if(isset($vars[Actualizar]))//actualizacion
		$sql = "Update `$tabla` set `Nombre` = '$vars[Nombre]',  `Descripci�n` = '$vars[Descripcion]' where id like $vars[registro]";
	//else
		//if(isset($vars[Borrar]))//borrado
			//$sql = "Delete from `$tabla` where id like $vars[registro]";
		//else //actualizacion
			//$sql = "Insert into `$tabla` (`Nombre`,`Descripci�n`) values ('$vars[Nombre]','$vars[Descripcion]')";
	if($consulta = mysql_db_query($dbname,$sql,$con))
		$cadena = "todo ok";
	else
		$cadena = $sql.",".$vars[Actualizar].",".$vars[Borrar];
		foreach($vars as $key => $valores)
		$cadena .= $key ."=>".$valores ."<p/>";
	return $cadena;
}
/****************************LISTADO DE TELEFONOS DEL CENTRO************************************/
function listado_telefonos()
{
	include("variables.php");
	$sql = "Select c.Nombre, z.valor from clientes as c join z_sercont as z on c.id like z.idemp where servicio like 'Telefono' order by c.Nombre";
	
	$consulta = mysql_db_query($dbname,$sql,$con);
	$cadena ="<input class='boton' value='[X] Cerrar' onclick='cierra_listado_copias()' ><table><tr>";
	$columnas='4';
	for($i=1;$i<=$columnas;$i++)
	$cadena .="<th class='impar'>Cliente</th><th class='par'>Telefono</th>";
	$cadena .="</tr><tr>";
	$i=0;
	while($resultado = mysql_fetch_array($consulta))
	{
		if($i%$columnas == 0)
		$cadena .= "</tr><tr>";
		$cadena .= "<td class='impar'>".traduce($resultado[0])."</td><td class='par'>".$resultado[1]."</td>";
		$i++;
	}
	$cadena .= "</tr></table>";
	$cadena .= listado_ip();
	return $cadena ;
}
/***************Listado de ips del centro****************************/
function listado_ip()
{
	include("variables.php");
	$sql = "Select c.Nombre, z.valor from clientes as c join z_sercont as z on c.id like z.idemp where servicio like 'Direccion IP' order by c.Nombre";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$cadena ="<table><tr>";
	$columnas='4';
	for($i=1;$i<=$columnas;$i++)
	$cadena .="<th class='impar'>Cliente</th><th class='par'>Direccion IP</th>";
	$cadena .="</tr><tr>";
	$i=0;
	while($resultado = mysql_fetch_array($consulta))
	{
		if($i%$columnas == 0)
		$cadena .= "</tr><tr>";
		$cadena .= "<td class='impar'>".traduce($resultado[0])."</td><td class='par'>".$resultado[1]."</td>";
		$i++;
	}
	$cadena .= "</tr></table>";
	return $cadena;
}
//***********************************************************************************************/
//formulario(telefono):Muestra el formulario para agregar los numeros de telefono del centro
//Mostrara los que estan asignados, pudiendo desasignarlos y dejara agregar nuevo telefonos,
//Modificar los existentes, y borrarlos o sea CRUD
//***********************************************************************************************/
function formulario_telefonos()
{
	
	$cadena = "<form class='formulario' id='frm_agrega_telefono' name='frm_agrega_telefono' onsubmit='agrega_telefono();return false' method='post'>";
	$cadena .= "Telefono:<input type='text' name='numero_telefono' size='12'/>";
	$cadena .= "<input type='submit' class='boton' name='agregar' value='[+]Agregar Telefono' />";
	$cadena .= "</form><div id='mensajes_estado'></div>";
	$cadena .= listado_telefonos_centro();
	return $cadena;
	
}
//Tres parametros, telefono, asignado, y a quien
//Telipext sera la base de datos de telefonos donde estaran todos,
//se compara con la de z_sercont para ver quien lo tiene
function listado_telefonos_centro()
{
	include("variables.php");
	$asignados_despacho_telefono=array();
	$asignados_despacho_adsl=array();
	$asignados_despacho_fax=array();
	$asignados_telefono=array();
	$asignados_adsl=array();
	$asignados_fax=array();
	$no_asignados=array();
	$sql = "select DISTINCT direccion from telipext where tipo like 'telefono'";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$asignados=array();
	$no_asignados=array();
	while($resultado = mysql_fetch_array($consulta))
	{
		//Aqui comparo las de la base con las que tengo asignadas
		//el telefono en telipext esta siempre 976 12 34 56
		//en la base puede estar asi o no
		//quito los espacios en blanco
		$teljunto="";
		$telefono = explode(" ",$resultado[direccion]);
		foreach($telefono as $tele)
			$teljunto .= $tele;
		$sql2 = "Select * from z_sercont where valor like '$resultado[direccion]%' or valor like '$teljunto%' ";
		
		$consulta2 = mysql_db_query($dbname,$sql2,$con);
		if(mysql_numrows($consulta2)>=1)
		{
			while($resultado2 = mysql_fetch_array($consulta2))
			{
				$tipo = categoria_del_cliente($resultado2[idemp]);
				//echo "<p/>".$resultado[direccion]."-".$tipo."-".$resultado2[servicio];
				if($tipo == "OK")
				{
					switch($resultado2[servicio])
					{
						case "Telefono":$asignados_despacho_telefono[] = $resultado[direccion]."-".$resultado2[idemp];break;
						case "Adsl":$asignados_despacho_adsl[] = $resultado[direccion]."-".$resultado2[idemp];break;
						case "Fax":$asignados_despacho_fax[] = $resultado[direccion]."-".$resultado2[idemp];break;
					}
				}
				else
				{
					switch($resultado2[servicio])
					{
						case "Telefono":$asignados_telefono[] = $resultado[direccion]."-".$resultado2[idemp];break;
						case "Adsl":$asignados_adsl[] = $resultado[direccion]."-".$resultado2[idemp];break;
						case "Fax":$asignados_fax[] = $resultado[direccion]."-".$resultado2[idemp];break;
					}
				}
			}
		}
		else
			$no_asignados[] = $resultado[direccion];
			
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
	$cadena.="<input class='boton' value='[X] Cerrar' onclick='cierra_listado_copias()' ><br><div class='tabla'><div class='listado_1'>Telefonos Despachos</div>";
	$i=0;
	foreach($asignados_despacho_telefono as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
		$clase = "par";
		else
		$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".utf8_encode(nombre_cliente($asignacion[1]))."</div>";
		
	}
	$cadena .="</div>";
	//Fin telefonos despachos
	//Telefonos Domiciliados
	$cadena.="<div class='tabla'><div class='listado_2'>Telefonos Domiciliados</div>";
	$i=0;
	foreach($asignados_telefono as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
		$clase = "par";
		else
		$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".utf8_encode(nombre_cliente($asignacion[1]))."</div>";
	}
	$cadena .="</div>";
	//Fin telefonos Domiciliados
	//Telefonos del centro
	$cadena.="<div class='tabla'><div class='listado_3'>Telefonos del Centro</div>";
	$i=0;
	foreach($no_asignados as $no_asignado)
	{
		//listado de telefonos del centro
                /*
                 * TODO: Se tienen que poder agregar y modificar desde el programa
                 */
		$centro = array("976 30 11 82","976 79 43 60","976 79 43 61","976 79 43 62");
		$i++;
		
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
		if(!in_array($no_asignado,$centro))
			$libres[]=$no_asignado;
		else
			$cadena .="<div class='".$clase."'>".$no_asignado."&nbsp;&nbsp;&nbsp;&nbsp;".descripcion_telefono($no_asignado)."</div>";
	}
	$cadena .="</div>";
	//Fin telefonos Domiciliados
	//telefonos Libres
	$cadena .= "<div class='tabla'><div class='listado_4'>Telefonos Libres</div>";
	$i=0;
	if($opcion==0)
    {
        if(is_array($libres))
            foreach($libres as $libre)
            {
                $i++;
                if($i%2==0)
                    $clase ="par";
                else
                    $clase = "impar";
            $cadena .="<div class='".$clase."'><img src='iconos/edittrash.png' alt='Borrar telefono' onclick='javascript:borrar_telefono_asignado(\"".$libre."\")'>&nbsp;<img src='iconos/kate.png' alt='Editar telefono' onclick='javascript:editar_telefono_asignado(\"".$libre."\")'>&nbsp;".$libre."&nbsp;&nbsp;&nbsp;&nbsp;<span id='edicion_".$libre."'>".descripcion_telefono($libre)."</span></div>";
            }
    }
	$cadena .="</div>";
	//Fin telefonos libres
	//Faxes de Despachos
	$cadena.="<div class='tabla'><div class='listado_1'>Faxes Despachos</div>";
	$i=0;
	foreach($asignados_despacho_fax as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".utf8_encode(nombre_cliente($asignacion[1]))."</div>";
	}
	$cadena .="</div>";
	//Fin telefonos despachos
	//Faxes Domiciliados
	$cadena.="<div class='tabla'><div class='listado_2'>Faxes Domiciliados</div>";
	$i=0;
	foreach($asignados_fax as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".utf8_encode(nombre_cliente($asignacion[1]))."</div>";
	}
	$cadena .="</div>";
	//Adsl de Despachos
	$cadena.="<div class='tabla'><div class='listado_1'>Adsl Despachos</div>";
	$i=0;
	foreach($asignados_despacho_adsl as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".utf8_encode(nombre_cliente($asignacion[1]))."</div>";
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
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".utf8_encode(nombre_cliente($asignacion[1]))."</div>";
	}
	$cadena .="</div>";
	$cadena .="<div class='tabla'><div class='listado_2'>Listado de IP's</div>";
	$cadena .= consulta_de_ips();
	$cadena .="</div></div></div><p/><p/>";
	return $cadena;
}
function nombre_cliente($id)
{
	include("variables.php");
	$sql = "Select Nombre from clientes where id like $id";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	return $resultado[0];
}
function categoria_del_cliente($id)
{
	include("variables.php");
	$sql = "Select Categoria from clientes where id like $id";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	switch($resultado[0])
	{
		case "Clientes despachos": $valor='OK';break;
		default: $valor = 'KO';break;
	}
	return $valor;
}
function descripcion_telefono($telefono)
{
	include("variables.php");
	$sql = "Select descripcion from telipext where direccion like '$telefono'";
	//echo $sql;
	$consulta = mysql_db_query($dbname,$sql,$con);
	$resultado = mysql_fetch_array($consulta);
	return $resultado[0];
}
function frm_agrega_telefono($vars)
{
	include("variables.php");
	$sql="Insert into telipext (tipo,direccion,asignada) values ('telefono','$vars[numero_telefono]','No')";
	
	if($consulta=@mysql_db_query($dbname,$sql,$con))
		$texto= "Telefono Agregado";
	else
		$texto= "No se ha agregado el telefono";
	return $texto;
		
}
//LISTADO ESPECIAL ¡¡TARDA MUCHO EN GENERARSE!!
function rarita()
{
	include("variables.php");
	$sql = "SELECT z.valor, c.Nombre, c.Categoria, f.observaciones FROM `facturacion` 
	as f join clientes as c on c.id like f.idemp join z_sercont as z on z.idemp like 
	c.id WHERE  Estado_de_cliente != 0 and 
	(c.Categoria like '%domiciliac%' or c.Categoria like '%despacho%' or c.Categoria like 'Otros' or c.Categoria like '%Telefonica%' or c.Categoria like '%oficina movil%') and
	z.servicio like 'Codigo Negocio' order by z.valor asc";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$cadena = "<input class='boton' value='[X] Cerrar' onclick='cierra_listado_copias()' ><input type='button' class='boton' onclick=window.open('inc/excel.php') value='Imprimir' />";
	$k=0;
	$cadena .= "<table width='100%' class='tabla'>";
	$cadena .= "<tr><th>Codigo</th><th>Cliente</th><th>Categoria</th><th>Observaciones</th></tr>";
	while ($resultado = mysql_fetch_array($consulta))
		{
			if(ereg("despacho",$resultado[2]))
				$color="#69C";
			else
				$color="#F90";
		
		$cadena .= "<tr><td bgcolor='".$color."'><font color='#fff' size='2'><b>".$resultado[0]."</b></font></td><td class='".clase($k)."'>".traduce($resultado[1])."</td><td class='".clase($k)."'>".traduce($resultado[2])."</td><td class='".clase($k)."'>".traduce($resultado[3])."</td></tr>";
		$k++;
		}
	$cadena .= "</table>";
	return $cadena;
}
/*
 * Funcion que genera el listado de ips asignadas y libres
 */
function consulta_de_ips()
{
	include("variables.php");
	$sql = "Select c.Nombre,z.valor from z_sercont as z join clientes as c on z.idemp like c.id where z.servicio like 'Direccion IP' order by z.valor";
	$consulta = mysql_db_query($dbname,$sql,$con);
	while($resultado=mysql_fetch_array($consulta))
	{
		$ipes=explode(".",$resultado[valor]);
		$ocupadas[intval($ipes[3])]=$resultado[0];
	}
	
	for($i=1;$i<=254;$i++)
	{
		
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
			
		if($ocupadas[$i]!="")
		{
			$j++;
			if($j%2==0)
				$clase ="par";
			else
				$clase = "impar";
			$cogidas .= "<div class='".$clase."'>172.26.0.".$i."&nbsp;&nbsp;&nbsp;&nbsp;".traduce($ocupadas[$i])."</div>";
		}
		else
		{	
			$k++;
			if($k%2==0)
				$clase ="par";
			else
				$clase = "impar";
			$no_cogidas .="<div class='".$clase."'>172.26.0.".$i."&nbsp;&nbsp;&nbsp;&nbsp;</div>";
		}
	}
	return $cogidas."-".$no_cogidas;
}
//***********************************************************************************************/
function listado_personalizado($vars)
{
	include("variables.php");
	//buscamos el nombre de la categoria_del_cliente
	$tabla = utf8_decode("categorías clientes");
	//$sql = "SELECT * FROM `$tabla`";
	
	if($vars[tipo]=='social')
	{
		$sql ="Select * from clientes where direccion not like '' and Estado_de_cliente like '-1' order by Nombre";
	}
	else
	{
		if($vars[tipo]=='comercial')
		{
			$sql="Select * from clientes where dcomercial != '' and Estado_de_cliente like '-1' order by Nombre";
		}
		else
		{
			if($vars[tipo]=='conserje')
				$sql = "Select * from clientes where (categoria like '%domicili%' or categoria like '%despachos%' or categoria like '%tencion telefo%') and Estado_de_cliente like '-1' order by Nombre";
			else
				if($vars[tipo]=='independencia')
					$sql ="Select * from clientes where direccion like '%Independencia, 8 dpdo%'   and Estado_de_cliente like '-1' order by Nombre";
				else	
					$sql = "Select * from clientes as c join `$tabla` as d on c.Categoria = d.Nombre where d.id like $vars[tipo] and c.Estado_de_cliente like '-1' order by c.Nombre";
		}
	}
	$consulta = @mysql_db_query($dbname,$sql,$con);
	$i=0;
	while($resultado = @mysql_fetch_array($consulta))
	{
		if($i%2==0)
			$clase='listado_par';
		else
			$clase='listado_impar';
		$i++;
		$cadena.="<div class='".$clase."'>".$i." ".traduce($resultado[1])."<span class='direccion_esp'> ".traduce($resultado[Direccion])."</span></div>";
		//$i++;
	}
	$cadena .= "<div><input type='button' class='boton' onclick=window.open('inc/excel.php?tipo=$vars[tipo]') value='Imprimir' /></div>";
	return $cadena;
}
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
if(SISTEMA == "windows")
	$bien = utf8_decode($texto); //para windows
else
	$bien = $texto;//para sistemas *nix
return $bien;
}

/*
 * Borra el telefono que esta libre
 */
function borra_telefono_asignado($vars)
{
	include("variables.php");
	$sql="Delete from telipext where direccion like '$vars[telefono]'";
	if($consulta = @mysql_db_query($dbname,$sql,$con))
		return "Telefono Borrado".$sql;
	else
		return "No borrado".$sql;
}
/*
 * Edita la descripcion del telefono libre
 */
 function edita_telefono_asignado($vars)
 {
 	include("variables.php");
	$sql = "SELECT * FROM `telipext` WHERE direccion LIKE '$vars[telefono]'";
 	$consulta = @mysql_db_query($dbname,$sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	$cadena="<input type='text' id='descripcion_".$vars[telefono]."' value='".$resultado[descripcion]."'><input type='hidden' id='identificador_".$vars[telefono]."' value='".$resultado[id]."'><input type='button' onclick='actualiza_descripcion_telefono(\"".$vars[telefono]."\")' value='Actualizar'>";
 	return $cadena;
 }
 /*
  * Actualiza la descripcion del telefono libre
  */
 function actualiza_telefono_asignado($vars)
 {
 	include("variables.php");
	$sql = "Update `telipext` set descripcion = '$vars[descripcion]' where id like $vars[id]";
	if($consulta = @mysql_db_query($dbname,$sql,$con))
        return true;
	else
        return false;
 }
 /* la tipica de par o impar*/
 function clase($k)
{
	if($k%2==0)
		$clase = "par";
	else
		$clase = "impar";
return $clase;
}
?>