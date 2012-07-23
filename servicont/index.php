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
$tituloGeneral = APLICACION. " - ". VERSION;
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<link href='../bootstrap/css/bootstrap.min.css' rel="stylesheet" />
	<link href="../estilo/custom-theme/jquery-ui-1.8.8.custom.css" 
	rel="stylesheet" />
	<link href='estilo/servicont.css' rel='stylesheet' type='text/css'>
	<script src='../js/jquery-1.7.2.min.js'></script>
	<script src='../js/jquery-ui-1.8.8.custom.min.js'></script>
	<script src='../bootstrap/js/bootstrap.js'></script>
	<script type='text/javascript' src='js/servicont.js' ></script>
	<title>Informes - <?= $tituloGeneral; ?></title>
</head>
<body>
	<div class='header'>
    	<span class="label label-info">
    		<strong>
    			Informes y busquedas de Consumos *Datos desde el
    			1 de Julio de 2007 obtenidos de la facturación
    		</strong>
    	</span> 
	</div>
	<br/>

	<div class="btn-toolbar well">
		<div class="btn-group">
			<button class="btn">Estadisticas Clientes</button>
	  		<button class="btn dropdown-toggle" data-toggle="dropdown">
	    		<span class="caret"></span>
	  		</button>
	  		<ul class="dropdown-menu">
	    		<!-- dropdown menu links -->
	    		<li><a href='javascript:menu(0)'>Por Cliente</a></li>
	    		<li><a href='javascript:menu(3)'>Por Cliente / Servicio</a></li>
	    		<li><a href='javascript:menu(6)'>Por Volumen de Facturación</a>
	    		</li>
	  		</ul>
	  	</div>
	  	<div class="btn-group">
	  		<button class="btn">Estadisticas Categorias</button>
	  		<button class="btn dropdown-toggle" data-toggle="dropdown">
	    		<span class="caret"></span>
	  		</button>
	  		<ul class="dropdown-menu">
	    		<!-- dropdown menu links -->
	    		<li><a href='javascript:menu(1)'>
	    		Por categoria de cliente</a></li>
	    		<li><a href='javascript:menu(4)'>
	    		Por categoria de cliente / servicios</a></li>
	  		</ul>
	  	</div>
	  	<div class="btn-group">
			<button class="btn">Estadisticas Servicios</button>
	  		<button class="btn dropdown-toggle" data-toggle="dropdown">
	    		<span class="caret"></span>
	  		</button>
	  		<ul class="dropdown-menu">
	    		<!-- dropdown menu links -->
	    		<li><a href='javascript:menu(2)'>Por Servicios</a></li>
	    		<li><a href='javascript:menu(5)'>
	    		Por Volumen de facturación</a></li>
	  		</ul>
	  	</div>
	  	<div class="btn-group">
	  		<button class='btn' onclick='menu(7)'>
	        	Comparativas
	        </button>
	  	</div>
	  	<div class="btn-group pull-right">
	  		<button class='btn btn-info' onclick='window.history.go(0)'>
	        	<i class='icon-refresh icon-white'></i> Recargar
	    	</button>
	    	<button class='btn btn-danger' onclick='window.close(this)'>
	        	<i class='icon-remove icon-white'></i> Cerrar
	    	</button>
	  	</div>	
  	</div>
	<div id='formulario'></div>
</body>
<?php
 