<?php
/**
 * FormularioAlmacen File Doc Comment
 *
 * Genera el formulario del modulo de almacenaje
 *
 * PHP Version 5.2.6
 *
 * @category FormularioAlmacen
 * @package  cni/almacen
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/configuracion.php';
if ( isset($_SESSION['usuario']) && isset( $_POST ) ) {
    sanitize( $_POST );
} else {
    notFound();
}
if ( isset( $_POST['item'] ) ) {
    $sql = "Select * from z_almacen where id like ". $_POST['item'];
    $resultado = consultaUnica( $sql );
    $numeroBultos = $resultado[2];
    $fechaInicio = cambiaf( $resultado[3] );
    $fechaFinal = cambiaf( $resultado[4] );
    $opcion = "update";
    $item = $_POST['item'];
    $boton = "[*]Modificar";
    $botonNuevo = "<input type='button' id='cancelar' value='[X] Cancelar' />";
} else {
    $numeroBultos='1';
    $fechaInicio=date("d-m-Y");
    $fechaFinal='';
    $opcion = "add";
    $item = "";
    $boton = "[+]Agregar";
    $botonNuevo = "";
}
?>
<form class='inline' id='frmalmacenaje' name='frmalmacenaje' method='post' action=''>
<fieldset>
<legend>Almacenaje <span id='nombreCliente'></span></legend>
<label for='bultos'>Bultos:</label>
<input type='hidden' id='idcliente' name='idcliente' 
value='<?php echo $_POST['cliente']; ?>' />
<input type='hidden' id='opcion' name='opcion' value='<?php echo $opcion; ?>' />
<input type='hidden' id='item' name='item' value='<?php echo $item; ?>' />
<input class='tipTip' id='bultos' name='bultos' size='3'
value='<?php echo $numeroBultos; ?>' title='Introduce el numero de bultos' />
<label for='fechaInicio'>Fecha Inicio:</label>
<input type='text' class='datepicker tipTip' id='fechaInicio' size='11'
name='fechaInicio' value='<?php echo $fechaInicio; ?>' 
title='Introduce la fecha de Inicio del almacenaje' />
<label for='fechaFin'>Fecha Fin:</label>
<input type='text' class='datepicker tipTip' id='fechaFin' size='11'
name='fechaFin' value='<?php echo $fechaFinal; ?>' 
title='Introduce la fecha de Fin del almacenaje' />
<input type='submit' value='<?php echo $boton; ?>' />
<?php echo $botonNuevo; ?>
</fieldset>
</form>
<script type="text/javascript">
$(function(){
	$(".datepicker").datepicker( { dateFormat: "dd-mm-yy" } );
	$(".datepicker").datepicker( $.datepicker.regional[ "es" ] );
	$(".tipTip").tipTip();
});
$('#cancelar').click(function(){
	formularioAlmacen();
	$('#resultadoAlmacen').html("");
});
$('#frmalmacenaje').submit(function(){
	var url = "funcionesAlmacen.php";
	var pars = $("form#frmalmacenaje").serialize();
	var div = "resultadoAlmacen";
	var proceso = "Procesando Acción";
	procesaAjax(url, pars, div, proceso, listadoAlmacen, "");
	return false;
});
</script>