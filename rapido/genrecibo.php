<?php
/**
 * genrecibo.php File Doc Comment
 *
 * Fichero que genera el Recibo para el cliente.
 *
 * PHP Version 5.2.6
 *
 * @category rapido
 * @package  cni/rapido
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
require_once '../inc/Cliente.php';
Cni::chequeaSesion();
if (isset($_SESSION['usuario']) && isset($_GET['id'])) {
    $sql = "SELECT
	id_cliente as cliente,
	codigo, 
	fpago as formaPago,
	importe,
	DATE_FORMAT(fecha, '%d-%m-%Y') as fecha,
	obs_alt as obs
	FROM regfacturas 
	WHERE id LIKE ?";
    $resultados = Cni::consultaPreparada(
        $sql,
        array($_GET['id']),
        PDO::FETCH_CLASS
    );
    $idCliente = false;
    $codigo = false;
    $formaPago = false;
    $importe = false;
    $fecha = false;
    $vto = false;
    $vencimiento = false;
    foreach ($resultados as $resultado) {
        $idCliente = $resultado->cliente;
        $codigo = $resultado->codigo;
        $formaPago = $resultado->formaPago;
        $importe = Cni::formateaNumero($resultado->importe, true);
        $fecha = $resultado->fecha;
        $vto = strtok($resultado->obs, "VTO ");
        if (isset($vto[1])) {
            $vencimiento = $vto;
        } else {
            $vencimiento = $fecha;
        }
    }
    $cliente = new Cliente($idCliente);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="utf-8">
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <title>RECIBO</title>
    <style>
        th {
            background-color: #eee;
        }

        .concepto {
            height: 6.25em;
        }

        .marca {
            font-weight: bold;
        }
        .primeraColumna {
            width: 40%;
        }
        .segundaColumna {
            width: 20%;
        }
    </style>
    </head>
    <body>
    <table class='table table-bordered'>
        <colgroup>
        <col span='2' class='primeraColumna'/>
        <col class='segundaColumna'/>
        </colgroup>
    <thead>
    <tr>
        <th>NUMERO FACTURA</th>
        <th>FORMA PAGO</th>
        <th>IMPORTE</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= $codigo; ?></td>
        <td><?= $formaPago; ?></td>
        <td><?= $importe; ?></td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th>FECHA FACTURA</th>
        <th>VENCIMIENTO</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= $fecha; ?></td>
        <td><?= $vencimiento; ?></td>
        <td></td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan='3'>CONCEPTO:</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan='3' class='concepto'></td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan='2'>DATOS CLIENTE</th>
        <th>FIRMA</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan='2'>
            <div class='row-fluid span8'>
                <div class='span2 marca'>
                    NOMBRE:<br/>
                    NIF:<br/>
                    DIRECCIÓN:<br/>
                    CP:<br/>
                    CIUDAD:<br/>
                    PAIS:
                </div>
                <div>
                    <?= $cliente->nombre; ?><br/>
                    <?= $cliente->nif; ?><br/>
                    <?= $cliente->direccion; ?><br/>
                    <?= $cliente->cp; ?><br/>
                    <?= $cliente->ciudad; ?><br/>
                    <?= $cliente->pais; ?>
                </div>
            </div>
        </td>
        <td></td>
    </tr>
    </tbody>
    </table>
    </body>
    </html>
    <?php
}

