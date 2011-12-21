<?php
/**
 * Facturas File Doc Comment
 *
 * Pagina Principal de la seccion de Facturacion
 *
 * PHP Version 5.2.6
 *
 * @category Facturas
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
$url = "facturasJSON.php";
if ( isset( $_POST['idCliente'] ) ) {
    $url .= "?idCliente=".$_POST['idCliente'];
}
?>
<table id='lstFacturas'></table>
<div id='pager'></div>
<div id='resultado'></div>
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
	multiselect: true,
	autowidth: true 
}).navGrid('#pager',{edit:false,add:false,del:true,save:false,search:false,refresh:true},{width:600});
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
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Factura",title:"Ver Factura",buttonicon :'ui-icon-newwin',
	onClickButton:factura
});
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Duplicado",title:"Ver Duplicado",buttonicon :'ui-icon-newwin',
	onClickButton:duplicado
});
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Recibo",title:"Ver Recibo",buttonicon :'ui-icon-newwin',
	onClickButton:recibo
});
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Imprimir",title:"Imprimir Factura en PDF",buttonicon :'ui-icon-print',
	onClickButton:imprimir
});
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Enviar",title:"Enviar Facturar por Email",buttonicon :'ui-icon-mail-closed',
	onClickButton:enviar
});
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Imprimir Duplicado",title:"Imprimir Factura Duplicada en PDF",buttonicon :'ui-icon-print',
	onClickButton:imprimirdup
});
$("#lstFacturas").jqGrid('navButtonAdd',"#pager",{caption:"Enviar Duplicado",title:"Enviar Facturar Duplicada por Email",buttonicon :'ui-icon-mail-closed',
	onClickButton:enviardup
});
$("#lstFacturas").jqGrid('filterToolbar');

function imprimir(elem) {
	var gsr = jQuery("#lstFacturas").jqGrid('getGridParam','selarrrow');
	if ( gsr ) {
		var url = "facturasPDF.php";
		window.open( url + "?codigo="+ encodeURI(gsr));
	} else {
		alert("Debes seleccionar al menos una factura");
	}
}
function enviar(elem) {
	var gsr = jQuery("#lstFacturas").jqGrid('getGridParam','selarrrow');
	if ( gsr ) {
		var url = "facturasPDF.php";
		var pars = "codigo=" + encodeURI(gsr) + "&envio=true";
		var div = "resultado";
		procesaAjax(url, pars, div, "Enviando Facturas por Email", true, true);
	} else {
		alert("Debes seleccionar al menos una factura");
	}
}
function imprimirdup(elem) {
	var gsr = jQuery("#lstFacturas").jqGrid('getGridParam','selarrrow');
	if ( gsr ) {
		var url = "facturasPDF.php";
		window.open( url + "?dup=true&codigo="+ encodeURI(gsr));
	} else {
		alert("Debes seleccionar al menos una factura");
	}
}
function enviardup(elem) {
	var gsr = jQuery("#lstFacturas").jqGrid('getGridParam','selarrrow');
	if ( gsr ) {
		var url = "facturasPDF.php";
		var pars = "dup=true&codigo=" + encodeURI(gsr) + "&envio=true";
		var div = "resultado";
		procesaAjax(url, pars, div, "Enviando Facturas por Email", true, true);
	} else {
		alert("Debes seleccionar al menos una factura");
	}
}
function factura(elem){
	var gsr = jQuery("#lstFacturas").jqGrid('getGridParam','selrow');
	if ( gsr ) {
		var url = "generaFactura.php";
		window.open( url + "?duplicado=false&codigo="+ encodeURI(gsr));
	} else {
		alert("Debes seleccionar al menos una factura");
	}
}
function recibo(elem){
	var gsr = jQuery("#lstFacturas").jqGrid('getGridParam','selrow');
	if ( gsr ) {
		var url = "generaRecibo.php";
		window.open( url + "?codigo="+ encodeURI(gsr));
	} else {
		alert("Debes seleccionar al menos una factura");
	}
}
function duplicado(elem) {
	var gsr = jQuery("#lstFacturas").jqGrid('getGridParam','selrow');
	if ( gsr ) {
		var url = "generaFactura.php";
		window.open( url + "?duplicado=true&codigo="+ encodeURI(gsr));
	} else {
		alert("Debes seleccionar al menos una factura");
	}
}

</script>
