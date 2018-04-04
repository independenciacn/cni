<?php require_once '../inc/variables.php';
// FIXME: Comprobar la autentificación
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/custom-theme/jquery-ui-1.8.8.custom.css" rel="stylesheet" type="text/css">
<link href="estilo/servicont.css" rel="stylesheet" type="text/css"></link>
<title>Informes - <?php echo APLICACION ?> - <?php echo VERSION ?></title>
</head>
<body>
<div id='titulo'>
	Informes y busquedas de Consumos *Datos desde el
	1 de Julio de 2007 obtenidos de la facturación
</div>
<div id='botones'>
	<span class='boton' id='menu_0'>Por cliente</span>
	<span class='boton' id='menu_1'>Por categoria de cliente</span>
	<span class='boton' id='menu_2'>Por servicios</span>
	<span class='boton' id='menu_3'>Por cliente / servicios</span>
	<span class='boton' id='menu_4'>Por categoria de cliente / servicios</span>
	<span class='boton' id='menu_5'>Servicios por volumen de facturacion</span>
	<span onclick='window.history.go(0)'>Limpiar</span>
	<span onclick='window.close(this)'>[X]Cerrar</span>
	<br/>
	<span class='boton' id='menu_6'>Clientes por volumen de facturacion</span>
	<span class='boton' id='menu_7'>Comparativas</span>
</div>
<div id='formulario'></div>
</body>
<script src='js/jquery-1.4.4.min.js' type="text/javascript"></script>
<script src='js/jquery-ui-1.8.8.custom.min.js' type="text/javascript"></script>
<script src='js/servicont.min.js' type="text/javascript"></script>
</html>