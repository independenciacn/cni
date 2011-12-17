<?php
require_once 'configuracion.php';
if( isset($_POST) && isset( $_SESSION['usuario'] ) ) {
    sanitize($_POST);
    echo submenus( $_POST['tabla'] );
    $pars = "tabla=" . $_POST['tabla'];
    $registro = "";
    if ( isset( $_POST['registro'] ) ) {
        $registro = "&registro=". $_POST['registro']."";
        $pars .= $registro;
    }
?>
<div id='formulario'>hola soy el formulario</div>
<script type='text/javascript'>
$('document').ready(function(){
	var url = 'inc/formularioGenerico.php';
	var pars = '<?php echo $pars; ?>';
	procesaAjax(url, pars, 'formulario', 'Cargando Formulario', false, false);
});
$('.submenus').click(function(){
	var url = 'inc/formularioGenerico.php';
	var pars = 'tabla='+ this.id + '<?php echo $registro; ?>';
	procesaAjax(url, pars, 'formulario', 'Cargando Formulario', false, false);
});
</script>
<?php
} else {
    header("Status: 404 Not Found");
    exit(0);
}
 
function submenus( $tabla ) {
    $html = "";
    $sql = "Select submenus.* from submenus 
    INNER JOIN menus ON menus.id = submenus.menu
    WHERE menus.pagina like '".$tabla."'";
    $resultados = consultaGenerica( $sql, MYSQL_ASSOC );
    foreach( $resultados as $resultado ) {
        $html .= "<input class='submenus' id='".$resultado['pagina']."' 
        type='button' value='".$resultado['nombre']."' />";
    }
    return $html;
}
 // FIN FICHERO