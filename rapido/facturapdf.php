<?php
/**
* facturapdf File Doc Comment
*
* Generación de facturas en PDF
*
* PHP Version 5.2.6
*
* @category Rapido
* @package  CniRapido
* @author   Ruben Lacasa Mas <ruben@ensenalia.com>
* @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ CC BY-NC-ND 3.0
* @version  GIT: Id$ In development. Very stable.
* @link     https://github.com/independenciacn/cni
*/
require_once '../inc/variables.php';
require_once '../inc/classes/Connection.php';
require_once '../inc/classes/Cni.php';
require_once '../inc/ezpdf/class.ezpdf.php';

$getParams = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$postParams = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$cni = new Cni();

if ((isset($_GET['factura'])) || (isset($_POST['factura']))) {
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
    $posX = 463;
    if ((isset($_GET['dup']))||(isset($_POST['dup']))) {
        $text = "<b>FACTURA (DUPLICADO)</b>";
        $dup = true;
        $posX = 358;
    }
    $pdf->addText($posX, 730, 16, $text);
    $pdf->rectangle(263, 660, 280, 50);
//ID CLIENTE
    $cliente = $resultado['Id'];
    $texto="FECHA: " . $cni->getFechaConNombreMes($resultado['Fecha']);
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
        if (isset($iva[$resultado['iva']])) {
            $iva[$resultado['iva']]['iva'] += (float) $totalIva;
            $iva[$resultado['iva']]['valor'] += (float) $totalBruto;
        } else {
            $iva[$resultado['iva']]['iva'] = (float) $totalIva;
            $iva[$resultado['iva']]['valor'] = (float) $totalBruto;
        }
        //$iva[$resultado['iva']][] = (float) $totalIva;
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
    // Quitada ,"shadeCol"=>array(0.866,0.866,0.866)
    $options = array("width"=>500,"maxWidth"=>500, "shadeCol"=>array(0.866,0.866,0.866),'fontSize' => 9,
         'titleFontSize' => 10,
    "cols"=>array(
        'Cant.'=>array('justification'=>'right'),
        'P/Unitario'=>array('justification'=>'right'),
        'Importe'=>array('justification'=>'right'),
        'IVA'=>array('justification'=>'center'),
        'TOTAL'=>array('justification'=>'right')));

    $pdf->ezTable($data, 6, "", $options);
    // Se almacena el array de datos del pie
    // Columnas Tipo Iva, Base Imponible, Cuota Iva, Total
    
    $pdf->ezSetY(150);
    $cols[] = array('Tipo Iva' => array('justification' => 'center'));
    $cols[] = array('Base Imponible' => array('justification' => 'center'));
    $cols[] = array('Cuota Iva' => array('justification' => 'center'));
    $cols[] = array('Total' => array('justification' => 'center'));
    
    foreach ($iva as $key => $val) {
        $linea["Tipo Iva"] = $key . '%';
        $linea["Base Imponible"] = number_format($val['valor'], 2, ',', '.') . "!";
        $linea["Cuota Iva"] = number_format($val['iva'], 2, ',', '.') . "!";
        $linea["Total"] = number_format($val['valor'] + $val['iva'], 2, ',', '.') . "!" ;
        $pie[] = $linea;
    }
    $pie[] = array(
        "Tipo Iva" => "",
        "Base Imponible" => "",
        "Cuota Iva" => "",
        "Total" => number_format(array_sum($total), 2, ',', '.') . "!"
    );
    //$cols[] = array('TOTAL' => array('justification' => 'center'));
    
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
            'cols'=>$cols,
            "shadeCol"=>array(0.866,0.866,0.866),
            'fontSize' => 9,
            'innerLineThickness' => 0,
            'outerLineThickness' => 0,
            'rowGap' => 1,
            'titleFontSize' => 10
        )
    );
    /**
     * obtenemos la forma de pago
     */
    $sql = "Select fpago, obs_fpago, obs_alt, pedidoCliente from regfacturas where codigo like $factura";
    $resultados = $conexion->consulta($sql);
    $resultado = current($resultados);
    $pdf->ezSetY(145);
    $pdf->ezText("   Forma de Pago:" .$resultado['fpago'], 10);
    //if(($resultado[fpago] != "Cheque") && ($resultado[fpago] != "Contado") && ($resultado[fpago] != "Tarjeta credito")&& ($resultado[fpago] != utf8_decode("Liquidación")))
    //$pdf->ezText("CC:".$resultado[1]);
    $observacion = preg_replace('|<br\/>|', "\n\r", $resultado['obs_fpago']);
    $observacion = preg_replace('|\(|', "\n\r(", $observacion);
    $observacion = preg_replace('|Vto|', "\n\rVto", $observacion);
    $observacion = preg_replace('|Vencimien|', "\n\rVencimien", $observacion);
    $pdf->ezText("   ".utf8_decode($observacion)." ".utf8_decode($resultado['obs_alt']), 10);
    // Agregamos si existe en Pedido de Cliente
    if (!is_null($resultado['pedidoCliente']) && strlen(trim($resultado['pedidoCliente'])) > 0) {
        $pdf->ezText("   Num. Pedido: ". $resultado['pedidoCliente'], 10);
    }
//Si se ha mandado a guardar escribimos en el fichero
    if (isset($_POST['factura'])) {
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
    } else {
        $pdf->ezStream();
    }
}
