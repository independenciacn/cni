<?php session_start();
require_once '../inc/variables.php';
/*
 * Inicializamos la fecha que se definiria como hoy que 
 * sera el punto de partida de 
 * todas las operaciones del calendario
 */
$meses=array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$dia=array("","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado","Domingo");
if(isset($_POST[fecha]))
	{
		$fdia = explode("-",$_POST[fecha]);
		$hoy = $_POST[fecha];
		$semana = date("W", mktime(0, 0, 0, $fdia[1], $fdia[0], $fdia[2]));
		$mes=$meses[$fdia[1]];
		$anyo=$fdia[2];
		$hoy_es=date("N", mktime(0, 0, 0, $fdia[1], $fdia[0], $fdia[2]));
	}
else
	{
		
		$hoy = date("d-m-Y");
		$semana = date("W");
		$mes=$meses[date("n")];
		$anyo=date("Y");
		$hoy_es=date("N");
	}

?>
<form name='seleccion_fecha' method='post' action='' onsubmit='cambia_fecha()'>
<label>Seleccionar Fecha: </label><input type='text' id='semana' name='semana' size ='10'  value='<? echo $hoy; ?>' onchange='cambia_fecha()' />
<button type='button' class='boton' id='f_trigger_semana' >...</button>
<input type='hidden' id='seccion' value='1' />
<? echo $mes." de ".$anyo.". Semana: ". $semana;?>
</form>
<table style={width:100%;} id='semana'>
<?php
/*
 * Calculamos las semanas totales del año actual
 * Dibujo la tabla luego la lleno
 */
$cadena.="<tr><th width='10%'></th>";
for($k=1;$k<=7;$k++)
{
	$dia_anyo[$k] = ver_dia($k,$hoy_es,$hoy);
	$borde="";
	if($k==$hoy_es)
		$cadena.="<th width='10%'class='hoy'>".$dia[$k]." ".$dia_anyo[$k]."</th>";
	else
		$cadena.="<th width='10%'>".$dia[$k]." ".$dia_anyo[$k]."</th>";
}
	$cadena.="</tr>";

/*
 * Llenado de horas horas enteras con medias dentro
 */
for($j=8;$j<=21;$j++)
	{
		$hora = $j.":00";
		$cadena.="<tr><th>".$hora."</th>";
		for($i=1;$i<=7;$i++)
			{
			if($i==$hoy_es)
				$borde="style='border-color:#aa0086;background:white;color:black;'";
			else
				$borde="style='background:white;color:black;'";
			$cadena.="<td $borde  valign='top'>
			<span class='mini_boton' onclick='formulario_interna($j,\"$dia_anyo[$i]\")'>&nbsp;+&nbsp;</span>".chequea_celda($hora,$dia_anyo[$i])."</td>";
			}
		$cadena.="</tr>";
	}
echo $cadena;

/*
 * FUNCIONES 
 */
 
/*
 * Funcion del cambio de fecha
 */
function cambiaf($stamp)
{
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}

/*
 * Funcion que genera el dia que es cada columna
 */
function ver_dia($k,$hoy_es,$hoy)
{
	$fdia = explode("-",$hoy);
	if($k==$hoy_es)
		$fecha=$hoy;
	else
		if($k<$hoy_es)
		{
			$diferencia = $hoy_es - $k;
			$fecha = date("d-m-Y",mktime(0, 0, 0, $fdia[1]  , $fdia[0]-$diferencia, $fdia[2]));
		}
		else
		{
			$diferencia = $k - $hoy_es;
		 	$fecha = date("d-m-Y",mktime(0, 0, 0, $fdia[1]  , $fdia[0]+$diferencia, $fdia[2]));
		}
	return $fecha;
}

/*
 * Devuelve el valor de la celda asignada a ver si tiene alguna tarea
 */
function chequea_celda($hora,$dia)
{
	global $con;
	$semana = array("","L","M","X","J","V","S","D");
	$dia =cambiaf($dia);
	//Chequemos si la celda tiene otro color
	$sql = "Select * from agenda_interna_estado where hour(hora) like hour('$hora') and dia like '$dia'";
	//$cadena.=$sql;
	$consulta = mysql_query($sql,$con);
	if(@mysql_numrows($consulta)!=0)
	{
		
		while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
		{
			$color_mod[$resultado[id_tarea]] = $resultado[color];
			$tarea_mod[] = $resultado[id_tarea];
		}
	}
	$sql = "Select * from agenda_interna where hour(inicio) like hour('$hora')";
	$consulta = mysql_query($sql,$con);
	while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
	{
		if(isset($color_mod) && (in_array($resultado[id],$tarea_mod)))
			$color_celda = $color_mod[$resultado[id]];
		else
			$color_celda = $resultado[color];
		//Asignacion a piñon del color de texto segun el color de celda
		//'white','PaleGoldenRod','Pink','LightSteelBlue','PaleVioletRed '
		switch($color_celda)
		{
			/*case 'white':$color_txt = "Black";break;
			case 'PaleGoldenRod':$color_txt = "SaddleBrown";break;
			case 'LightSteelBlue':$color_txt = "DimGrey";break;*/
			default:$color_txt = "Black";break;
		}	
		$celda ="<div class='celda' style='background:$color_celda;color:$color_txt;font-weight:bold;' onclick='edita_tarea($resultado[0],\"$hora\",\"$dia\")'>".quita_segundos($resultado[inicio])." - ".$resultado[descripcion]."</div>";
		if($dia >= $resultado[dia])
		{
			/*'N','D','S','M','A','O'*/
			switch($resultado[repetir])
			{
				case "N":
					if($dia == $resultado[dia])
						$cadena.=$celda;
					else
						$cadena.="";
					break;
					
				case "D":$cadena.=$celda;break;
				case "S":
					if(date("w",strtotime($dia)) == date("w",strtotime($resultado[dia])))
						$cadena.=$celda;
					else
						$cadena.="";
					break;
				case "M":if(date("j",strtotime($dia)) == date("j",strtotime($resultado[dia])))
						$cadena.=$celda;
					else
						$cadena.="";
					break;
				case "A":if(date("z",strtotime($dia)) == date("z",strtotime($resultado[dia])))
						$cadena.=$celda;
					else
						$cadena.="";
					break;
				case "O":$dias=explode(";",$resultado[repeticion]);
						foreach($dias as $key=>$repes)
						{
							$clave=array_search($repes,$semana);
							if((date("w",strtotime($dia))==$clave) && $clave != 0)
								$cadena.=$celda." ".calculo_frecuencia($resultado[0],strtotime($dia));
							else
								$cadena.="";
						}				
				break;
				default:$cadena.="";break;
			}
		}
		else
			$cadena.="";
	}
	return $cadena;
}
/*
 * Funcion de calculo de la frecuencia
 */
 function calculo_frecuencia($id,$dia)
 {
 	/*1 semana es 604800 * frecuencia si coincide se muestra si
 	 * y tendra que ser multiplo
 	 */
	global $con;
	return "";//$id."-".$dia;
 }
/*
 * Funcion que posiciona el div en la celda
 */
function posiciona($hora,$tipo,$color)
{
	if($tipo==0)
	{
		$hora_inicio = explode(":",$hora);
		if(intval($hora_inicio[1])<=14)
			$posicion="";
		else
			if((intval($hora_inicio[1])>=15)&&(intval($hora_inicio[1])<=29))
				$posicion = "<div style='height:25%;'>$hora:00 -&nbsp;</div>";
	 		else 
				if((intval($hora_inicio[1])>=30)&&(intval($hora_inicio[1])<=44))
					$posicion="<div style='height:25%;'>$hora:00 -&nbsp;</div><div style='height:25%;'>$hora:15 -&nbsp;</div>";
		  		else
		  			$posicion="<div style='height:25%;'>$hora:00 -&nbsp;</div><div style='height:25%;'>$hora:15 -&nbsp;</div><div style='height:25%;'>$hora:30 -&nbsp;</div>";
	}
	else
	{
		$hora_fin = explode(":",$hora);
		if(intval($hora_fin[1])<=14)
			$posicion="";
		else
			if((intval($hora_fin[1])>=15)&&(intval($hora_fin[1])<=29))
				$posicion = "<div style='height:25%;background:$color;'>$hora:00 -&nbsp;</div>";
	 		else 
				if((intval($hora_fin[1])>=30)&&(intval($hora_fin[1])<=44))
					$posicion="<div style='height:25%;background:$color;'>$hora:00 -&nbsp;</div><div style='height:25%;background:$color;'>$hora:15 -&nbsp;</div>";
		  		else
		  			$posicion="<div style='height:25%;background:$color;'>$hora:00 -&nbsp;</div><div style='height:25%;background:$color;'>$hora:15 -&nbsp;</div><div style='height:25%;background:$color;'>$hora:30 -&nbsp;</div>";
	}	
	return $posicion;
}

/*
 * Funcion que quita los segundos de la hora
 */
function quita_segundos($hora)
{
	$sin_sec=explode(":",$hora);
	$final = $sin_sec[0].":".$sin_sec[1];
	return $final;
}
?>
</table>