<?php
require_once 'variables.php';
require_once 'Cni.php';
if (isset($_POST['cantidad'])) {
    $precio = floatval(Cni::cambiaFormatoNumerico($_POST['precio']));
    $cantidad = floatval(Cni::cambiaFormatoNumerico($_POST['cantidad']));
    $iva = floatval(Cni::cambiaFormatoNumerico($_POST['iva']));
    $importe = $precio * $cantidad;
    $total = Cni::totalconIva($importe, $iva);
    echo Cni::formateaNumero($importe)."#".
        Cni::formateaNumero($total);
}