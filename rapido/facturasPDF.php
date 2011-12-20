<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
	notFound();
}
sanitize( $_REQUEST );
$idFacturas = explode(',',$_REQUEST['codigo']);
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
require_once 'envio.php';
$charEncoding = 'UTF-8';
$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
$estilo = new Zend_Pdf_Style();
$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
$estilo->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
$estilo->setFont($font, 16);
$estilo2 = new Zend_Pdf_Style();
$estilo2->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
$estilo2->setFont($font, 10);
$estilo3 = new Zend_Pdf_Style();
$estilo3->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
$estilo3->setFont($font, 8);
$estilo3->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
$page->setStyle($estilo);
// Imagenes
$logo = Zend_Pdf_Image::imageWithPath('images/logo_n.jpg');//200x88
$aniversario = Zend_Pdf_Image::imageWithPath('images/image001.png'); // 90x80
$htmlText = Zend_Pdf_Image::imageWithPath('images/pie_n.jpg');//600x60
$nif = Zend_Pdf_Image::imageWithPath('images/nif_n.jpg');//33x365
//Esquina superior izquierda 0,843
//Esquina inferior izquierda 0,0
//Esquina superior derecha 595,843
//Esquina inferior derecha 595,0
//x1,y1->esquina inferior izquierda, x2,y2->esquina superior derecha
//anterior addImage(img,x,y,w,[h],[quality=75])
$page->drawImage($logo, 33, 740, 233, 828)
	->drawImage($aniversario, 200, 100, 290, 180)
	->drawImage($htmlText, 0, 0, 595, 60)
	->drawImage($nif, 0, 150, 33, 515);
//Caja de Datos de factura
$page->drawRectangle(300,750,590,805,Zend_Pdf_Page::SHAPE_DRAW_STROKE);
// Encabezados del listado
$page->setStyle($estilo2);
$page->drawText('SERVICIO', 33, 685, $charEncoding )
	->drawText('CANT.',322,685,$charEncoding )
	->drawText('P/U',372,685,$charEncoding )
	->drawText('IMP',422,685,$charEncoding )
	->drawText('IVA',472,685,$charEncoding )
	->drawText('TOTAL',522,685,$charEncoding )
	->drawLine(33, 680, 562, 680);
//Lineas de tabla
$line = 1;
for ( $i = 660, $j=680; $j >= 80; $i-=20,$j-=20) {
	if ($line % 2 != 0 ) {
		$estilo3->setFillColor(new Zend_Pdf_Color_GrayScale(0.9));
	} else {
		$estilo3->setFillColor(new Zend_Pdf_Color_GrayScale(1));
	}
	$page->setStyle($estilo3);
	$page->drawRectangle(33, $i, 562, $j, Zend_Pdf_Page::SHAPE_DRAW_FILL);
	$line++;
}
//Lineas Separadoras de Tabla
$page->drawLine(320, 680, 320, 60)
	->drawLine(370, 680, 370, 60)
	->drawLine(420, 680, 420, 60)
	->drawLine(470, 680, 470, 60)
	->drawLine(520, 680, 520, 60);
//Guardamos el pdf base con todo
$pdf = new Zend_Pdf();
$pdf->pages[] = $page;
$facturaBase = $pdf->render();
// Creamos un nuevo Pdf que tomara como base el anterior para generar las facturas
$todasFacturas = new Zend_Pdf();
foreach ( $idFacturas as $idFactura ) {
	$page = clone $pdf->pages[0];
	//Cabezera de la factura
	$sql="Select c.Nombre,c.Direccion,c.CP,c.Ciudad,
	c.NIF,date_format(r.fecha,'%d-%m-%Y') as fecha,
	r.pedidoCliente, c.id, r.codigo from clientes as c
	join regfacturas as r on r.id_cliente = c.id
	where r.id like " . $idFactura;
	$resultado = consultaUnica( $sql, MYSQL_ASSOC );
	$numeroFactura = $resultado['codigo'];
	
	// Propiedades del Fichero
	
	$page->setStyle($estilo);
	if ( isset( $_REQUEST['dup'] ) ) {
		$page->drawText("FACTURA (DUPLICADO)", 400, 810, $charEncoding );
		$dup = true;
	} else {
		$page->drawText("FACTURA", 500, 810, $charEncoding );
		$dup = false;
	}
	$page->setStyle($estilo2);
	$page->drawText("FECHA:".$resultado['fecha'], 33, 730, $charEncoding )
		->drawText("Num. FACTURA:". $resultado['codigo'],33, 720, $charEncoding )
		->drawText($resultado['Nombre'], 310, 790, $charEncoding )
		->drawText($resultado['Direccion'], 310, 780, $charEncoding )
		->drawText($resultado['CP'] ." - " . $resultado['Ciudad'], 310, 770, $charEncoding )
		->drawText("NIF: ".$resultado['NIF'], 310, 760, $charEncoding );
	// Forma de Pago
	$sql = "Select fpago,obs_fpago,obs_alt, pedidoCliente from regfacturas 
	where codigo like ".$numeroFactura;
	$resultado = consultaUnica($sql,MYSQL_ASSOC);
	$page->drawText("Forma de Pago:" .$resultado['fpago'],310,730, $charEncoding)
		->drawText($resultado['obs_fpago']." ".$resultado['obs_alt'], 310, 720, $charEncoding);
	if ( !is_null( $resultado['pedidoCliente'] ) ) {
		$page->drawText( $resultado['pedidoCliente'],310, 710, $charEncoding );
	}
	$estilo3->setFillColor(new Zend_Pdf_Color_GrayScale(0));
	$page->setStyle($estilo3);
	$sql = "SELECT factura, concat(servicio,' ', obs) as servicios, cantidad, 
	unitario, (cantidad*unitario) as importe, iva,
	(cantidad*unitario*(1+iva/100)) as total
	FROM historico
	WHERE factura like " . $numeroFactura;
	$resultados = consultaGenerica( $sql, MYSQL_ASSOC );
	$line = 665;
	$colum = 35;
	foreach ( $resultados as $resultado ) {
		$page->drawText(ucwords($resultado['servicios']), 35, $line, $charEncoding)
			->drawText(round($resultado['cantidad'],2), 325, $line, $charEncoding)
			->drawText(precioFormateado($resultado['unitario']), 375, $line, $charEncoding)
			->drawText(precioFormateado($resultado['importe']), 425, $line, $charEncoding)
			->drawText($resultado['iva'], 475, $line, $charEncoding)
			->drawText(precioFormateado($resultado['total']), 525, $line, $charEncoding);
		$line-=20;
	}
//Totales
	$sql = "select sum(cantidad) as cantidad, sum(unitario*cantidad) as unitario ,
	sum(cantidad*unitario*(iva/100)) as iva,
	sum(cantidad*unitario*(1+iva/100)) as total
	from historico where factura like ".$numeroFactura." group by factura";
	$resultados = consultaUnica($sql, MYSQL_ASSOC);
	$page->drawLine(33, 80, 562, 80)
		->drawText(round($resultados['cantidad'],2), 325, 70, $charEncoding)
		->drawText(precioFormateado($resultados['unitario']), 375, 70, $charEncoding)
		->drawText(precioFormateado($resultados['iva']),425,70, $charEncoding)
		->drawText(precioFormateado($resultados['total']), 525, 70, $charEncoding);
	// Escribimos y mostramos
	
	if ( isset($_REQUEST['envio']) && $_REQUEST['envio']=='true' ) {
		$todasFacturas->properties['Title'] = "Factura ". $numeroFactura;
		$todasFacturas->properties['Author'] = "Independencia Centro de Negocios";
		$todasFacturas->pages[0] = $page;
		$facturaPDF = $todasFacturas->render();
		// parte del envio
		envia($facturaPDF,$numeroFactura,$dup);
	} else {
		if ( count($idFacturas) > 1 ) {
			$todasFacturas->properties['Title'] = "Facturas ". date("r");
		} elseif( count($idFacturas) == 1 ) {
			$todasFacturas->properties['Title'] = "Factura ".$numeroFactura;
		}
		$todasFacturas->properties['Author'] = "Independencia Centro de Negocios";
		$todasFacturas->pages[] = $page;	
	}
}
if ( !isset($_REQUEST['envio'] ) ) {
	header('Content-type: application/pdf');
	header('Content-Disposition: attachment; filename="facturas_'.date("r").'.pdf"');
	echo $todasFacturas->render();
}