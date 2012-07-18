/**
 * Funciones Javascript de Estadisticas
 *
 * @package  cni/servicont
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */

/*jslint plusplus: true, undef: true, indent: 4, maxlen: 120*/
/*global $, Ajax, Form, $F, Calendar*/

/**
 * Url de destino de las peticiones
 * 
 * @type {String}
 */
var url = "estadisticas.php";
/**
 * Imagen de Cargando...
 * 
 * @type {String}
 */
var imgCarga = "<center><img src='imagenes/loading.gif' alt='cargando' /></center>";
/**
 * Procesa la peticion Ajax
 * 
 * @param  {String}   pars        parametros
 * @param  {String}   divPrecarga div donde se ejecuta la precarga
 * @param  {String}   divCarga    div donde se carga el resultado
 * @param  {Function} callback    funcion que se ejecuta o false si no ha funcion
 * 
 */
var procesaAjax = function (pars, divPrecarga, divCarga, callback) {
    "use strict";
    new Ajax.Request(url,
        {
            method: 'post',
            parameters: pars,
            onCreate: $(divPrecarga).innerHTML = imgCarga,
            onComplete: function gen(respuesta) {
                $(divCarga).innerHTML = respuesta.responseText;
                if (callback) {
                    callback();
                }
            }
        });
};
/**
 * Funcion que envia la peticion y genera el formulario
 * 
 * @param  {String} form [description]
 * 
 */
var menu = function (form) {
    "use strict";
    var pars;
    pars = "opcion=0&form=" + form;
    procesaAjax(pars, 'formulario', 'formulario', false);
};
/**
 * Funcion que recibe la peticion procesa y muestra la respuesta
 * 
 * @return {[type]} [description]
 */
var procesa = function () {
    "use strict";
    var pars;
    pars = "opcion=1&" + Form.serialize($('consulta'));
    procesaAjax(pars, 'resultados', 'resultados', false);
};
/**
 * Genera los datepickers
 * 
 */
var camposFecha = function () {
    "use strict";
    var fields, buttons, i;
    fields = ['fecha_inicio_a', 'fecha_fin_a', 'fecha_inicio_b', 'fecha_fin_b'];
    buttons = ['boton_fecnicio_a', 'boton_fecha_fin_a', 'boton_fecha_inicio_b', 'boton_fecha_fin_b'];
    for (i = 0; i < fields.length; i++) {
        Calendar.setup({
            inputField  :    fields[i],  // id of the input field
            ifFormat    :    '%d-%m-%Y', // format of the input field
            showsTime   :    true,       // will display a time selector
            buttons     :    buttons[i], // trigger for the calendar (button ID)
            singleClick :    false,// double-click mode
            step        :    1   // show all years in drop-down boxes (instead of every other year as default)
        });
    }
};
/**
 * Segun el tipo de comparativa muestra una cosa u otra
 * 
 */
var comparativa = function () {
    "use strict";
    var pars;
    pars = "opcion=2&tipo=" + $F('tipo_comparativa');
    procesaAjax(pars, 'comparativas', 'comparativas', camposFecha);
};