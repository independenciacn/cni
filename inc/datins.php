<?php
require_once 'variables.php';
$vars = $_POST; 
array_walk( $vars, 'sanitize' ); 
//datins.php Generacion de datos de interes para la aplicacion, avisos, cumplea�os, estadisticas, etc, lo vamos a controlar por opcion -> funcion si no es un kaos
/*Indice de datos
1 - Avisos
2 - Cumplea�os
*/
//GENERAL recibe - procesa - devuelve
switch ( $_POST['dato'] ) {
	case 1:
	    $datos = avisos();
	break;
	case 2:
	    $datos = cumples();
	break;
	case 3:
	    $datos = frmBusquedaAvanzada();
	break; 
}
echo $datos;
//backup base de datos
//system("/usr/local/mysql/bin/mysqldump -u cni -p inc $destino > back.sql");
//FUNCIONES
/*********************************AQUI EMPIEZA LA FUNCION DE LOS AVISOS********************************/
/*function avisos()
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
/*include("variables.php");
session_start();
$ssid = session_id();
$cadena .="<table class='tabla'>";
$cadena .= "<tr><th colspan='2' align='left'><span class='boton' onclick='cierralo()' onkeypress='cierralo()'>[X] Cerrar</span></td></tr>";
$cadena .= "<tr><th colspan='2'>AVISOS</th></tr>";
//Clientes FInalizan HOY
$cadena .= "<tr><th Colspan='2'>Hoy finalizan contrato</th></tr>";
$sql = "SELECT facturacion.id, 
	facturacion.idemp, 
	facturacion.finicio, 
	facturacion.duracion, 
	facturacion.renovacion, 
	clientes.Nombre
FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
WHERE date_format(renovacion,'%d %c %y') LIKE date_format(curdate(),'%d %c %y')";
$consulta = mysql_db_query($dbname,$sql,$con);
$total = mysql_numrows($consulta);
	if ($total >= 1)
	{
		while($resultado = mysql_fetch_array($consulta))
		{
			$cadena .="<tr><td><a href='javascript:muestra(".$resultado[1].")'>".traduce($resultado[5])."</a></td></tr>";
		}
	}
else
	$hnocump++;
//$cadena .= "</table>";
//return $cadena;
//Clientes que finalizan contrato este mes
//Clientes FInalizan este mes
//$cadena .= "<table>";
$cadena .= "<tr><th>Dia</th><th>Finalizan contrato este mes</th></tr>";
$sql = "SELECT facturacion.id, 
	facturacion.idemp, 
	facturacion.finicio, 
	facturacion.duracion, 
	facturacion.renovacion, 
	clientes.Nombre
FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
WHERE month(renovacion) LIKE month(curdate()) and year(renovacion) like year(curdate()) order by renovacion asc";
$consulta = mysql_db_query($dbname,$sql,$con);
$total = mysql_numrows($consulta);
	if ($total >= 1)
	{
		while($resultado = mysql_fetch_array($consulta))
		{
			$cadena .="<tr><td>".cambiaf($resultado[4])."</td><td><a href='javascript:muestra(".$resultado[1].")'>".traduce($resultado[5])."</a></td></tr>";
		}
	}
else
	$hnocump++;
//$cadena .= "</table>";
//Clientes que finalizan contrato dentro de los proximos 60 dias
//Clientes FInalizan este mes
//$cadena .= "<table>";
$cadena .= "<tr><th>Dia</th><th>Finalizan contrato en los proximos 60 dias</th></tr>";
$sql = "SELECT facturacion.id, 
	facturacion.idemp, 
	facturacion.finicio, 
	facturacion.duracion, 
	facturacion.renovacion, 
	clientes.Nombre
FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
WHERE (CURDATE() <= renovacion) and (DATE_ADD(CURDATE(),INTERVAL 60 DAY)) >= renovacion order by Month(renovacion) asc, DAY(renovacion) asc";
$consulta = mysql_db_query($dbname,$sql,$con);
$total = mysql_numrows($consulta);
	if ($total >= 1)
	{
		while($resultado = mysql_fetch_array($consulta))
		{
			$cadena .="<tr><td>".cambiaf2($resultado[4])."</td><td><a href='javascript:muestra(".$resultado[1].")'>".traduce($resultado[5])."</a></td></tr>";
		}
	}
else
	$hnocump++;
$cadena .= "</table>";
return $cadena;
}

/*********************************FIN DE LA FUNCION DE AVISOS**********************************************/

/*********************************AQUI EMPIEZA LA FUNCION DE LOS CUMPLEA�OS********************************/
//esta ahora en el fichero cumples.php
/**
 * Formulario busqueda avanzada
 * 
 * @return string
 */
function frmBusquedaAvanzada()
{
	$cadena = "
	<form id='busqueda_avanzada' onsubmit='busqueda_avanzada(); return false' >
	<table class='tabla'>
		<tr>
			<th aling='left'>
				<span class='boton' onclick='cierralo()' onkeypress='cierralo()'>
				[X] Cerrar
				</span>
			</th>
			<th>
				Busqueda Avanzada
			</th>
		</tr>
		<tr>
			<th colspan='2'>
				<input type='text' name='texto' size='40'/>
				<input type='submit' name='Buscar' value='Buscar' />
			</th>
		</tr>
		<tr>
			<td colspan='2'>
				<div id='resultados_busqueda_avanzada'></div>
			</td>
		</tr>
	</table>
	</form>";
	return $cadena;
}
