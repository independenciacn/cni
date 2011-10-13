<?php
    session_start();
	function cambiaf($stamp)
	{
		$fdia = explode("-",$stamp);
		$fdia2 = explode(" ",$fdia[2]);
		$fecha = $fdia2[0]."-".$fdia[1]."-".$fdia[0];
		return $fecha;
	}
	
	if(isset($_SESSION[titulo]))
	{
		include("../inc/variables.php");
		//echo $_SESSION[consulta];
		$sql = stripslashes($_GET[sql]);
		//echo $sql;
		$consulta = @mysql_db_query($dbname,$sql,$con);
		$cadena.="<table class='tabla' width='100%'><tr>";
		$cadena.="<tr><th colspan='".@mysql_num_fields($consulta)."'>".$_SESSION[titulo]."</th></tr>";
		if(@mysql_numrows($consulta)>=10000 || @mysql_numrows($consulta)==0)
		{
			if(@mysql_numrows($consulta)>=10000)
				$cadena.="<tr><th colspan='".@mysql_num_fields($consulta)."'>Demasiados Resultados. Filtre Mas</th></tr>";
			else
				$cadena.="<tr><th colspan='".@mysql_num_fields($consulta)."'>No hay Resultados.</th></tr>";
		}
		else
		{
			for($i=0;$i<=@mysql_num_fields($consulta)-1;$i++)
				$cadena.= "<th>".@mysql_field_name($consulta,$i)."</th>";
			$cadena.="</tr>";
			$j=0;
			while($resultado = @mysql_fetch_array($consulta))
			{
				$j++;
				if($j%2==0)
					$clase = "par";
				else
					$clase = "impar";
				$cadena."<tr>";
				for($i=0;$i<=@mysql_num_fields($consulta)-1;$i++)
				{
					switch(@mysql_field_type($consulta,$i))
					{
						case "string":$campo = utf8_encode($resultado[$i]);break;
						case "real":$campo = number_format($resultado[$i],2,',','.');
									$tot[$i]=$tot[$i]+$resultado[$i];break;
						case "date":$campo = cambiaf($resultado[$i]);break;
						default:$campo = $resultado[$i];
								$tot[$i] ="";break;
					}
					$cadena.="<td class='".$clase."'>".$campo."</td>";
				}
				$cadena.="</tr>";
			}
			$cadena.="<tr>";
			for($i=0;$i<=@mysql_num_fields($consulta)-1;$i++)
			{
				switch(@mysql_field_type($consulta,$i))
				{
					case "string":$cadena.="<th></th>";break;
					case "real":$cadena.="<th>".number_format($tot[$i],2,',','.')."</th>";break;
					default:$cadena.="<th></th>";break;
				}
			}
		}
		$cadena.="</tr>";
		$cadena.="</table>";
		$cadena.="<div id='titulo'>Total Resultados: ".@mysql_numrows($consulta)."</div>";
		//echo $cadena;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<link href="estilo/print.css" rel="stylesheet" type="text/css"></link>
<title>Aplicacion Gestion Independencia Centro Negocios </title>
<body>
	<span class='volver' onclick='window.history.back()'><- Volver</span>
	<? echo $cadena; ?>
</body>
</html>