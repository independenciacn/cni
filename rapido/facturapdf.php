<?php
require_once '../inc/variables.php';
require_once '../inc/classes/Connection.php';
require_once '../inc/ezpdf/class.ezpdf.php';

/**
 * @param $mes
 * @return string
 */
function dameElMes($mes)
{
    $marcado = 'Enero';
    $meses = array(
        1 => "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    );
    if (array_key_exists(intval($mes), $meses)) {
        $marcado = $meses[intval($mes)];
    }
    return $marcado;
}

/**
 * Funcion del cambio de fecha
 * @param $stamp
 * @return string
 */
function cambiaf($stamp)
{
    //formato en el que llega aaaa-mm-dd o al reves
    $fdia = explode("-", $stamp);
    $fecha = $fdia[2] . " de " . dameElMes($fdia[1]) . " de " . $fdia[0];
    return $fecha;
}

if((isset($_GET['factura'])) || (isset($_POST['factura']))) {
    $factura = (isset($_POST['factura'])) ? $_POST['factura'] : $_GET['factura'];
    $conexion = new Connection();
	$pdf =& new Cezpdf('a4');
	$all = $pdf->openObject();
	$pdf->saveState();
	$euro_diff = array(33 => 'Euro');
	$pdf->selectFont(
        '../inc/ezpdf/fonts/Helvetica.afm',
        array('encoding'=>'WinAnsiEncoding', 'differences'=>$euro_diff)
    );
	$pdf->addInfo('Title', 'Factura');
	$pdf->addInfo('Author', 'Independencia Centro de Negocios');
	$pdf->ezSetCmMargins(3, 2, 1.2, 1.2);
	$im = imagecreatefromjpeg("logo_n.jpg");
	$pdf->addImage($im, 33, 740, 200);
    //10 aniversario
    //$gif = imagecreatefromgif("image001.gif");
	//$pdf->addImage($gif, 470, 750, 90);
	//fin 10 aniversario
	/**
	 * TODO Genera consumo alto
	 * @var resource
	 */
    $im = imagecreatefromjpeg("pie_n1.jpg");
	$pdf->addImage($im, 0, 15, 600);
	/**
	 * TODO Genera consumo alto revisar formato imagen
	 * @var resource
	 */
	$im = imagecreatefromjpeg("nif_n1.JPG");
	$pdf->addImage($im, 5, 115, 35);
	$pdf->restoreState();
	$pdf->closeObject();
	$pdf->addObject($all, 'all');

//Cabezera de la factura
	$sql ="Select c.Nombre as Nombre, c.Direccion as Direccion, c.CP as CP, c.Ciudad as Ciudad,
	c.NIF as NIF, r.fecha as Fecha, r.pedidoCliente as PedidoCliente, c.id as Id
	FROM clientes as c
	join regfacturas as r on r.id_cliente = c.id 
	where r.codigo like ".$factura;
    $resultados = $conexion->consulta($sql);
    $resultado = current($resultados);
    $dup = false;
    $text = "<b>FACTURA</b>";
	if((isset($_GET['dup']))||(isset($_POST['dup']))){
		$text = "<b>FACTURA (DUPLICADO)</b>";
		$dup = true;
	}
    $pdf->addText(463, 730, 16, $text);
	$pdf->rectangle(263, 660, 280, 50);
//ID CLIENTE
	$cliente = $resultado['Id'];
	$texto="FECHA:" . cambiaf($resultado['Fecha']);
	$pdf->addText(50, 700, 12, $texto);
	$texto="Num. FACTURA: " . $factura;
	$pdf->addText(50, 685, 12, $texto);
/*Datos cliente*/
	$pdf->addText(265, 698, 10, "<b>".utf8_decode($resultado['Nombre']) . "</b>");
	$pdf->addText(265, 687, 10, "<b>".utf8_decode($resultado['Direccion']) . "</b>");
	$pdf->addText(265, 676, 10, "<b>".utf8_decode($resultado['CP']) . "-" . utf8_decode($resultado['Ciudad']) . "</b>");
	$pdf->addText(265, 665, 10, "<b>NIF:" . $resultado['NIF'] . "</b>");
//Asi se pone el fondo en todas
     
//Paso de datos de historico
	$sql = "Select servicio, cantidad, unitario, iva, obs from historico where factura like " . $factura;
    $resultados = $conexion->consulta($sql);
	$bruto = array();
    $total = array();
    $lineas = 0;
    $bruto = array();
    $total = array();
    $iva = array();
    foreach ($resultados as $resultado) {
        $totalBruto = $resultado['unitario'] * $resultado['cantidad'];
        $totalIva = $totalBruto * $resultado['iva'] / 100;
        $totalConIva = $totalBruto + $totalIva;
        $bruto[] = (float) $totalBruto;
        $total[] = (float) $totalConIva;
        $iva[$resultado['iva']][] = (float) $totalIva;
        $lineas++;
        $data[] = array(
            "Servicio" => ucfirst(utf8_decode($resultado['servicio'])) . " " . ucfirst(utf8_decode($resultado['obs'])),
            "Cant." => number_format($resultado['cantidad'], 2, ',', '.'),
            "P/Unitario" => number_format($resultado['unitario'], 2, ',', '.') . "!",
            "Importe" => number_format($totalBruto, 2, ',', '.') . "!",
            "IVA" => $resultado['iva'] . "%",
            "TOTAL" => number_format($totalConIva, 2, ',', '.') . "!");
    }
    for ($k = $lineas; $k <= 30; $k++) {
        $data[] = array(
            "Servicio" => "",
            "Cant." => "",
            "P/Unitario" => "",
            "Importe" => "",
            "IVA" => "",
            "TOTAL" => ""
        );
    }
    $pdf->ezSetY(640);
//Opciones de tabla
    $options = array("width"=>500,"maxWidth"=>500,"shadeCol"=>array(0.866,0.866,0.866),
    "cols"=>array(
        'Cant.'=>array('justification'=>'right'),
        'P/Unitario'=>array('justification'=>'right'),
        'Importe'=>array('justification'=>'right'),
        'IVA'=>array('justification'=>'center'),
        'TOTAL'=>array('justification'=>'right')));

    $pdf->ezTable($data, 6, "", $options);
    // Se almacena el array de datos del pie
    $linea["TOTAL BRUTO"] = number_format(array_sum($bruto), 2, ',', '.') . "!";
    $cols[] = array('TOTAL BRUTO' => array('justification' => 'center'));
    foreach ($iva as $key => $val) {
        $linea['IVA '.$key.'%'] = number_format(array_sum($val), 2, ',', '.') . "!" ;
        $cols[] = array('IVA ' . $key . '%' => array('justification' => 'center'));
    }
    $linea["TOTAL"] = number_format(array_sum($total), 2, ',', '.') . "!";
    $cols[] = array('TOTAL' => array('justification' => 'center'));
    $pie[] = $linea;
    $pdf->ezText("");
    // Se genera la tabla del pie con los datos, numero columnas y titulares
    $pdf->ezTable(
        $pie,
        count($cols),
        "",
        array(
            'xPos'=>'398',
            'width'=>'300',
            'maxWidth'=>'300',
            'cols'=>$cols
        )
    );
    /**
     * obtenemos la forma de pago
     */
    $sql = "Select fpago, obs_fpago, obs_alt, pedidoCliente from regfacturas where codigo like $factura";
    $resultados = $conexion->consulta($sql);
    $resultado = current($resultados);
    $pdf->ezSetY(115);
    $pdf->ezText("Forma de Pago:" .$resultado['fpago']);
    //if(($resultado[fpago] != "Cheque") && ($resultado[fpago] != "Contado") && ($resultado[fpago] != "Tarjeta credito")&& ($resultado[fpago] != utf8_decode("Liquidación")))
    //$pdf->ezText("CC:".$resultado[1]);
    $observacion = preg_replace('|<br\/>|', "\n\r", $resultado['obs_fpago']);
    $observacion = preg_replace('|\(|' ,"\n\r(", $observacion );
    $observacion = preg_replace('|Vto|',"\n\rVto", $observacion );
    $observacion = preg_replace('|Vencimien|',"\n\rVencimien", $observacion );
    $pdf->ezText(utf8_decode($observacion)." ".utf8_decode($resultado['obs_alt']));
    // Agregamos si existe en Pedido de Cliente
    if ( !is_null( $resultado['pedidoCliente'] ) ) {
        $pdf->ezText( $resultado['pedidoCliente'] );
    }
//Si se ha mandado a guardar escribimos en el fichero
    if(isset($_POST['factura']))
    {
        $pdfcode = $pdf->output();
        $nombre_factura = "factura_".$factura.".pdf";
        $ruta = "\\\\HALL_TRES\\RED\\PLANTILLAS\\facturas\\";
        if (isset($_POST['envio'])) {
            include_once 'envia.php';
            set_time_limit(120);
            envia($pdfcode, $factura, $dup);
        } else {
            if (isset($_POST['debug']) && $_POST['debug']) {
                $ruta = __DIR__ . "/";
            }
            $ruta = $ruta . $nombre_factura;
            $fp = fopen($ruta, 'wb');
            fwrite($fp, $pdfcode);
            fclose($fp);
        }
        unset($pdfcode);
    }
    else {
        $pdf->ezStream();
    }
}