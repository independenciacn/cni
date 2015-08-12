<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- <html> -->
<!-- <head> -->
<!-- <link href="../estilo/cni.css" rel="stylesheet" type="text/css"></link> -->
<!-- <title>Aplicacion Gestion Independencia Centro Negocios </title> -->
<!-- <script src='js/entradas.js' type="text/javascript"></script> -->
<!-- <script src='../js/prototype.js' type="text/javascript"></script> -->
<!-- </head> -->
<!-- <body> -->
<!-- 	Cuadro de Entradas<br/> -->
// <?
// 	include("../inc/tabla.php");
// 	$domicilia=array("Domicili%Basi%","Domicili%Integra%","Domicili%Espe%");
// 	$titulo=array("Basica","Integral","Especial");
// 	$ocupacion=array("Sala","Despacho");
// 	$meses_texto=array("","Enero","Febrero","Marzo","Abril","Mayo","Junio",
// 	"Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
// 	/*Calculo de años en la facturacion*/
// 	$sql = "Select year(fecha) from regfacturas group by year(fecha) order by year(fecha)";
// 	$anyos = datos_columna_simple($sql);
// 	/*Dibujamos la tabla base*/
// 	//Tantas tablas como años
// 	/*Tabla de ocupaciones puntuales*/
// 	foreach($anyos as $anyo)
// 	{
// 	$cadena.="<table class='tabla'>";
// 	$cadena.="<tr><th>Ocupacion Puntual ".$anyo."</th>";
// 	for($i=1;$i<=12;$i++)
// 		$cadena.="<th align='center'>".$meses_texto[$i]."</th>";
// 	$cadena.="<th>Acumulado</th></tr>";
// 	foreach($ocupacion as $clave => $tit)
// 	{
// 		$cadena.="<tr><td class='impar' align='center'>".$tit." jc</td>";
// 		//Volcamos los datos de todos los meses en el array
// 		for($j=0;$j<=12;$j++)
// 		{
// 			$jc[$j]=calcula_sala($tit,$j,$anyo,"Jornada Completa");
// 			if($j==12)
// 				$cadena.="<td class='impar' align='center'>".array_sum($jc)."</td>";
// 			else
// 				$cadena.="<td class='impar' align='center'>".$jc[$j]."</td>";
		
// 		}
// 		$cadena.="</tr><tr><td class='par' align='center'>".$tit." mj</td>";
// 		for($j=0;$j<=12;$j++)
// 		{
// 			$mj[$j]=calcula_sala($tit,$j,$anyo,"Media Jornada");
// 			if($j==12)
// 				$cadena.="<td class='par' align='center'>".array_sum($mj)."</td>";
// 			else
// 				$cadena.="<td class='par' align='center'>".$mj[$j]."</td>";
// 		}
// 		$cadena.="</tr><tr><th>Total ".$tit."</th>";
// 		for($j=0;$j<=12;$j++)
// 		{	
// 			if($j==12)
// 			$cadena.="<th>".(array_sum($jc)+array_sum($mj))."</th>";
// 			else
// 			$cadena.="<th>".($jc[$j]+$mj[$j])."</th>";
// 		}
// 		$cadena.="</tr>";
// 	}
// 	/*
// 	 * Parte del despacho/sala horas
// 	 */
// 	$cadena.="<tr><td class='impar'>Despacho/sala horas</td>";
// 	for($j=0;$j<12;$j++)
// 		$cadena.="<td class='impar'>--</td>";
// 	$cadena.="<td class='impar'>--</td>";
// 	$cadena.="</tr>";
// 	$cadena.="</tabla><br>";
// 	}
// 	$cadena.="<br>";
// 	/*Tabla de domiciliaciones en DESARROLLO*/
// 	foreach($anyos as $anyo)
// 	{
// 	$cadena.="<table class='tabla'>";
// 	$cadena.="<tr><th>Domiciliaci&oacute;n ".$anyo."</th>";
// 	for($i=1;$i<=12;$i++)
// 		$cadena.="<th align='center'>".$meses_texto[$i]."</th>";
// 	$cadena.="<th>Acumulado</th></tr>";
// 	foreach($titulo as $clave => $tit)
// 	{
// 		$cadena.="<tr><td class='impar' align='center'>".$tit." (entrada)</td>";
// 		//Volcamos los datos de todos los meses en el array
// 		for($j=0;$j<=12;$j++)
// 		{
// 			$lista = lista_entradas($domicilia[$clave],$j,$anyo);
// 			if(is_array($lista_anterior))
// 				$dato = compara_arrays($lista,$lista_anterior,"entrada");
// 			else
// 				$dato = "--";
// 			$cadena.="<td class='impar' align='center'>".$dato."</td>";
// 			$lista_anterior = $lista;
// 		}
// 		$cadena.="</tr><tr><td class='par' align='center'>".$tit." (salida)</td>";
// 		for($j=0;$j<=12;$j++)
// 		{
// 			$lista = lista_entradas($domicilia[$clave],$j,$anyo);
// 			if(is_array($lista_anterior))
// 				$dato = compara_arrays($lista,$lista_anterior,"salida");
// 			else
// 				$dato = "--";
// 			$cadena.="<td class='par' align='center'>".$dato."</td>";
// 			$lista_anterior = $lista;
// 		}
// 		$cadena.="</tr><tr><th>Total domic. ".$tit."</th>";
// 		for($j=0;$j<=12;$j++)
// 		{
// 			$dato=round(calcula_totales($domicilia[$clave],$j,$anyo),2);
// 			if($j==12)
// 				$dato = $aux;
// 			$cadena.="<th>".$dato."</th>";
// 			$aux=$dato;
// 		}
// 		$cadena.="</tr>";
// 	}
// 	$cadena.="</tabla><br>";
// }
// $cadena.="<br>";
// echo $cadena;

// /*************************Funciones****************************/
// function calcula_totales($tit,$mes,$anyo)
// {
// 	include("../inc/variables.php");
// 	$mes = $mes +1;
// 	if($mes >= 8 && $anyo == 2007 || $anyo >= 2008)
// 	{
// 	$sql="select r.id_cliente, sum(h.cantidad) from historico h inner join
// 	regfacturas r on h.factura like r.codigo
// 	where servicio like '$tit'
// 	and month(r.fecha) like $mes
// 	and year(r.fecha) like $anyo group by r.id_cliente";
// 	$consulta = mysql_query($sql,$con);
// 	$cadena .= mysql_num_rows($consulta);
// 	}
// 	else
// 		$cadena = "--";			
// 	return $cadena;
// }	
// function lista_entradas($tit,$mes,$anyo)
// {
// 	include("../inc/variables.php");
// 	$mes = $mes +1;
// 	if($mes >= 8 && $anyo == 2007 || $anyo >= 2008)
// 	{
// 	$sql="select r.id_cliente, sum(h.cantidad) from historico h inner join
// 	regfacturas r on h.factura like r.codigo
// 	where servicio like '$tit'
// 	and month(r.fecha) like $mes
// 	and year(r.fecha) like $anyo group by r.id_cliente";
// 	$consulta = mysql_query($sql,$con);
// 	while(true == ($resultado = mysql_fetch_array($consulta)))
// 		$cadena[]= $resultado;
// 	return $cadena;
// 	}
// 	else
// 	return false;
// }
// function compara_arrays($nueva,$anterior,$tipo)
// {
// 	$entrada=0;
// 	$sale=0;
// 	if($tipo=="entrada")
// 	{
// 		if(is_array($nueva))
// 		{
// 			foreach($nueva as $new)
// 			{
// 				foreach($anterior as $ant)
// 					$check[$ant[0]]=$ant[1];//array axiliar
// 				if(!array_key_exists($new[0],$check))
// 					$entrada++;
// 			}
// 		}
// 		else
// 			$entrada=0;
// 	}
// 	else
// 	{
// 		if((is_array($anterior))&&(is_array($nueva)))
// 		{
// 			foreach($anterior as $new)
// 			{
// 				foreach($nueva as $ant)
// 					$check[$ant[0]]=$ant[1];//array axiliar
// 				if(!array_key_exists($new[0],$check))
// 					$entrada++;
// 			}
// 		}
// 		else
// 			$entrada=0;
// 	}
// 	return $entrada;
// }
// function calcula_sala($tit,$j,$anyo,$tipo)
// {
// 	$j=$j+1;
// 	include("../inc/variables.php");
// 	$sql="SELECT sum(h.cantidad)
// 	FROM historico h 
// 	inner join regfacturas r on h.factura = r.codigo
// 	inner join clientes c on r.id_cliente = c.id
// 	where h.servicio like '$tit%'
// 	and !Locate('Clientes',h.servicio)
// 	and !Locate('$tipo',h.servicio)
// 	and !Locate('(una hora)',h.servicio)
// 	and month(r.fecha) like $j
// 	and year(r.fecha) like $anyo
// 	and c.categoria like 'Clientes externos'
// 	order by r.fecha";
// 	$consulta = @mysql_db_query($dbname,$sql,$con);
// 	$resultado = @mysql_fetch_array($consulta);
// 	return round($resultado[0]);
// }	
?>	
<!-- </body>	 -->
<!-- </html> -->