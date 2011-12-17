<?php
/**
 * BORRAR
 */
require_once '../inc/configuracion.php';
if ( !isset( $_SESSION['usuario'] ) && !isset( $_POST ) ) {
    notFound();
} else {
    sanitize( $_POST );
}
$j = 0;
$cantidad = 0;
$sql = "
Select d.Servicio as servicio, d.Cantidad as cantidad, 
date_format(c.fecha,'%d-%m-%Y') as fecha, 
d.PrecioUnidadEuros as precio, d.ImporteEuro as importe, 
d.iva as iva, c.`Id Pedido` as idPedido,
d.Observaciones as observaciones, 
d.Id as id, 
(d.ImporteEuro * (1 + (d.iva/100) ) ) as subtotal
from `detalles consumo de servicios` as d
join `consumo de servicios` as c on c.`Id Pedido` = d.`Id Pedido`
where c.Cliente like " . $_POST['idCliente'] ." and (" . $_POST['anyo'] . "
like date_format(c.fecha,'%Y') and '" . $_POST['mes'] . "'
like date_format(c.fecha,'%c')) order by c.fecha asc";
$resultados = consultaGenerica($sql);
/* Repasar
 * $almacenaje = almacenaje( $vars['idCliente'], $vars['mes'], $vars['anyo'] );
	$html .= $almacenaje[0];
	$subtotal = $almacenaje[1];
	$total = $almacenaje[2];
 */
?>
<input id='agregarServicios' type='button' value='Agregar Servicios' />
<div id='frmServicios'><!-- El formulario de servicios aqui --></div>
<table class='tabla'>
    <thead>
	<tr>
	    <th>Fecha</th>
	    <th>Servicio</th>
	    <th>Cantidad</th>
	    <th>Precio Unidad</th>
	    <th>Importe</th>
	    <th>Iva</th>
	    <th>Total</th>
	</tr>
	</thead>
	<tbody>
<?php
	foreach( $resultados as $resultado ) {
		
//acumulados
		$total = $resultado['subtotal'] + $total;
		$cantidad = $resultado['cantidad'] + $cantidad;
//fin acumulados
	?>
	<tr>
	<td><?php echo $resultado['fecha']; ?></td>
	<!--  <td>
	    <input class='accion' type='button' value='Modificar' 
	    id='edit_<?php echo $resultado['id']; ?>' />
	</td>
	<td>    
	    <input class='accion' type='button' value='Borrar'
	    id='del_<?php echo $resultado['id']; ?>' />
	</td> -->
	<td>
	    <?php echo $resultado['servicio'] . " " . $resultado['observaciones']; ?>
	</td>
	<td>
	    <?php echo $resultado['cantidad']; ?>
	</td>
	<td>
	    <?php echo precioFormateado( $resultado['precio'] ); ?>
	</td>
	<td>
	    <?php echo precioFormateado( $resultado['importe'] ); ?>
	</td>
	<td>
	    <?php echo $resultado['iva']; ?>
	</td>
	<td>
	    <?php echo precioFormateado($resultado['subtotal']); ?>
	</td>
	</tr>
<?php } ?>
	
	
	    <tr>
	    <th colspan='4'>&nbsp;</th>
	    <th><?php echo $cantidad; ?></th>
	    <th colspan='3'>&nbsp;</th>
	    <th><?php echo precioFormateado( $total ); ?></th>
	</tbody>
	</table>
    <script>
    $('.tabla').flexigrid();
    $("#agregarServicios").click(function(){
        var url='formularioServicios.php';
	    var pars='opt=new&cliente='+ $('#idCliente').val();
	    var div='frmServicios';
	    procesaAjax(url, pars, div, 'Cargando Formulario', false, false );
    });
    $(".accion").click(function(){
        var url='formularioServicios.php';
        var div='frmServicios';
        var ops = this.id.split('_');
        if ( ops[0] == 'edit' ) {
            var pars = 'opt=edit&cliente='+ $("#idCliente").val();
            procesaAjax(url, pars, div, 'Cargando Datos en Formulario', false, false);
        }
        if ( ops[0] == 'del') {
            if ( confirm("Desea Borrar la Asignacion? " ) ){
                var pars = 'opt=edit&cliente='+ $('#idCliente').val();
                procesaAjax(url, pars, div, 'Borrando Asignación', false, false);
            } else {
                alert('No se ha borrado la asignacion');
            }
        }
    });
	</script>