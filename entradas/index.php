<?php 
/**
 * index.php File Doc Comment
 *
 * Pagina principal del modulo de Entradas y Salidas
 *
 * PHP Version 5.2.6
 *
 * @category entradas
 * @package  cni/entradas
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
$anyo = date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<link href="../estilo/custom-theme/jquery-ui-1.8.8.custom.css"
	rel="stylesheet" />
	<link href="css/entradas.css" rel="stylesheet" />
	<title>Cuadro de Entradas, Salidas y Comparativas - 
		The Perfect Place <?= $anyo; ?>
	</title>
	<script src='../js/jquery-1.7.2.min.js'></script>
	<script src='../js/jquery-ui-1.8.8.custom.min.js'></script>
</head>
<body>
<div id='container'>
	<div id='header'>
	<img src='css/bc.png' alt='The Perfect Place' width='125px' />
	<h1>Cuadro de Entradas, Salidas y Comparativas</h1>
</div>
<div id='menu'>
	<p>Seleccione el Año de Inicio, Año de Fin, tipo de Visualización y
	datos que desea visualizar</p>
<a id='arriba'></a>
<form id='frmopciones' action="" method="post">
	<select id='inicio' name='inicio'>
		<option value='0'>--Año Inicial--</option>
<?php
for ($i = '2008'; $i <= date ( 'Y' ); $i ++) {
	echo "<option value='" . $i . "'>" . $i . "</option>";
}
?>
	</select>
	<span class='flecha'>&raquo;</span>
	<select id='fin' name='fin'>
		<option value='0'>--Año Final--</option>
<?php
for ($i = '2008'; $i <= date ( 'Y' ); $i ++) {
	echo "<option value='" . $i . "'>" . $i . "</option>";
}
?>
	</select>
	<span class='flecha'>&raquo;</span>
	<select id='vista' name='vista'>
		<option value='0'>--Tipo de Vista--</option>
		<option value='1'>Acumulada</option>
		<option value='2'>Detallada</option>
		<option value='3'>Gráfica</option>
	</select>
	<span class='flecha'>&raquo;</span> 
	<select id='datos' name='datos'>
		<option value='0'>--Datos a Visualizar--</option>
		<option value='1'>Movimientos Clientes</option>
		<option value='2'>Consumo de Servicios</option>
	</select>
	<span class='flecha'>&raquo;</span>
	<input type='submit' value='Ver Datos' /> 
	<input type="reset"  value="Limpiar" />
</form>
</div>
<div id='resultado'>
	<div id='carga'>
		<img src="../estilo/custom-theme/images/ajax-loader.gif" 
		alt="Cargando" />
	</div>
	<div id='resultados'></div>
<!-- Visualizaremos el resultado de la opción seleccionada -->
</div>
<div id='footer'>
	&copy;<?= $anyo ?>
</div>
</div>
<script src='js/entradas.js'></script>
</body>
</html>
<?php

