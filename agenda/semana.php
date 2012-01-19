<?php
/*
 * Inicializamos la fecha que se definiria como hoy que sera el
 * punto de partida de todas las operaciones del calendario
 */
require_once '../inc/variables.php';
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
<form name='seleccion_fecha' method='' action='' onsubmit='cambia_fecha();return false'>
<label>Seleccionar Fecha: </label><input type='text' id='semana'  name='semana' size ='10'  value='<? echo $hoy; ?>' onchange=cambia_fecha() />
<button type='button' class='boton' id='f_trigger_semana' >...</button>
<input type='hidden' id='seccion' value='0' />
<? echo $mes." de ".$anyo.". Semana: ". $semana;?>
</form>

<table id='semana'  width='100%'>
<?php
/*
 * Calculamos las semanas totales del año actual
 * Dibujo la tabla luego la lleno
 */
$cadena.="<tr><th></th>";
for($k=1;$k<=7;$k++)
{
	$dia_anyo[$k] = ver_dia($k,$hoy_es,$hoy);
	$borde="";
	if($k==$hoy_es)
		$cadena.="<th width='10%' class='hoy'>".$dia[$k]."<br>".$dia_anyo[$k]."</th>";
	else
		$cadena.="<th width='10%'>".$dia[$k]."<br>".$dia_anyo[$k]."</th>";
}
$cadena.="</tr>";
for($j=1;$j<=36;$j++)
{
	if(despacho_ocupado($j)==0)
	{
		if($j == 23)
			$muestra = "Sala de Juntas";
		else
			$muestra = "Despacho ".$j;
		$cadena.="<tr><th width='10%'>".$muestra."<br/></th>";
		for($i=1;$i<=7;$i++)
			{
			if($i==$hoy_es)
				$borde="";
			else
				$borde="";
			$cadena.="<td $borde valign='top'><span class='mini_boton' onclick='formulario_despacho_semana($j,\"$dia_anyo[$i]\")'>&nbsp;+&nbsp;</span>".datos_ocupacion($j,$dia_anyo[$i])."<p/></td>";
			}
		$cadena.="</tr>";
		}
	}
echo $cadena;

/*
 * Cambia fecha a modo sql e inversa
 */
function cambiaf($stamp) 
{
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}

/*
 * Devuelve el nombre del cliente
 */
function nombre_cliente($id)
{
	global $con;
	$sql="Select Nombre from clientes where id like $id";
	$consulta = @mysql_query($sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	return $resultado['Nombre'];
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
 * Chequea si el despacho tiene ocupacion total, si es asi no lo muestra
 */
function despacho_ocupado($despacho)
{
	global $con;
	//parche para libre
	//if($despacho == 23)
		//$param = "LIBRE JUNTAS";
	//else 
		//$param = "LIBRE ".$despacho;
	//$sql = "Select * from clientes where Nombre like '$param'";
	//$consulta = @mysql_query($sql,$con);
	//if(@mysql_numrows($consulta!=0))
		//$ocupado=0;
	//else
	//{
		if($despacho<=9)
			$despacho = "000".$despacho;
		else
			$despacho = "00".$despacho;
		$sql="Select * from z_sercont where servicio like 'Codigo Negocio' and valor like '$despacho'";
		$consulta = @mysql_query($sql,$con);
		if(@mysql_numrows($consulta)!=0)
			$ocupado=1;
		else
			$ocupado=0;
	//}
	return $ocupado;
}

/*
 * Devuelve el nombre del cliente si ese despacho esta ocupado ese dia
 */
function datos_ocupacion($despacho,$fecha)
{
	$dias_co=array("","L","M","X","J","V","S","D");
	
	global $con;
	$stamp = cambiaf($fecha);
	$tokeao = explode("-",$stamp);
	$paso_el=date("w",mktime(0, 0, 0, $tokeao[1], $tokeao[2], $tokeao[0]));
	
	$sql = "Select * from agenda where datediff(finc,'$stamp') <=0 and datediff(ffin,'$stamp')>=0 and despacho like $despacho or repeticion like '%$dias_co[$paso_el];%' and despacho like $despacho and datediff(finc,'$stamp') <=0 order by hinc asc";
		$consulta = @mysql_query($sql,$con);
		if(@mysql_numrows($consulta)!=0)
		{
			while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
			{
				$cadena.="<div id='ocupacion_".$resultado[0]."' class='despacho_ocupado'>";
				$hinc = quita_segundos($resultado[hinc]);
				$hfin = quita_segundos($resultado[hfin]);
			//!!CASO RESERVAS A NO CLIENTES
				if($resultado[id_cliente]!="")
				{
					$cadena.="<div class='las_horas'>".$hinc."-".$hfin."</div>".nombre_cliente($resultado[1]);
					$cadena.="<br/><span class='mini_boton' onclick='informacion_cliente($resultado[1],0,$resultado[0])'>&nbsp;Observaciones&nbsp;</span><input type='hidden' id='cliente_despacho_$resultado[1]' value='$resultado[1]' /><p/>";
					$cadena.=confirmado($resultado[conformidad]);
					$cadena.="<p/>";
				}
				else
				{
					$cadena.="<div class='las_horas'>".$hinc."-".$hfin."</div>".$resultado[otro];
					$cadena.="<br/><span class='mini_boton' onclick='informacion_cliente($resultado[0],1)'>&nbsp;Observaciones&nbsp;</span><input type='hidden' id='cliente_despacho_$resultado[1]' value='$resultado[1]' /><p/>";
					$cadena.=confirmado($resultado[conformidad]);
					$cadena.="<p/>";
				}
				$cadena.="</div>&nbsp;";
			}
		}
		else $cadena.="&nbsp;";
	return $cadena;
}

/*
 * Funcion que muesta si esta o no confirmado
 */
function confirmado($var)
{
	if($var=="Si")
		$cadena = "<div class='confirmado'>Confirmado</div>";
	else
		$cadena = "<div class='no_confirmado'>No Confirmado</div>";
	return $cadena;
}

/*
 * Quita los segundos de la hora para el calculo de ocupacion
 */
function quita_segundos($hora)
{
	$sin_sec=explode(":",$hora);
	$final = $sin_sec[0].":".$sin_sec[1];
	return $final;
}
?>
</table>