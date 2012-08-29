/**
 * Funciones Javascript Generales de la aplicación
 *
 * @package  cni
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */

/*jslint plusplus: true, undef: false, indent: 4, maxlen: 120 */
/*global $, Ajax, Form, $F, Calendar, confirm, console, worker, jQuery, prototypejs*/
/*jshint undef:false */
/*global $:true, jQuery:true, worker:true, prototypejs:true, udef:true */

/**
 * URL Generica de la aplicacion
 * @type {String}
 */
var urlDatos = "inc/generator.php";
/**
 * Valida el usuario
 */
function validar() {
    "use strict";
    var datos, pars;
    pars = "opcion=0&usuario=" + $F('usuario') + "&passwd=" + $F('passwd');
    datos = new DatosAjax();
    datos.setUrl("inc/validacion.php");
    datos.setPars(pars);
    datos.setDivName("cuerpo");
    datos.setDivPrecarga("cuerpo");
    datos.procesaAjax();
}
/**
 * Muestra el menu de la aplicación
 *
 * @param {Number} codigo
 */
function menu(codigo) {
    "use strict";
    var datos, pars;
    pars = "opcion=0&codigo=" + codigo;
    datos = new DatosAjax();
    datos.setUrl(urlDatos);
    datos.setPars(pars);
    datos.setDivName("principal");
    datos.setDivPrecarga("principal");
    datos.procesaAjax();
}
/**
 * Busca el cliente
 */
function busca() {
    "use strict";
    var datos, pars;
    muestraCapa('resultados');
    pars = "opcion=1&texto=" + $('texto').toUpperCase + "&tabla=" + $F('tabla');
    datos = new DatosAjax();
    datos.setUrl(urlDatos);
    datos.setPars(encodeURI(pars));
    datos.setDivName("resultados");
    datos.procesaAjax();
}
/**
 * Cierra el formulario de busqueda
 */
function cierra_frm_busca() {
    "use strict";
    ocultaCapa('resultados');
}
/**
 * Cierra el formulario
 */
function cierra_el_formulario() {
    "use strict";
    ocultaCapa('formulario');
}
/**
 * Muestra el formulario
 */
function muestra_el_formulario() {
    "use strict";
    var estilo = $('formulario').style;
    estilo.visibility = "visible";
    estilo.display = "block";
}
/**
 * Cierra el formulario de busqueda, carga los campos de fecha y muestra
 * el formulario
 *
 * @param {String} tabla
 */
function callbackFrmBusqueda(tabla) {
    "use strict";
    campos_fecha(tabla);
    cierra_frm_busca();
    muestra_el_formulario();
}
/**
 * Muestra los datos relacionados con la busqueda general
 *
 * @param {String} registro
 */
function muestra(registro) {
    "use strict";
    var datos, pars;
    pars = "opcion=2&registro=" + registro + "&tabla=" + $F("tabla");
    datos = new DatosAjax();
    datos.setUrl(urlDatos);
    datos.setPars(pars);
    datos.setDivName("formulario");
    datos.setCallback(callbackFrmBusqueda);
    datos.setParams($F("tabla"));
    datos.procesaAjax();
}
/**
 * Muestra el submenu
 *
 * @param codigo
 */
function submenu(codigo) {
    "use strict";
    var datos, pars;
    pars = "opcion=3&codigo=" + codigo + "&registro=" + $F("idemp");
    datos = new DatosAjax();
    datos.setUrl(urlDatos);
    datos.setPars(pars);
    datos.setDivName("formulario");
    datos.setCallback(campos_fecha);
    datos.setParams($F("nombre_tabla"));
    datos.procesaAjax();
}

//funcion generica para los campos de fecha
function campos_fecha(tabla) {
    "use strict";
    var campos, x;
    switch (tabla) {
        case "facturacion":
            campos = ["finicio", "duracion", "renovacion"];
        break;
        case "pcentral":
            campos = ["cumple"];
        break;
        case "pempresa":
            campos = ["cumple"];
        break;
        case "z_facturacion":
            campos = ["finicio", "renovacion"];
        break;
        case "empleados":
            campos = ["fnac", "fcon"];
        break;
        case "entradas_salidas":
            campos = ["entrada", "salida"];
        break;
        default :
            campos = [];
        break;
    }
    if (campos.length > 0) {
        for (x in campos) {
            Calendar.setup({
                inputField: campos[x], // id of the input field
                ifFormat: '%d-%m-%Y', // format of the input field
                showsTime: true, // will display a time selector
                button: 'f_trigger_' + campos[x], // trigger for the calendar (button ID)
                singleClick: false, // double-click mode
                step: 1                // show all years in drop-down boxes (instead of every other year as default)
            });
        }
    }
}
//funcion nueva de actualizacion
function actualiza_registro() {
    var url = "inc/generator.php";
    var registro = $F('numero_registro');
    var formulario = $('formulario_actualizacion');
    var pars = "opcion=4&" + Form.serialize(formulario);
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('debug').innerHTML = t.responseText;
                muestra(registro);
                var p = setTimeout("cierra_debug()", 2000);

            }
        });

}

function muestra_debug() {
    $('debug').innerHTML = "";
    var estilo = $('debug').style;
    estilo.visibility = "visible";
    estilo.display = "block";
}

function cierra_debug() {
    var estilo = $('debug').style;
    estilo.visibility = "hidden";
    estilo.display = "none";
}
/**
 * Editor de textos
 */
function editor() {

    var allTextAreas = document.getElementsByTagName("textarea");
    for (var i = 0; i < allTextAreas.length; i++) {
        var oFCKeditor = new FCKeditor(allTextAreas[i].name);
        oFCKeditor.BasePath = "FCKeditor/";
        oFCKeditor.ToolbarSet = 'Default';
        oFCKeditor.ReplaceTextarea();
    }
}

//generacion de un nuevo registro
function nuevo(codigo) //generacion del formulario de uno nuevo
{

    var tabla = $F('tabla');
    var url = "inc/generator.php";
    var pars = "opcion=5&tabla=" + codigo;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('formulario').innerHTML = t.responseText;
                //vamos a mirar aqui casos de tabla pe en empleados
                campos_fecha(tabla);
                muestra_el_formulario();
            }
        });
}

function agrega_registro() {
    var url = "inc/generator.php";
    var opcion = $F('opcion');
    var formulario = $('formulario_alta');
    muestra_debug();
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:"opcion=6&" + Form.serialize(formulario),
            onComplete:function gen(t) {
                busca();
                $('debug').innerHTML = t.responseText;
                var p = setTimeout("cierra_debug()", 2000);
                //en principo el submenu solo es para clientes y proveedores //PEGOTEE
                if (opcion == 2 || opcion == 3 || opcion == 4 || opcion == 5 || opcion == 6 || opcion == 8 || opcion == 11)
                    submenu(opcion);
            }

        });
}

function borrar_registro(registro) {
    var t = confirm("�Borrar Registro?");
    if (t == true) {
        //var codigo = $F('codigo')
        var tabla = $F('nombre_tabla');
        var opcion = $F('opcion');
        var url = "inc/generator.php";
        var pars = "opcion=7&tabla=" + tabla + "&registro=" + registro;
        pars = encodeURI(pars);
        muestra_debug();
        var myAjax = new Ajax.Request(url,
            {
                method:'post',
                parameters:pars,
                onComplete:function gen(t) {
                    busca();
                    $('debug').innerHTML = t.responseText;
                    var p = setTimeout("cierra_debug()", 2000);
                    //$('formulario').innerHTML = ""
                    if (opcion != 0)
                        submenu(opcion);
                    else
                        nuevo($F('nuevo'));
                }
            });
    }
}

function muestra_registro(registro) {

    var codigo = $F('codigo');
    var tabla = $F('nombre_tabla');
    var opcion = $F('opcion');
    var url = "inc/generator.php";
    var pars = "opcion=3&codigo=" + codigo + "&registro=" + registro + "&tabla=" + tabla + "&marcado=1";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('formulario').innerHTML = t.responseText;
                campos_fecha(tabla);
            }
        });
}

//FACTURACION SERVICIOS FIJOS

//GENERAMOS EL FORMULARIO PARA AGREGAR SERVICIOS FIJOS QUE SE COBRARAN MENSUALMENTE
function frm_srv_fijo(cliente) {
    var url = "inc/generator.php";
    var pars = "opcion=8&cliente=" + cliente;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('frm_srv_fijos').innerHTML = t.responseText;
            }
        });
}
function muestra_srv_fijo(id) {

    var url = "inc/generator.php";
    var pars = "opcion=8&id=" + id;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('frm_srv_fijos').innerHTML = t.responseText;
            }
        });
}
//Segun el servicio que cargamos se carga su importe y su iva al lado
function cambia_los_otros() {
    var url = "inc/generator.php";
    var servicio = $('servicio').value;
    var pars = "opcion=9&servicio=" + servicio;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                var valores = new String(t.responseText);
                var lista = valores.split(":");
                $('importe').value = lista[0];
                $('iva').value = lista[1];
            }
        });
}

function agrega_srv_fijos() {
    var url = "inc/generator.php";
    var cliente = $F('id_Cliente');
    var pars = "opcion=10&" + Form.serialize($('frm_srv_fijos'));
    muestra_debug();
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('debug').innerHTML = t.responseText;
                var p = setTimeout("cierra_debug()", 2000);
                submenu(2);
                frm_srv_fijo(cliente);
            }
        });
}

function borra_srv_fijo(id) {
    var url = "inc/generator.php";
    var pars = "opcion=11&id=" + id;
    muestra_debug();
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('debug').innerHTML = t.responseText;
                var p = setTimeout("cierra_debug()", 2000);
                submenu(2);
            }
        });
}

function actualiza_srv_fijos() {
    var url = "inc/generator.php";
    var cliente = $F('id_Cliente');
    var pars = "opcion=12&" + Form.serialize($('frm_srv_fijos'));
    muestra_debug();
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('debug').innerHTML = t.responseText;
                var p = setTimeout("cierra_debug()", 2000);
                submenu(2);
                frm_srv_fijo(cliente);
            }
        });
}

//PARTE DE LAS COPIAS DE SEGURIDAD

/**
 * Muestra el formulario de Modificación de Contraseña de acceso
 */
function nuevaPass() {
    var url = "inc/datos_gestion.php";
    var pars = "opcion=18";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('listado_copias').innerHTML = respuesta.responseText;
            }
        });
}
/**
 * Manda el valor de la nueva pass y si todo es correcto actualiza
 */
function estableceNuevaPass() {
    var url = 'inc/datos_gestion.php';
    var pars = "opcion=19&" + Form.serialize($('nuevaPass'));
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('resultadoNuevaPass').innerHTML = respuesta.responseText;
            }
        });
}

function lista_backup() {
    var url = "inc/datos_gestion.php";
    var pars = "opcion=0";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('listado_copias').innerHTML = respuesta.responseText;
            }
        });
}

function hacer_backup() {
    var respuesta = confirm("Hacer Copia de Seguridad?");
    if (respuesta == true) {
        var url = "inc/datos_gestion.php";
        var pars = "opcion=1";
        var myAjax = new Ajax.Request(url,
            {
                method:'post',
                parameters:pars,
                onComplete:function gen(respuesta) {
                    $('estado_copia').innerHTML = respuesta.responseText;
                    lista_backup();
                },
                onRequest:$('estado_copia').innerHTML = "<center>Generando Copia...<p><img src='imagenes/loader.gif' alt='Generando Copia ...' /></center>"
            });
    }
}

function restaurar_backup(archivo) {
    var respuesta = confirm("Restaurar Copia?");
    if (respuesta == true) {
        var url = "inc/datos_gestion.php";
        var pars = "opcion=2&archivo=" + archivo;
        var myAjax = new Ajax.Request(url,
            {
                method:'post',
                parameters:pars,
                onComplete:function gen(respuesta) {
                    $('estado_copia').innerHTML = respuesta.responseText;
                },
                onRequest:$('estado_copia').innerHTML = "<center>Restaurando Copia...<p><img src='imagenes/loader.gif' alt='Restaurando Copia ...' /></center>"
            });
    }
}

function borrar_backup(archivo) {
    var respuesta = confirm("Borrar Copia?");
    if (respuesta == true) {
        var url = "inc/datos_gestion.php";
        var pars = "opcion=3&archivo=" + archivo;
        var myAjax = new Ajax.Request(url,
            {
                method:'post',
                parameters:pars,
                onComplete:function gen(respuesta) {
                    $('estado_copia').innerHTML = respuesta.responseText;
                    lista_backup();
                },
                onRequest:$('estado_copia').innerHTML = "<center>Borrando Copia...<p><img src='imagenes/loader.gif' alt='Borrando Copia ...' /></center>"
            });
    }
}
function revisar_tablas() {
    var url = "inc/datos_gestion.php";
    var pars = "opcion=4";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('status_tablas').innerHTML = respuesta.responseText;
                lista_backup();
            },
            onRequest:$('status_tablas').innerHTML = "<center>Revisando Tablas...<p><img src='imagenes/indicator.gif' alt='Revisando Tablas ...' /></center>"
        });
}
function reparar_tablas() {
    var url = "inc/datos_gestion.php";
    var pars = "opcion=5";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('status_tablas').innerHTML = respuesta.responseText;
                lista_backup();
            },
            onRequest:$('status_tablas').innerHTML = "<center>Reparando Tablas...<p><img src='imagenes/indicator.gif' alt='Reparando Tablas ...' /></center>"
        });
}
function optimizar_tablas() {
    var url = "inc/datos_gestion.php";
    var pars = "opcion=5";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('status_tablas').innerHTML = respuesta.responseText
                lista_backup();
            },
            onRequest:$('status_tablas').innerHTML = "<center>Optimizando Tablas...<p><img src='imagenes/indicator.gif' alt='Optimizando Tablas ...' /></center>"
        });
}

//listado_telefonos() -> Genera el listado de telefonos del centro

function listado_telefonos() {
    var url = "inc/datos_gestion.php";
    var pars = "opcion=10";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('listado_copias').innerHTML = respuesta.responseText;
            }
        });
}

//formulario_telefonos() -> Genera el formulario para agregar telefonos

function formulario_telefonos() {
    var url = "inc/datos_gestion.php";
    var pars = "opcion=11";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('listado_copias').innerHTML = respuesta.responseText;
            },
            onCreate:$('listado_copias').innerHTML = "<center><p class='validacion'>Generando Listado...<br/><img src='imagenes/loader.gif' alt='Generando Listado' /></p></center>"

        });
}
function agrega_telefono() {
    var url = "inc/datos_gestion.php";
    var pars = "opcion=12&" + Form.serialize($('frm_agrega_telefono'));
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('mensajes_estado').innerHTML = respuesta.responseText;
                formulario_telefonos();
            }
        });
}
function consulta_especial()//Observaciones de clientes de despachos con codigos de negocio
{
    var url = "inc/datos_gestion.php";
    var pars = "opcion=13";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('listado_copias').innerHTML = respuesta.responseText;
            },
            onCreate:$('listado_copias').innerHTML = "<center><p class='validacion'>Generando Listado...<br/><img src='imagenes/loader.gif' alt='Generando Listado' /></p></center>"
        });
}
function cierra_listado_copias() {
    $('listado_copias').innerHTML = "";
}
//Para gestion muestra el listado de los tipos de clientes que hemos seleccionado
function filtra_listado() {
    var url = 'inc/datos_gestion.php';
    pars = 'opcion=14&tipo=' + $('tipo_cliente').value;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('listado_copias').innerHTML = respuesta.responseText;
            },
            onCreate:$('listado_copias').innerHTML = "Generando listado"
        });
}

function datos(dato) //funcion qui iniica el proceso de muestra de cumplea�os
{
    var estilo = $('datos_interesantes').style;
    //directamente tenemos que recibir el contenido del fichero, ho hacen falta parametros
    estilo.visibility = "visible";
    pars = 'dato=' + dato;
    if (dato == 2)
        url = 'inc/cumples.php';
    else
        url = 'inc/datins.php';
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('datos_interesantes').innerHTML = respuesta.responseText;
            }
        });
}

function cierralo() {
    var estilo = $('datos_interesantes').style;
    estilo.visibility = "hidden";
}

function popUp(URL) {
    window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable=0,width=900,height=700');
}

function categorias(categoria) //carga las categorias y las muestra por pantalla
{
    pars = 'opcion=7&categoria=' + categoria;
    url = 'inc/datos_gestion.php';
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('listado_copias').innerHTML = respuesta.responseText;
            }
        });
}

function editar_categoria(registro) {
    pars = 'opcion=8&categoria=' + $F('categoria') + '&registro=' + registro;
    url = 'inc/datos_gestion.php';
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('detalles_categoria').innerHTML = respuesta.responseText;
            }
        });
}


function actualiza_categoria() {
    pars = 'opcion=9&' + Form.serialize($('formulario_categorias'));
    url = 'inc/datos_gestion.php';
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                //categorias($F('categoria'))
                $('resultadoActCategoria').innerHTML = respuesta.responseText;
                var p = setTimeout("categorias($F('categoria'))", 2000);
            }
        });
}

function ver_detalles(opcion, accion, tipo, cliente) //muestra el formulario
{
    url = 'inc/detalles.php'; //vamos a separar cosas
    if (opcion != 0)
        observacion = encodeURI($F('detalles_obs'));
    else
        observacion = "";
    pars = 'opcion=' + opcion + '&accion=' + accion + '&tipo=' + tipo + '&cliente=' + cliente + '&observacion=' + observacion;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('edicion_actividad').innerHTML = respuesta.responseText;
            }
        });
}

function cierra_frm_observaciones() //oculta el formulario
{
    $('edicion_actividad').innerHTML = "";
}


function muestra_campo() {
    url = 'inc/telecos.php'; //continuamos separando cosas
    campo = encodeURI($('servicio').value);
    pars = 'opcion=0&campo=' + campo;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('tipo_teleco').innerHTML = respuesta.responseText
            }
        });
}
function chequea_valor() {
    var url = "inc/telecos.php";
    var valor = $F('valor');
    var campo = encodeURI($('servicio').value);
    var pars = "opcion=1&campo=" + campo + "&valor=" + valor;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                var estilo = $('valor').style;
                estilo.background = respuesta.responseText;
                var boton = $('boton_envio').style;
                if (respuesta.responseText == "#ff0000") //no funciona pero lo dejo
                    boton.visibility = "hidden"; //si no es correcto no se visualiza el boton
                else
                    boton.visibility = "visible";
            }
        });
}

function busqueda_avanzada() {
    var url = "inc/bavanzada.php";
    pars = 'opcion=0&' + Form.serialize($('busqueda_avanzada'));
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('resultados_busqueda_avanzada').innerHTML = respuesta.responseText;
            }
        });
}

/*
 * Tablon de Avisos
 */
/*
 * Cierra el panel de avisos
 */
function cerrar_avisos() {
    $('avisos').innerHTML = "<input class='boton' type='button' onclick='ver_avisos()' value='[>]Avisos' />";
}
/*
 * Muestra el panel de avisos
 */
function ver_avisos() {
    var url = 'inc/avisos.php';
    pars = 'opcion=0';
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                var estilo = $('avisos').style;
                estilo.visibility = 'visible';
                estilo.display = 'block';
                estilo.height = '90%';
                estilo.width = '100%';
                $('avisos').innerHTML = respuesta.responseText;
            },
            onSuccess:cerrar_tablon_telefonos
        });
}

function cerrar_tablon_telefonos() {
    $('tablon_telefonos').innerHTML = "<input type='button' onclick='ver_tablon_telefonos()' value='[^] Ver Telefonos' />";
    var estilo = $('tablon_telefonos').style;
    estilo.height = "18px";
    estilo.width = "115px";
    estilo.overflow = "hidden";
}

function ver_tablon_telefonos() {
    var url = 'inc/avisos.php';
    pars = 'opcion=1';
    var estilo = $('tablon_telefonos').style;
    estilo.height = "600px";
    estilo.width = "900px";
    estilo.overflow = "auto";
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('tablon_telefonos').innerHTML = respuesta.responseText;
            }
        });
}
//PARA ESTABLECER LOS PARAMETROS DE LA FACTURA
function parametros_factura(cliente) {
    var estilo = $('parametros_factura').style;
    estilo.visibility = "visible";
    estilo.display = "block";
    var url = 'inc/parametros_factura.php';
    pars = 'opcion=0&cliente=' + cliente;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(respuesta) {
                $('parametros_factura').innerHTML = respuesta.responseText;
            }
        });
}
function cerrar_parametros_factura() {
    var estilo = $('parametros_factura').style;
    estilo.visibility = "hidden";
    estilo.display = "none";
    $('parametros_factura').innerHTML = "";
}
function establecer_fecha(cliente) {
    var url = 'inc/parametros_factura.php';
    pars = 'opcion=1&cliente=' + cliente + '&dia=' + $F('fecha_facturacion');
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:parametros_factura(cliente)
        });
}
function agrupar_servicio(cliente) {
    var url = 'inc/parametros_factura.php';
    pars = 'opcion=2&cliente=' + cliente + '&servicio=' + $('servicio').value;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:parametros_factura(cliente)
        });
}
function quitar_agrupado(id, cliente) {
    var url = 'inc/parametros_factura.php';
    pars = 'opcion=3&id=' + id;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:parametros_factura(cliente)
        });
}
function ver_extensiones() {
    var url = 'inc/telecos.php';
    pars = 'opcion=2&despacho=' + $('despacho').value;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('s_extensiones').innerHTML = t.responseText;
            }
        });
}

/*
 * Borra el telefono que se queda libre
 */
function borrar_telefono_asignado(telefono) {
    var url = 'inc/datos_gestion.php';
    pars = 'opcion=15&telefono=' + telefono;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                alert(t.responseText);
                formulario_telefonos();
            }
        });
}
/*
 * Edicion de la descripcion del telefono libre
 */
function editar_telefono_asignado(telefono) {
    var url = 'inc/datos_gestion.php';
    pars = 'opcion=16&telefono=' + telefono;
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                $('edicion_' + telefono).innerHTML = t.responseText;
            }
        });
}
/*
 * Actualiza la descripcion del telefono libre
 */
function actualiza_descripcion_telefono(telefono) {
    var url = 'inc/datos_gestion.php';
    var descripcion = $F('descripcion_' + telefono);
    var id = $F('identificador_' + telefono);
    pars = 'opcion=17&telefono=' + telefono + '&descripcion=' + descripcion + '&id=' + id;
    console.log(pars);
    var myAjax = new Ajax.Request(url,
        {
            method:'post',
            parameters:pars,
            onComplete:function gen(t) {
                formulario_telefonos();
            }
        });
}
