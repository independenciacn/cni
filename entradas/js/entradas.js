function ver_empresas(entrada)
{
	var estilo = $('empresas_'+ entrada).style
    estilo.display = 'block';
    estilo.visibility = 'visible';
    var boton = $('boton_'+entrada).style
    boton.display = 'none';
    boton.visibility = 'hidden';
}
function ocultar_empresas(entrada)
{
    var estilo = $('empresas_'+ entrada).style
    estilo.display = 'none';
    estilo.visibility = 'hidden';
    var boton = $('boton_'+entrada).style
    boton.display = 'block';
    boton.visibility = 'visible';
}
function ocultar_entradas(entrada)
{
    var estilo = $('entradas_'+ entrada).style
    estilo.display = 'none';
    estilo.visibility = 'hidden';
    var boton = $('boton_e_'+entrada).style
    boton.display = 'block';
    boton.visibility = 'visible';
}
function ocultar_salidas(entrada)
{
    var estilo = $('salidas_'+ entrada).style
    estilo.display = 'none';
    estilo.visibility = 'hidden';
    var boton = $('boton_s_'+entrada).style
    boton.display = 'block';
    boton.visibility = 'visible';
}
function ver_entradas(entrada)
{
   var estilo = $('entradas_'+ entrada).style
    estilo.display = 'block';
    estilo.visibility = 'visible';
    var boton = $('boton_e_'+entrada).style
    boton.display = 'none';
    boton.visibility = 'hidden';
}
function ver_salidas(entrada)
{
   var estilo = $('salidas_'+ entrada).style
    estilo.display = 'block';
    estilo.visibility = 'visible';
    var boton = $('boton_s_'+entrada).style
    boton.display = 'none';
    boton.visibility = 'hidden';
}
//Las anteriores se pueden borrar ya que no sirven ya
function ver_datos(fecha,seccion)
{
   var estilo = $('detalles_mes').style
    estilo.display = 'block';
    estilo.visibility = 'visible';
   var url = "datos.php";
   var pars = fecha + ";"+ seccion
   new Ajax.Request(url,
	 {
			method:'post',
			parameters: pars,
			onComplete: function gen(t)
			{
				$('detalles_mes').innerHTML = t.responseText

			}
	});
}
function cierra_datos()
{
    var estilo = $('detalles_mes').style
    estilo.display = 'none';
    estilo.visibility = 'hidden';
}