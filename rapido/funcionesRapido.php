<?php
function clientes( $cliente )
{
	global $con;
	$html = "";
	$sql = "Select Id,Nombre from clientes 
	where `Estado_de_cliente` like '-1' or `Estado_de_cliente` like 'on' 
	order by Nombre";
	$resultados = consultaGenerica($sql);
	foreach ( $resultado as $resultado ) {
		$seleccionado = ( $cliente == $resultado[0] ) ? "selected" : "";
		$html .= 
		"<option ".$seleccionado." value='".$resultado[0]."'>
			".$resultado[1]."
		</option>";
	}
	return $html;
}
/**
 * Muestra el select de meses
 * 
 * @param integer $mes
 * @return string $html
 */
function seleccionMeses( $mes = null )
{
	global $meses;
	if ( is_null( $mes ) ) {
		$mesActual = date("m");
	}
	$html = "<select name='mes' id='mes'>";
	$html .= "<option value='0'>--Mes--</option>";
	foreach( $meses as $key => $nombreMes ) {
		$marcado = ( $mesActual == $key ) ? "selected" : "";
		$html .= 
			"<option value='".$key."' " . $marcado . ">" 
			. $nombreMes . "</option>";
	}
	$html .= "</select>";
	return $html;
}
/**
 * Muestra el select de los años
 * 
 * @return string $html
 */
function seleccionAnyos( )
{
	$html = "<select name='anyo' id='anyo'>";
	for ( $i=2007;$i<=date('Y')+2; $i++ ) {
		$marcado = ( date('Y') == $i ) ? "selected" : "";
		$html .= 
			"<option ". $marcado ." value ='" . $i . "' >" . $i . "</option>";
	}
	$html .="</select>";
	return $html;
}
/**
 * Devuelve los datos de la cabezera de la factura
 */
function cabezeraFactura( $factura ) {
	$sql = "Select c.Nombre,c.Direccion,c.CP,c.Ciudad,
		c.NIF,r.fecha, r.pedidoCliente, c.id from clientes as c 
		join regfacturas as r on r.id_cliente = c.id 
		where r.codigo like " . $factura;
	$resultados = consultaUnica( $sql );
	return $resultados;
}	