<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'></link>
<link href="css/custom-theme/jquery-ui-1.8.8.custom.css"
	rel="stylesheet" type="text/css"></link>
<link href="css/entradas.css" rel="stylesheet" type="text/css"></link>
<title>Cuadro de Entradas, Salidas y Comparativas - The Perfect Place <?php
echo date ( 'Y' );
?></title>
<script src='js/jquery-1.4.4.min.js' type="text/javascript"></script>
<script src='js/jquery-ui-1.8.8.custom.min.js' type="text/javascript"></script>
</head>
<body>
<div id='container'>
<div id='header'><img src='css/bc.png' alt='The Perfect Place'
	width='125px' />
<h1>Cuadro de Entradas, Salidas y Comparativas</h1>
</div>
<div id='menu'>
<p>Seleccione el Año de Inicio, Año de Fin, tipo de Visualización y
datos que desea visualizar</p>
<a name='arriba'></a>
<form id='frmopciones' action="" method="post"><select id='inicio'
	name='inicio'>
	<option value='0'>--Año Inicial--</option>
				<?php
				for($i = '2008'; $i <= date ( 'Y' ); $i ++) {
					echo "<option value='" . $i . "'>" . $i . "</option>";
				}
				?>
			</select> <span class='flecha'>&raquo;</span> <select id='fin'
	name='fin'>
	<option value='0'>--Año Final--</option>
				<?php
				for($i = '2008'; $i <= date ( 'Y' ); $i ++) {
					echo "<option value='" . $i . "'>" . $i . "</option>";
				}
				?>
			</select> <span class='flecha'>&raquo;</span> <select id='vista'
	name='vista'>
	<option value='0'>--Tipo de Vista--</option>
	<option value='1'>Acumulada</option>
	<option value='2'>Detallada</option>
	<option value='3'>Gráfica</option>
</select> <span class='flecha'>&raquo;</span> <select id='datos'
	name='datos'>
	<option value='0'>--Datos a Visualizar--</option>
	<option value='1'>Movimientos Clientes</option>
	<option value='2'>Consumo de Servicios</option>
</select> <span class='flecha'>&raquo;</span> <input type='submit' 
	value='Ver Datos' /> <input type="reset"  value="Limpiar" /></form>
</div>
<div id='resultado'>
<div id='carga'><img src="css/custom-theme/images/ajax-loader.gif"
	alt="Cargando"></img></div>
<div id='resultados'></div>
<!-- Visualizaremos el resultado de la opción seleccionada --></div>


<div id='footer'>devel by <a href="http://rubenlacasa.wordpress.com" target="_blank">&copy;rubenlacasa::<?php
echo date ( 'Y' )?></a></div>
</div>
<script type='text/javascript'>

$("input[type=reset]").click(function(){
	$("#resultados").html("");
	
});

$('#frmopciones').submit(function(){
	$('select').removeClass('errorfrm');
	var fallo = 0;
	var mensaje = "ERROR: Revise los datos\n";

	if($('#inicio').val() == 0){
		mensaje += "Debe especificar un año Inicial\n";
		$('#inicio').addClass('errorfrm');
		fallo = 1;
	}
	if($('#fin').val() == 0){
		mensaje += "Debe especificar un año Final\n";
		$('#fin').addClass('errorfrm');
		fallo = 1;
	}
	if($('#vista').val() == 0){
		mensaje += "Debe especificar un tipo de Vista\n";
		$('#vista').addClass('errorfrm');
		fallo = 1;
	}
	if($('#datos').val() == 0){
		mensaje += "Debe especificar que datos quiere visualizar\n";
		$('#datos').addClass('errorfrm');
		fallo = 1;
	}
	if($('#inicio').val() > $('#fin').val())
	{
		mensaje += "El año de fin debe ser mayor o igual que el año de inicio\n";
		$('#inicio').addClass('errorfrm');
		$('#fin').addClass('errorfrm');
		fallo = 1;
	}
	
	if( ( $('#fin').val() - $('#inicio').val()) > 1){
		mensaje += "El rango maximo es de 2 años. ej(2008 - 2009)\n";
		$('#inicio').addClass('errorfrm');
		$('#fin').addClass('errorfrm');
		fallo = 1;
	}
	
	if(fallo == 0){
		$.post('handler.php',$('#frmopciones').serialize(),function(data){
			
			$('#resultados').html(data);
			$('#resultado').height($('#resultados').height()+50);
			
		});
	}
	else{
		alert(mensaje);
	}
	return false;
});

$("#frmopciones").ajaxStart(function(){ $("#carga").show();});
$("#frmopciones").ajaxStop(function(){
	$("#carga").hide();
	$("#resultado").height($('#resultados').height() + 50);
});

</script>
</body>
</html>