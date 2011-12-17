<?php
/**
 * Pagina principal de asigacion de servicios. 
 * Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007-2008
 */ 
require_once '../inc/configuracion.php';
require_once 'funcionesRapido.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
echo $cabezeraHtml;
?>
<div id='cuerpo' class='container'>
<h1><img src='../<?php echo $imagen['asignacion']; ?>' alt='agenda' /> Asignación</h1>
    <form name='frmDatos' id='frmDatos' method='post' action=''>
        <fieldset>
        <legend>Seleccione Cliente</legend>
        <label for='cliente'>Cliente:</label>
        <input type='hidden' name='idCliente' id='idCliente' />
        <input type='text' name='cliente' id='cliente' 
        placeholder='Seleccione Cliente' size='50'/>
        <label for='fecha'>Seleccione Mes y Año:</label>
        <?php echo seleccionMeses(); ?>
        <?php echo seleccionAnyos(); ?>
        <input type='submit' value='Ver Servicios' />
        <input type='reset' value='Limpiar' /><br/>
        <input id='listadoFacturasCliente' type='button' 
        value='Ver Facturas Cliente' />
        <input id='listadoFacturas' type='button' 
        value='Ver Todas las Facturas' />
        <!--  <input type='button' onclick='cliente_rango(0)' 
                value='>Facturacion Mensual' />
	           <input type='button' onclick='cliente_rango(1)' 
                value='>Facturacion Puntual' />
	           <input type='button' onclick='gestion_facturas(0)' 
                value='>Gestión Facturas'/>
	           <input type='button' onclick='oculta_parametros()' 
                value='>Ocultar Ventana' />
	           <input type='button' onclick='gestion_facturas(1)' 
               value='>Listar todas las facturas' />-->
        </fieldset>
    </form>
    <!--  <table  class='tabla'>
        <tr>
            <td align='left' valign='top' colspan='4'>
                <input type='button' class='boton' onclick='window.close()' 
                value='[X] Cerrar' />
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th valign='top'>
                <input type='hidden' id='id_cliente' name='id_cliente' value=''/>
                <img src='../imagenes/personal.png' alt='cliente' />&nbsp;Cliente:
            </th>
            <td>
                <input type='text' name='cliente' id='cliente' value=''
                autocomplete='off' onkeyup='busca_cliente()' size='60'/>
            </td>
            <th>
                <img src='../imagenes/date.png' alt='Mes' />&nbsp;Mes:
            </th>
            <td>
        		<?php //echo seleccionMeses(); ?>
            </td>
            <td>
				<?php //echo seleccionAnyos(); ?> 
            </td>
            <td>
                <input type='button' class='ver_servicios' 
                    onclick='ver_servicios_contratados()' value='Ver Servicios' />
            </td>
            <td>
                <input type='reset' class='limpiar' value='Limpiar' />
            </td>
        </tr>
        <tr>
            <td colspan='2'>
	           <input type='button' onclick='cliente_rango(0)' 
                value='>Facturacion Mensual' />
	           <input type='button' onclick='cliente_rango(1)' 
                value='>Facturacion Puntual' />
	           <input type='button' onclick='gestion_facturas(0)' 
                value='>Gesti&oacute;n Facturas'/>
	           <input type='button' onclick='oculta_parametros()' 
                value='>Ocultar Ventana' />
	           <input type='button' onclick='gestion_facturas(1)' 
               value='>Listar todas las facturas' /></td>
        </tr>
        </table>
    </form>-->
    <div id='parametros_facturacion'></div>
    <div id='listado_clientes'></div>
    <div id='tabla'><!-- Autogenera la tabla --></div>
    <div id='observa'></div>
    <div id='modificar'></div>
    <div id='debug'></div>
    </div>
    <?php echo $firmaAplicacion; ?>
<script>
$("#cliente").autocomplete({
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
			$('#idCliente').val(ui.item.id);
		}
});
$("#frmDatos").submit( function(){
	var url= "servicios.php";
	var pars = $("#frmDatos").serialize();
	var div = "tabla";
	procesaAjax(url, pars, div, 'Cargando Datos', false, false);
	return false;
});

$('#listadoFacturas').click(function(){
	var url="facturas.php";
	var pars = false;
	var div = "tabla";
	procesaAjax(url, pars, div, 'Cargando Datos', false, false);
	return false;
});

$('#listadoFacturasCliente').click(function(){
	var url="facturas.php";
	if ($("#idCliente").val()!='') {
	    var pars = "idCliente=" + $("#idCliente").val();
	    var div = "tabla";
	    procesaAjax(url, pars, div, 'Cargando Datos', false, false);
	} else {
	    alert('Debe Introducir un Cliente');
	}
	return false;
});
</script>    
</body>
</html>