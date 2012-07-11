<?php
/**
 * index.php File Doc Comment
 * 
 * Pagina principal del modulo de estadisticas
 * 
 * PHP Version 5.2.6
 * 
 * @category servicont
 * @package  cni/servicont
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/variables.php'; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<link href='../estilo/calendario.css' rel='stylesheet' type='text/css'>
	<link href='estilo/servicont.css' rel='stylesheet' type='text/css'>
	<script type='text/javascript' src='../js/prototype.js' ></script>
	<script type='text/javascript' src='../js/calendar.js'></script>
	<script type='text/javascript' src='../js/lang/calendar-es.js'></script>
	<script type='text/javascript' src='../js/calendar-setup.js'></script>
	<script type='text/javascript' src='js/servicont.js' ></script>
	<title>Informes - <?php echo APLICACION; ?> - <?php echo VERSION; ?></title>
</head>
<body>
	<div id='titulo'>
    	Informes y busquedas de Consumos *Datos desde el
    	1 de Julio de 2007 obtenidos de la facturación
	</div>
	<div id='botones'>
	    <button class='boton' onclick='menu(0)'>
	        Por cliente
	    </button>
	    <button class='boton' onclick='menu(1)'>
	        Por categoria de cliente
	    </button>
	    <button class='boton' onclick='menu(2)'>
	        Por servicios
	    </button>
	    <button class='boton' onclick='menu(3)'>
	        Por cliente / servicios
	    </button>
	    <button class='boton' onclick='menu(4)'>
	        Por categoria de cliente / servicios
	    </button>
	    <button class='boton' onclick='menu(5)'>
	        Servicios por volumen de facturacion
	    </button>
	    <button class='boton' onclick='window.history.go(0)'>
	        Limpiar
	    </button>
	    <button class='boton' onclick='window.close(this)'>
	        [X]Cerrar
	    </button>
	    <br/>
	    <button class='boton' onclick='menu(6)'>
	        Clientes por volumen de facturacion
	    </button>
	    <button class='boton' onclick='menu(7)'>
	        Comparativas
	    </button>
	</div>
	<div id='formulario'></div>
</body>