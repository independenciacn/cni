<?php
/**
 * Servicios File Doc Comment
 *
 * Pagina Principal de la seccion de la Asiganción de Servicios
 *
 * PHP Version 5.2.6
 *
 * @category Servicios
 * @package  cni/rapido
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
sanitize( $_POST );
$url = "serviciosJSON.php?idCliente=".$_POST['idCliente']."&anyo=".$_POST['anyo']."&mes=".$_POST['mes'];
$hoy = date('d-m-Y');
?>
<table id='listadoServicios'></table>
<div id='pager'></div>
<script>
datePick = function(elem){
	$(elem).datepicker({
		dateFormat:"dd-mm-yy",
		
	});	
};
autoComplete = function(elem){
	$(elem).autocomplete({
	source: function( request, response ) {
		$.ajax({
			url: "serviciosListadoJSON.php",
			dataType: "json",
			data: {
				table: 'servicios2',
				maxrows: 12,
				text: request.term
			},
			success:function(data){ response(data); }
		});
	},
	minLength: 2,
	select: function( event, ui ) {
	    var precio = parseFloat( ui.item.precio );
	    var iva = parseFloat( ui.item.iva );
	    var cantidad = parseFloat( $('#cantidad').val() );
	    var importe = parseFloat( cantidad * precio );
	    var subtotal = parseFloat( importe * ( 1 + ( iva/100 ) ) );
		$('#precio').val( precio.toFixed(2) );
		$('#iva').val( iva.toFixed(2) );
		$('#importe').val( importe.toFixed(2) );
		$('#subtotal').val( subtotal.toFixed(2) );
	}
});
};
changePrice = function(elem){
    var cantidad = parseFloat($('#cantidad').val());
    var precio = parseFloat($('#precio').val());
    var iva = parseFloat( $('#iva').val() );
    if ( isFinite( cantidad ) && isFinite( precio ) && isFinite( iva ) ) {
        var importe = parseFloat( cantidad * precio);
        var subtotal = parseFloat(importe * ( 1 + (iva/100) ) );
        $('#importe').val( importe.toFixed(2) );
        $('#subtotal').val( subtotal.toFixed(2) );
    }
};

$('#listadoServicios').jqGrid({
    caption:'Listado de Servicios Asignados Mes/Año Cliente',
   	url:'<?php echo $url; ?>',
	datatype: 'json',
   	colNames:['Id','idPedido','idCliente','Fecha','Servicio','Observaciones','Cantidad','Precio Unidad','Importe','Iva','Total'], 
   	colModel:[
   		{name:'id',index:'id', hidden:true},
   		{name:'idPedido',index:'idPedido', hidden:true, editable:true, edittype:'text',editrules:{edithidden:true}, formoptions:{label:'Nº Pedido'}, editoptions:{readonly:true}},
   		{name:'idCliente',index:'idCliente', hidden:true, editable:true, edittype:'text',editrules:{edithidden:true}, formoptions:{label:'Nº Cliente'}, editoptions:{readonly:true, value:'<?php echo $_POST['idCliente']; ?>'}},
   		{name:'fecha',index:'fecha', width:80,align:'center', editable:true,  edittype:'text', editoptions: {dataInit : datePick, size: 10, value:'<?php echo $hoy; ?>'}},
   		{name:'servicio',index:'servicio', width:80,align:'center', editable:true, edittype:'text', editoptions:{dataInit : autoComplete}},
   		{name:'observaciones',index:'observaciones', width:80, align:'center', sortable:false, editable:true, edittype:'text', editoptions:{size:50}},
   		{name:'cantidad',index:'cantidad', width:80, align:'center', editable:true, edittype:'text', editoptions:{dataEvents:[{type: 'keyup', fn:changePrice }], size:5, value:'1.00'}},
   		{name:'precio',index:'precio', width:80, align:'center',sortable:true, editable:true, edittype:'text', editoptions:{dataEvents:[{type: 'keyup', fn:changePrice }]}},
   		{name:'importe',index:'importe', width:80, align:'left',sortable:true, editable: true, edittype:'text',editoptions:{readonly:true}},				
   		{name:'iva',index:'iva', width:80, align:'left',sortable:true, sortable:false, editable: true, edittype:'text', editoptions:{dataEvents:[{type: 'keyup', fn:changePrice }],size:4}},				
   		{name:'subtotal',index:'subtotal', width:80, align:'left',sortable:false, editable: true, edittype:'text', editoptions:{readonly:true}},				
   	],
   	rowNum:10,
   	rowList:[10,20,30],
   	pager: '#pager',
   	sortname: 'fecha',
    viewrecords: true,
    sortorder: "desc",
	height:"300",
	autowidth:true,
	editurl: "serviciosCRUD.php",
	rownumbers: true,
	rownumWidth: 40,
	footerrow: true,
    userDataOnFooter: true	
}).navGrid('#pager',{edit:true,add:true,del:true,save:true,search:false,refresh:true},{width:600});
$("#listadoTareas").jqGrid('navButtonAdd',"#pager",{caption:"Ver/Ocultar",title:"Ver/Ocultar Barra Busqueda", buttonicon :'ui-icon-pin-s',
	onClickButton:function(){
		$("#listadoTareas")[0].toggleToolbar();
	} 
});
$("#listadoTareas").jqGrid('navButtonAdd',"#pager",{caption:"Ver Todo",title:"Limpiar Busqueda",buttonicon :'ui-icon-refresh',
	onClickButton:function(){
		$("#listadoTareas")[0].clearToolbar();
	} 
});
$("#listadoTareas").jqGrid('filterToolbar');
</script>