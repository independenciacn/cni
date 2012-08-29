/**
 * Funciones Javascript de ayuda para Prototype de la aplicación
 *
 * @package  cni
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */

/*jslint plusplus: true, undef: true, indent: 4, maxlen: 120*/
/*global $, Ajax, Form, $F, Calendar, confirm, console*/

/**
 * Clase datosAjax. Estructura
 */
function DatosAjax() {
    "use strict";
    this.pars = false;
    this.divName = false;
    this.divPrecarga = false;
    this.callback = false;
    this.params = false;
    this.url = false;
    this.imgCarga = "<img src='../estilo/custom-theme/images/ajax-loader.gif' " +
        "alt='cargando' />";
    this.msgError = "<span class='alert alert-danger'>" +
        "<strong>Error</strong>Consulte Parametros</span>";
    this.precarga = function () {
        if (this.divName) {
            $(this.divName).innerHTML = this.imgCarga;
        }
    };
    this.failure = function () {
        if (this.divName) {
            $(this.divName).innerHTML = this.msgError;
        }
    };
}
/**
 * Establece los parametros de la consulta Ajax
 *
 * @param {String} pars
 */
DatosAjax.prototype.setPars = function (pars) {
    "use strict";
    this.pars = (!pars) ? false : pars;
};
/**
 * Establece el Div de la carga de Datos
 *
 * @param {String} divName
 */
DatosAjax.prototype.setDivName = function (divName) {
    "use strict";
    this.divName = (!divName) ? false : divName;
};
/**
 * Establece el Div de Precarga de datos
 *
 * @param {String} divPrecarga
 */
DatosAjax.prototype.setDivPrecarga = function (divPrecarga) {
    "use strict";
    this.divPrecarga = (!divPrecarga) ? false : divPrecarga;
};
/**
 * Establece la funcion de respuesta de la peticion Ajax
 *
 * @param {String} callback
 */
DatosAjax.prototype.setCallback = function (callback) {
    "use strict";
    this.callback = (!callback) ? false : callback;
};
/**
 * Establece los parametros de la funcion de respuesta
 *
 * @param {String} params
 */
DatosAjax.prototype.setParams = function (params) {
    "use strict";
    this.params = (!params) ? false : params;
};
/**
 * Establece la URL de la peticion
 *
 * @param {String} url
 */
DatosAjax.prototype.setUrl = function (url) {
    "use strict";
    this.url = (!url) ? false : url;
};
/**
 * Procesa las peticiones Ajax y devuelve los resultados
 */
DatosAjax.prototype.procesaAjax = function () {
    "use strict";
    var options, myAjax;
    options = {
        method: 'post',
        parameters: this.pars,
        onSuccess: function gen(t) {
            if (this.divName) {
                $(this.divName).innerHTML = t.responseText;
            }
            if (this.callback) {
                this.callback(this.params, t);
            }
        },
        onFailure: this.failure(),
        onCreate: this.precarga()
    };
    if (this.url) {
        myAjax = new Ajax.Request(this.url, options);
    } else {
        console.log('Error: Falta la URL de destino');
    }
};

/**
 * Muestra la capa seleccionada
 *
 * @param {String} divName
 */
function muestraCapa(divName) {
    "use strict";
    var estilo = $(divName).style;
    estilo.visibility = "visible";
    estilo.display = "block";
}
/**
 * Oculta la capa seleccionada
 *
 * @param {String} divName
 */
function ocultaCapa(divName) {
    "use strict";
    var estilo = $(divName).style;
    estilo.visibility = "hidden";
    estilo.display = "none";
}
/**
 * Cambia la visiblidad de la capa
 *
 * @param {String} divName
 */
function cambiaVisibilidad(divName) {
    "use strict";
    var estilo;
    estilo = $(divName).style;
    if (estilo.visibility === "visible") {
        ocultaCapa(divName);
    } else {
        muestraCapa(divName);
    }
}