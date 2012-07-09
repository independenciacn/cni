// <?php 
// /**
//  * NO USADO - BORRAR
//  */
// ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- <html> -->
<!-- <head> -->
<!-- <link href="estilo/servicont.css" rel="stylesheet" type="text/css"></link> -->
<!-- <title>Aplicacion Gestion Independencia Centro Negocios </title> -->
<!-- </head> -->
<!-- <body> -->
// <?php
//     include("../inc/variables.php");
// 	//$sqls[] = "select * from regfacturas where id_cliente like 28";
// 	//$sqls[] = "select * from historico where cliente like 28";
// 	$sqls[] = "select h.Servicio, h.cantidad, h.unitario, h.iva, r.Fecha from historico h inner join regfacturas r on h.factura like r.codigo where r.id_cliente like 28 group by h.servicio order by h.servicio";
// 	//$sqls[] = "select Servicio from tarifa_cliente where ID_Cliente like 28 group by Servicio";
// 	foreach($sqls as $sql)
// 	{
// 		echo tabla($sql);
// 	}
// $sql = "select Servicio from tarifa_cliente where ID_Cliente like 28 group by Servicio";
// $consulta = mysql_query($sql,$con);
// while(true == ($resultado = mysql_fetch_array($consulta)))
// {
// 	//$resultado[0]=utf8_encode($resultado[0]);
// 	$sql2 = "select h.servicio, h.cantidad, h.unitario, h.iva, r.id_cliente, r.fecha from historico h inner join regfacturas r on h.factura like r.codigo where r.id_cliente like 28 and servicio like '$resultado[0]%'";
// 	//$sql2 = "Select * from historico where servicio like '$resultado[0]%'";
// 	echo $sql2;
// 	echo tabla($sql2);
// }
// function nombre_raiz_servicio($servicio)
// {
// 	$cadena = explode("(",$servicio);
// 	return $cadena[0];
// }
// function tabla($sql)
// {
// 	include("../inc/variables.php");
// 	$consulta = mysql_query($sql,$con);
// 	$cadena.="<table class='tabla' width='100%'>";
// 	$cadena.= "<tr>";
// 		for($i=0;$i<=mysql_num_fields($consulta)-1;$i++)
// 			$cadena.="<th>".ucfirst(mysql_field_name($consulta,$i))."</th>";
// 		$cadena.="</tr>";
// 		while(true == ($resultado = mysql_fetch_array($consulta)))
// 		{
// 			$j++;
// 			if($j%2==0)
// 				$class="par";
// 			else
// 				$class="impar";
// 			$cadena.= "<tr>";
// 			for($i=0;$i<=mysql_num_fields($consulta)-1;$i++)
// 				{
// 				if(mysql_field_name($consulta,$i)== "servicio")
// 					$resultado[$i]=nombre_raiz_servicio($resultado[$i]);
// 				$resultado[$i]=chuminadas($resultado[$i]);
// 				$cadena.= "<td class=".$class.">".$resultado[$i]."</td>";
// 				}
// 			$cadena.= "</tr>";
// 		}
// 		$cadena.= "</table>";
// 		return $cadena;
// }
// function chuminadas($cadena)
// {
// 	$params = array("Bultos Almacenados del","Clientes Sala Sin Cargo","Consumo Teléfono");
// 	$fijos = array("Almacenaje","Clientes Sala Sin Cargo","Consumo Teléfono");
// 	for($i=0;$i<=count($params)-1;$i++)
// 	{
// 		if(strncmp($params[$i],$cadena,strlen($params[$i]))==0)
// 			$cadena = $fijos[$i];
// 	}
// 	return $cadena;
	
// }
// ?>
<!-- </body> -->
<!-- </html> -->