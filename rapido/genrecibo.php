<?php
require_once '../inc/variables.php'; 
//genrecibo.php. Fichero que genera el Recibo para el cliente. Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007
if (isset($_GET['id'])) {
	function cambiaf($stamp) //funcion del cambio de fecha
	{
		//formato en el que llega aaaa-mm-dd o al reves
		$fdia = explode("-",$stamp);
		$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
		return $fecha;
	}
	
	function ficha_cliente($cliente)
	{
		global $con;
		$sql = "Select * from clientes where id like ".$cliente;
		$consulta = mysql_query($sql,$con);
		$resultado = mysql_fetch_array($consulta);
		$cadena = strtoupper($resultado[1])."<br>
				".$resultado[6]."<br>
				".$resultado[8]."&nbsp;&nbsp;-&nbsp;&nbsp;".$resultado[7]."<br>
				NIF:".$resultado[5];
		return $cadena;
	}
	
	function forma_pago($cliente)
	{
		global $con;
		$sql = "SELECT fpago from facturacion where idemp like ".$cliente;
		$consulta = mysql_query($sql,$con);
		$resultado = mysql_fetch_array($consulta);
		return $resultado['fpago'];
	}

$sql = "Select * from regfacturas where id like ".$_GET['id'];
$consulta = mysql_query($sql,$con);
$resultado = mysql_fetch_array($consulta);
/* Analisis de las observaciones para fijar vencimiento*/
$vto = strtok($resultado['obs_alt'],"VTO ");
if(isset($vto[1])) {
	$vencimiento = $vto;
} else {
	$vencimiento = cambiaf($resultado['fecha']);
}
?>
<html>
<head>
<title>RECIBO</title>
<link rel="stylesheet" type='text/css' href="estilo.css" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table cellpadding='2px' cellspacing='0px' width='100%' id='tabloide'>
<tr>
	<th align='left'>NUMERO FACTURA</th>
	<th align='left'>FORMA PAGO</th>
	<th align='left'>IMPORTE</th>
</tr>
<tr>
	<td><? echo $resultado['codigo']; ?></td>
	<td><? echo forma_pago($resultado['id_cliente']);?></td>
	<td><? echo number_format($resultado['importe'],2,',','.'); ?></td>
</tr>
<tr>
	<th align='left'>FECHA FACTURA</th>
	<th align='left'>VENCIMIENTO</th>
	<th>&nbsp;</th>
</tr>
<tr>
	<td><? echo cambiaf($resultado['fecha']); ?></td>
	<td><? echo $vencimiento; ?></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<th colspan='3' align='left'>CONCEPTO:</th>
</tr>
<tr>
	<td colspan='3' height='100px'>&nbsp;</td>
</tr>
<tr>
	<th colspan='2' align='left'>CLIENTE</th>
	<th align='left'>FIRMA</th>
</tr>
<tr>
	<td colspan='2'><?php echo ficha_cliente( $resultado['id_cliente'] ); ?></td>
	<td>&nbsp;</td>
</tr>
</table>
</body></html>
<?php }
