<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<link href="estilo/servicont.css" rel="stylesheet" type="text/css"></link>
<link href="../estilo/calendario.css" rel="stylesheet" type="text/css"></link>
<title>Aplicacion Gestion Independencia Centro Negocios </title>
<script src='js/servicont.js' type="text/javascript"></script>
<script src='../js/prototype.js' type="text/javascript"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/lang/calendar-es.js"></script>
<script type="text/javascript" src="../js/calendar-setup.js"></script>
</head>
<body>
<div id='titulo'>Informes y busquedas de Consumos *Datos desde el 1 de Julio de 2007 obtenidos de la facturaci&oacute;n</div>
<div id='botones'>
<span class='boton' onclick='menu(0)'>Por cliente</span>
<span class='boton' onclick='menu(1)'>Por categoria de cliente</span>
<span class='boton' onclick='menu(2)'>Por servicios</span>
<span class='boton' onclick='menu(3)'>Por cliente / servicios</span>
<span class='boton' onclick='menu(4)'>Por categoria de cliente / servicios</span>
<span class='boton' onclick='menu(5)'>Servicios por volumen de facturacion</span>
<span class='boton' onclick='window.history.go(0)'>Limpiar</span>
<span class='boton' onclick='window.close(this)'>[X]Cerrar</span><br><br>
<span class='boton' onclick='menu(6)'>Clientes por volumen de facturacion</span>
<span class='boton' onclick='menu(7)'>Comparativas</span>

</div>	
<div id='formulario'></div>
</body>