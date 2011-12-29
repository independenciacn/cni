<?php
require_once 'variables.php';
checkSession();
if ( isset($_POST ) ) {
    sanitize($_POST);
}
$cadena = "";
if(isset($_SESSION['usuario']))
{
	if ( isset($_POST['opcion'] ) ) {
        switch($_POST['opcion'])
	    {
		    case 0:$cadena = avisos();break;
		    case 1:$cadena = telefonos();break;
		    default:$cadena= avisos();break;
	    }
	} else {
	    $cadena = avisos();
	}
echo $cadena;
}
/*
 * Muestra los avisos
 */
function avisos()
{
	global $con;
	$texto="<input type='button' class='boton' value='[<]Ocultar Avisos' 
	onclick='cerrar_avisos()'/>
	<table class='tabla'><tr><th colspan='2'>Cartel de Avisos</th></tr>
	<tr><th>Cumplea&ntilde;os</th><th>Contratos</th></tr>";
	
	//Esto es solo para cumpleaños, tablas con fecha nacimiento
	//tres tablas, empleados[FechNac],pcentral[cumple],pempresa[cumple]
	/*Contador*/
	$k=0;
	$texto.= "<tr><td valign='top'>
	<table width='100%'><tr><th colspan='2'>Hoy hace los a&ntilde;os</th></tr>";
	//personas de la central*******************************
	$sql ="SELECT  
		clientes.Nombre, 
		pcentral.persona_central, 
		pcentral.cumple,
	clientes.id
	FROM clientes INNER JOIN pcentral ON clientes.Id = pcentral.idemp where date_format(pcentral.cumple,'%d %c') like date_format(curdate(),'%d %c') and clientes.Estado_de_cliente != 0";
	$consulta = mysql_query( $sql, $con );
	$nocump=0;
	if(mysql_numrows($consulta)!=0)
	{
		$nocump=1;
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
		$texto .= "<tr><td class='".clase($k++)."' colspan='2'>".$resultado[1]." de <a href='javascript:muestra($resultado[3])'>".traduce($resultado[0])."</a></td></tr>";
		//$k++;
		}
	}
	
	//personas de la empresa*********************************************************************/
	$sql ="SELECT  
		clientes.Nombre, 
		pempresa.nombre,
		pempresa.apellidos, 
		pempresa.cumple,
	clientes.id
	FROM clientes INNER JOIN pempresa ON clientes.Id = pempresa.idemp where date_format(pempresa.cumple,'%d %c') like date_format(curdate(),'%d %c') and clientes.Estado_de_cliente != 0";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		$nocump=1;
		while( true == ($resultado = mysql_fetch_array($consulta)))
		{
			$texto .="<tr><td class='".clase($k++)."' colspan='2'>". $resultado[1]." ".traduce($resultado[2])." de <a href='javascript:muestra($resultado[4])'>".traduce($resultado[0])."</a></td></tr>";
			//$k++;
		}
	}
	
	//empleados********************************************************************************/
	$sql = "Select * from empleados where date_format(FechNac,'%d %c') like date_format(curdate(),'%d %c')";
	$consulta = mysql_query( $sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		$nocump=1;
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			$texto .="<tr><td class='".clase($k++)."' colspan='2'>". $resultado[3]." ".traduce($resultado[1])." ".traduce($resultado[2])."</td></tr>";
			//$k++;
		}
	}
	if($nocump==0)
	{
		$texto.="<tr><td class='".clase($k++)."' colspan='2'>Nadie cumple los a&ntilde;os hoy</td></tr>";
		//$k++;
	}
	//MA�ANA*************************************************************************************/
	$nocump=0; //Inicializamos el chivato
	$texto.= "<tr><th colspan='2'>Y ma&ntilde;ana:</th></tr>";
	//personas de la central********************
	$sql ="SELECT  
		clientes.Nombre, 
		pcentral.persona_central, 
		pcentral.cumple,
	clientes.id
	FROM clientes INNER JOIN pcentral ON clientes.Id = pcentral.idemp where date_format(pcentral.cumple,'%d %c' ) like date_format(adddate(curdate(),1),'%d %c') and clientes.Estado_de_cliente != 0";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		$nocump=1;
		while(true == ($resultado = mysql_fetch_array($consulta)))
		$texto .="<tr><td class='".clase($k++)."' colspan='2' >". $resultado[1]." de <a href='javascript:muestra($resultado[3])'>".traduce($resultado[0])."</a></td></tr>";
	}
	//personas de la empresa********************
	$sql ="SELECT  
		clientes.Nombre, 
		pempresa.nombre,
		pempresa.apellidos, 
		pempresa.cumple,
	clientes.id
	FROM clientes INNER JOIN pempresa ON clientes.Id = pempresa.idemp where date_format(pempresa.cumple,'%d %c' ) like date_format(adddate(curdate(),1),'%d %c') and clientes.Estado_de_cliente != 0";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		$nocump=1;
		while(true == ($resultado = mysql_fetch_array($consulta)))
		$texto .="<tr><td class='".clase($k++)."' colspan='2'>". $resultado[1]." ".traduce($resultado[2])." de <a href='javascript:muestra($resultado[4])'>".traduce($resultado[0])."</a></td></tr>";
	}
	//empleados*********************************
	$sql = "Select * from empleados where date_format(FechNac,'%d %c' ) like date_format(adddate(curdate(),1),'%d %c')";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		$nocump=1;
		while(true == ($resultado = mysql_fetch_array($consulta)))
		$texto .="<tr><td class='".clase($k++)."' colspan='2'>". $resultado[3]." ".traduce($resultado[1])." ".traduce($resultado[2])."</td></tr>";
	}
	if($nocump==0)
		$texto.="<tr><td class='".clase($k++)."' colspan='2'>Nadie cumple los a&ntilde;os ma&ntilde;ana</td></tr>";
	
	//En los siguientes 40 dias*************************************************************************************/
	/*
     * Modificacion Julio 2009 ordenado por dias no como sale
     */
    $nocump=0;
	$texto.= "<tr><th colspan='2'>En los siguientes dias:</th></tr>";
	//personas de la central********************
	if(date('m')==12)
		$orden = " desc ";
	else
		$orden = " ";
	$sql ="SELECT
		clientes.Nombre, 
		pcentral.persona_central, 
		pcentral.cumple,
	clientes.id, date_format( pcentral.cumple, '0000-%m-%d' ) AS cumplea
	FROM clientes INNER JOIN pcentral ON clientes.Id = pcentral.idemp where
 (
 day(pcentral.cumple) > day(curdate()) and
 month(pcentral.cumple) like month(curdate())
 or
 month(pcentral.cumple) like month(date_add(curdate(), interval 1 month))
) and clientes.`Estado_de_cliente` != 0
 order by month(pcentral.cumple)".$orden.", day(pcentral.cumple) ";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		$nocump=1;
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
		//$texto .="<tr><td class='".clase($k)."'>".dia_y_mes($resultado[2])."</td><td class='".clase($k)."'>". traduce($resultado[1])." de <a href='javascript:muestra($resultado[3])'>".traduce($resultado[0])."</a></td></tr>";
		
        $cumplesmil[]=array(invierte(dia_y_mes($resultado[2])),dia_y_mes($resultado[2]),traduce($resultado[1]),$resultado[3],traduce($resultado[0]));
        //$k++;
		}
	}
	//personas de la empresa********************
	if(date('m')==12)
		$orden = " desc ";
	else
		$orden = " ";
	$sql ="SELECT
		clientes.Nombre,
		pempresa.nombre,
		pempresa.apellidos,
		pempresa.cumple,
	clientes.id, date_format( pempresa.cumple, '0000-%m-%d' ) AS cumplea
	FROM clientes INNER JOIN pempresa ON clientes.Id = pempresa.idemp where
 (
 day(pempresa.cumple) > day(curdate()) and
 month(pempresa.cumple) like month(curdate())
 or
 month(pempresa.cumple) like month(date_add(curdate(), interval 1 month))
) and clientes.`Estado_de_cliente` != 0
 order by month(pempresa.cumple)".$orden.", day(pempresa.cumple) ";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		$nocump=1;
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
		//$texto .="<tr><td class='".clase($k)."'>".dia_y_mes($resultado[3])."</td><td class='".clase($k)."'>". traduce($resultado[1])." ".traduce($resultado[2])." de <a href='javascript:muestra($resultado[4])'>".traduce($resultado[0])."</a></td></tr>";
		$cumplesmil[]=array(invierte(dia_y_mes($resultado[3])),dia_y_mes($resultado[3]),$resultado[1]." ".$resultado[2],$resultado[4],$resultado[0]);

        //$k++;
		}
	}
	//empleados*********************************
	$sql = "Select * from empleados where (datediff(date_format(DATE_ADD(CURDATE(),INTERVAL 40 DAY),'0000-%m-%d'),date_format(FechNac,'0000-%m-%d' )) <= 39
	and
	datediff(date_format(DATE_ADD(CURDATE(),INTERVAL 40 DAY),'0000-%m-%d'),date_format(FechNac,'0000-%m-%d' )) >= 0)";
	$consulta = mysql_query($sql,$con);
	if(mysql_numrows($consulta)!=0)
	{
		$nocump=1;
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			//$texto .="<tr><td class='".clase($k)."'>".cambiaf($resultado[FechNac])."</td><td class='".clase($k)."'>". traduce($resultado[3])." ".traduce($resultado[1])." ".traduce($resultado[2])."</td></tr>";
			$cumplesmil[]=array(invierte(cambiaf($resultado['FechNac'])),cambiaf($resultado['FechNac']),$resultado[3]." ".$resultado[1],NULL,NULL);

            //$k++;
		}
	}
    sort($cumplesmil);
    foreach($cumplesmil as $cumple)
    {
    $texto.="<tr class='".clase($k)."'><td>".$cumple[1]."</td><td>".$cumple[2];
    if($cumple[4]!=NULL)
    $texto.=" de <a href='javascript:muestra($cumple[3])'>".$cumple[4]."</a>";
    $texto.="</td></tr>";
    $k++;
    
    }
    if($nocump==0)
		$texto.="<tr><td class='".clase($k)."' colspan='2'>Nadie cumple los a&ntilde;os en los proximos 15 dias</td></tr>";
	
	/*!!!!PEGOTEEEE!!!!! FUNCION REPETIDA EN DATINS.php*/
	$texto.="</table></td>";
	$texto.= "<td valign='top'>".avisos_new()."</td></tr></table>";
	return $texto;
	//Y mañana adddate(curdate(),1) like  cumple Select * from empleados where date_format(FechNac,'%d %c' ) like date_format(adddate(curdate(),1),'%d %c')
}
function cambiaf($stamp) //funcion del cambio de fecha
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}
function dia_y_mes($stamp)
{
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1];
	return $fecha;
}
/*************************************************************************************************************/
function telefonos()
{
	include('variables.php');
	$cadena.="<input type='button' value='[v]Ocultar telefonos' onclick='cerrar_tablon_telefonos()'/>";
	$cadena .= listado('Telefono');
	$cadena .= listado('Fax');
	$cadena .= listado('Adsl');
	return $cadena;
}
function listado($servicio)
{
	include('variables.php');
	$cadena .="<p/><u><b>".$servicio." del centro</b></u><p/>";
	$sql = "SELECT c.Id,c.Nombre, z.valor, z.servicio, 
	(
	SELECT valor
	FROM z_sercont
	WHERE servicio LIKE 'Codigo Negocio'
	AND idemp LIKE z.idemp
	LIMIT 1
	) AS Despacho, c.Categoria
	FROM clientes AS c
	INNER JOIN z_sercont AS z ON c.Id = z.idemp
	WHERE z.servicio LIKE '$servicio'
	ORDER BY Despacho";
	$consulta = mysql_query($sql,$con);
	$cadena .="<table><tr>";
	$i=0;
	if (mysql_numrows($consulta)!=0)
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			if(ereg("despacho",$resultado[5]))
				$color="#69C";
			else
			if (ereg("domicili",$resultado[5]))
				$color="#F90";
			else
				$color="#ccc";
			if($i%4==0)
			$cadena .="</tr><tr>";
			$cadena .= "<th bgcolor='".$color."' align='left'>
			<a href='javascript:muestra($resultado[0])'>".$resultado[4]."-".traduce($resultado[1])."-
			<u><b>".$resultado[2]."</b></u></a></th>";
			$i++;
		}
	$cadena .="</tr></table>";
	
	return $cadena;
}
/*
 * Parte de los contratos
 */
function avisos_new()
{
//que queremos avisar principalmente fin de contratos en el dia y en el mes tanto de clientes como
//de proveedores. De donde se coge ese dato, de la tabla facturacion
/*AUDITAREMOS campos finicio, duracion, valores de duracion dias-espacio es decir 1-H, 1-D, 1-S, 1-M, 1-A 
Clientes (facturacion)
1.- Fecha inicio + duracion
2.- Dia de Pago - Si es hoy el dia del mes de pago
Proveedores (z_facturacion)
1.- Fecha inicio + duracion
2.- Dia de Pago - Si es hoy el dia del mes de pago
*/
global $dbname, $con;
$hnocump = 0;
$k=0;
$cadena ="<table width='100%'>";
//$cadena .= "<tr><th><span class='boton' onclick='cierralo()' onkeypress='cierralo()'>[X] Cerrar</span></th></tr>";
//$cadena .= "<tr><th colspan='2'>AVISOS</th></tr>";
//Clientes FInalizan HOY
$cadena .= "<tr><th Colspan='2'>Hoy finalizan contrato</th></tr>";
$sql = "SELECT facturacion.id, 
	facturacion.idemp, 
	facturacion.finicio, 
	facturacion.duracion, 
	facturacion.renovacion, 
	clientes.Nombre
FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
WHERE date_format(renovacion,'%d %c %y') LIKE date_format(curdate(),'%d %c %y') and clientes.Estado_de_cliente != 0";
$consulta = mysql_query($sql,$con);
$total = mysql_numrows($consulta);
	if ($total >= 1)
	{
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			$cadena .="<tr><td class='".clase($k++)."'><a href='javascript:muestra(".$resultado[1].")' >".traduce($resultado[5])."</a></td></tr>";
		}
	}
else{
		$hnocump++;
		$cadena.="<tr><td class='".clase($k++)."' colspan='2'>Nadie Finaliza contrato hoy</td></tr>";
	}
$cadena .= "</table>";
//return $cadena;
//Clientes que finalizan contrato este mes
//Clientes FInalizan este mes
$cadena .= "<table width='100%'>";
$cadena .= "<tr><th>Dia</th><th>Finalizan contrato este mes</th></tr>";
$sql = "SELECT facturacion.id, 
	facturacion.idemp, 
	facturacion.finicio, 
	facturacion.duracion, 
	facturacion.renovacion, 
	clientes.Nombre
FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
WHERE month(renovacion) LIKE month(curdate()) and year(renovacion) like year(curdate()) and clientes.Estado_de_cliente != 0 order by renovacion asc";
$consulta = mysql_query($sql,$con);
$total = mysql_numrows($consulta);
	if ($total >= 1)
	{
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			$cadena .="<tr><td class='".clase($k)."'>".cambiaf($resultado[4])."</td><td class='".clase($k)."'><a href='javascript:muestra(".$resultado[1].")' >".traduce($resultado[5])."</a></td></tr>";
			$k++;
		}
	}
else{
		$hnocump++;
		$cadena.="<tr><td colspan='2' class='".clase($k++)."'>Nadie Finaliza contrato este mes</td></tr>";
	}
$cadena .= "</table>";
//Clientes que finalizan contrato dentro de los proximos 60 dias
//Clientes FInalizan este mes
$cadena .= "<table width='100%'>";
$cadena .= "<tr><th>Dia</th><th>Finalizan contrato en los proximos 60 dias</th></tr>";
$sql = "SELECT facturacion.id, 
	facturacion.idemp, 
	facturacion.finicio, 
	facturacion.duracion, 
	facturacion.renovacion, 
	clientes.Nombre
FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
WHERE (CURDATE() <= renovacion) and (DATE_ADD(CURDATE(),INTERVAL 60 DAY)) >= renovacion and clientes.Estado_de_cliente != 0 order by Month(renovacion) asc, DAY(renovacion) asc";
$consulta = mysql_query($sql,$con);
$total = mysql_numrows($consulta);
	if ($total >= 1)
	{
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			$cadena .="<tr><td class='".clase($k)."'>".cambiaf($resultado[4])."</td><td class='".clase($k)."'><a href='javascript:muestra(".$resultado[1].")' >".traduce($resultado[5])."</a></td></tr>";
			$k++;
		}
	}
else{
		$hnocump++;
		$cadena.="<tr><td colspan='2' class='".clase($k++)."'>Nadie Finaliza contrato en los proximos 60 dias</td></tr>";
	}
$cadena .= "</table>";
return $cadena;
}
function invierte($fecha)
{
    $reves = explode("-",$fecha);
    return $reves[1]."-".$reves[0];
}
?>