/**
 * 
 */
$("input[type=reset]").click(function(){
	$("#resultados").html("");
	
});
$('#frmopciones').submit(function(){
	$('select').removeClass('errorfrm');
	var fallo = 0;
	var mensaje = "ERROR: Revise los datos\n";

	if($('#inicio').val() == 0){
		mensaje += "Debe especificar un año Inicial\n";
		$('#inicio').addClass('errorfrm');
		fallo = 1;
	}
	if($('#fin').val() == 0){
		mensaje += "Debe especificar un año Final\n";
		$('#fin').addClass('errorfrm');
		fallo = 1;
	}
	if($('#vista').val() == 0){
		mensaje += "Debe especificar un tipo de Vista\n";
		$('#vista').addClass('errorfrm');
		fallo = 1;
	}
	if($('#datos').val() == 0){
		mensaje += "Debe especificar que datos quiere visualizar\n";
		$('#datos').addClass('errorfrm');
		fallo = 1;
	}
	if($('#inicio').val() > $('#fin').val())
	{
		mensaje += "El año de fin debe ser mayor o igual que el año de inicio\n";
		$('#inicio').addClass('errorfrm');
		$('#fin').addClass('errorfrm');
		fallo = 1;
	}
	
	if( ( $('#fin').val() - $('#inicio').val()) > 1){
		mensaje += "El rango maximo es de 2 años. ej(2008 - 2009)\n";
		$('#inicio').addClass('errorfrm');
		$('#fin').addClass('errorfrm');
		fallo = 1;
	}
	
	if(fallo == 0){
		$.post('handler.php',$('#frmopciones').serialize(),function(data){
			
			$('#resultados').html(data);
			$('#resultado').height($('#resultados').height()+50);
			
		});
	}
	else{
		alert(mensaje);
	}
	return false;
});

$("#frmopciones").ajaxStart(function(){ $("#carga").show();});
$("#frmopciones").ajaxStop(function(){
$("#carga").hide();
$("#resultado").height($('#resultados').height() + 50);
});