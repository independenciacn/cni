<?php /*Sencillo genera tablas pasandole el sql*/
/**
 * @deprecated - Obsoleto, chequear y borrar
 */
/*
require_once 'variables.php';
checkSession();
function tabla($sql)
{
	global $con;
	$consulta = mysql_query($sql,$con);
	$cadena.="<table class='tabla' width='100%'>";
	$cadena.= "<tr>";
		for($i=0;$i<=mysql_num_fields($consulta)-1;$i++)
			$cadena.="<th>".ucfirst(mysql_field_name($consulta,$i))."</th>";
		$cadena.="</tr>";
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			$j++;
			if($j%2==0)
				$class="par";
			else
				$class="impar";
			$cadena.= "<tr>";
			for($i=0;$i<=mysql_num_fields($consulta)-1;$i++)
				{
					if(mysql_field_type($consulta,$i)=="date")
						{
							$resultado[$i]=cambiaf($resultado[$i]);
						}
					if(mysql_field_type($consulta,$i)=="int")
							$resultado[$i]=mes_y_anyo($resultado[$i]);
					$cadena.= "<td class=".$class.">".$resultado[$i]."</td>";
				}
			$cadena.= "</tr>";
		}
		$cadena.= "</table>";
		return $cadena;
}
/*Funcion que devuelve un array con los datos pedidos UNA SOLA COLUMNA*/
/*
function datos_columna($sql)
{
	//$matriz = array_fill(0, 12, '');
	include("variables.php");
	$consulta = @mysql_db_query($dbname,$sql,$con);
	while($resultado = @mysql_fetch_array($consulta))
	$matriz[$resultado[1]]=$resultado[0];
	return $matriz;
}
function datos_columna_simple($sql)
{
	include("variables.php");
	$consulta = @mysql_db_query($dbname,$sql,$con);
	while($resultado = @mysql_fetch_array($consulta))
	$matriz[]=$resultado[0];
	return $matriz;
}

function cambiaf($stamp)
{
	$fdia = explode("-",$stamp);
	$fdia2 = explode(" ",$fdia[2]);
	$fecha = $fdia2[0]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}
function mes_y_anyo($stamp)
{
	
	switch($stamp)
	{
		case 1:$fecha = "Enero";break;
		case 2:$fecha = "Febrero";break;
		case 3:$fecha = "Marzo";break;
		case 4:$fecha = "Abril";break;
		case 5:$fecha = "Mayo";break;
		case 6:$fecha = "Junio";break;
		case 7:$fecha = "Julio";break;
		case 8:$fecha = "Agosto";break;
		case 9:$fecha = "Septiembre";break;
		case 10:$fecha = "Octubre";break;
		case 11:$fecha = "Noviembre";break;
		case 12:$fecha = "Diciembre";break;
	}
	return $fecha;
}
*/

