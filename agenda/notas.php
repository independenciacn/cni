<?php
	require_once '../inc/variables.php';
	if(isset($_POST[nota]))
	{
		$sql ="Select * from notas where id like ".$_POST[nota]."";
		$consulta = @mysql_query($sql,$con);
		$resultado = @mysql_fetch_array($consulta);
		$hoy = cambiaf($resultado[fecha]);
		$nota = $resultado[nota];
		$boton = "Actualizar nota";
		$accion = "actualiza_nota(".$_POST[nota].")";
	}
	else
	{
		$hoy=date('d-m-Y');
		$nota ="";
		$boton="Agregar Nota";
		$accion = "agrega_nota()";
	}
	
	
    $cadena.="<form id='notas' method='post' onsubmit='".$accion.";return false'>";
	$cadena.="Fecha:<input type='text' name='vencimiento' id='semana' size='10' value='".$hoy."' />
<button type='button' class='boton' id='f_trigger_semana' >...</button>";
	$cadena.="<br>Nota:<br>";
	$cadena.="<textarea name='nota' id='nota' cols='100' rows='10'>".$nota."</textarea>";
	$cadena.="<div id='estado_nota'></div>";
	$cadena.="<br><input type='submit' value='".$boton."' /></form>";
	$cadena.="<br>Listado de Notas<br>";
	/*listado de las notas*/
	
	$sql="Select * from notas order by fecha desc";
	$consulta = @mysql_query($sql,$con);
	if(@mysql_numrows($consulta)==0)
	$cadena.="No hay notas";
	else
	{
		while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
		{
			
			$cadena.="&nbsp;&nbsp;<span class='fecha_nota'>".cambiaf($resultado[fecha])."&nbsp;&nbsp;&nbsp;&nbsp;<img src='imagenes/editar.png' alt='Editar Nota' onclick='editar_nota(".$resultado[0].")' /> &nbsp;|&nbsp;<img src='imagenes/borrar.png' alt='Borrar Nota' onclick='borra_nota(".$resultado[0].")' /></span>
			&nbsp;&nbsp;<div class='texto_nota'>".$resultado[nota]."</div><br>";
		}
	}
	
echo $cadena;

/*
 * Cambio de formato de fecha, en ambos sentidos
 */
function cambiaf($stamp)
{
	$fdia = explode("-",$stamp);
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}

?>
