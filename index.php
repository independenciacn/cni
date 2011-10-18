<?php
/**
 * Index File Doc Comment
 * 
 * Fichero principal de la aplicacion
 * 
 * PHP Version 5.2.6
 * 
 * @category Index
 * @package  cni
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
error_reporting( E_ALL );
require_once 'inc/variables.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--  <link href="estilo/blueprint/screen.css" rel="stylesheet" type="text/css"></link>-->
<link href="estilo/cni.css" rel="stylesheet" type="text/css"></link>
<link href="estilo/calendario.css" rel="stylesheet" type="text/css"></link>
<script type="text/javascript" src='js/prototype17.js'></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/lang/calendar-es.js"></script>
<script type="text/javascript" src="js/calendar-setup.js"></script>
<script type="text/javascript" src='js/independencia.js'></script>
<title>Aplicación Gestión Independencia Centro Negocios 2.0d</title>
</head>
<body>
	<div id='cuerpo' class='container showgrid'>
	<?php require_once 'inc/principal.php'; ?>
	</div>
	<div id='datos_interesantes'></div>
	<div id='debug'></div>
</body>
</html>