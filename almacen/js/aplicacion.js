//scripts de la parte del almacen
var t
var url = 'datos.php'
function abreform()
{
	$('formulario_almacen').innerHTML='Cargando Datos ... <p/><img src="../imagenes/loader.gif" alt="Actualizando Datos.." />'; 
	t = setTimeout("abre()",1000)
	abre()
	
}
/****************************************************************************************/
function abre()
{
	valor = $F('cliente')
	pars = 'cliente='+valor
	var myAjax = new Ajax.Request(url,
	{
		method: 'post',
		parameters: pars,
		onComplete: generadora
	}); 
}
/****************************************************************************************/
function generadora(respuesta) 
{ 
				$('formulario_almacen').innerHTML = respuesta.responseText
				Calendar.setup({
        		inputField     :    'finicio',      // id of the input field
        		ifFormat       :    '%d/%m/%Y',       // format of the input field
        		showsTime      :    true,            // will display a time selector
        		button         :    'f_trigger_a',   // trigger for the calendar (button ID)
        		singleClick    :    false,           // double-click mode
        		step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    			}); 
    			Calendar.setup({
        		inputField     :    'ffinal',      // id of the input field
        		ifFormat       :    '%d/%m/%Y',       // format of the input field
        		showsTime      :    true,            // will display a time selector
        		button         :    'f_trigger_b',   // trigger for the calendar (button ID)
        		singleClick    :    false,           // double-click mode
        		step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    			});
    		nuval = $F('cliente')
    		tabla(nuval)
}
/****************************************************************************************/
function tabla(nuval)
{
	
	pars = 'tabla='+nuval
	var myAjax = new Ajax.Request(url,
	{
		method: 'get',
		parameters: pars,
		onComplete: 
			function gen(respuesta) 
			{ 
				$('tabla_datos').innerHTML = respuesta.responseText
			}
	});
}

/****************************************************************************************/	
//agrega valores al almacen
function a_almacen()
{

	op = $F('opcion')
	cliente = $F('cliente')
	bultos = $F('bultos')
	finicio = $F('finicio')
	ffinal = $F('ffinal')
	pars = 'op='+op+'&bcliente='+cliente+'&bultos='+bultos+'&finicio='+finicio+'&ffinal='+ffinal
	var myAjax = new Ajax.Request(url,
		{
		method:'post',
		parameters:pars,
		onComplete:abreform
		});
	
}
/****************************************************************************************/
function edita_almacen(item)
{
	
	cliente = $F('cliente')
	pars = 'cliente='+cliente+'&item='+item
	var myAjax = new Ajax.Request(url,
	{
		method:'post',
		parameters:pars,
		onComplete:generadora
	});
}
/****************************************************************************************/
function borra_almacen(item)
{
	pars = 'borra='+item
	var respuesta = confirm("Borrar almacenaje?")
	if (respuesta==true)
	{
		var myAjax = new Ajax.Request(url,
		{
		method:'post',
		parameters:pars,
		onComplete:abre
		});
	}
}
/****************************************************************************************/	
function reportError()
{
	alert("Error")
}