/**
 * Funciones Javascript de Asignacion de servicios
 *
 * @package  cni/rapido
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */

/*jslint plusplus: true, undef: true, indent: 4, maxlen: 120*/
/*global $, Ajax, Form, $F, Calendar*/

/**
 * Url principal donde van las consultas Ajax
 * 
 * @type {String}
 */
var url = "datos.php";
/**
 * Imagen que muestra el cargando mientras carga datos
 * 
 * @type {String}
 */
var imgCarga = "<center>" +
		"<img src='../estilo/custom-theme/images/ajax-loader.gif' " +
		"alt='cargando' />" +
		"</center>";
/**
 * Muestra el mensaje de error
 * 
 * @type {String}
 */
var msgError = "<center>" +
		"<span class='alert alert-danger'>" + 
		"<strong>Error</strong> Consulte Parametros</span>";
/**
 * Muestra la precarga de datos en el div especificado
 * 
 * @param  {[type]} div Capa especificada
 * 
 * @return {[type]}     [description]
 */
var precarga = function (div) {
	if (div) {
		$(div).innerHTML = imgCarga;
	}
};
/**
 * Muestra el mensaje de fallo en el div especificado
 * 
 * @param  {[type]} div capa
 * 
 * @return {[type]}     [description]
 */
var failure = function (div) {
	if (div) {
		$(div).innerHTML = msgError;
	}
};
/**
 * Cambia la visibilidad de la capa
 * 
 * @param  {[type]} div capa
 * 
 * @return {[type]}     [description]
 */
var cambiaVisibilidad = function (div) {
	var estilo = $(div).style;
	if (estilo.visibility == "visible") {
		estilo.visibility = "hidden";
		estilo.display = "none";
	} else {
		estilo.visibility = "visible";
		estilo.display = "block";
	}
};
/**
 * [procesaAjax description]
 * 
 * @param  {[type]}   pars        [description]
 * @param  {[type]}   div         [description]
 * @param  {[type]}   divPrecarga [description]
 * @param  {Function} callback    [description]
 * @param  {[Type]}   params      callback params
 * @return {[type]}               [description]
 */
var procesaAjax = function (pars, div, divPrecarga, callback, params) {
	var options = {
			method: 'post',
			parameters: pars,
			onSuccess: function (respuesta) {
				$(div).innerHTML = respuesta.responseText;
				if (callback) {
					callback(params);
				}
			},
			onFailure: failure(divPrecarga),
			onCreate: precarga(divPrecarga)	
		};
	new Ajax.Request(url, options);
};
/**
 * Busca el cliente que escribimos y muestra el listado en el 
 * campo de texto
 * 
 * @return {[type]} [description]
 */
var buscaCliente = function ()
{
	var pars = "opcion=1&texto=" + $F('cliente');
	procesaAjax(pars, 'listado_clientes', false, false, false);
};
/**
 * Una vez marcado el cliente borra el listado, y muestra la funcion de
 * servicios contratados
 * 
 * @param  {[type]} idCliente [description]
 * 
 * @return {[type]}           [description]
 */
var marca = function (idCliente)
{
	var pars = "opcion=2&cliente=" + idCliente;
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onSuccess: function (respuesta) { 
			var datos = respuesta.responseText.split('#');
			$('id_cliente').value = datos[0];
			$('cliente').value = datos[1];
			$('listado_clientes').innerHTML = "";
			verServiciosContratados(datos[0]);
		}
	});
};
/**
 * Hacemos click en ver servicios y vemos que es lo que ha contratado
 * ademas de poder editarlo
 * 
 * @param  {[type]} idCliente [description]
 * 
 * @return {[type]}           [description]
 */
var verServiciosContratados = function (idCliente)
{
	if (idCliente === false) {
		idCliente = $F('id_cliente');
	}
	var pars = "opcion=3&cliente=" + idCliente + 
		"&mes=" + $('meses').value + "&anyo=" + $('anyo').value;
	procesaAjax(pars, 'tabla', 'tabla', false, false);
};
/**
 * Borrra el servicio asignado al cliente
 * 
 * @param  {[type]} servicio [description]
 * 
 * @return {[type]}          [description]
 */
var borra = function (servicio)
{
	var respuesta = confirm("Borrar servicio?");
	if (respuesta) {
		var pars = "opcion=4&servicio=" + servicio;
		procesaAjax(pars, 'tabla', 'tabla', verServiciosContratados, false);
	}
};
/**
 * Funcion que muestra el panel de modificacion de servicios
 * 
 * @param  {[type]} servicio [description]
 * 
 * @return {[type]}          [description]
 */
var modificar = function (servicio)
{
	cambiaVisibilidad('modificar');
	var pars = "opcion=5&servicio=" + servicio;
	procesaAjax(pars, 'modificar', 'modificar', false, false);
};
/**
 * Recoge y manda los datos de modificación del servicio
 * 
 * @param  {[type]} servicio [description]
 * 
 * @return {[type]}          [description]
 */
var modifica = function (servicio)
{
	var pars = "opcion=6&servicio=" + servicio + 
		"&" + Form.serialize($('modificacion'));
	procesaAjax(pars, 'tabla', 'tabla', verServiciosContratados, false);
};
/**
 * Cierra el formulario de modificacion
 * 
 * @return {[type]} [description]
 */
var cierraFrmModificacion = function ()
{
	cambiaVisibilidad('modificar');
};
/**
 * Si modificamos el precio lo recalcula
 * 
 * @return {[type]} [description]
 */
var recalcula = function ()
{
	var url = '../inc/moneyHandler.php';
	var pars = 'precio=' + $F('precio') + '&cantidad=' + $F('cantidad') +
		'&iva=' + $F('iva');
	new Ajax.Request(url, {
		method: 'post',
		parameters: pars,
		onSuccess: function (respuesta) {
			var datos = respuesta.responseText.split('#');
			$('importe').innerHTML = datos[0];
			$('total').innerHTML = datos[1];
		}
	});	
};
/**
 * Creación del formulario de asignación de servicios
 * 
 * @param  {[type]} cliente [description]
 * 
 * @return {[type]}         [description]
 */
var frmAgregarServicio = function (cliente)
{
	var pars='opcion=7&cliente=' + cliente;
	procesaAjax(pars,'form_agregar', 'form_agregar', false, false);
};
/**
 * Segun el servicio seleccionado devuelve un valor u otro
 * 
 * @return {[type]} [description]
 */
var valorServicio = function ()
{
	var servicio = $('servicios').value;
	var pars='opcion=8&servicio=' + $('servicios').value;
	new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onSuccess: function (respuesta) {
			var datos = respuesta.responseText.split('#');
			$('precio').value = datos[0];
			$('iva').value = datos[1];
			recalcula();
		}
	});
};

//Pasamos los datos del formulario a la pagina de datos para agregar el servicio y mostramos la respuesta.
function agrega_servicio()
{
	var url='datos.php'
	var cliente = $F('id_cliente')
	var formulario = $('frm_alta')
	var pars='opcion=9&cliente='+cliente+'&'+Form.serialize(formulario)
	var myAjax = new Ajax.Request(url,
		{
		method: 'post',
		parameters: pars,
		onComplete: function gen(t)
			{
				precarga()
				$('debug').innerHTML = t.responseText
			}
		});
	
}
//precarga de actualizacion de datos*****************************************************************/
// function precarga()
// {
// 	var t
// 	$("tabla").innerHTML='Actualizando Datos ... <p/><img src="../imagenes/loader.gif" alt="Actualizando Datos.." />';
// 	//mes(mes_actual)
// 	t = setTimeout("ver_servicios_contratados()",1)
// }


//muestra las observaciones del servicio*********************************************************
function observaciones(servicio)
{
	var url="datos.php"
	var pars='opcion=10&servicio='+servicio
	var estilo = $('observa').style
	estilo.display='block'
	estilo.visibility='visible'
	var myAjax = new Ajax.Request(url,
		{
		method: 'post',
		parameters: pars,
		onComplete: function gen(t)
			{
				$('observa').innerHTML = t.responseText
			}
		});
	
}
//cierra la ventana de observaciones***************************************************************
function cierra_ventana_observaciones()
{
	var estilo = $('observa').style
	estilo.display = "none";
	estilo.visibility = "hidden";
}

//LANZA LA FUNCION DE GENFACTURA****************************************************************
function genera_factura(codigo)
{
	var cliente = $F('id_cliente');
	var mes=$('meses').value;
	var fecha_factura = $F('fecha_factura');
	var observaciones = encodeURI($F('observaciones'));
	var codigo = $F('codigo');
	//parte nueva claseado
	if($F('tipo') == 1) //puntual
	{
	 	var fecha_inicial_factura = $F('fecha_inicial_factura');
	 	var fecha_final_factura = $F('fecha_final_factura');
	}
	else
	{
		var fecha_inicial_factura = "0000-00-00";
		var fecha_final_factura = "0000-00-00";
	}
	if(cliente != 0 || mes !=0)
	{
	var url = "genfactura.php?mes="+mes+"&cliente="+cliente+"&fecha_factura="+fecha_factura+"&codigo="+codigo+"&fecha_inicial_factura="+fecha_inicial_factura+"&fecha_final_factura="+fecha_final_factura+"&observaciones="+observaciones;
	window.open(url);
	}
	else
	alert("Debe seleccionar un cliente y Mes");
}
function genera_factura_prueba(codigo)
{
	var cliente = $F('id_cliente');
	var mes=$('meses').value;
	var fecha_factura = $F('fecha_factura');
	var observaciones = encodeURI($F('observaciones'));
	var codigo = $F('codigo');
	if($F('tipo') == 1) //puntual
	{
	 	var fecha_inicial_factura = $F('fecha_inicial_factura');
	 	var fecha_final_factura = $F('fecha_final_factura');
	}
	else
	{
		var fecha_inicial_factura = "0000-00-00";
		var fecha_final_factura = "0000-00-00";
	}
	if(cliente != 0 || mes !=0)
	{
		var url = "genfactura.php?mes="+mes+"&cliente="+cliente+"&fecha_factura="+fecha_factura+"&codigo="+codigo+"&fecha_inicial_factura="+fecha_inicial_factura+"&fecha_final_factura="+fecha_final_factura+"&observaciones="+observaciones
	+"&prueba=1";
	window.open(url);
	cliente_rango(0);
	}
	else
	alert("Debe seleccionar un cliente y Mes");
}
//GENERA EL LISTADO DE SERVICIOS PARA IMPRIMIR*************************************************
function generar_excel()
{
	var cliente = $F('id_cliente')
	var mes=$('meses').value
    var anyo=$('anyo').value
	var fecha_factura = $F('fecha_factura')
	var codigo = $F('codigo')
	if($F('tipo') == 1) //puntual
	{
	 	var fecha_inicial_factura = $F('fecha_inicial_factura')
	 	var fecha_final_factura = $F('fecha_final_factura')
	}
	else
	{
		var fecha_inicial_factura = "foo"
		var fecha_final_factura = "foo"
	}
	if(cliente != 0 || mes !=0)
	{
	var url = "excel.php?anyo="+anyo+"&mes="+mes+"&cliente="+cliente+"&fecha_factura="+fecha_factura+"&codigo="+codigo+"&fecha_inicial_factura="+fecha_inicial_factura+"&fecha_final_factura="+fecha_final_factura
	
	window.open(url,"menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes")
	}
	else
	alert("Debe seleccionar un cliente y Mes");
}
//GESTION DE FACTURAS - PRINCIPAL*************************************************************
function gestion_facturas(tipo) //ya genera el listado
{
	var url="datos.php"
	var pars
	tipo = eval(tipo)
	if(tipo == 1)
	{
		pars='opcion=11&tipo=1'
		
	}
	else
	{
		var cliente = $F('id_cliente')
		var mes = $('meses').value
		pars='opcion=11&cliente='+ cliente +'&mes='+ mes + '&tipo=0'
	}
	var estilo = $('tabla').style
	estilo.display='block'
	estilo.visibility='visible'
	var myAjax = new Ajax.Request(url,
		{
		method: 'post',
		parameters: pars,
		onComplete: function gen(t)
			{
				$('tabla').innerHTML = t.responseText
			}
		});
}
//Genera el listado de las facturas del cliente en tiempo que hemos marcado
function listado_facturas()
{
	var url="datos.php"
	var cliente = $F('id_cliente')
	var mes = $('meses').value
	var pars='opcion=12&cliente='+ cliente +'&mes='+ mes
	var myAjax = new Ajax.Request(url,
		{
		method: 'post',
		parameters: pars,
		onComplete: function gen(t)
			{
				$('facturacion').innerHTML = t.responseText
			}
		});
		
}
//Borra una factura , pregunta y lo del contador como lo hacemos
function borrar_factura(factura)
{
	var url="datos.php"
	var cliente = $F('id_cliente')
	var mes = $('meses').value
	var pars='opcion=13&cliente='+ cliente +'&mes='+ mes +'&factura='+factura
	var myAjax = new Ajax.Request(url, {
		method: 'post',
		parameters: pars,
		//onComplete: 
		onComplete: listado_facturas()
		//onCreate:$('tabla_resultados').innerHTML = "Cargando...<p/><img src='loader.gif' alt='Cargando...'/>"
	
	});
		
}
function ver_factura(id)
{
	
	var url = "genfactura.php?factura="+id
	window.open(url)
}
function duplicado_factura(id)
{
	
	var url = "genfactura.php?duplicado="+id
	window.open(url)
}
function genera_recibo(id)
{
	var url = "genrecibo.php?id="+id
	window.open(url)
	
}
//###################################Nueva facturacion#########################################
function cliente_rango(opcion)
{
 	var url='nufact.php'
	var cliente = $F('id_cliente')	
	var pars='opcion='+opcion+'&cliente='+cliente
	var myAjax = new Ajax.Request(url,{
	method: 'post',
	parameters: pars,
	onComplete: function gen(t)
		{
			$('parametros_facturacion').innerHTML = t.responseText
			campos_fecha(opcion)
			
		}
	});
}
function campos_fecha(opcion)
{
	if(opcion==1)
	{
		Calendar.setup({
		inputField     :    'fecha_inicial_factura',     
		ifFormat       :    '%d-%m-%Y',       
		showsTime      :    true,            
		button         :    'f_trigger_fecha_inicial_factura',   
		singleClick    :    false,           
		step           :    1                
		})
		Calendar.setup({
		inputField     :    'fecha_final_factura',      
		ifFormat       :    '%d-%m-%Y',       
		showsTime      :    true,            
		button         :    'f_trigger_fecha_final_factura',   
		singleClick    :    false,           
		step           :    1                
		})
	}
	Calendar.setup({
	inputField     :    'fecha_factura',      
	ifFormat       :    '%d-%m-%Y',       
	showsTime      :    true,            
	button         :    'f_trigger_fecha_factura',   
	singleClick    :    false,           
	step           :    1
	})
}
function oculta_parametros()
{
	$('parametros_facturacion').innerHTML = "";
}
function cambia_color(id)
{
	var estilo = $('linea_'+id).style
	estilo.backgroundColor = "#00FF00"
}
function quitar_color(id)
{
	var estilo = $('linea_'+id).style
	estilo.backgroundColor = ""
}
//sorts del listado de facturas
function sort(seccion,valor,tipo)
{
	var url="datos.php"
	var pars='opcion=11&seccion='+seccion+'&valor='+valor+'&tipo='+tipo
	var myAjax = new Ajax.Request(url,
		{
		method: 'post',
		parameters: pars,
		onComplete: function gen(t)
			{
				$('tabla_resultados').innerHTML = t.responseText
			},
		onCreate:$('tabla_resultados').innerHTML = "Cargando...<p/><img src='loader.gif' alt='Cargando...'/>"
		});
}
/**
 * Aplica los filtros al listado de facturas
 */
function filtro(filtro)
{
	var busca = false;
	var url="datos.php";
	var texto=$F('filtro_'+filtro);
	var pars='opcion=14&filtro='+filtro+'&texto='+texto;
	// filtro 2 y texto mayor o igual a 10
	if ( filtro == 2 ) {
		if ( texto.length >= 10 ) {
			busca = true;
		}
	} else {
		if ( texto.length >= 3 ) { // minimo 3 caracteres para los demas
			busca = true;
		}
	}
	if ( busca ) {
		var myAjax = new Ajax.Request(url,
		{
		method: 'post',
		parameters: pars,
		onComplete: function gen(t)
			{
				$('tabla_resultados').innerHTML = t.responseText
			},
		onCreate:$('tabla_resultados').innerHTML = "Cargando...<p/><img src='loader.gif' alt='Cargando...'/>"
		});
	}
}
function sort(seccion)
{
	var url="datos.php"
	//var texto=$F('filtro_'+filtro)
	var marca_cliente = $F('marca_cliente')
	var marca_factura = $F('marca_factura')
	var marca_fecha = $F('marca_fecha')
	var marca_importe = $F('marca_importe')
	var pars='opcion=15&seccion='+seccion+'&marca_cliente='+marca_cliente+'&marca_factura='+marca_factura+'&marca_fecha='+marca_fecha+'&marca_importe='+marca_importe
	var myAjax = new Ajax.Request(url,
		{
		method: 'post',
		parameters: pars,
		onComplete: function gen(t)
			{
				$('tabla_resultados').innerHTML = t.responseText
			},
		onCreate:$('tabla_resultados').innerHTML = "Cargando...<p/><img src='loader.gif' alt='Cargando...'/>"
		});
}
function guarda_check_pdf(dup)
{
	if (dup == 1)
		var adic = "&dup=1"
	else
		var adic = ""
	var x = document.getElementsByName("code")
	var y = new Array()
	var w = 0
	var linea = ""
	for(z=0;z<x.length;z++)
	{
		if(x[z].checked)
		{
		
		var url='facturapdf.php'
		linea = linea + "<br>Generada Factura " +x[z].value
			var myAjax = new Ajax.Request(url,
			{
				method: 'post',
				parameters:'factura='+x[z].value+ adic,
				onComplete: function gen(t)
				{
					$('linea_generacion').innerHTML = linea
				}
			
			});
		}
	
	}
}

function check_all()
{
	var x = document.getElementsByName("code")
	for(z=0;z<x.length;z++)
	if(x[z].checked == false)
	{
		x[z].checked=true
	}
	
}
function uncheck_all()
{
	var x = document.getElementsByName("code")
	for(z=0;z<x.length;z++)
	if(x[z].checked == true)
	{
		x[z].checked=false
	}
}
function envia_check_pdf(dup)
{
	
	var x = document.getElementsByName("code")
	var y = new Array()
	var w = 0
	
	for(z=0;z<x.length;z++)
	{
		if(x[z].checked)
		{
			var t = setTimeout("envia_la_factura("+x[z].value+","+dup+")",5000)
		}
	}

}
function envia_la_factura(numero,dup)
{
	if (dup == 1)
		var adic = "&dup=1"
	else
		var adic = ""
	var url='facturapdf.php'
	var linea = $('linea_generacion').
	linea = linea + "<br /><b>Enviada Factura " +numero+" - Envio Completado</b>";
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters:'factura='+numero+'&envio=1'+ adic,
		onComplete: function gen(t)
		{
			$('linea_generacion').innerHTML = linea
		}
	});
}   
