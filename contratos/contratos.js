function datos_necesarios(cliente)
{
	contrato = $F('contrato')
	cliente = $F('cliente')
	url='datos.php'
	pars='cliente='+cliente+'&contrato='+contrato
	var myAjax = new Ajax.Request(url,
	{
	method:'post',
	parameters:pars,
	onComplete:ver_respuesta
	});
	
}

function ver_respuesta(respuesta)
{
	$('datos').innerHTML = respuesta.responseText
}

function generalo()
{
	var serie=''
	fichero = $F('fichero')
	totcamp = $F('totcamp')
	for (i=0;i<=totcamp-1;i++)
	{
	nucampo = 'campo' + i
		nucampo=$F('campo'+i)
		
	serie = serie + 'campo' + i +'='+ nucampo + '&'
	}
	serie = encodeURI(serie)
	$('enlace').innerHTML = "<a href='"+fichero+"?"+serie+"' target='_blank' >Mostrar Contrato</a>";
}