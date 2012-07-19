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
	<link href='../estilo/calendario.css' rel='stylesheet'>
	<link href='estilo/servicont.css' rel='stylesheet' type='text/css'>
	<script src='../js/prototype.js' ></script>
	<script src='../js/calendar.js'></script>
	<script src='../js/lang/calendar-es.js'></script>
	<script src='../js/calendar-setup.js'></script>
	<link href='../bootstrap/css/bootstrap.min.css' rel="stylesheet" />
	<script src='../bootstrap/js/bootstrap.min.js'></script>
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
    	<div class="btn-group pull-right">
			<button class='btn btn-info' onclick='window.history.go(0)'>
	        	<i class='icon-refresh icon-white'></i> Recargar
	    	</button>
	    	<button class='btn btn-danger' onclick='window.close(this)'>
	        	<i class='icon-remove icon-white'></i> Cerrar
	    	</button>
		</div>  
	</div>
	<div class="btn-toolbar well">
		<div class="btn-group">
  			<button class="btn" onclick='menu(0)'>
  				Por Clientes
  			</button>
  			<button class="btn" onclick='menu(3)'>
  				Por cliente / servicios
  			</button>
  			<button class="btn" onclick='menu(6)'>
  				Clientes por volumen de Facturación
  			</button>
		</div>
		<div class="btn-group">
			<button class='btn' onclick='menu(1)'>
	        	Por categoria de cliente
	    	</button>
	    	<button class='btn' onclick='menu(4)'>
	        	Por categoria de cliente / servicios
	    	</button>
		</div>
		<div class="btn-group">
			<button class='btn' onclick='menu(2)'>
	        	Por servicios
	    	</button>
	    	<button class='btn' onclick='menu(5)'>
	        	Servicios por volumen de facturacion
	    	</button>	
		</div>
		<div class="btn-group">
			<button class='btn' onclick='menu(7)'>
	        	Comparativas
	        </button>
	    </div>	 		
	</div>
	<div id='formulario'></div>
</body>
<?php
 