/*
 * Cambio de vistas de la principal, segun la vista carga una hoja u otra, ahora habra que ver las funciones
 */
var agenda = {
    opciones: ["despachos.php", "semana.php", "interna.php", "tareas.php", "notas.php"],
    cambiaVista: function(vista) {
        $.post(this.opciones[vista], function(data){
            $('#vista').html(data);
        });
    }
}
$('#tipoVista').on('change', function(){
    agenda.cambiaVista($(this).val());
});

/* function cambia_vista()
{
    switch($('tipo_vista').value)
    {
        case "0":var url="despachos.php"
        break
        case "1":var url="semana.php"
        break
        case "2":var url="interna.php"
        break
        case "3":var url="tareas.php"
        break
        case "4":var url="notas.php"
    }
    var myAjax = new Ajax.Request(url,
        {
            method: 'post',
            onComplete: 
            function gen(t) 
            {	 
                $('vista').innerHTML = t.responseText
                if ($('tipo_vista').value!=0) 
                {
                    Calendar.setup({
                    inputField     :    'semana',      // id of the input field
                    ifFormat       :    '%d-%m-%Y',       // format of the input field
                    showsTime      :    false,            // will display a time selector
                    button         :    'f_trigger_semana',   // trigger for the calendar (button ID)
                    singleClick    :    true,           // double-click mode
                    step           :    1                // show all years in drop-down boxes (instead of every other year as default)
                    })
                if($('tipo_vista').value == 3)
                tareas_no_realizadas()
                }
                
            }
        });
}*/