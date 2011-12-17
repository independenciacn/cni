<?php
	include("../inc/variables.php");
	if(isset($_POST[nota]))
	{
		$sql ="Select * from notas where id like ".$_POST[nota]."";
		$consulta = @mysql_query( $sql, $con );
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
	
	
    $html.="<form id='notas' method='post' onsubmit='".$accion.";return false'>";
	$html.="Fecha:<input type='text' name='vencimiento' id='semana' size='10' value='".$hoy."' />
<button type='button' class='boton' id='f_trigger_semana' >...</button>";
	$html.="<br>Nota:<br>";
	$html.="<textarea name='nota' id='nota' cols='100' rows='10'>".$nota."</textarea>";
	$html.="<div id='estado_nota'></div>";
	$html.="<br><input type='submit' value='".$boton."' /></form>";
	$html.="<br>Listado de Notas<br>";
	/*listado de las notas*/
	
	$sql="Select * from notas order by fecha desc";
	$consulta = @mysql_query( $sql, $con );
	if(@mysql_numrows($consulta)==0)
	$html.="No hay notas";
	else
	{
		while($resultado = @mysql_fetch_array($consulta))
		{
			
			$html.="&nbsp;&nbsp;<span class='fecha_nota'>".cambiaf($resultado[fecha])."&nbsp;&nbsp;&nbsp;&nbsp;<img src='imagenes/editar.png' alt='Editar Nota' onclick='editar_nota(".$resultado[0].")' /> &nbsp;|&nbsp;<img src='imagenes/borrar.png' alt='Borrar Nota' onclick='borra_nota(".$resultado[0].")' /></span>
			&nbsp;&nbsp;<div class='texto_nota'>".$resultado[nota]."</div><br>";
		}
	}
	
echo $html;

?>
