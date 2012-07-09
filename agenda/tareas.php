<?php 
require_once '../inc/variables.php';
if (isset($_POST[tarea])) 
{
	$sql = "Select * from tareas_pendientes where id like $_POST[tarea]";
	$consulta = @mysql_query($sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	$accion="actualiza_tarea_pendiente($resultado[id])";		
}
else
{
	$resultado = array();
	$accion="agregar_tarea_pendiente()";	
} 
?>
<form id='tareas_pendientes' name='tareas_pendientes' method='post' action='' onsubmit='<? echo $accion; ?>;return false' >
<label>Tarea:</label><br/><textarea name='nombre' cols='50' rows='6'><? echo $resultado[nombre]; ?></textarea>
<p/><label>Vencimiento:</label><input type='text' name='vencimiento' id='semana' size='10' value='<? echo cambiaf($resultado[vencimiento]); ?>' />
<button type='button' class='boton' id='f_trigger_semana' >...</button>
<label>Asignada a:</label>
<select name='asignada'>
<?php
$seleccion_asignada="<option value='0'>-No Asignada-</option>";

	$sql2 = "Select Id,Apell1,Nombre from empleados";
	$consulta2 = mysql_query($sql2,$con);
	while(true == ($resultado2 = @mysql_fetch_array($consulta2)))
	{	
		if($resultado[asignada]==$resultado2[0])
			$check_as = "selected";
		else
			$check_as = "";
	$seleccion_asignada.="<option value='".$resultado2[0]."' ".$check_as.">".$resultado2[2]." ".$resultado2[1]."</option>";
	}
echo $seleccion_asignada;
?>
</select>
<label>Prioridad:</label>
<select name='prioridad'>
	<option <?php if ($resultado[prioridad]==0) echo "selected"; ?> value='0'>Normal</option>
	<option <?php if ($resultado[prioridad]==1) echo "selected"; ?> value='1'>Media</option>
	<option <?php if ($resultado[prioridad]==2) echo "selected"; ?> value='2'>Alta</option>
	<option <?php if ($resultado[prioridad]==3) echo "selected"; ?> value='3'>Urgente</option>
</select>
<?php 
if(isset($resultado[id]))
	echo "<input type='submit' value='Actualizar Tarea' /><input type='button' value='Limpiar' onclick='cambia_vista()'/>";
else 
	echo "<input type='submit' value='Agregar Tarea' />";
?>
</form>
<div id='estado_tarea'></div>	
<?php
/*
 * Cambia la fecha a sql y a la inversa
 */
function cambiaf($stamp) 
{
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}
/*
 * Codigo principal
 */
/*
 * Listado de tareas sin realizar y realizadas
 */
$tareas = array("Normal","Media","Alta","Muy Alta");
$opcion = array("No","Si");
$tipo = array("pendientes","realizadas");
/*
 * valores del filtro de fecha
 */
$sql = "SELECT vencimiento
FROM `tareas_pendientes`
GROUP BY vencimiento";
$consulta = @mysql_query($sql,$con);
while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
$fechas[]=$resultado[0];
/*Fin*/
for($j=0;$j<=1;$j++)
{
	$texto.= "<div class='seccion'>Listado de tareas ".$tipo[$j]."<br />";
	$texto.= "Filtrados:
	<select id='filtro_vencimiento' onchange='filtro_vencimiento()'>";
	foreach ($fechas as $fecha)
	$texto.= "<option value='".$fecha."'>".cambiaf($fecha)."</option>";
	$texto.="</select>";
	$texto.=" O <select id='filtro_prioridad' onchange='filtro_prioridad()'>
	<option value='0'>Normal</option>
	<option value='1'>Media</option>
	<option value='2'>Alta</option>
	<option value='3'>Urgente</option></select>
	0 <select id='filtro_asignado' onchange='filtro_asignado()'>";
	$texto.= $seleccion_asignada;
	$texto.="</select></div><div id='lista_tareas_pendientes'>";
	$sql = "Select * from tareas_pendientes where realizada like '$opcion[$j]' order by prioridad desc ,vencimiento asc";
	$consulta = @mysql_query($sql,$con);
	if(@mysql_numrows($consulta)!=0)
	{
		$i = 0;
		while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
		{
			$clase = ( $i++ % 2) ? "lista_par" : "lista_impar";
		/*
 		 * Colores de las prioridades
 		 */	
		if($resultado[realizada]=="Si")
			$realizada = "checked";
		else
			$realizada = "";
		$color_tarea = array("normal","media","alta","muy_alta");
		$texto.="<div class='".$clase."'><span class='fecha_tarea'>".cambiaf($resultado[vencimiento])."</span>&nbsp;&nbsp;<span class='prioridad_".$color_tarea[$resultado[prioridad]]."'>&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;".nombre_emp($resultado[asignada])."&nbsp;&nbsp;<a href='javascript:edita_tarea_pendiente(".$resultado[0].")'>".$resultado[nombre]."</a>&nbsp;&nbsp;&nbsp;&nbsp;<span align='right'><input type='checkbox' id='tarea_".$resultado[0]."' onchange='cambia_estado_tarea(".$resultado[0].")' ".$realizada." />&nbsp;|&nbsp;<img src='imagenes/borrar.png' alt='Borrar Tarea' onclick='borra_tarea(".$resultado[0].")' /></span></div>";
		}
	}
	else
		$texto .= "<div class='lista_impar'>No hay tareas ".$tipo[$j]."</div>";
}
echo $texto."</div>";

function nombre_emp($id)
{
	global $con;
	$sql = "Select * from empleados where Id like $id";
	$consulta = @mysql_query($sql,$con);
	$resultado = @mysql_fetch_array($consulta);
	return "<span class='fecha_tarea'>".$resultado[3]." ".$resultado[1].":</span>";
	//return "CO";
}