<?php
/**
 * Busqueda File Doc Comment
 *
 * Formulario de Busqueda de datos
 *
 * PHP Version 5.2.10
 *
 * @category Busqueda
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once 'configuracion.php';
if ( !isset( $_SESSION['usuario'] ) ) {
    header("Status: 404 Not Found");
    exit(0);
}
?>
<form class='inline' id='buscar' name='buscar' method='post' action=''>
    <fieldset>
    <legend><img src='<?php echo $imagen['buscar']; ?>' alt='buscar' width='32px' />
     Buscar</legend>
    <label for='texto'>Buscar:</label>
    <input type='text' id='texto' name='texto' placeholder='texto a buscar' />
    <input type='submit' value='buscar' />
    </fieldset>
</form>
<div id='resultadosBusqueda'></div>
<script type='text/javascript'>
$('#buscar').submit(function(){
    var url = 'inc/funcionesBusqueda.php';
    var pars = $('#buscar').serialize();
    procesaAjax(url, pars, 'resultadosBusqueda', 'Buscando...', false, false);
    return false;
});
</script>