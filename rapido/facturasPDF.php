<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
	notFound();
}
sanitize( $_GET );
require_once 'Zend/Pdf.php';
//require_once '../inc/Cell.php'; //http://agoln.net/archives/81
$charEncoding = 'UTF-8';
$pdf = new Zend_Pdf();
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
// Propiedades del Fichero
$pdf->properties['Title'] = "Factura ". $_GET['codigo'];
$pdf->properties['Author'] = "Independencia Centro de Negocios";
// Imagenes
$logo = Zend_Pdf_Image::imageWithPath('images/logo_n.jpg');//200x88
$aniversario = Zend_Pdf_Image::imageWithPath('images/image001.png'); // 90x80
$pie = Zend_Pdf_Image::imageWithPath('images/pie_n.jpg');//600x60
$nif = Zend_Pdf_Image::imageWithPath('images/nif_n.jpg');//33x365
//Esquina superior izquierda 0,843
//Esquina inferior izquierda 0,0
//Esquina superior derecha 595,843
//Esquina inferior derecha 595,0
//x1,y1->esquina inferior izquierda, x2,y2->esquina superior derecha
//anterior addImage(img,x,y,w,[h],[quality=75])
$page->drawImage($logo, 33, 740, 233, 828);
$page->drawImage($aniversario, 200, 100, 290, 180);
$page->drawImage($pie, 0, 0, 595, 60);
$page->drawImage($nif, 0, 150, 33, 515);
$page->drawText( 'factura', '100', '100', $charEncoding );
//Cabezera de la factura
$sql="Select c.Nombre,c.Direccion,c.CP,c.Ciudad,
c.NIF,date_format(r.fecha,'%d-%m-%Y') as fecha,
 r.pedidoCliente, c.id, r.codigo from clientes as c
join regfacturas as r on r.id_cliente = c.id
where r.id like " . $_GET['codigo'];
$resultado = consultaUnica( $sql, MYSQL_ASSOC );
if( (isset( $_GET['dup'] ) ) || ( isset( $_POST['dup'] ) ) ) {
	$page->drawText("FACTURA (DUPLICADO)", 400, 810, $charEncoding );
} else {
	$page->drawText("FACTURA", 500, 810, $charEncoding );
}
$page->drawRectangle(300,750,590,805,Zend_Pdf_Page::SHAPE_DRAW_STROKE);
$page->setStyle($estilo2);
$page->drawText("FECHA:".$resultado['fecha'], 33, 720, $charEncoding )
	->drawText("Num. FACTURA:". $resultado['codigo'],33, 710, $charEncoding )
	->drawText($resultado['Nombre'], 310, 790, $charEncoding )
	->drawText($resultado['Direccion'], 310, 780, $charEncoding )
	->drawText($resultado['CP'] ." - " . $resultado['Ciudad'], 310, 770, $charEncoding )
	->drawText("NIF: ".$resultado['NIF'], 310, 760, $charEncoding );
//Cuadricula
//$page->drawRectangle(33, 70, 562, 700,Zend_Pdf_Page::SHAPE_DRAW_STROKE);
//altura maxima 690, 700
// minima 70, 80
$line = 1;
//Encabezados
/*
 * Servicio"=>"",
			"Cant."=>"",
			"P/Unitario"=>"",
			"Importe"=>"",
			"IVA"=>"",
			"TOTAL"=>"");
 */
$page->drawText('SERVICIO', 33, 685, $charEncoding )
	->drawText('CANT.',300,685,$charEncoding )
	->drawText('P/U',350,685,$charEncoding )
	->drawText('IMP',400,685,$charEncoding )
	->drawText('IVA',450,685,$charEncoding )
	->drawText('TOTAL',500,685,$charEncoding );


$page->drawLine(33, 680, 562, 680);
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
$page->drawLine(300, 680, 300, 60)
	->drawLine(350, 680, 350, 60)
	->drawLine(400, 680, 400, 60)
	->drawLine(450, 680, 450, 60)
	->drawLine(500, 680, 500, 60);

$sql = "Select * from historico where factura like " . $resultado['codigo'];
$resultados = consultaGenerica($sql,MYSQL_ASSOC);
foreach( $resultados as $resultado ) {
	$importe_sin_iva = $resultado['cantidad']*$resultado['unitario'];
	$data[]=array("Servicio"=>ucfirst($resultado[2])." ".ucfirst($resultado[6]),
			"Cant."=>number_format($resultado[cantidad],2,',','.'),
					"P/Unitario"=>number_format($resultado[unitario],2,',','.')."!",
					"Importe"=>number_format($importe_sin_iva,2,',','.')."!",
							"IVA"=>$resultado[iva]."%",
							"TOTAL"=>number_format(iva($importe_sin_iva,$resultado[iva]),2,',','.')."!");
							$total = $total + iva($importe_sin_iva,$resultado[5]);
							$bruto = $bruto + $importe_sin_iva;
							$celdas++;
	//$cantidad++;
	$cantidad = $cantidad + number_format($resultado[cantidad],2,',','.');
			$j++;
		}
/*
 * //ID CLIENTE
		$cliente = $resultado[6];
		$texto="FECHA:".cambiaf($resultado[5]);
		$pdf->addText(50,700,12,$texto);
		$texto="Num. FACTURA:".$factura;
		$pdf->addText(50,685,12,$texto);
//Datos cliente
		$pdf->addText(265,698,10,"<b>".$resultado[0]."</b>");
		$pdf->addText(265,687,10,"<b>".$resultado[1]."</b>");
		$pdf->addText(265,676,10,"<b>".$resultado[2]."-".$resultado[3]."</b>");
		$pdf->addText(265,665,10,"<b>NIF:".$resultado[4]."</b>");
 */
/*
 * //Cabezera de la factura
/*		$sql ="Select c.Nombre,c.Direccion,c.CP,c.Ciudad,
		c.NIF,r.fecha, r.pedidoCliente, c.id from clientes as c 
		join regfacturas as r on r.id_cliente = c.id 
		where r.codigo like $factura";
		$consulta = mysql_query( $sql, $con );
		$resultado = mysql_fetch_array($consulta);
		if((isset($_GET[dup]))||(isset($_POST[dup])))
			$pdf->addText(363,730,16,"<b>FACTURA (DUPLICADO)<b>");
		else
			$pdf->addText(463,730,16,"<b>FACTURA<b>");
		$pdf->rectangle(263,660,280,50);
 */
$pdf->pages[] = $page;
header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="factura'.$resultado["codigo"].'.pdf"');
echo $pdf->render();
//var_dump($_POST);