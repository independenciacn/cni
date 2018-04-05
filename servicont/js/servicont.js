/* global $ */
/* FIXME: Migrar a jQuery */
/*
 * Nueva seccion de estadisticas, tabla rasa Julio 2008,
 * todas actuan en el div resultados, fichero estadisticas.php
 */
/**
 * Funcion que envia la peticion y genera el formulario.
 * @param {string} form
 */
function menu (form) {
  var url = 'estadisticas.php'
  var pars = 'opcion=0&form=' + form
  $.post(url, pars, function (data) {
    $('#formulario').html(data)
  })
}
/*
 * Funcion que recibe la peticion procesa y muestra la respuesta
 */
function procesa() {
  var url ='estadisticas.php'
  var pars ='opcion=1&' + Form.serialize($('consulta'))
  Ajax.Request(url,
    {
      method: 'post',
      parameters: pars,
      onCreate: $('resultados').innerHTML = "<center><img src='imagenes/loading.gif' alt='cargando' /></center>",
      onComplete: function gen (respuesta) {
        $('resultados').innerHTML = respuesta.responseText
      }
    })
}
/*
 * Segun el tipo de comparativa muestra una cosa u otra
 */
function comparativa()
{
  var url="estadisticas.php";
  var pars="opcion=2&tipo="+$F('tipo_comparativa');
  var myAjax = new Ajax.Request(url,
    {
      method:'post',
      parameters: pars,
      onComplete: function gen(respuesta)
      {
        $('comparativas').innerHTML = respuesta.responseText;
        campos_fecha();
      }
    });
  
}
function campos_fecha()
{
  Calendar.setup({
              inputField     :    'fecha_inicio_a',      // id of the input field
              ifFormat       :    '%d-%m-%Y',       // format of the input field
              showsTime      :    true,            // will display a time selector
              button         :    'boton_fecha_inicio_a',   // trigger for the calendar (button ID)
              singleClick    :    false,           // double-click mode
              step           :    1                // show all years in drop-down boxes (instead of every other year as default)
          });
  Calendar.setup({
              inputField     :    'fecha_fin_a',      // id of the input field
              ifFormat       :    '%d-%m-%Y',       // format of the input field
              showsTime      :    true,            // will display a time selector
              button         :    'boton_fecha_fin_a',   // trigger for the calendar (button ID)
              singleClick    :    false,           // double-click mode
              step           :    1                // show all years in drop-down boxes (instead of every other year as default)
          });
  Calendar.setup({
              inputField     :    'fecha_inicio_b',      // id of the input field
              ifFormat       :    '%d-%m-%Y',       // format of the input field
              showsTime      :    true,            // will display a time selector
              button         :    'boton_fecha_inicio_b',   // trigger for the calendar (button ID)
              singleClick    :    false,           // double-click mode
              step           :    1                // show all years in drop-down boxes (instead of every other year as default)
          });
  Calendar.setup({
              inputField     :    'fecha_fin_b',      // id of the input field
              ifFormat       :    '%d-%m-%Y',       // format of the input field
              showsTime      :    true,            // will display a time selector
              button         :    'boton_fecha_fin_b',   // trigger for the calendar (button ID)
              singleClick    :    false,           // double-click mode
              step           :    1                // show all years in drop-down boxes (instead of every other year as default)
          });
}

$(document).ready(function () {
  $('.boton').on('click', function () {
    menu(this.id)
  })
})
