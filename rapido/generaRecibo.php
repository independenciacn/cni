<?php 
//genrecibo.php. Fichero que genera el Recibo para el cliente. Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2007
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
	notFound();
}
sanitize( $_GET );
if (isset($_GET['codigo'])) {
	$sql = "Select date_format(r.fecha,'%d-%m-%Y') as fecha, 
	r.obs_alt, r.codigo, r.importe, 
	c.Nombre, c.Direccion, c.Ciudad, c.CP, c.NIF, 
	f.fpago as fpago 
	FROM regfacturas as r
	INNER JOIN clientes as c on r.id_cliente = c.Id 
	INNER JOIN facturacion as f on c.Id = f.idemp
	WHERE r.id like " . $_GET['codigo'];
	$resultado = consultaUnica($sql, MYSQL_ASSOC);
/* Analisis de las observaciones para fijar vencimiento*/
	$vto = strtok($resultado['obs_alt'],"VTO ");
	if( isset( $vto[1] ) ) {
		$vencimiento = $vto;
	} else {
		$vencimiento = $resultado['fecha'];
	}
?>
<html>
<head>
<title>RECIBO</title>
<link rel="stylesheet" type='text/css' href="../estilo/print.css" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
	<table id='tabloide'>
	<tr>
		<th align='left'>NUMERO FACTURA</th>
		<th align='left'>FORMA PAGO</th>
		<th align='left'>IMPORTE</th>
	</tr>
	<tr>
		<td><?php echo $resultado['codigo']; ?></td>
		<td><?php echo $resultado['fpago']; ?></td>
		<td><?php echo precioFormateado( $resultado['importe'] ); ?></td>
	</tr>
	<tr>
		<th align='left'>FECHA FACTURA</th>
		<th align='left'>VENCIMIENTO</th>
		<th>&nbsp;</th>
	</tr>
	<tr>
		<td><?php echo $resultado['fecha']; ?></td>
		<td><?php echo $vencimiento; ?></td>
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
		<td colspan='2'><?php 
				echo strtoupper($resultado['Nombre'])."<br>
				".$resultado['Direccion']."<br>
				".$resultado['CP']." - ".$resultado['Ciudad']."<br>
				NIF:".$resultado['NIF']; ?></td>
		<td>&nbsp;</td>
	</tr>
</table>
</body>
</html>
<?php } ?>