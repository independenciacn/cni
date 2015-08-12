/*
 * Muestra el formulario del despacho
 */
function formulario_despacho(despacho)
{
		var url ="datos.php"
		var cliente = $F('cliente_despacho_'+despacho)
		var pars = "opcion=0&despacho="+despacho+"&cliente="+cliente
		var myAjax = new Ajax.Request(url,
		 {
				method:'post',
				parameters: pars,
				onComplete: function gen(t)
				{
					ver_formulario_agenda()
					$('formulario_agenda').innerHTML = t.responseText
					campos_fecha()
				}
		});
}
/*
 * Muestra el formulario del despacho en semana
 */
function formulario_despacho_semana(despacho,dia)
{
	
		var url ="datos.php"
		var pars = "opcion=0&despacho="+despacho+"&dia="+dia
		
		var myAjax = new Ajax.Request(url,
		 {
				method:'post',
				parameters: pars,
				onComplete: function gen(t)
				{
					ver_formulario_agenda()
					$('formulario_agenda').innerHTML = t.responseText
					campos_fecha()
					
				}
		});
}
/*
 * Muestra el formulario en la agenda
 */
function ver_formulario_agenda()
{
	var estilo=$('formulario_agenda').style
	estilo.visibility="visible"
	estilo.display="block"
}
/*
 * Cierra formulario del despacho
 */
function cerrar_formulario_despacho()
{
	$('formulario_agenda').innerHTML = ""
}
/*
 * Busca el cliente
 */
function busca_cliente()
{
	var cadena = $F('cliente');
	var url = "datos.php";
	var pars = "opcion=1&texto="+cadena
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: 
			function gen(respuesta) 
			{ 
				$('listado_clientes_agenda').innerHTML = respuesta.responseText
			}
	});
}
/*
 * Una vez seleccionamos el cliente lo muestra en el campo de texto y borra el listado
 */
function marca(id_cliente)
{
	var url = "datos.php";
	var pars = "opcion=2&cliente="+id_cliente
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: 
			function gen(respuesta) 
			{ 
				var datos = new String(respuesta.responseText)
				var final = datos.split(';')
				$('id_cliente').value = final[0]
				$('cliente').value = final[1]
				$('listado_clientes_agenda').innerHTML = ""
				
			}
	});
}
/*
 * Cambia el color al pasar por encima del cliente en el cuadro de busqueda
 */
function cambia_color(id)
{
	var estilo = $('linea_'+id).style
	estilo.backgroundColor = "#00FF00"
}
/*
 * Quita el color al perder el foco en el cuadro de busqueda
 */
function quitar_color(id)
{
	var estilo = $('linea_'+id).style
	estilo.backgroundColor = ""
}
/*
 * Quita el nombre del cliente en el cuadro de busqueda
 */
function limpia_nombre_cliente()
{
	$('cliente').value = ""
}
/*
 * Formato para la lanzar el script de la fecha
 */
function campos_fecha()
{

		Calendar.setup({
		inputField     :    'finc',     
		ifFormat       :    '%d-%m-%Y',       
		showsTime      :    false,            
		button         :    'trigger_finc',   
		singleClick    :    true,           
		step           :    1                
		})
		
		Calendar.setup({
		inputField     :    'ffin',      
		ifFormat       :    '%d-%m-%Y',       
		showsTime      :    false,            
		button         :    'trigger_ffin',   
		singleClick    :    true,           
		step           :    1                
		})
}
/*
 * Muestra la informacion del cliente
 */
function informacion_cliente(despacho,tipo,id)
{
	var url = "datos.php";
	var pars = "opcion=3&despacho="+despacho+"&tipo="+tipo+"&id="+id
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: 
			function gen(t) 
			{ 
				var estilo=$('informacion_despacho').style
				estilo.visibility="visible"
				estilo.display="block"
				$('informacion_despacho').innerHTML = t.responseText
			}
	});
}
/*
 * Guardamos la observacion del cliente
 */
function guarda_obs(id,despacho,tipo)
{
	var url="datos.php";
	///	var cos = "&repe_l=" + $F('repe_l') + "&repe_m=" + $F('repe_m') + "&repe_x=" + $F('repe_x') + "&repe_j=" + $F('repe_j') + "&repe_v=" + $F('repe_v')
	
	//if (tipo == 1) 
		//var pars = "opcion=9&id=" + id + "&obs=" + encodeURI($F('obs')) + "&conformidad=" + $F('conformidad')
	//else {
		var cos = "&repe_l=" + $F('repe_l') + "&repe_m=" + $F('repe_m') + "&repe_x=" + $F('repe_x') + "&repe_j=" + $F('repe_j') + "&repe_v=" + $F('repe_v')
		var pars = "opcion=9&id=" + id + "&obs=" + encodeURI($F('obs')) + "&conformidad=" + $F('conformidad') + cos + "&hinc=" + $F('hinc') + "&hfin=" + $F('hfin') + "&finc=" + $F('finc') + "&ffin=" + $F('ffin')
	//}
	//alert(pars)
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: function gen(t){
			informacion_cliente(despacho, tipo,id)
			cambia_vista();
			cambia_fecha($F('finc'));
		}
		
	});
}
/*
 * Cierra el formulario del despacho
 */
function cerrar_informacion_despacho()
{
	var estilo=$('informacion_despacho').style
	estilo.visibility="hidden"
	estilo.display="none"
	$('informacion_despacho').innerHTML = ""
}

/*
 * Cierra el formulario de la agenda
 */
function cerrar_formulario_agenda()
{
	var estilo=$('formulario_agenda').style
	estilo.visibility="hidden"
	estilo.display="none"
	$('formulario_agenda').innerHTML = ""
}

/*
 * Guardado de la informacion del despacho
 */
function guarda_despacho()
{
	var url = "datos.php"
	var despacho = $F('despacho')
	var formulario = $('form_despachos')
	var pars="opcion=4&"+Form.serialize(formulario)
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: 
			function gen(t) 
			{ 
				switch($('tipo_vista').value)
				{
					case "0":$('despacho_'+despacho).innerHTML = t.responseText
					break
					case "1":if(t.responseText=='Despacho Ocupado')
					$('debug').innerHTML ="<span class='no_confirmado'>Despacho Ocupado</span>"
					break
				}
			}
	});
	cambia_vista()
	cambia_fecha($F('finc'))
}

/*
 * Muestra todos los detalles relacionados con la ocupacion del despacho, desde aqui se actualiza y borra
 */
function detalles_ocupacion(despacho)
{
	ver_formulario_agenda()
	var url="datos.php"
	var pars='opcion=5&despacho='+despacho
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: 
			function gen(t) 
			{ 
				$('formulario_agenda').innerHTML = t.responseText
			}
	});
}
/*
 * Edita la ocupacion en el formulario agenda
 */
function editar_ocupacion(ocupacion)
{
	
	var url="datos.php"
	var pars='opcion=6&ocupacion='+ocupacion
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: 
			function gen(t) 
			{ 
				$('formulario_agenda').innerHTML = t.responseText
				campos_fecha()
			}
	});
}

/*
 * Actualiza el estado de ocupacion del despacho
 */
function actualiza_ocupacion()
{
	var url = "datos.php"
	var despacho = $F('despacho')
	var formulario = $('form_despachos')
	var pars="opcion=7&"+Form.serialize(formulario)
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: 
			function gen(t) 
			{ 
				$('despacho_'+despacho).innerHTML = t.responseText
				detalles_ocupacion(despacho)
			}
	});	
}

/*
 * Borra la ocupacion seleccionada
 */
function borra_ocupacion(ocupacion)
{
	var t=confirm(String.fromCharCode(191)+"Borrar Ocupacion?")
	if (t == true)
	{
		var url="datos.php"
		var despacho=$F('codigo_despacho')
		var pars='opcion=8&ocupacion='+ocupacion+'&despacho='+despacho
		var myAjax = new Ajax.Request(url,
			{
				method: 'post',
				parameters: pars,
				onComplete: 
					cambia_vista()
					
				
				
			});
		cambia_fecha($F('finc'))
		cerrar_informacion_despacho()
	}
}

/*
 * Cambio de vistas de la principal, segun la vista carga una hoja u otra, ahora habra que ver las funciones
 */
function cambia_vista()
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
}
function tareas_no_realizadas()
{
	var url="datos.php"
	var pars='opcion=24'
		var myAjax = new Ajax.Request(url,
			{
				method: 'post',
				parameters: pars,
				onComplete: function gen(t)
				{
					 alert(t.responseText)
				}
			});
		 
}
/*
 * Cambia la visualizacion de la parrilla segun la fecha seleccionada
 */
function cambia_fecha(fecha)
{
	switch($F('seccion'))
	{
		case "0":
		var url="semana.php"
		break
		case "1":
		var url="interna.php"
		break
	}
	
	if(fecha == undefined)
		var pars="fecha="+$F('semana')
	else
		var pars="fecha="+fecha
	
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				$('vista').innerHTML = t.responseText
				
					Calendar.setup({
					inputField     :    'semana',      // id of the input field
					ifFormat       :    '%d-%m-%Y',       // format of the input field
					showsTime      :    false,            // will display a time selector
					button         :    'f_trigger_semana',   // trigger for the calendar (button ID)
					singleClick    :    true,           // double-click mode
					step           :    1                // show all years in drop-down boxes (instead of every other year as default)
					})
			}
		});
	return false;
}

/*
 * Parte de la agenda Interna
 */
function formulario_interna(hora,dia)
{
	var url ='datos.php'
	var pars="opcion=10&dia="+dia+"&hora="+hora
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				ver_formulario_agenda()
				$('formulario_agenda').innerHTML = t.responseText
			}
		});
}
/*
 * Previa de los colores
 */
function previa_color()
{
	var estilo=$('previa_color').style
	estilo.background=$('color').value
}

/*
 * Previa de los tipos
 */
function previa_tipo()
{
	var url='datos.php'
	var pars="opcion=11&repetir="+$('repetir').value
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				$('personalizar').innerHTML = t.responseText
			}
		});
}

/*
 * Muestra el formulario de agregar tareas
 */
function agrega_tarea()
{
	var url = "datos.php"
	var formulario = $('tareas')
	var pars="opcion=12&"+Form.serialize(formulario)
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				$('estado_tarea').innerHTML = t.responseText
				//alert("cambio")
				cambia_fecha()
			}
		});
}
/*
 * Muestra el fomulario para la edicion de la tarea
 */
function edita_tarea(tarea,hora,dia)
{
	var url ='datos.php'
	var pars="opcion=10&tarea="+tarea+"&dia="+dia+"&hora="+hora
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				ver_formulario_agenda()
				$('formulario_agenda').innerHTML = t.responseText
			}
		});
}
/*
 * Actualiza la tarea de la agenda interna
 */
function actualiza_tarea(tarea)
{
	var url = "datos.php"
	var formulario = $('tareas')
	var pars="opcion=17&tarea="+tarea+"&"+Form.serialize(formulario)
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				$('estado_tarea').innerHTML = t.responseText
				cambia_fecha()
			}
		});
}
function actualiza_esta_tarea()
{
	var url="datos.php"
	var pars="opcion=23&tarea="+$F('id_tarea')+"&dia="+$F('dia_marc')+"&hora="+$F('hora_marc')+"&color="+$('color').value
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				$('estado_tarea').innerHTML = t.responseText
				cambia_fecha()
			}
		});
}
/*
 * Borra la tarea de la agenda interna
 */
function borra_tarea_interna(tarea)
{
	var url = "datos.php"
	var pars="opcion=18&tarea="+tarea+"&fecha="+$F('semana')
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				$('estado_tarea').innerHTML = t.responseText
				cambia_fecha()
				cerrar_formulario_agenda()
			}
		});
}
/*
 * Agrega la tarea
 */
function agregar_tarea_pendiente()
{
	var url = "datos.php"
	var formulario = $('tareas_pendientes')
	var pars="opcion=13&"+Form.serialize(formulario)
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				$('estado_tarea').innerHTML = t.responseText
				cambia_vista()
			}
		});
}

/*
 * Edita la tarea pendiente
 */
function edita_tarea_pendiente(tarea)
{
	var url = "tareas.php"
	var pars="tarea=" + tarea
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: function gen(t)
		{
			$('vista').innerHTML = t.responseText
			Calendar.setup({
					inputField     :    'semana',      // id of the input field
					ifFormat       :    '%d-%m-%Y',       // format of the input field
					showsTime      :    false,            // will display a time selector
					button         :    'f_trigger_semana',   // trigger for the calendar (button ID)
					singleClick    :    true,           // double-click mode
					step           :    1                // show all years in drop-down boxes (instead of every other year as default)
					})
		}
		
	});
	
}

/*
 * Actualizamos la tarea pendiente
 */
function actualiza_tarea_pendiente(tarea)
{
	var url = "datos.php"
	var formulario = $('tareas_pendientes')
	var pars="opcion=14&tarea="+ tarea + "&" + Form.serialize(formulario)
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				$('estado_tarea').innerHTML = t.responseText
				cambia_vista()
			}
		});
}

/*
 * Cambia el estado de la tarea
 */
function cambia_estado_tarea(tarea)
{
	var url = "datos.php"
	var pars="opcion=15&tarea="+ tarea + "&estado=" + $F('tarea_'+tarea)
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete:function gen(t) 
			{	 
				$('estado_tarea').innerHTML = t.responseText
				cambia_vista()
			}
		
	});
}

/*
 * Borra la tarea
 */
function borra_tarea(tarea)
{
	var t=confirm(String.fromCharCode(191)+"Borrar Tarea?")
	if (t == true) {
		var url = "datos.php"
		var pars = "opcion=16&tarea=" + tarea
		var myAjax = new Ajax.Request(url, {
			method: 'post',
			parameters: pars,
			onComplete:function gen(t) 
			{	 
				$('estado_tarea').innerHTML = t.responseText
				cambia_vista()
			}
			
		});
	}
}

/*
 * Filtros en tareas
 */
function filtro_asignado()
{
	var valor= $('filtro_asignado').value
	var url = "datos.php"
	var pars = "opcion=19&asignada=" + valor
	var myAjax = new Ajax.Request(url, {
			method: 'post',
			parameters: pars,
			onComplete:function gen(t) 
			{	 
				$('lista_tareas_pendientes').innerHTML = t.responseText
				
			}
			
		});
	
}
function filtro_prioridad()
{
	var valor= $('filtro_prioridad').value
	var url = "datos.php"
	var pars = "opcion=19&prioridad=" + valor
	var myAjax = new Ajax.Request(url, {
			method: 'post',
			parameters: pars,
			onComplete:function gen(t) 
			{	 
				$('lista_tareas_pendientes').innerHTML = t.responseText
				
			}
			
		});
}

function filtro_vencimiento()
{
	var valor= $('filtro_vencimiento').value
	var url = "datos.php"
	var pars = "opcion=19&vencimiento=" + valor
	var myAjax = new Ajax.Request(url, {
			method: 'post',
			parameters: pars,
			onComplete:function gen(t) 
			{	 
				$('lista_tareas_pendientes').innerHTML = t.responseText
				
			}
			
		});
}
/*
 * Agrega la nota a la base de datos
 */
function agrega_nota()
{
	var url = "datos.php"
	var formulario = $('notas')
	var pars="opcion=20&" + Form.serialize(formulario)
	var myAjax = new Ajax.Request(url,
		{
			method: 'post',
			parameters: pars,
			onComplete: 
			function gen(t) 
			{	 
				$('estado_nota').innerHTML = t.responseText
				cambia_vista()
			}
		});
}
/*
 * Edita la nota 
 */
function editar_nota(nota)
{
	var url="notas.php"
	var pars="nota="+nota
	var myAjax = new Ajax.Request(url,
	{
		method:'post',
		parameters: pars,
		onComplete:
		function gen(t)
		{
			$('vista').innerHTML = t.responseText
		}
	});
}
/*
 * Actualiza la nota
 */
function actualiza_nota(id)
{
	var url="datos.php"
	var pars="opcion=21&id="+id+"&"+Form.serialize($('notas'))
	
	var myAjax = new Ajax.Request(url,
	{
		method:'post',
		parameters: pars,
		onComplete:
		function gen(t)
		{
			$('estado_nota').innerHTML = t.responseText
			cambia_vista()
		}
	});
}
/*
 * Borra la nota
 */
function borra_nota(nota)
{
	var t=confirm(String.fromCharCode(191)+"Borrar Nota?")
	if (t == true) {
		var url = "datos.php"
		var pars = "opcion=22&nota=" + nota
		var myAjax = new Ajax.Request(url, {
			method: 'post',
			parameters: pars,
			onComplete:function gen(t) 
			{	 
				$('estado_nota').innerHTML = t.responseText
				cambia_vista()
			}
			
		});
	}
}