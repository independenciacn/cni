<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
$lista = ":Todas;";
foreach(listadoEmpleados() as $empleado ) {
    $lista.=$empleado['Id'].":".$empleado['empleada'].";";
}
$prioridades = ":Todas;0:Normal;1:Media;2:Alta;3:Urgente";

?>
<table id='listadoTareas'></table>
<div id='pager'></div>
<script>
datePick = function(elem){
	$(elem).datepicker({
		dateFormat:"dd-mm-yy",
		onSelect: function(dateText, inst){ 
			$("#listadoTareas")[0].triggerToolbar();     
		}
	});	
};
$('#listadoTareas').jqGrid({
    caption:'Listado de Tareas Pendientes',
   	url:'tareasJSON.php',
	datatype: 'json',
   	colNames:['Tarea','Prioridad', 'Vencimiento', 'Realizada','Empleada','Descripción Tarea'], 
   	colModel:[
   		{name:'id',index:'id', width:0, hidden:true},
   		{name:'prioridad',index:'prioridad', width:80,align:'center', sortable:true, editable:true, edittype:'select', editoptions:{value:'<?php echo $prioridades; ?>'}, stype:'select'},
   		{name:'vencimiento',index:'vencimiento', width:100, align:'center', sortable:true, editable:true, sorttype:'date', edittype:'text', editoptions: {dataInit : datePick}, stype:'text', searchoptions:{dataInit:datePick, attr:{title:'Selecciona Fecha'}}},
   		{name:'realizada',index:'realizada', width:80, align:'center',sortable:true, editable:true, edittype:'select', editoptions:{value:{'':'Todos','Si':'Si','No':'No'} }, stype:'select' },
   		{name:'empleada',index:'empleada', width:200, align:'left',sortable:true, editable: true, edittype:'select', editoptions:{value:'<?php echo $lista; ?>'}, stype:'select' },				
   		{name:'nombre',index:'nombre', width:450, sortable:false, editable:true, edittype: 'textarea', editoptions:{rows:"5",cols:"70"}, stype:'text'}		
   	],
   	rowNum:10,
   	rowList:[10,20,30],
   	pager: '#pager',
   	sortname: 'vencimiento',
    viewrecords: true,
    sortorder: "desc",
	height:"300",
	editurl: "tareasCRUD.php"	
}).navGrid('#pager',{edit:true,add:true,del:true,save:true,search:false,refresh:false},{width:600});
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