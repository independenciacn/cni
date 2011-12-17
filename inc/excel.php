<?php 
// TODO: Revisar con excel si hay problemas con los caracteres
//rarita en excel Listado Despachos y Domiciliados:
require_once 'variables.php';
$html = "";
sanitize( $_GET );
$i = 0;
	if ( isset( $_GET['tipo'] ) ){
		if ( $_GET['tipo'] == 'social' ) {
			$sql ="Select * from clientes where 
			direccion like '%Independencia 8%' and 
			Estado_de_cliente like '-1' order by Nombre";
		} else {
			if ( $_GET['tipo'] == 'conserje' ) {
				$sql = "Select * from clientes 
				where (categoria like '%domicili%' or 
				categoria like '%despachos%') and 
				Estado_de_cliente like '-1' order by Nombre";
			} else {
				$sql = "Select * from clientes as c 
				join `categorías clientes` as d on c.Categoria = d.Nombre 
				where d.id like " . $_GET['tipo'] . " and 
				c.Estado_de_cliente like '-1' order by c.Nombre";
			}
		}
		$consulta = mysql_query( $sql, $con );
		$html .= "<table>";
		$res1 = mysql_fetch_array($consulta);
		if ( $_GET['tipo'] != 'social' ){
			if ( $_GET['tipo'] == 'conserje' ) {
				$html .= "<tr><th>Listado Clientes Centro Negocios</th></tr>";
			} else {
				$html .= "<tr><th>" . $res1['Categoria'] . "</th></tr>";
			}
		} else {
			$html.="<tr><th>Domicilio social Independencia 8 Dpo</th></tr>";
		}
		while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
			$html.="<tr><td>".$resultado[1]."</td></tr>";
		}
		$html.="</table>";	
	} else {
		$sql = "SELECT z.valor, c.Nombre, c.Categoria, f.observaciones FROM `facturacion` 
			as f join clientes as c on c.id like f.idemp join z_sercont as z on z.idemp like 
			c.id WHERE  Estado_de_cliente != 0 and 
			(c.Categoria like '%domiciliac%' or c.Categoria like '%despacho%') and 
			z.servicio like 'Codigo Negocio' order by z.valor asc";
		$consulta = mysql_query( $sql, $con );
		$html = "<table width='100%' cellpadding='1px' cellspacing='1px' 
			style={border-style:solid;border-width:1px;}>";
		$html .= "<tr><th>Codigo</th><th>Cliente</th><th>Categoria</th><th>Observaciones</th></tr>";
		while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
			$html .= "<tr><td>".$resultado[0]."</td>
			<td>".$resultado[1]."</td>
			<td>".$resultado[2]."</td>
			<td>".$resultado[3]."</td></tr>";
		}
		$html .= "</table>";
	}
	header("Content-type: application/vnd.ms-excel; charset: UTF-8");
	header("Content-Disposition:inline; filename=listado.xls");
	echo $html;
	exit(0);