<?php 
require_once '../inc/configuracion.php'; 
require_once 'funcionesAgenda.php';
if ( isset($_SESSION['usuario']) && isset( $_POST ) ) {
    sanitize( $_POST );
} else {
    notFound();
}
$tiposAgenda = array(
        0 => 'agendaDespachos',
        1 => 'agendaSemana',
        3 => 'tareasPendientes'
        );
if ( isset($_POST['agenda'] ) && function_exists($tiposAgenda[$_POST['agenda']])) {
    echo $tiposAgenda[$_POST['agenda']]();
    /*
?>
<table id='tablaAgenda'>
<colgroup span='6' ></colgroup>
<?php
echo $tiposAgenda[$_POST['agenda']]();
?>
</table>
<script>
$('.informacionCliente').click(function(){
	var url = "datos.php";
	alert( this.id );
	var pars = "opcion=3&despacho="+this.id;
	var div = "informacionDespacho";
	procesaAjax(url, pars, div, 'Cargando datos Despacho', false, false);
});
</script>

<?php */
} else {
    echo "<div class='alert'>La agenda seleccionada no existe</div>";
}
/*
echo "<table>";
	for( $i=0; $i<=5; $i++ ) {
		$cadena.="<tr>";
		for($j=0;$j<=5;$j++)
		{
			$despacho++;
			if($despacho == 23)
			$muestra = "Sala de Juntas";
			else
			$muestra = "Despacho ".$despacho;
			$cadena.="<td width='16.66%' id='despacho_$despacho' valign='top'><div class='cabezera_despacho'>".$muestra."
		";
			if($cliente[$despacho]!='')
			{
				$cadena.="</div>
				<div class='".$clase[$despacho]."' height='100%'>
		".$despachos[$despacho];
				$cadena.="<p/><span class='mini_boton' 
				onclick='informacion_cliente($cliente[$despacho])'>&nbsp;+Info&nbsp;</span>
				<input type='hidden' id='cliente_despacho_$despacho' value='$cliente[$despacho]' />";
			}
			else
			{
				$cadena.="</div>";
				$cadena.="<input type='hidden' id='cliente_despacho_$despacho' value='' />";
				$cadena.="<div class='despacho_parcial'>&nbsp;</div>";
			}
		$cadena.="</div>
		</td>";
		}
		$cadena.="</tr>";
	}
	echo $cadena;
*/