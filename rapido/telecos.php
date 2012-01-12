<?php //fichero telecos.php generacion del formulario y gestion de datos de las telecomunicaciones.Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007
require_once '../inc/variables.php';
if ( isset($_POST['opcion'])) {
    switch($_POST['opcion']) {
    	case 1:$devuelve = genera_formulario();break;
    	case "telefonos":$devuelve = genera_telefonos();break;
    	case "ip":$devuelve = genera_ips();break;
    	case "fotocopias":$devuelve = genera_fotocopias();break;
    	case "afotocopias":$devuelve = genera_afotocopias();break;
    	case "negocio":$devuelve = genera_negocio();break;
    	case "adsl":$devuelve = genera_adsl();break;
    	case "ckadsl":$devuelve = check_adsl($_POST['adsl']);break;
    	case "agradsl":$devuelve = agrega_adsl($_POST['cliente'],$_POST['adsl']);break;
    	case "fax":$devuelve = genera_fax();break;
    	case "ckfax":$devuelve = check_fax($_POST['fax']);break;
    	case "agrfax":$devuelve = agrega_fax($_POST['cliente'],$_POST['fax']);break;
    	case "aip":$devuelve = agrega_ips($_POST['cliente'],$_POST['ip']);break;
    	case "cktel":$devuelve = check_telefono($_POST['telefono']);break;
    	case "agrtel":$devuelve = agrega_telefono($_POST['cliente'],$_POST['telefono']);break; 
    	case "ckfot":$devuelve = check_fotocopias($_POST['fotocopias']);break;
    	case "agrfot":$devuelve = agrega_fotocopias($_POST['cliente'],$_POST['fotocopias']);break;
    	case "ckafot":$devuelve = check_afotocopias($_POST['afotocopias']);break;
    	case "agrafot":$devuelve = agrega_afotocopias($_POST['cliente'],$_POST['afotocopias']);break;
    	case "ckneg":$devuelve = check_negocio($_POST['negocio']);break;
    	case "agrneg":$devuelve = agrega_negocio($_POST['cliente'],$_POST['negocio']);break; 
    	case "lstcli":$devuelve = listado_telecos($_POST['cliente']);break;
    	case "borrasrv":$devuelve = borra_teleco($_POST['servicio']);break;
    }
    echo $devuelve;
}
//parte de las funciones

//Genera el formulario principal
function genera_formulario()
{
	$tabla = "<table class='listado_telecos'><tr><th colspan='2'>Telefonos,Ip,Codigos Fotocopias,Codigos Negocio,Adsl,Fax</th></tr>";
	$tabla .= "<tr><th colspan='2'>Agregar</th></tr>";
	$tabla .= "<tr><th>Tipo</th><td>".tipos_telecos()."</td></tr>";
	$tabla .= "<tr><th colspan='2'><div id='frm_teleco'></div></tr>";
	$tabla .= "</table>";
	return $tabla;
}
//Ffin formulario principal
//Genera el select de los tipos de comunicaciones
function tipos_telecos()
{
	$tabla = "<select id='lista_telecos' onchange='muestra_campo()'>
		<option value='0'>--Seleccione--</option>
		<option value='telefonos'>Telefono</option>
		<option value='ip'>Direccion IP</option>
		<option value='fotocopias'>Codigo Fotocopias</option>
		<option value='afotocopias'>Codigo Fotocopias Autoservicio</option>
		<option value='negocio'>Codigo Negocio</option>
		<option value='adsl'>Adsl</option>
		<option value='fax'>Fax</option>
		</select>";
	return $tabla;
}
//Fin del formulario de los tipos de comunicaciones
//Formulario que genera el listado de las ip - rango del 172.26.0.1 - 254
function genera_ips()
{
//1.- Consultamos cuales estan libre y despues mostramos las ocupadas
	global $con;
	$sql = "Select valor from z_sercont where servicio like 'ip'";
	$consulta = mysql_query($sql,$con);
	$totaloc = mysql_numrows($consulta);
	$i=0;
	while(true == ($resultado = mysql_fetch_array($consulta)))
	{
		$ocupadas[$i++] = $resultado[0];
	}
	sort($ocupadas); //ordenamos la array
	reset($ocupadas); //liberamos la array
	$tabla = "Seleccione IP:<select id='lista_ips'>
			<option value='0'>--Seleccione IP--</option>";
	for($j=1;$j<=254;$j++)
	{
			if(!in_array($j,$ocupadas,false))
			$tabla .= "<option value='".$j."'>172.26.0.".$j."</option>";
	}
	$tabla .= "</select>";
	$tabla .= "<span class='boton' onclick='agrega_ip()'>[+]Asignar IP</span>";
	return $tabla;			
}
//Formulario que genera el listado de las ip - rango del 172.26.0.1 - 254
//este es cuadro de texto que comprueba si existe el que le pasemos
function genera_telefonos()
{
	$tabla = "<input id='telefono' type='text' size='10' onkeyup='chequea_telefono()' />";
	$tabla .="<span class='boton' onclick='agrega_telefono()'>[+]Asignar Telefono</span>"; 
	return $tabla;
}
function genera_fotocopias()
{
	$tabla = "<input id='fotocopias' type='text' size='10' onkeyup='chequea_fotocopias()' />";
	$tabla .="<span class='boton' onclick='agrega_fotocopias()'>[+]Asignar Codigo Fotocopias</span>"; 
	return $tabla;
}
function genera_afotocopias()
{
	$tabla = "<input id='afotocopias' type='text' size='10' onkeyup='chequea_afotocopias()' />";
	$tabla .="<span class='boton' onclick='agrega_afotocopias()'>[+]Asignar Codigo Fotocopias Autoservicio</span>"; 
	return $tabla;
}
function genera_negocio()
{
	$tabla = "<input id='negocio' type='text' size='10' onkeyup='chequea_negocio()' />";
	$tabla .="<span class='boton' onclick='agrega_negocio()'>[+]Asignar Codigo Negocio</span>"; 
	return $tabla;
}
function genera_adsl()
{
	$tabla = "<input id='adsl' type='text' size='10' onkeyup='chequea_adsl()' />";
	$tabla .="<span class='boton' onclick='agrega_adsl()'>[+]Asignar Adsl</span>"; 
	return $tabla;
}
function genera_fax()
{
	$tabla = "<input id='fax' type='text' size='10' onkeyup='chequea_fax()' />";
	$tabla .="<span class='boton' onclick='agrega_fax()'>[+]Asignar Fax</span>"; 
	return $tabla;
}
//funciones de agregado
function agrega_ips($cliente,$ip)
{
	global $con;
	$sql = "Insert into z_sercont (servicio,idemp,valor) values ('ip','".$cliente."','".$ip."')";
	if ( mysql_query($sql,$con))
		$tabla = "<span class='avisok'>Direccion Ip asignada al cliente</span>";
	else
		$tabla = "<span class='avisonok'>No se ha asignado la direccion IP al cliente</span>";
	return $tabla;
}
/***************************************TELEFONO*******************************************************************/
//funcion de chequeo de telefonos, si no existe devuelve el color verde si existe devuelve el rojo
function check_telefono($telefono)
{
	global $con;
	$sql = "Select * from z_sercont where servicio like 'telefono' 
	and valor like '".$telefono."'";
	$consulta = mysql_query($sql,$con);
	$total = mysql_numrows($consulta);
	if ($total == 0)
		$tabla = "#00ff00";//.$telefono;
	else
		$tabla = "#ff0000";//.$telefono;
	return $tabla;
}
//funcion que agrega el telefono a la lista
function agrega_telefono($cliente,$telefono)
{
	global $con;
	$previa = check_telefono($telefono);
	if ($previa == "#00ff00") //el numero es correcto
	{	
		$sql = "Insert into z_sercont (servicio,idemp,valor) 
		values ('telefono','".$cliente."','".$telefono."')";
		if ( mysql_query($sql,$con) )
			$tabla = "<span class='avisok'>Telefono asignado al cliente</span>";
		else
			$tabla = "<span class='avisonok'>No se ha asignado el telefono al cliente</span>";
	}
	else
		$tabla = "<span class='avisonok'>No se ha asignado el telefono al cliente</span>";
	return $tabla;
}
/***************************************ADSL*******************************************************************/
//funcion de chequeo de adsl, si no existe devuelve el color verde si existe devuelve el rojo
function check_adsl($adsl)
{
	global $con;
	$sql = "Select * from z_sercont where servicio 
	like 'adsl' and valor like '".$adsl."'";
	$consulta = mysql_query($sql,$con);
	$total = mysql_numrows($consulta);
	if ($total == 0)
		$tabla = "#00ff00";//.$telefono;
	else
		$tabla = "#ff0000";//.$telefono;
	return $tabla;
}
//funcion que agrega el adsl a la lista
function agrega_adsl($cliente,$adsl)
{
	global $con;
	$previa = check_adsl($adsl);
	if ($previa == "#00ff00") //el numero es correcto
	{	
		$sql = "Insert into z_sercont (servicio,idemp,valor) 
		values ('adsl','".$cliente."','".$adsl."')";
		if ( mysql_query($sql,$con) )
			$tabla = "<span class='avisok'>Adsl asignada al cliente</span>";
		else
			$tabla = "<span class='avisonok'>No se ha asignado el adsl al cliente</span>";
	}
	else
		$tabla = "<span class='avisonok'>No se ha asignado el adsl al cliente</span>";
	return $tabla;
}
/***************************************FAX*******************************************************************/
//funcion de chequeo de fax, si no existe devuelve el color verde si existe devuelve el rojo
function check_fax($fax)
{
	global $con;
	$sql = "Select * from z_sercont 
	where servicio like 'fax' and valor like '".$fax."'";
	$consulta = mysql_query($sql,$con);
	$total = mysql_numrows($consulta);
	if ($total == 0)
		$tabla = "#00ff00";//.$telefono;
	else
		$tabla = "#ff0000";//.$telefono;
	return $tabla;
}
//funcion que agrega el fax a la lista
function agrega_fax($cliente,$fax)
{
	$previa = check_fax($fax);
	if ($previa == "#00ff00") //el numero es correcto
	{	
		$sql = "Insert into z_sercont (servicio,idemp,valor) 
		values ('fax','".$cliente."','".$fax."')";
		if (mysql_query($sql,$con))
			$tabla = "<span class='avisok'>Fax asignado al cliente</span>";
		else
			$tabla = "<span class='avisonok'>No se ha asignado el Fax al cliente</span>";
	}
	else
		$tabla = "<span class='avisonok'>No se ha asignado el Fax al cliente</span>";
	return $tabla;
}
/***************************************FOTOCOPIAS*******************************************************************/
//Aqui las gemelas codigos de fotocopias y negocios
//funcion de chequeo de codigos de fotocopias, si no existe devuelve el color verde si existe devuelve el rojo
function check_fotocopias($fotocopias)
{
	global $con;
	$sql = "Select * from z_sercont 
	where servicio like 'fotocopias' and valor like '".$fotocopias."'";
	$consulta = mysql_query($sql,$con);
	$total = mysql_numrows($consulta);
	if ($total == 0)
		$tabla = "#00ff00";
	else
		$tabla = "#ff0000";
	return $tabla;
}
//funcion que agrega el codigo de fotocopias a la lista
function agrega_fotocopias($cliente,$fotocopias)
{
	global $con;
	$previa = check_fotocopias($fotocopias);
	if ($previa == "#00ff00") //el numero es correcto
	{	
		$sql = "Insert into z_sercont (servicio,idemp,valor) 
		values ('fotocopias','".$cliente."','".$fotocopias."')";
		if (mysql_query($sql,$con))
			$tabla = "<span class='avisok'>Codigo de fotocopias asignado al cliente</span>";
		else
			$tabla = "<span class='avisonok'>No se ha asignado el codigo de fotocopias al cliente</span>";
	}
	else
		$tabla = "<span class='avisonok'>No se ha asignado el codigo de fotocopias al cliente</span>";
	return $tabla;
}
/***************************************FOTOCOPIAS AUTOSERVICIO*******************************************************************/
//funcion de chequeo de codigos de fotocopias autoservicio , si no existe devuelve el color verde si existe devuelve el rojo
function check_afotocopias($afotocopias)
{
	global $con;
	$sql = "Select * from z_sercont 
	where servicio like 'afotocopias' and valor like '".$afotocopias."'";
	$consulta = mysql_query($sql,$con);
	$total = mysql_numrows($consulta);
	if ($total == 0)
		$tabla = "#00ff00";
	else
		$tabla = "#ff0000";
	return $tabla;
}
//funcion que agrega el codigo de fotocopias autoservico a la lista
function agrega_afotocopias($cliente,$afotocopias)
{
	global $con;
	$previa = check_afotocopias($afotocopias);
	if ($previa == "#00ff00") //el numero es correcto
	{	
		$sql = "Insert into z_sercont (servicio,idemp,valor) 
		values ('afotocopias','".$cliente."','".$afotocopias."')";
		if (mysql_query($sql,$con))
			$tabla = "<span class='avisok'>Codigo de fotocopias 
		Autoservicio asignado al cliente</span>";
		else
			$tabla = "<span class='avisonok'>No se ha asignado el codigo de 
		fotocopias Autoservicio al cliente</span>";
	}
	else
		$tabla = "<span class='avisonok'>No se ha asignado el codigo de 
	fotocopias Autoservicio al cliente</span>";
	return $tabla;
}
/***************************************CODIGOS DE NEGOCIO*******************************************************************/
//funcion de chequeo de codigos de negocio, si no existe devuelve el color verde si existe devuelve el rojo
function check_negocio($negocio)
{
	global $con;
	$sql = "Select * from z_sercont 
	where servicio like 'negocio' and valor like '".$negocio."'";
	$consulta = mysql_query($sql,$con);
	$total = mysql_numrows($consulta);
	if ($total == 0)
		$tabla = "#00ff00";//.$telefono;
	else
		$tabla = "#ff0000";//.$telefono;
	return $tabla;
}
//funcion que agrega el telefono a la lista 
function agrega_negocio($cliente,$negocio)
{
	global $con;
	$previa = check_negocio($negocio);
	if ($previa == "#00ff00") //el numero es correcto
	{	
		$sql = "Insert into z_sercont (servicio,idemp,valor) 
		values ('negocio','".$cliente."','".$negocio."')";
		if (mysql_query($sql,$con))
			$tabla = "<span class='avisok'>Codigo de negocio asignado al cliente</span>";
		else
			$tabla = "<span class='avisonok'>No se ha asignado el codigo de negocio al cliente</span>";
	}
	else
		$tabla = "<span class='avisonok'>No se ha asignado el codigo de negocio al cliente</span>";
	return $tabla;
}
//listado de telecomunicaciones que tiene contratadas el cliente *******************CONFLICTIVA***************************++++++++++++++++++++++++++++++++++++++++
function listado_telecos($cliente)
{
global $con;
//4 niveles ip, telefonos, 
	$tabla="<table class='listado_telecos' cellpadding='2px' cellspacing='2px' width='100%'>
<tr><th colspan='7'>Listado de servicios asignados</th></tr>";
//cabezeras
	$tabla .="<tr>";
	$tabla .= "<th>Direcciones Ip</th>";
	$tabla .= "<th>Telefonos</th>";
	$tabla .= "<th>Adsl</th>";
	$tabla .= "<th>Fax</th>";
	$tabla .= "<th>Codigos de fotocopias</th>";
	$tabla .= "<th>Fotocopias Autoservicio</th>";
	$tabla .= "<th>Codigos de Negocio</th>";
	$tabla .= "</tr>";
//Direcciones IP******************************************************************************************/
	$sql = "Select * from z_sercont 
	where servicio like 'ip' and idemp like ".$cliente;
	$consulta = mysql_query($sql,$con);
	$lista_ips = "<table class='listado_telecos' width='100%' cellpadding='2px' cellspacing='2px'>";	
	if(mysql_numrows($consulta) >= 1)
		while (true == ($resultado = mysql_fetch_array($consulta)))
			$lista_ips .= "<tr><td>172.26.0.".$resultado[3]."</td>
			<td><span class='boton' onclick='borra_servicio(".$resultado[0].")'>
			<img src='../imagenes/borrar.png' alt='Borrar' /></span></td></tr>";
	else
		$lista_ips .= "<tr><td>No tiene asignada ninguna direccion IP</td></tr>";
	$lista_ips .= "</table>";
//Telefonos******************************************************************************************/
	$sql = "Select * from z_sercont 
	where servicio like 'telefono' and idemp like ".$cliente;
	$consulta = mysql_query($sql,$con);
	$lista_telefonos = "<table class='listado_telecos' width='100%' cellpadding='2px' cellspacing='2px'>";	
	if(mysql_numrows($consulta) >= 1)
		while (true == ($resultado = mysql_fetch_array($consulta)))
			$lista_telefonos .= "<tr><td>".$resultado[3]."</td><td>
			<span class='boton' onclick='borra_servicio(".$resultado[0].")'>
			<img src='../imagenes/borrar.png' alt='Borrar' /></span></td></tr>";
	else
		$lista_telefonos .= "<tr><td>No tiene asignada ningun telefono asignado</td></tr>";
	$lista_telefonos .= "</table>";
//Adsl******************************************************************************************/
	$sql = "Select * from z_sercont 
	where servicio like 'adsl' and idemp like ".$cliente;
	$consulta = mysql_query($sql,$con);
	$lista_adsl = "<table class='listado_telecos' width='100%' cellpadding='2px' cellspacing='2px'>";	
	if(mysql_numrows($consulta) >= 1)
		while (true == ($resultado = mysql_fetch_array($consulta)))
			$lista_adsl .= "<tr><td>".$resultado[3]."</td>
			<td><span class='boton' onclick='borra_servicio(".$resultado[0].")'>
			<img src='../imagenes/borrar.png' alt='Borrar' /></span></td></tr>";
	else
		$lista_adsl .= "<tr><td>No tiene asignada ninguna ADSL asignada</td></tr>";
	$lista_adsl .= "</table>";
//Fax******************************************************************************************/
	$sql = "Select * from z_sercont 
	where servicio like 'fax' and idemp like ".$cliente;
	$consulta = mysql_query($sql,$con);
	$lista_fax = "<table class='listado_telecos' width='100%' cellpadding='2px' cellspacing='2px'>";	
	if(mysql_numrows($consulta) >= 1)
		while (true==($resultado = mysql_fetch_array($consulta)))
			$lista_fax .= "<tr><td>".$resultado[3]."</td>
			<td><span class='boton' onclick='borra_servicio(".$resultado[0].")'>
			<img src='../imagenes/borrar.png' alt='Borrar' /></span></td></tr>";
	else
		$lista_fax .= "<tr><td>No tiene asignada ningun telefono asignado</td></tr>";
	$lista_fax .= "</table>";
//Codigos de fotocopias******************************************************************************************/
	$sql = "Select * from z_sercont where servicio like 'fotocopias' 
	and idemp like ".$cliente;
	$consulta = mysql_query($sql,$con);
	$lista_fotocopias = "<table class='listado_telecos' width='100%' cellpadding='2px' cellspacing='2px'>";	
	if(mysql_numrows($consulta) >= 1)
		while (true == ($resultado = mysql_fetch_array($consulta)))
			$lista_fotocopias .= "<tr><td>".$resultado[3]."</td>
			<td><span class='boton' onclick='borra_servicio(".$resultado[0].")'>
			<img src='../imagenes/borrar.png' alt='Borrar' /></span></td></tr>";
	else
		$lista_fotocopias .= "<tr><td>No tiene asignado ningun codigo de fotocopias</td></tr>";
	$lista_fotocopias .= "</table>";
//Codigos de fotocopias autoservicio******************************************************************************************/
	$sql = "Select * from z_sercont 
	where servicio like 'afotocopias' and idemp like ".$cliente;
	$consulta = mysql_query($sql,$con);
	$lista_afotocopias = "<table class='listado_telecos' width='100%' cellpadding='2px' cellspacing='2px'>";	
	if(mysql_numrows($consulta) >= 1)
		while ( true == ($resultado = mysql_fetch_array($consulta)))
			$lista_afotocopias .= "<tr><td>".$resultado[3]."</td>
			<td><span class='boton' onclick='borra_servicio(".$resultado[0].")'>
			<img src='../imagenes/borrar.png' alt='Borrar' /></span></td></tr>";
	else
		$lista_afotocopias .= "<tr><td>No tiene asignado ningun codigo de fotocopias autoservicio </td></tr>";
	$lista_afotocopias .= "</table>";
//Codigos de negocio******************************************************************************************/
	$sql = "Select * from z_sercont 
	where servicio like 'negocio' and idemp like ".$cliente;
	$consulta = mysql_query($sql,$con);
	$lista_negocios ="<table class='listado_telecos' width='100%' cellpadding='2px' cellspacing='2px'>";	
	if(mysql_numrows($consulta) >= 1)
		while (true == ($resultado = mysql_fetch_array($consulta)))
			$lista_negocios .= "<tr><td>".$resultado[3]."</td><td>
			<span class='boton' onclick='borra_servicio(".$resultado[0].")'>
			<img src='../imagenes/borrar.png' alt='Borrar' /></span></td></tr>";
	else
	$lista_negocios .= "<tr><td>No tiene asignado ningun codigo de negocio</td></tr>";
	$lista_negocios .= "</table>";
//llenado de la tabla con tablas******************************************************************************************/
	$tabla .= "<tr><td valign='top'>".$lista_ips."</td>
	<td valign='top'>".$lista_telefonos."</td>
	<td valign='top'>".$lista_adsl."</td>
	<td valign='top'>".$lista_fax."</td>
	<td valign='top'>".$lista_fotocopias."</td>
	<td valign='top'>".$lista_afotocopias."</td>
	<td valign='top'>".$lista_negocios."</td></tr>";
	$tabla .= "</table>";
	return $tabla;
}
//FUNCION QUE BORRA EL SERVICIOS DE TELECOMUNICACIONES MARCADO***************************
function borra_teleco($servicio)
{
	global $con;
	$sql = "Delete from z_sercont where id like ".$servicio;
	if (mysql_query($sql,$con))
		$tabla = "<span class='avisok'>Servicio borrado</span>";
	else
		$tabla = "<span class='avisonok'>No se ha borrado el servicio</span>";
	return $tabla;
}