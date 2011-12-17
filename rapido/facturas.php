<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
sanitize( $_POST );
$url = "facturasJSON.php";
if ( isset( $_POST['idCliente'] ) ) {
    $url .= "?idCliente=".$_POST['idCliente'];
}
?>
<table id='lstFacturas'></table>
<div id='pager'></div>
<script>
datePick = function(elem){
	$(elem).datepicker({
		dateFormat:"dd-mm-yy",
		onSelect: function(dateText, inst){ 
			$("#lstFacturas")[0].triggerToolbar();     
		}
	});	
};

$('#lstFacturas').jqGrid({
    caption:'Listado de Facturas',
   	url:'<?php echo $url; ?>',
	datatype: 'json',
   	colNames:['id','idCliente','Cliente', 'Factura', 'Fecha','Importe','Observaciones'], 
   	colModel:[
   		{name:'id',index:'id', width:0, hidden:true},
   		{name:'idCliente',index:'idCliente', width:0, hidden:true},
   		{name:'cliente',index:'cliente', width:250,align:'left', sortable:true, editable:true, edittype:'select', stype:'select'},
   		{name:'codigo',index:'codigo', width:80, align:'center', sortable:true, editable:true, edittype:'text', stype:'text', },
   		{name:'fecha',index:'fecha', width:80, align:'center',sortable:true, editable:true, sorttype:'date', edittype:'text', editoptions: {dataInit : datePick}, stype:'text',searchoptions:{dataInit:datePick, attr:{title:'Selecciona Fecha'}} },
   		{name:'importe',index:'importe', width:80, align:'center',sortable:true, editable: false, edittype:'text' },				
   		{name:'observaciones',index:'observaciones', width:450, sortable:false, editable:true, edittype: 'textarea', editoptions:{rows:"5",cols:"70"}, stype:'text'}
   			
   	],
   	pager: '#pager',
   	sortname: 'codigo',
    viewrecords: true,
    sortorder: "desc",
	height:"300",
	editurl: "facturasCRUD.php",
	rownumbers: true,
	rownumWidth: 40,
	rowNum:50,
	rowTotal: 2000,
	rowList : [20,30,50],
	scroll:1,
	autowidth: true 
}).navGrid('#pager',{edit:true,add:true,del:true,save:true,search:false,refresh:false},{width:600});
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Ver/Ocultar",title:"Ver/Ocultar Barra Busqueda", buttonicon :'ui-icon-pin-s',
	onClickButton:function(){
		$("#lstFacturas")[0].toggleToolbar();
	} 
});
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Ver Todo",title:"Limpiar Busqueda",buttonicon :'ui-icon-refresh',
	onClickButton:function(){
		$("#lstFacturas")[0].clearToolbar();
	} 
});
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Imprimir",title:"Limpiar Busqueda",buttonicon :'ui-icon-print',
	onClickButton:imprimir 
});
$("#lstFacturas").jqGrid('filterToolbar');

function imprimir() {
	alert('imprimir');
}

</script>
