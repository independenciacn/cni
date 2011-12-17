// JavaScript Document
//Javascript principal remasterizado
//VALIDACION DE USUARIO
/**
 * Variable que almacena el gif animado utilizado en la solicitud ajax
 */
var loader = "<img src='imagenes/ajax-loader.gif' alt='procesando datos' />";
/**
 * Cambia la visibilidad de la capa dependiendo de como este
 * 
 * @param {String} div
 */
function cambiaVisibilidad( div ) {
	$('#'+ div ).toggle();
}
/**
 * Funcion auxiliar que activa la visibilidad del formulario
 * 
 * @param {String} formulario
 */
function muestraFormulario( formulario ) {
	cambiaVisibilidad( formulario );
}
/**
 * Funcion auxiliar que desactiva la visibilidad del formulario
 * 
 * @param {String} formulario
 */
function cierraFormulario( formulario ) {
	cambiaVisibilidad( formulario );
}
/**
 * Muestra el texto con el loader en el proceso
 * 
 * @param {String} proceso
 * @returns {String}
 */
function procesaDatos( proceso ) {
	var texto = proceso + " ... <br />" + loader;
	return texto;
}
function errorDatos( proceso ) {
	var texto = "Error " + proceso;
	return texto;
}
/*function actualizaRegistro() {
	var texto = "Actualizando Datos ... <br />" + loader ;
	return texto;
}
function borraRegistro() {
	var texto = "Borrando Datos ...<br />" + loader;
	return texto;
}
function agregaRegistro() {
	var texto = "Insertando Datos ...<br />" + loader;
	return texto;
}
function validaDatos() {
	var texto = "Validando Datos ...<br />" + loader;
	return texto;
}
function procesaDatos() {
	var texto = "Procesando Datos ...<br />" + loader;
	return texto;
}*/
/*function parseFunction( func, param ) {
	return func(param);
}*/
/**
 * Procesa la peticion Ajax y devuelve la respuesta
 * 
 * @param {String} url
 * @param {String} pars
 * @param {String} div
 * @param {String} proceso
 */
function procesaAjax( url, pars, div, proceso, callback, params ) {
	$.ajax({
		  	type: 'POST',
			url: url,
			data: pars,
			beforeSend: function( data ) {
				var texto = procesaDatos( proceso );
				$('#' + div).html( texto );
			},
			success: function( data ) {
				$('#' + div).html(data);
			},
			complete: function() {
				if( callback != false ) {
					callback( params );
				}
			}
		});
/*	var myAjax = new Ajax.Request( url,
			{
				method:'post',
				parameters: pars,
				onCreate: procesaDatos( proceso ),
				onError: errorDatos( proceso ),
				onComplete: function gen(t)
				{
					$(div).innerHTML = t.responseText;
					campos_fecha($F('tabla'));
					campos_fecha($F('nombre_tabla'));
					if ( callback ) {
						callback( params );
					}
				}
			 });*/
}

/**
 * Muestra la opcion de menu segun el codigo pasado
 * 
 * @param {String} codigo
 */
function menu( codigo )
{
	var url = "inc/generator.php";
	var pars = "opcion=0&codigo=" + codigo;
	procesaAjax( url, pars, 'principal', 'Cargando Opcion', false, false );
	$('#avisos').hide();
}
/**
 * Muestra la seccion de gestion
 */
function gestion()
{
	var url = "inc/gestion.php";
	var pars = "";
	procesaAjax( url, pars, 'principal', 'Cargando Datos', false, false );
}
/**
 * Muestra la seccion de busqueda
 */
function busqueda()
{
	var url= "inc/busqueda.php";
	var pars = "";
	procesaAjax( url, pars, 'principal', 'Cargando Datos', false, false );
	$('#avisos').hide();
}
/**
 * Muestra el formulario de busqueda
 */
function busca()
{
	$('#resultados').show();
	var url = "inc/generator.php";
	var texto = $('#texto').val().toUpperCase();
	var tabla = $('#tabla').val();
	var pars = encodeURI( "opcion=1&texto="+texto+"&tabla="+tabla );
	procesaAjax( url, pars, 'resultados', 'Buscando Datos', false, false );
}
/**
 * Refractorizar las funciones de cierre y muestra formulario pasandoles
 * el parametro en el otro sitio, de momento esto por compatibilidad
 */
function cierra_frm_busca()
{
	cierraFormulario('resultados');
}
function cierra_el_formulario()
{
	cierraFormulario('formulario');
}
function muestra_el_formulario()
{
	muestraFormulario('formulario');
}

/**
 * Muestra el Formulario del registro seleccionado
 * Solo vale para las raices
 * 
 * @param {String} registro
 */
function muestra( registro ) 
{
	var url = "inc/generator.php";
	var tabla = $('#tabla').val();
	var pars = "opcion=2&registro="+registro+"&tabla="+tabla;
	procesaAjax( url, pars, 'formulario', 'Cargando Datos', false, false );
	campos_fecha( tabla );
	cierraFormulario('resultados');
	muestraFormulario('formulario');
}
/**
 * Genera el Submenu seleccionado del registro seleccionado
 * 
 * @param {Integer} codigo
 */
function submenu( codigo )
{
	var url = "inc/generator.php";
	var registro = $('#idemp').val();
	var pars = "opcion=3&codigo="+codigo+"&registro="+registro;
	procesaAjax( url, pars, 'formulario', 'Cargando Datos', false, false );	
}
/**
 * Funcion generica para los campos de tipo fecha
 * 
 * @param {String} tabla
 */
function campos_fecha( tabla )
{
	var i = 0;
	var fechas = [];
	switch( tabla ) {
		case "facturacion": fechas = ["finicio","duracion","renovacion"];
		break;
		case "pcentral": fechas = ["cumple"];
		break;
		case "pempresa": fechas = ["cumple"];
		break;
		case "z_facturacion": fechas = ["finicio","renovacion"];
		break;
		case "empleados": fechas = ["fnac","fcon"];
		break;
		case "entradas_salidas": fechas = ["entradas","salidas"];
		break;
	}
	if ( fechas.length > 0 ) {
		$.datepicker.setDefaults( $.datepicker.regional[ "" ] );
		for ( i = 0; i < fechas.length; i++ ) {
			$( "#"+fechas[i] ).datepicker( $.datepicker.regional[ "es" ] );
		}
	}
}
/**
 * Actualiza el registro
 */
function actualiza_registro()
{
		var url = "inc/generator.php";
		var registro = $('#numero_registro').val();
		var formulario = $('#formulario_actualizacion');
		var pars = "opcion=4&" + formulario.serialize();
		procesaAjax( url, pars, 'formulario_actualizacion', 'Actualizando Datos', 'muestra', registro );
		//muestra( registro );
		/*var myAjax = new Ajax.Request(url,
								  {
									  method:'post',
									  parameters:pars,
									  onComplete:function gen(t)
										{
											$('debug').innerHTML = t.responseText
											muestra(registro)
											var p=setTimeout("cierra_debug()",2000)
											
										}
								  });*/
	
}
//***********************************************************************************************/
/*function muestra_debug()
{
	$('debug').innerHTML = ""
	var estilo = $('debug').style
	estilo.visibility = "visible"
	estilo.display = "block"
}*/
//***********************************************************************************************/
/*function cierra_debug()
{
	var estilo = $('debug').style
	estilo.visibility = "hidden"
	estilo.display = "none"
}*/
//EDITOR DE TEXTOS***************************************/
/*function editor()
{
		
		var allTextAreas = document.getElementsByTagName("textarea");
        for (var i=0; i < allTextAreas.length; i++) {
        var oFCKeditor = new FCKeditor( allTextAreas[i].name ) ;
        oFCKeditor.BasePath = "FCKeditor/" ;
		oFCKeditor.ToolbarSet = 'Default' ;
        oFCKeditor.ReplaceTextarea() ;
		}
}*/

/**
 * Genera el formulario de nuevo registro
 * 
 * @param {String} codigo
 */
function nuevo( codigo )
{
	//var tabla = $('#tabla').val();
	var url = "inc/generator.php";
	var pars = "opcion=5&tabla="+codigo;
	var callback = 'muestraFormulario';
	var params = 'formulario';
	procesaAjax( url, pars, 'formulario', 'Cargando Datos', callback, params);
	campos_fecha( codigo );
}
/**
 * Agrega el registro
 */
function agrega_registro()
{
	var url = "inc/generator.php";
	var opcion = $('#opcion').val();
	var formulario = $('#formulario_alta');
	var pars = "opcion=6&" + formulario.serialize( );
	var funcion = false;
	var param = false;
	if(opcion == 2 || opcion == 3 || opcion == 4 || opcion == 5 || opcion == 6 || opcion == 8 || opcion == 11) {
		funcion = 'submenu';
		param = opcion;
	} 
	procesaAjax( url, pars, 'formulario_alta', 'Agregando Datos', funcion, param);
}
/**
 * Borra el registro
 * 
 * @param {String} registro
 */
function borrar_registro( registro )
{
	var t = confirm("¿Borrar Registro?");
	if ( t == true) {
	//var codigo = $F('codigo')
		var tabla = $('#nombre_tabla').val();
		var opcion = $('#opcion').val();
		var url = "inc/generator.php";
		var pars = encodeURI( "opcion=7&tabla="+tabla+"&registro="+registro );
		var funcion = 'nuevo';
		var param = $('#nuevo').val();
		if ( opcion != 0) {
			funcion = 'submenu';
			param = opcion;
		}
		procesaAjax( url, pars, 'formulario', 'Borrando Registro', funcion, param);
	} else {
		alert( "No se ha borrado el registro" );
	}
}
/**
 * Muestra el registro
 * 
 * @param {String} registro
 */
function muestra_registro( registro )
{
	var codigo = $('#codigo').val();
	var tabla=$('#nombre_tabla').val();
	//var opcion = $('#opcion').val();
	var url = "inc/generator.php";
	var pars = "opcion=3&codigo="+codigo+"&registro="+registro+"&tabla="+tabla+"&marcado=1";
	var funcion = 'campos_fecha';
	var param = tabla;
	procesaAjax(url, pars, 'formulario', 'Cargando Datos', funcion, param);
}
/**
 * Generamos el formulario para agregar Servicios Fijos que se cobraran
 * mensualmente
 * 
 * @param {String} cliente
 */
function frm_srv_fijo( cliente )
{
	var url = "inc/generator.php";
	var pars = "opcion=8&cliente="+cliente;
	procesaAjax(url, pars, 'frm_srv_fijos', 'Cargando Datos', false, false);
}
/**
 * Muestra el servicio fijo
 * 
 * @param {String} id
 */
function muestra_srv_fijo( id )
{
	var url = "inc/generator.php";
	var pars = "opcion=8&id="+id;
	procesaAjax(url, pars, 'frm_srv_fijos', 'Cargando Datos', false, false);
}
/**
 * Segun el servicio que cargamos se carga su importe y su iva al lado
 */
function cambia_los_otros()
{
	var url ="inc/generator.php";
	var servicio = $('#servicio').val();
	var pars = "opcion=9&servicio="+servicio;
	$.post(url,pars,function( data ){
		var valores = data;
		var lista = valores.split(":");
		$('#importe').val(lista[0]);
		$('#iva').val(lista[1]);
	});
}
/**
 * Funcion que agrega los servicios fijos
 */
function agrega_srv_fijos()
{
	var url ="inc/generator.php";
	var cliente = $('#id_Cliente').val();
	var pars = "opcion=10&"+ $('#frm_srv_fijos').serialize();
	procesaAjax(url, pars, 'frm_srv_fijos', 'Agregando Datos', false, false);
	submenu(2);
	frm_srv_fijo(cliente);
	/*muestra_debug()
	var myAjax = new Ajax.Request(url,
	 {
			method:'post',
			parameters: pars,
			onComplete: function gen(t)
			{
				$('debug').innerHTML = t.responseText
				var p=setTimeout("cierra_debug()",2000)
				submenu(2)
				frm_srv_fijo(cliente)
			}
	});*/
	
}
/**
 * Borra el servicio fijo
 * 
 * @param {String} id
 */
function borra_srv_fijo(id)
{
	var url ="inc/generator.php";
	var pars = "opcion=11&id="+id;
	var funcion = 'submenu';
	var params = 2;
	procesaAjax(url, pars, 'frm_srv_fijos', 'Borrando Datos', funcion, params);
}
/**
 * Actualiza el servicio fijo
 */
function actualiza_srv_fijos()
{
	var url ="inc/generator.php";
	var cliente = $('#id_Cliente').val();
	var pars = "opcion=12&"+ $('#frm_srv_fijos').serialize();
	procesaAjax(url, pars, 'frm_srv_fijos', 'Actualizando Datos', false, false);
	submenu(2);
	frm_srv_fijo(cliente);
	/*muestra_debug()
	var myAjax = new Ajax.Request(url,
	 {
			method:'post',
			parameters: pars,
			onComplete: function gen(t)
			{
				$('debug').innerHTML = t.responseText
				var p=setTimeout("cierra_debug()",2000)
				submenu(2)
				frm_srv_fijo(cliente)
			}
	});*/
}
/**
 * Muestra la lista de las copias de seguridad
 */
/*function lista_backup()
{
	var url="inc/datos_gestion.php";
	var pars="opcion=0";
	procesaAjax(url, pars, 'dialogoGestion', 'Cargando Datos', false, false);
}*/
/**
 * Realiza la copia de seguridad
 */
/*function hacer_backup()
{
	var respuesta = confirm("Hacer Copia de Seguridad?");
	if ( respuesta == true) {
		var url="inc/datos_gestion.php";
		var pars="opcion=1";
		procesaAjax(url, pars, 'dialogoGestion', 'Generando Copia', false, false);
		lista_backup();
		/*var myAjax = new Ajax.Request(url,
			{
				method: 'post',
				parameters: pars,
				onComplete: function gen(respuesta)
				{
					$('estado_copia').innerHTML = respuesta.responseText
					lista_backup()
				},
				onRequest: $('estado_copia').innerHTML = "<center>Generando Copia...<p><img src='imagenes/loader.gif' alt='Generando Copia ...' /></center>"
			});*/
	/*} else {
		alert( "No se ha realizado la copia de seguridad" );
	}
}*/
/**
 * Restauracion de copia de seguridad
 * 
 * @param {String} archivo
 */
function restaurar_backup( archivo )
{
	var respuesta = confirm("Restaurar Copia?");
	if ( respuesta == true ) {
		var url="inc/datos_gestion.php";
		var pars="opcion=2&archivo="+archivo;
		procesaAjax(url, pars, 'estado_copia', 'Restaurnado Copia', false, false);
		/*var myAjax = new Ajax.Request(url,
			{
				method: 'post',
				parameters: pars,
				onComplete: function gen(respuesta)
				{
					$('estado_copia').innerHTML = respuesta.responseText
				},
				onRequest: $('estado_copia').innerHTML = "<center>Restaurando Copia...<p><img src='imagenes/loader.gif' alt='Restaurando Copia ...' /></center>"
			});*/
	} else {
		alert( "No se ha restaurado la copia de seguridad" );
	}
}
/**
 * Borra la copia de seguridad
 * 
 * @param {String} archivo
 */
function borrar_backup( archivo )
{
	var respuesta = confirm("Borrar Copia de Seguridad?");
	if ( respuesta == true ) {
		var url="inc/datos_gestion.php";
		var pars="opcion=3&archivo="+archivo;
		var funcion = 'lista_backup';
		var param = "";
		procesaAjax(url, pars, 'estado_copia', 'Borrando Copia', funcion, param);
	} else {
		alert( "No se ha borrado la copia de seguridad ");
	}
}
/**
 * Revisa las tablas de la base de datos - Opcion Desactivada?
 */
function revisar_tablas()
{
	var url="inc/datos_gestion.php";
	var pars="opcion=4";
	var funcion = 'lista_backup';
	var params = '';
	procesaAjax(url, pars, 'status_tablas', 'Revisando Tablas', funcion, params);
}
/**
 * Reparamos las tablas - Opcion Desactivada?
 */
function reparar_tablas()
{
	var url="inc/datos_gestion.php";
	var pars="opcion=5";
	var funcion = 'lista_backup';
	var params = '';
	procesaAjax(url, pars, 'status_tablas', 'Reparando Tablas', funcion, params);
}
/**
 * Optimizamos las tablas - Opcion Desactivada?
 */
function optimizar_tablas()
{
	var url="inc/datos_gestion.php";
	var pars="opcion=5";
	var funcion = 'lista_backup';
	var params = '';
	procesaAjax(url, pars, 'status_tablas', 'Optimizando Tablas', funcion, params);
}
/**
 * Genera el listado de telefonos del centro
 */
function listado_telefonos()
{
	var url="inc/datos_gestion.php";
	var pars="opcion=10";
	procesaAjax( url, pars, 'listado_copias', 'Cargando Datos', false, false );
}
/**
 * Genera el formulario para agregar telefonos
 */
function formulario_telefonos()
{
	var url="inc/datos_gestion.php";
	var pars="opcion=11";
	procesaAjax( url, pars, 'listado_copias', 'Cargando Datos', false, false );
}
/**
 * Agrega el telefono
 */
function agrega_telefono()
{
	var url="inc/datos_gestion.php";
	var pars="opcion=12&"+$('#frm_agrega_telefono').serialize();
	var funcion = 'formulario_telefonos';
	var params = '';
	procesaAjax(url, pars, 'mensajes_estado', 'Agregando Telefono', funcion, params);
}
/**
 * Observaciones de clientes de despachos con codigos de negocio
 */
function consulta_especial()
{
	var url="inc/datos_gestion.php";
	var pars="opcion=13";
	procesaAjax(url, pars, 'listado_copias', 'Generando Listado', false, false);
}
/**
 * Cierra el listado de copias de seguridad - ELIMINAR Y SUSTITUIR EN HTML
 */
function cierra_listado_copias()
{
	cierraFormulario('listado_copias');
	//$('listado_copias').innerHTML = "";
}
/**
 * Para gestion muestra el listado de los tipos de clientes que hemos
 * seleccionado
 */
function filtra_listado()
{
	var url='inc/datos_gestion.php';
	var pars='opcion=14&tipo='+$('tipo_cliente').value;
	procesaAjax(url, pars, 'listado_copias', 'Generando Listado', false, false);
}
/**
 * Funcion que inicia el proceso de la muestra de cumpleaños 
 * 
 * @param {String} dato
 */
function datos( dato )
{
	cambiaVisibilidad('datos_interesantes');
	var pars='dato='+dato;
	url = 'inc/datins.php';
    /*if ( dato==2 ) {
        url='inc/cumples.php';
    } else {
        url='inc/datins.php';
    }*/
    procesaAjax(url, pars, 'datos_interesantes', 'Cargando Datos', false, false);
}
/**
 * Cierra el formulario de datos Interesantes - ELIMINAR Y SUSTITUIR en HTML
 */
function cierralo()
{
	cierraFormulario('datos_interesantes');
}
/**
 * Abre el popup pasada la URL como parametro
 * 
 * @param URL
 */
function popUp(URL) 
{
		window.open(URL, '" + id + "','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable=0,width=900,height=700');
}
/**
 * Carga las categorias y las muestra por pantalla
 * 
 * @param {String} categoria
 */
function categorias( categoria )
{
	var pars='opcion=7&categoria='+categoria;
	var url='inc/datos_gestion.php';
	procesaAjax(url, pars, 'listado_copias', 'Cargardo Datos', false, false);
}
/**
 * Edita la categoria
 * 
 * @param {String} registro
 */
function editar_categoria( registro )
{
	var pars='opcion=8&categoria='+$('#categoria').val()+'&registro='+registro;
	var url='inc/datos_gestion.php';
	procesaAjax(url, pars, 'detalles_categoria', 'Cargando Datos', false, false);
}
/**
 * Actualiza la categoria
 */
function actualiza_categoria()
{
	var pars='opcion=9&'+$('#formulario_categorias').serialize();
	var url='inc/datos_gestion.php';
	var funcion = 'categorias';
	var params = $('categoria').val();
	procesaAjax(url, pars, 'formulario_categorias', 'Actualizando Datos', funcion, params);
}
/**
 * Muestra el formulario 
 * 
 * @param {String} opcion
 * @param {String} accion
 * @param {String} tipo
 * @param {String} cliente
 */
function ver_detalles(opcion,accion,tipo,cliente) 
{
	var url='inc/detalles.php';
	var observacion = ( opcion != 0) ? encodeURI($('#detalles_obs').val()) : "";
	var pars='opcion='+opcion+'&accion='+accion+'&tipo='+tipo+'&cliente='+cliente+'&observacion='+observacion;
	procesaAjax(url, pars, 'edicion_actividad', 'Cargando Datos', false, false);
}
/**
 * Oculta el formulario - ELIMINAR y RENOMBRAR EN HTML
 */
function cierra_frm_observaciones()
{
	cierraFormulario('edicion_actividad');
	//$('edicion_actividad').innerHTML = "";
}
/**
 * Muestra el campo en telecos
 */
function muestra_campo()
{
	var url='inc/telecos.php';
	var campo =encodeURI( $('#servicio').val() );
	var pars='opcion=0&campo='+campo;
	procesaAjax(url, pars, 'tipo_teleco', 'Cargando Datos', false, false);
}
/**
 * Chequeamos el valor introducido - Esta no se para que vale
 */
function chequea_valor()
{
	var url="inc/telecos.php";
	var valor = $('#valor').val();
	var campo =encodeURI( $('#servicio').val() );
	var pars="opcion=1&campo="+campo+"&valor="+valor;
	$.post(url, pars, function( data ){
		$('#valor').css('background-color', data);
		if ( data == "#ff0000" ) {
			$('#boton_envio').hidden();
		} else {
			$('#boton_envio').show();
		}
	});
}
/**
 * Formulario de busqueda avanzada
 */
function busqueda_avanzada()
{
	var url="inc/bavanzada.php";
	var pars='opcion=0&'+$('#busqueda_avanzada').serialize();
	procesaAjax(url, pars, 'resultados_busqueda_avanzada', 'Cargando Datos', false, false);
}
/*
 * Cierra el panel de avisos - DEPRECATED Borrar
 */
function cerrar_avisos()
{
	$('#avisos').html = "<input class='boton' type='button' onclick='ver_avisos()' value='[>]Avisos' />";
}
/*
 * Muestra el panel de avisos - DEPRECATED Borrar
 */
function ver_avisos()
{
	var url='inc/avisos.php';
	var pars='opcion=0';
	cerrar_tablon_telefonos();
	muestraFormulario('avisos');
	procesaAjax(url, pars, 'avisos', 'Cargando Datos', false, false);
}

/**
 * Muestra el boton de cerrar tablon de telefonos - DEPRECATED SUSTITUIR
 */
function cerrar_tablon_telefonos()
{
	/*$('#tablon_telefonos').html = "<input type='button' onclick='ver_tablon_telefonos()' value='[^] Ver Telefonos' />";
	var estilo = $('tablon_telefonos').style;
	estilo.height = "18px";
	estilo.width = "115px";
	estilo.overflow = "hidden";*/
	cierraFormulario($('#tablon_telefonos'));
}
/**
 * Muestra el tablon de telefonos
 */
function ver_tablon_telefonos()
{
	var url='inc/avisos.php';
	var pars='opcion=1';
	/*var estilo = $('tablon_telefonos').style;
	estilo.height = "600px";
	estilo.width = "900px";
	estilo.overflow = "auto";*/
	muestraFormulario($('#tablon_telefonos'));
	procesaAjax(url, pars, 'tablon_telefonos', 'Cargando Datos', false, false);
}

/**
 * Funciones de los parametros de factura
 */
/**
 * Mostramos el formulario de parametros de factura
 * 
 * @param {Integer} cliente
 */
function parametros_factura(cliente)
{
	
	muestraFormulario('parametros_factura');
	var url='inc/parametros_factura.php';
	var pars='opcion=0&cliente=' + cliente;
	procesaAjax(url, pars, 'parametros_factura', 'Cargando Parametros', false, false);	
}
/**
 * Cierra el formulario de parametros de factura
 */
function cerrar_parametros_factura()
{
	cierraFormulario('parametros_factura');
}
/**
 * Establece la fecha de Facturacion
 * 
 * @param {Integer} cliente
 */
function establecer_fecha(cliente)
{
	var url='inc/parametros_factura.php';
	var pars='opcion=1&cliente='+cliente+'&dia='+$('#fecha_facturacion').val();
	var funcion = 'parametros_factura';
	var params = cliente;
	procesaAjax(url, pars, 'parametros_factura', 'Aplicando Cambios', funcion, params);
}
/**
 * Establece el servicio agrupado
 * 
 * @param {Integer} cliente
 */
function agrupar_servicio(cliente)
{
	var url='inc/parametros_factura.php';
	var pars='opcion=2&cliente='+cliente+'&servicio='+ $('#servicio').val();
	var funcion = 'parametros_factura';
	var params = cliente;
	procesaAjax(url, pars, 'parametros_factura', 'Aplicando Cambios', funcion, params);
}
/**
 * Quita el servicio agrupado
 * 
 * @param id
 * @param cliente
 */
function quitar_agrupado(id,cliente)
{
	var url='inc/parametros_factura.php';
	var pars='opcion=3&id='+id;
	var funcion = 'parametros_factura';
	var params = cliente;
	procesaAjax(url, pars, 'parametros_factura', 'Aplicando Cambios', funcion, params);
}
/**
 * Fin de las funciones de parametros de factura
 */
function ver_extensiones()
{
	var url='inc/telecos.php';
	var pars='opcion=2&despacho='+$('#despacho').val();
	procesaAjax(url, pars, 's_extensiones', 'Cargando Datos', false, false);
}

/**
 * Borra el telefono que se queda libre
 * 
 * @param {String} telefono
 */
function borrar_telefono_asignado( telefono )
{
	var url='inc/datos_gestion.php';
	var pars ='opcion=15&telefono='+telefono;
	var funcion = 'formulario_telefonos';
	var params = '';
	procesaAjax(url, pars, 'edicion_' + telefono, 'Borrando Telefono', funcion, params);
}
/**
 * Edicion de la descripcion del telefono libre
 * 
 * @param {String} telefono
 */
function editar_telefono_asignado( telefono )
{
	var url='inc/datos_gestion.php';
	var pars = 'opcion=16&telefono='+telefono;
	procesaAjax(url, pars, 'edicion_'+telefono, 'Cargando Datos', false, false);
}
/**
 * Actualiza la descripcion del telefono libre
 * 
 * @param {String} telefono
 */
function actualiza_descripcion_telefono( telefono )
{
	var url='inc/datos_gestion.php';
	var descripcion = $('#descripcion_'+ telefono).val();
	var id = $('#identificador_'+ telefono).val();
	var pars = 'opcion=17&telefono='+telefono+'&descripcion='+descripcion+'&id='+id;
	var callback = 'formulario_telefonos';
	var params = '';
	procesaAjax(url, pars, 'edicion_'+telefono, 'Actualizando Datos', callback, params);
}
/**
 * Funcion de la seccion de almacenaje 
 */
/**
 * Muestra el formulario del Almacen
 */
function formularioAlmacen() {
	var url = "formularioAlmacen.php";
	var pars = "cliente=" + $("#idcliente").val();
	var div = "formularioAlmacen";
	var proceso = "Cargando Datos Formulario";
	procesaAjax(url, pars, div, proceso, false, false);
}
/**
 * Muestra el listado del Almacen
 */
function listadoAlmacen() {
	var url = "listadoAlmacen.php";
	var pars = "cliente=" + $("#idcliente").val();
	var div = "listadoAlmacen";
	var proceso = "Cargando Datos Almacenaje";
	procesaAjax(url, pars, div, proceso, false, false);
}