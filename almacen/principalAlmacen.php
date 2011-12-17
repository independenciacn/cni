<?php
/**
 * PrincipalAlmacen File Doc Comment
 *
 * Seccion Intermedia del modulo de Almacenaje 
 *
 * PHP Version 5.2.6
 *
 * @category principalAlmacen
 * @package  cni/almacen
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/configuracion.php';
if ( isset($_SESSION['usuario'] ) && isset( $_POST['cliente'] ) ) {
    sanitize( $_POST );
} else {
    notFound();
}
?>
<input type='hidden' id='idcliente' name='idcliente' 
value='<?php echo $_POST['cliente']; ?>' />
<div id="formularioAlmacen"><!-- Autogeneramos el Formulario --></div>
<div id="resultadoAlmacen"><!-- Mostrara el mensaje de la accion --></div>
<div id="listadoAlmacen"><!-- Autogeneramos el Listado --></div>
<script type='text/javascript'>
$(function(){
	formularioAlmacen();
	listadoAlmacen();
});
</script>