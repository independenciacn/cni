<?php
require_once 'configuracion.php';
if ( isset( $_SESSION['usuario'] ) ) {
	if ( isset( $_POST['opcion'] ) ) {
		if ( $_POST['opcion'] == 0 ) {
			$html = avisos();
		} elseif ( $_POST['opcion'] == 1) {
			$html = telefonos();
		}
	} else {
		$html = avisos();
	}
	echo $html;
}


/**
 * Seleccion de telefonos no se de donde Borrar o poner en su sitio
 * 
 * @todo Borrar o poner el el sitio
 */
function telefonos()
{
	
	$cadena.="<input type='button' value='[v]Ocultar telefonos' onclick='cerrar_tablon_telefonos()'/>";
	$cadena .= listado('Telefono');
	$cadena .= listado('Fax');
	$cadena .= listado('Adsl');
	return $cadena;
}
/**
 * Saca un listado, no se de donde Borrar o poner en su sitio
 * 
 * @todo Borrar o poner en el sitio
 * @param unknown_type $servicio
 * 
 */
function listado($servicio)
{
	global $con;
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
	$consulta = mysql_query( $sql, $con );
	$cadena .="<table><tr>";
	$i=0;
	if (mysql_numrows($consulta)!=0)
		while (true == ( $resultado = mysql_fetch_array( $consulta ) ) )
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
			<a href='javascript:muestra($resultado[0])'>".$resultado[4]."-".$resultado[1]."-
			<u><b>".$resultado[2]."</b></u></a></th>";
			$i++;
		}
	$cadena .="</tr></table>";
	
	return $cadena;
}