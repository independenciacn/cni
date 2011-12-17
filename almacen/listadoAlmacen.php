<?php
/**
 * listadoAlmacen File Doc Comment
 *
 * Genera el listado de la seccion de almacenaje
 *
 * PHP Version 5.2.6
 *
 * @category ListadoAlmacen
 * @package  cni/almacen
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */ 
require_once '../inc/configuracion.php';
if ( isset($_SESSION['usuario'] ) && isset( $_POST ) && isset( $_POST['cliente'] ) ) {
    sanitize( $_POST );
} else {
    notFound();
}
?>
<table>
<thead>
<tr>
<th>#</th>
<th>Bultos</th>
<th>Fecha Inicio</th>
<th>Fecha Fin</th>
<th>Total €</th>
<th></th>
</thead>
<tbody>
<?php
/**
 * Generamos la tabla
*/
$i = 1;
$importeBase = importeAlmacen();
$sql = "Select id, bultos,
date_format( inicio, '%d-%m-%Y' ) as fechaInicio,
date_format( fin, '%d-%m-%Y' ) as fechaFin
from z_almacen where cliente like
". $_POST['cliente'] ." AND
(Year( now( ) ) - Year( inicio )) <= 1";
$resultados = consultaGenerica( $sql, MYSQL_ASSOC );
foreach( $resultados as $resultado ) {
    $calculo = calculo( $resultado );
    $class = ($calculo == 'En Almacen') ? "class='almacen'":'';
    echo "<tr ".$class.">
    <td>". $i++ ."</td>
    <td>".$resultado['bultos']."</td>
    <td>".$resultado['fechaInicio']."</td>
    <td>".$resultado['fechaFin']."</td>
    <td>".$calculo."</td>
    <td>
    <img id='edit_".$resultado['id']."' class='tipTip accion'
    src='../estilo/iconos/editar.png' alt='Editar'
    title='Editar el almacenaje' />
    <img id='del_".$resultado['id']."'class='tipTip accion'
    src='../estilo/iconos/borrar.png' alt='Borrar'
    title='Borrar el almacenaje' />
    </td>
    </tr>";
}
?>
</tbody>
</table>
<script type="text/javascript">
$(function(){
	$(".tipTip").tipTip();
});
$(".accion").click(function() {
    var vars = this.id.split("_");
	if ( vars[0] == "edit" ) {
	    var url = "formularioAlmacen.php";
	    var pars = "cliente="+ $('#idcliente').val() + "&item=" + vars[1];
	    var div = "formularioAlmacen";
	    var proceso = "Cargando Datos";
	    procesaAjax(url, pars, div, proceso, false, false);
	    $("#resultadoAlmacen").html("<div class='notice'>Modificacion de Registro</div>");
	    $.scrollTo( "#" + div, 800 );
	}
	if ( vars[0] == "del" ) {
	    if ( true == confirm("Desea borrar este almacenaje?") ) {
	        var url = "funcionesAlmacen.php";
	        var pars = "opcion=del&item="+ vars[1];
	        var div = "resultadoAlmacen";
	        var proceso = "Borrando Registro";
	        procesaAjax(url, pars, div, proceso, listadoAlmacen, "");
	        $.scrollTo("#"+ this.id , 800 );
		} else {
		    alert("No se ha borrado el Almacenaje");
		}
	}
});
</script>
<?php 
/**
 * Carga el valor del almacenaje
 *
 * @return integer
 */
function importeAlmacen()
{
    $sql = "Select PrecioEuro from servicios2 where nombre like 'Almacenaje'";
    $resultado = consultaUnica($sql);
    return $resultado[0];
}
/**
 * Calcula el valor del almacenaje
 *
 * @param array $vars
 * @return string $total
 */
function calculo( $vars )
{
    global $importeBase;

    if($vars['fechaFin'] == '00-00-0000' )
    {
        $dias = round( (strtotime( date("d-m-Y") ) - strtotime( $vars['fechaInicio'] ) )/(24*60*60),0);
        $total = $vars['bultos'] * $dias * $importeBase;
        $total = 'En Almacen';
    } else {
        $dias = round((strtotime( $vars['fechaFin'] )-strtotime( $vars['fechaInicio'] ) )/(24*60*60),0);
        $total = $vars['bultos'] * $dias * $importeBase;
        $total = round( $total, 2 );
        $total = precioFormateado( $total );
    }
    return $total;
}