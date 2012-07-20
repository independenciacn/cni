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
 * @todo Agregar a funciones generales
 */
var procesaAjax = function (pars, divPrecarga, divCarga, callback) {
    "use strict";
    $.ajax({
        url: 'estadisticas.php',
        type: 'POST',
        dataType: 'html',
        data: pars,
        beforeSend: function() {
            $('#' + divPrecarga).html(imgCarga)
        },
        success: function(data) {
            $('#' + divCarga).html(data);
            if (callback) {
                callback();
            }
        }
    });
};
/**
 * Genera el datepicker
 * 
 * @return {[type]} [description]
 * @todo Agregar a funciones generales
 */
var datePicker = function() {
    $('.datepicker').datepicker({
        dateFormat: "dd-mm-yy",
        changeYear: true,
        changeMonth: true,
        firstDay: 1,
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
        monthNames: 
            ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
            "Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
        monthNamesShort: 
            ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
        yearRange: "2007:nnnn",
    });  
}
/**
 * Funcion que envia la peticion y genera el formulario
 * 
 * @param  {String} form [description]
 * 
 */
var menu = function (formulario) {
    "use strict";
    var pars;
    pars = {opcion:0,form:formulario};
    procesaAjax(pars, 'formulario', 'formulario', datePicker);
};
/**
 * Funcion que recibe la peticion procesa y muestra la respuesta
 * 
 * @return {[type]} [description]
 */
var procesa = function () {
    "use strict";
    var pars;
    pars = "opcion=1" + "&" + $('#consulta').serialize();
    procesaAjax(pars, 'resultados', 'resultados', false);
};
/**
 * Segun el tipo de comparativa muestra una cosa u otra
 * 
 */
var comparativa = function () {
    "use strict";
    var pars;
    pars = {opcion:2,tipo:$('tipo_comparativa').val()};
    procesaAjax(pars, 'comparativas', 'comparativas', datePicker);
};