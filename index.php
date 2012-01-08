<?php
require_once 'inc/variables.php';
checkSession();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="estilo/cni.css" rel="stylesheet" type="text/css"></link>
<link href="estilo/calendario.css" rel="stylesheet" type="text/css"></link>
<script type="text/javascript" src='js/prototype.js'></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/lang/calendar-es.js"></script>
<script type="text/javascript" src="js/calendar-setup.js"></script>
<script type="text/javascript" src='js/independencia.js'></script>
<title>Principal - <?php echo APLICACION; ?> - <?php echo VERSION; ?></title>
</head>
<body>
<div id='cuerpo'>
<?php
/*
 * TODO: Que se pueda modificar la contraseña de acceso
 * TODO: Agregar un nuevo campo a la factura: Nº Pedido
 */
if(isset($_SESSION['usuario'])) {
	include_once 'inc/menu.php';
	echo "<div id='menu_general'>";
	echo menu();
	echo "</div>";
} else {
?>
<div id='registro'>
<center>
	<img src='imagenes/logotipo2.png' width='538px' alt='The Perfect Place' />
</center>
<p />
<center>
<?php
	if(isset($_GET["exit"]))
		echo "<span class='ok'>Sesion Cerrada</span>";
	if(isset($_GET["error"]))
		echo "<span class='ko'>Usuario o Contrase&ntilde;a Incorrecta</span>";
?>
	<form id='login_usuario' method='post' action='inc/valida.php'>
	<table width='30%' class="login">
  	<tr>
  	<td align='right'>
	Usuario:
	</td><td>
	<input type='text' id="usuario" name="usuario" accesskey="u" tabindex="1" />
	</td></tr>
	<tr>
	<td align='right'>
	Contrase&ntilde;a:
	</td><td>
	<input type='password' id="passwd" name="passwd" accesskey="c" tabindex="2" />
	</td></tr>
	<tr>
	<td align='center' colspan="2">
	<input type='submit' class='boton' accesskey="e" tabindex="3"  value = '[->]Entrar' />
	</td></tr>
	<tr><td colspan='2'></td></tr>
	</table>
	</form>
</center>
<p />
<center>
  <p>
  	<span class="etiqueta">Desarrollado por:</span>
  </p>
  <p>
  	<a href='http://www.ensenalia.com'><img src='imagenes/ensenalia.jpg' width='128' /></a>
  </p></center>
 </div>
<?php 
} 
?>
</div>
<div id='datos_interesantes'></div>
<div id='debug'></div>
<?php 
if(isset($_SESSION['usuario']))
{
	echo "<div id='avisos'>";
	include("inc/avisos.php");//Se muestran los avisos solo con el include
	//echo "Eh co lets go";
	//echo avisos();
	echo "</div>";
	echo "<div id='resultados'></div>";//linea de los resultados de busqueda
	echo "<div id='formulario'></div>";//linea del formulario
	//echo "<div id='debug'></div>";//linea de depuracion
}
?>
</body>
</html>