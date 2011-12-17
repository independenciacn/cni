<?php
/**
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
<h1><img src='../<?php echo $imagen['almacenaje']; ?>' alt='almacenaje' /> Almacenaje</h1>
<form id='cliente' action='' method='post'>
    <fieldset>
    <legend>
    <img src='../<?php echo $imagen['clientes']; ?>' alt='Clientes' />
    Clientes
    </legend>
    <input class='text' type='text' id='texto' name='texto' 
        placeholder='Introduce el nombre del cliente' />
    <input type='reset' value='Limpiar texto' />
</fieldset>
</form>
<div id='almacenaje'><!-- Seccion Autogenerada --></div>
</div>
<?php echo $firmaAplicacion; ?>
<script type='text/javascript'>
$("#texto").autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: "../inc/busquedaJSON.php",
				dataType: "json",
				data: {
					table: 'clientes',
					maxrows: 12,
					text: request.term
				},
				success:function(data){ response(data); }
			});
		},
		minLength: 2,
		select: function( event, ui ) {
		    var url = 'principalAlmacen.php';
		    var pars = 'cliente=' + ui.item.id; 
		    procesaAjax( url, pars, 'almacenaje', 'Cargando Datos', false, false ); 
        }
});
</script>
</body>
</html> 