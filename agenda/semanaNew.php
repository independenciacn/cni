<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
?>
<div id='loading' style='display:none'>Cargando Datos...</div>
<div id='calendario'></div>
<script>
$('document').ready(function(){
    $('#calendario').fullCalendar({
    	header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month, basicWeek, basicDay'
		},
		defaultView: 'basicWeek',
		buttonText: {
		    today: 'Hoy',
		    month: 'Mes',
		    week: 'Semana',
		    day: 'Dia'
		},
		firstHour: 8,
		minTime: 8,
		maxTime: 22,
    	firstDay: 1,
    	theme: true,
    	timeFormat: { '': 'H:mm{ - H:mm}' },
    	monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio',
    	             'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    	monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
    	                  'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    	dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles',
    	           'Jueves', 'Viernes', 'Sabado'],
    	dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie',
    	                'Jue', 'Vie', 'Sab'],
        selectable: true,
		selectHelper: true,
		select: function(start, end, allDay) {
			var title = prompt('Descripcion Evento:');
			if (title) {
				calendar.fullCalendar('renderEvent',
					{
						title: title,
						start: start,
						end: end,
						allDay: allDay
					},
					true // make the event "stick"
				);
			}
			calendar.fullCalendar('unselect');
		},
		editable: true,               
        eventSources: [
	               {
	                    url: 'agendaJSON.php', 
	                    color: 'blue',    
	                    textColor: 'white',  
	                    allDayDefault: false,
	                    data: { ocupacion: 'parcial'}
	                },
	                {
	                    url: 'agendaJSON.php', 
	                    color: 'green',    
	                    textColor: 'white',  
	                    allDayDefault: false,
	                    data: { ocupacion: 'total' }
	                }
	            ],
    	loading: function(bool) {
			if (bool) $('#loading').show();
			else $('#loading').hide();
		},
		eventClick: function(calEvent, jsEvent, view) {

	        alert('Event: ' + calEvent.title);
	        alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
	        alert('View: ' + view.name);

	        // change the border color just for fun
	        $(this).css('border-color', 'red');

	    }
    // put your options and callbacks here
    });
});
</script>