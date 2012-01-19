<?php require_once '../inc/variables.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="estilo/servicont.css" rel="stylesheet" type="text/css"></link>
<link href="../estilo/calendario.css" rel="stylesheet" type="text/css"></link>
<script src='js/servicont.js' type="text/javascript"></script>
<script src='../js/prototype.js' type="text/javascript"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/lang/calendar-es.js"></script>
<script type="text/javascript" src="../js/calendar-setup.js"></script>
<title>Informes - <?php echo APLICACION; ?> - <?php echo VERSION; ?></title>
</head>
<body>
<div id='titulo'>
	Informes y busquedas de Consumos *Datos desde el 
	1 de Julio de 2007 obtenidos de la facturación
</div>
<div id='botones'>
	<span class='boton' onclick='menu(0)'>Por cliente</span>
	<span class='boton' onclick='menu(1)'>Por categoria de cliente</span>
	<span class='boton' onclick='menu(2)'>Por servicios</span>
	<span class='boton' onclick='menu(3)'>Por cliente / servicios</span>
	<span class='boton' onclick='menu(4)'>Por categoria de cliente / servicios</span>
	<span class='boton' onclick='menu(5)'>Servicios por volumen de facturacion</span>
	<span class='boton' onclick='window.history.go(0)'>Limpiar</span>
	<span class='boton' onclick='window.close(this)'>[X]Cerrar</span>
	<br/>
	<span class='boton' onclick='menu(6)'>Clientes por volumen de facturacion</span>
	<span class='boton' onclick='menu(7)'>Comparativas</span>
</div>	
<div id='formulario'></div>
</body>