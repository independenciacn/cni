<?php
/**
 * Establecemos la variable con el año actual
 * 
 * @var integer
 */
$anyoActual = date('Y');
/**
 * Establecemos la variable con la cabezera de la aplicacion en la raiz
 * @var string
 */
$cabezeraHtml = <<<EON
<!DOCTYPE HTML>
<html lang="es">
<head>
<meta charset=utf-8 /> 
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' />
<link href="{$conf['root']}/estilo/blueprint/screen.css" rel="stylesheet" />
<link href="{$conf['root']}/estilo/custom-theme/jquery-ui-1.8.8.custom.css" rel="stylesheet" />
<link href="{$conf['root']}/estilo/tipTip.css" rel="stylesheet" />
<link href="{$conf['root']}/estilo/ui.jqgrid.css" rel="stylesheet" />
<link href="{$conf['root']}/estilo/fullcalendar/fullcalendar.css" rel="stylesheet" />
<link href="{$conf['root']}/estilo/perfect.css" rel="stylesheet" />
<script src="{$conf['root']}/js/jquery-1.7.1.min.js"></script>
<script src="{$conf['root']}/js/jquery-ui-1.8.8.custom.min.js"></script>
<script src="{$conf['root']}/js/jquery.ui.datepicker-es.js" charset="utf-8"></script>
<script src="{$conf['root']}/js/jquery.tipTip.minified.js"></script>
<script src="{$conf['root']}/js/jquery.scrollTo-1.4.2-min.js"></script>
<script src="{$conf['root']}/js/grid.locale-es.js" charset="utf-8"></script>
<script src="{$conf['root']}/js/jquery.jqGrid.min.js"></script>
<script src="{$conf['root']}/js/fullcalendar/fullcalendar.min.js"></script>
<script src='{$conf['root']}/js/independencia.js'></script>
<title>Aplicacion Gestion Independencia Centro Negocios {$conf['version']}</title>
</head>
EON;
/**
 * Establecemos la variable con la firma por defecto en toda la aplicacion
 * 
 * @var string
 */
$firmaAplicacion = <<<EON
<footer class='container'>
<div class='span-6 prepend-18 signature'>
Devel by
<a href='http://rubenlacasa.wordpress.com'>
&copy;Ruben Lacasa::{$anyoActual}
</a>
-
<a rel="license" href="http://creativecommons.org/licenses/by-nd/3.0/">
<img alt="Licencia de Creative Commons" style="border-width:0"
src="http://i.creativecommons.org/l/by-nd/3.0/80x15.png" />
</a>
at Cloud
</div>
</footer>
EON;

/**
 *  Array que define los meses para usar en todos los sitios
 *  
 *  @var array $meses
 */
$meses = array( 
	1 =>'Enero', 
		'Febrero', 
		'Marzo', 
		'Abril', 
		'Mayo', 
		'Junio', 
		'Julio', 
		'Agosto', 
		'Septiembre', 
		'Octubre', 
		'Noviembre', 
		'Diciembre'
);
/**
 * Array que define los dias de la semana en todas las funciones
 * 
 * @var array $dias
 */
$dias = array(
	1 =>'Lunes',
		'Martes',
		'Miercoles',
		'Jueves',
		'Viernes',
		'Sabado',
		'Domingo'
);
/**
 * Path donde estan las imagenes
 * 
 * @var string $imagenPath
 */
$imagenPath = 'estilo/iconos/';
/**
 * Array con las imagenes de las categorias
 *
 * @var array $imagenes
 */
$imagen = array(
        'agenda' => $imagenPath.'agenda.png',
        'almacenaje' => $imagenPath.'almacenaje.png',
        'asignacion' => $imagenPath.'asignacion.png',
        'avisos' => $imagenPath.'avisos.png',
        'buscar' => $imagenPath.'buscar.png',
        'cerrar' => $imagenPath.'cerrar.png',
        'clientes' => $imagenPath.'clientes.png',
        'empleados' => $imagenPath.'empleados.png',
        'entradas' => $imagenPath.'entradas.png',
        'estadisticas' => $imagenPath.'estadisticas.png',
        'gestion' => $imagenPath.'gestion.png',
        'listin' => $imagenPath.'listin.png',
        'proveedores' => $imagenPath.'proveedores.png',
        'servicios' => $imagenPath.'servicios.png'
);
/**
 * Array con la lista blanca de las tablas para autocomplete
 * 
 * @var array $whiteTables
 */
$whiteTables = array (
    'clientes',
    'servicios2',
    'empleados',
    'proveedores',
    'listin'        
);
/**
 * Array de servicios agrupados
 */
$agrupados = array(
		"Franqueo",
		"Consumo Tel%fono",
		"Material de oficina",
		"Secretariado",
		"Ajuste"
);