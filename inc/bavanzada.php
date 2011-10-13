<?
if(isset($_POST[opcion]))
{
	switch($_POST[opcion])
	{
		case 0:$cad = busca_valores($_POST);break;
	}
	echo $cad;
}

function busca_valores($vars)
{
	include("variables.php");
	$k=0;
	//valores texto,razon,comercial,empleado,onombre,telefono,email
	if($vars[texto] != null)
		$cadena .= "Busqueda de:".$vars[texto];
	if($vars[empleado]!= null)
		$cadena .= " empleado ";
	if($vars[onombre]!= null)
		$cadena .= " onombre ";
	if($vars[telefono]!= null)
		$cadena .= " telefono ";
	if($vars[email]!= null)
		$cadena .= " email ";
	/*Chequeamos si es un telefono*/
	$token = ereg_replace(" ","",$vars[texto]);
	if(is_numeric($token) && (strlen($token)==9))/*es numero de telefono*/
	{
		$vars[texto]=$token;
	}
    $vars[texto] = utf8_decode($vars[texto]); //convertimos caracteres
	$sql = "SELECT c.id, c.Nombre, c.Contacto, p.nombre, p.apellidos
	FROM clientes AS c
	JOIN pempresa AS p ON c.id = p.idemp
	WHERE (c.Nombre LIKE '%$vars[texto]%'
	OR c.Contacto LIKE '%$vars[texto]%'
	OR p.nombre LIKE '%$vars[texto]%'
	OR p.apellidos LIKE '%$vars[texto]%'
	OR concat(p.nombre,'',p.apellidos,'%') LIKE '%$vars[texto]%')
    and c.Estado_de_cliente = '-1'
	";

	//echo $sql; //Punto de control
	$cadena.="<p><b><u>Resultados busqueda en Clientes</u></b></p>";
	$consulta = mysql_db_query($dbname,$sql,$con);
	if(mysql_numrows($consulta)!=0)
	while($resultado = mysql_fetch_array($consulta))
		$cadena .= "<p class='".clase($k++)."'><a href='javascript:muestra(".$resultado[0].")'>
		".utf8_encode($resultado[1])." - ".utf8_encode($resultado[2])."-".utf8_encode($resultado[3])." ".utf8_encode($resultado[4])." </a></p>";
	else
		//Aqui parte especifica de telefonos
		$cadena .= "<p class='".clase($k++)."'>No hay resultados</p>";
	//Consultamos telefonos de cliente
	$sql = "Select id, Nombre from clientes where 
	(replace(Tfno1, ' ', '') LIKE '%$vars[texto]%' or
	replace(Tfno2, ' ', '') LIKE '%$vars[texto]%' or
	replace(Tfno3, ' ', '') LIKE '%$vars[texto]%')
    and Estado_de_cliente = '-1'";
	$consulta = mysql_db_query($dbname,$sql,$con);
	
	if(mysql_numrows($consulta)!=0)
	{
		while($resultado = mysql_fetch_array($consulta))
			$cadena.="<p class='".clase($k++)."'><a href='javascript:muestra(".$resultado[0].")'>".utf8_encode($resultado[1])."</a></p>";
	}
	//Consultamos telefonos de empleados
	$sql = "Select c.id ,p.nombre,p.apellidos,c.Nombre from pempresa as p 
	JOIN clientes as c on c.id = p.idemp
where  replace(p.telefono, ' ', '') like '%$vars[texto]%'
and c.Estado_de_cliente = '-1'";
	$consulta = mysql_db_query($dbname,$sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		while($resultado = mysql_fetch_array($consulta))
			$cadena.="<p class='".clase($k++)."'><a href='javascript:muestra(".$resultado[0].")'>".utf8_encode($resultado[1])." ".utf8_encode($resultado[2])." de ".utf8_encode($resultado[3])."</a></p>";
	}
	//consultamos telefonos de pcentral
	$sql = "Select c.id ,p.persona_central,c.Nombre from pcentral as p 
	JOIN clientes as c on c.id = p.idemp 
    where replace(p.telefono, ' ', '') like '%$vars[texto]%'
    and c.Estado_de_cliente = '-1'";
	$consulta = mysql_db_query($dbname,$sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
	while($resultado = mysql_fetch_array($consulta))
	$cadena.="<p class='".clase($k++)."'><a href='javascript:muestra(".$resultado[0].")'>".utf8_encode($resultado[1])." de ".utf8_encode($resultado[2])."</a></p>";
	}
	//Consultamos datos de proveedores
	$cadena.="<p><b><u>Resultados busqueda en Proveedores</u></b></p>";
	//$sql = "Select Id, Nombre from proveedores where Nombre like '%$vars[texto]%' or nocor like '%$vars[texto]%'";
	$sql = "SELECT c.id, c.Nombre, p.nombre, p.apellidos
	FROM proveedores AS c
	left JOIN pproveedores AS p ON c.id = p.idemp
	WHERE c.Nombre LIKE '%$vars[texto]%'
	OR p.nombre LIKE '%$vars[texto]%'
	OR p.apellidos LIKE '%$vars[texto]%'
	OR concat( p.nombre, '', p.apellidos, '%' ) LIKE '%$vars[texto]%'";
	$consulta = mysql_db_query($dbname,$sql,$con);
	$prov = 0;
	if(mysql_numrows($consulta)!=0)
	{
		$prov = 1;
		while($resultado = mysql_fetch_array($consulta))
		$cadena.="<p class='".clase($k++)."'><a href='javascript:muestra(".$resultado[0].")'>".utf8_encode($resultado[1])." - ".utf8_encode($resultado[2])." ".utf8_encode($resultado[3])."</a></p>";
	}
		
	/*$sql = "Select * from pproveedores where nombre like '%$vars[texto]%' 
	or apellidos like '%$vars[texto]%'
	or telefono like '%$vars[texto]%' 
	or email like '%$vars[texto]%'";
	$consulta = @mysql_db_query($dbname,$sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		$prov = 1;
		while($resultado = mysql_fetch_array($consulta))
		$cadena.="<p/><a href='javascript:muestra(".$resultado[1].")'>
		".utf8_encode($resultado[2])." ".utf8_encode($resultado[3])."
		</a>";
	
	}
	if($prov == 0)*/
	else
		$cadena.="<p class='".clase($k++)."'>No hay resultados de ".utf8_encode($vars[texto])." en Proveedores</p>";
	/*Busqueda de valores en teleco*/
	$cadena.="<p><b><u>Resultados busqueda en Telecomunicaciones</u></b></p>";
	$sql = "Select c.ID, c.Nombre, z.valor, z.servicio from clientes c
inner join z_sercont z on c.ID like z.idemp
where replace(valor, ' ', '') like '%$vars[texto]%'
and c.Estado_de_cliente = '-1'";
	$consulta = @mysql_db_query($dbname,$sql,$con);
	if(mysql_numrows($consulta)!= 0)
	while($resultado = @mysql_fetch_array($consulta))
		$cadena.="<p class='".clase($k++)."'><a href='javascript:muestra(".$resultado[0].")'>".utf8_encode($resultado[1])." - ".utf8_encode($resultado[2])." - ".utf8_encode($resultado[3])."</a></p>";
	else
		$cadena.="<p class='".clase($k++)."'>No hay resultados de ".utf8_encode($vars[texto])." en Telecomunicaciones</p>";
	
	return $cadena;
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
