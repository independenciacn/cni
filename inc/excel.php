<?php 
/**
 * Excel File Doc Comment
 *
 * Fichero que genera el listado en excel, los utf8_decode son necesarios para
 * que se visualize bien en excel
 *
 * PHP Version 5.2.6
 *
 * @category Excel
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
require_once 'variables.php';
checkSession();
if (isset($_GET)) {
    sanitize($_GET);
}
$cadena = "";	
if(isset($_GET['tipo'])) {
    if ($_GET['tipo']=='social') {
        $sql ="Select * from clientes where direccion not like ''
        and Estado_de_cliente like '-1' order by Nombre";
    } else {
        if($_GET['tipo']=='comercial') {
            $sql="Select * from clientes where dcomercial != ''
            and Estado_de_cliente like '-1' order by Nombre";
        } else {
            if($_GET['tipo']=='conserje') {
                $sql = "Select * from clientes where
                (categoria like '%domicili%' or categoria
                like '%despachos%' or categoria like '%tencion telefo%')
                and Estado_de_cliente like '-1' order by Nombre";
            } else {
                if($_GET['tipo']=='independencia') {
                    $sql ="Select * from clientes where direccion
                    like '%Independencia, 8 dpdo%'
                    and Estado_de_cliente like '-1' order by Nombre";
                } else {
                    $sql = "Select * from clientes as c join `categorías clientes`
                    as d on c.Categoria = d.Nombre
                    where d.id like ".$_GET['tipo']."
                    and c.Estado_de_cliente like '-1' order by c.Nombre";
                }
            }
        }
    }
    $consulta = mysql_query($sql,$con);
	$i=0;
	$fichero = "listado";
	$cadena.="<table>";
	$res1=mysql_fetch_array($consulta);
	if ($_GET['tipo']=='social') {
	    $cadena.="<tr><th>Listado Clientes Con Direcci&oacute;n de Facturaci&oacute;n</th></tr>";
	    $fichero = "Clientes Con direccion de facturacion";
	} else {
	    if($_GET['tipo']=='comercial') {
	        $cadena.="<tr><th>Listado Clientes Con Direcci&oacute;n de Contrato</th></tr>";
	        $fichero = "Clientes con Direccion de Contrato";
	    } else {
	        if($_GET['tipo']=='conserje') {
	            $cadena.="<tr><th>Listado Clientes Centro Negocios</th></tr>";
	            $fichero = "Listado Clientes Centro Negocios";
	        } else {
	            if($_GET['tipo']=='independencia') {
	            $cadena.="<tr><th>Domicilio social Independencia 8 Dpo</th></tr>";
	            $fichero = "Clientes con Domicilio social Independencia 8Dpo";
	            } else {
	                $cadena.="<tr><th>".utf8_decode($res1[2])."</th></tr>";
	                $fichero = $res1[2];
	            }
	        }
	    }
	}
	$consulta = mysql_query($sql,$con);
	$j=0;
	while(true == ($resultado = mysql_fetch_array($consulta))) {
		$cadena.="<tr><td>". ++$j." " .utf8_decode($resultado[1])."</td></tr>";
	}
	$cadena.="</table>";	
} else {
	$sql = "SELECT z.valor, c.Nombre, c.Categoria, f.observaciones FROM `facturacion` 
	as f join clientes as c on c.id like f.idemp join z_sercont as z on z.idemp like 
	c.id WHERE  Estado_de_cliente != 0 and 
	(c.Categoria like '%domiciliac%' or c.Categoria like '%despacho%') and 
	z.servicio like 'Codigo Negocio' order by z.valor asc";
	$consulta = mysql_query($sql,$con);
	$cadena = "<table width='100%' cellpadding='1px' cellspacing='1px' 
	style={border-style:solid;border-width:1px;}>";
	$cadena .= "<tr><th>Codigo</th><th>Cliente</th><th>Categoria</th>
	<th>Observaciones</th></tr>";
	while (true == ($resultado = mysql_fetch_array($consulta))) {
		$cadena .= "<tr><td>".$resultado[0]."</td>
		<td>".utf8_decode($resultado[1])."</td>
		<td>".utf8_decode($resultado[2])."</td>
		<td>".utf8_decode($resultado[3])."</td></tr>";
	}
	$cadena .= "</table>";
}
header("Content-type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:inline; filename=".urlencode( $fichero ).".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $cadena;
exit(0);