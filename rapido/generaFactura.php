<?php
/**
 * Tenemos dos opciones raiz
 * a - se pasa como parametro factura con el numero de factura
 * b - se pasa como parametro duplicado con el numero de factura
 */
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
require_once '../inc/Cliente.php';
require_once '../inc/Servicio.php';
require_once '../inc/Facturas.php';
Cni::chequeaSesion();
$duplicado = false;
if (isset($_SESSION['usuario'])) {
	if (isset($_GET['factura']) || isset($_GET['duplicado'])) {
		if (isset($_GET['duplicado'])) {
			$factura = new Facturas($_GET['duplicado'], true);
		} else {
			$factura = new Facturas($_GET['factura']);
		}
	} else {
		 	$factura = new Facturas();
		 	$factura->generacionFactura($_GET);
	}
	var_dump($factura);
} else {
	exit('Error, la solicitud no puede completarse');
}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
	<meta charset="utf-8">
	<link  href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
	<title><?= $factura->nombreFactura;?></title>
	</head>
	<body>
	<div class='container'>
		<div class='header'>
			<h1><?= $factura->tituloFactura; ?></h1>
		</div>
		<table class='table table-bordered table-striped'>
		<tr>
			<td>
				FECHA:<?= $factura->fechaDeFactura; ?><br/>
				<?= $factura->nombreFactura; ?> - 
				<?= $factura->numeroFactura; ?><br/>
			</td>
			<td>
				<?= strtoupper($factura->cliente->nombre) ?><br/>
				<?= $factura->cliente->direccion ?><br/>
				<?= $factura->cliente->cp ?>
			 	- 
				<?= $factura->cliente->ciudad ?><br/>
				NIF:<?= $factura->cliente->nif ?><br/>
			</td>
		</tr>
		</table>
		<table class='table table-bordered table-striped'>
		<caption>
			<?= $factura->nombreFactura; ?> - 
			<?= $factura->numeroFactura; ?> - 
			<?= $factura->fechaDeFactura; ?>
		</caption>
		<thead>
			<tr>
				<th>Servicio</th>
				<th>Unidades</th>
				<th>Precio/U</th>
				<th>Subtotal</th>
				<th>Iva</th>
				<th>Total</th>
			</tr>
		</thead>
		<?= $factura->presentaFactura(); ?>	
		</table>
		<table class='table table-bordered table-striped'>
			<colgroup width='15%' />
			<colgroup />
			<colgroup width='15%' />
			<colgroup />
			<colgroup width='15%' />
			<colgroup />
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>TOTAL BRUTO</th>
				<th>&nbsp;</th>
				<th>IVA</th>
				<th>&nbsp;</th>
				<th>TOTAL</th>
			</tr>
 			<tr>
				<th>&nbsp;</th>
				<th><?= Cni::formateaNumero($factura->totalBruto, true) ?></th>
				<th>&nbsp;</th>
				<th><?= Cni::formateaNumero($factura->totalGlobal - $factura->totalBruto, true) ?>
				</th>
				<th>&nbsp;</th>
				<th><?= Cni::formateaNumero($factura->totalGlobal, true) ?></th>
		</tr>
		</thead>
		</table>
		<div class='span4'>
			<strong>Forma de pago: </strong><?= $factura->formaPago; ?><br/>
			<?= $factura->obsFormaPago; ?><br/>
			<?= $factura->pedidoCliente;?> <?= $factura->obs ?>
		</div>
	</div>
	</body>
</html>
 
