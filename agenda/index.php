<?php
/**
 * FIXME: Si agregamos una repeticion a martes y miercoles, sale el domingo y tambien el sabado
 * FIXME: Segun como agregamos cosas a la hora de borrar no se borran del todo.
 *
 *
 * Index File Doc Comment
 *
 * Pagina principal del modulo de almacenaje
 *
 * PHP Version 5.2.6
 *
 * @category Index
 * @package  cni/almacen
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
echo $cabezeraHtml;
?>
<body>
<div id='cuerpo' class='container'>
<h1><img src='../<?php echo $imagen['agenda']; ?>' alt='agenda' /> Agenda</h1>
<form id='agenda' action='' method='post'>
    <fieldset>
    <legend>
    Tipo de Vista de Agenda
    </legend>
    <select id='vista'>
		<option selected value=''>--Opcion--</option>
		<option value='0'>Despachos</option>
		<option value='1'>Semana</option>
		<option value='2'>Interna</option>
		<option value='3'>Tareas Pendientes</option>
		<option value='4'>Notas</option>
	</select>
</fieldset>
</form>
<div id='frmagenda'><!-- Seccion Autogenerada --></div>
<div id='informacionDespacho'></div>
<div id='seccionAgenda' class='container showgrid'><!-- Seccion Autogenerada --></div>
</div>
<?php echo $firmaAplicacion; ?>
<script>
$('#vista').change(function(){
	var url = 'generadorAgenda.php';
	var pars = 'agenda='+$(this).val();
	var div = 'seccionAgenda';
	procesaAjax(url, pars, div, 'Generando Agenda', false, false);
});
</script>
</body>
</html>
