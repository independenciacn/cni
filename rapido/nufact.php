<?php
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
require_once '../inc/Cliente.php';
/**
 * nufact.php File Doc Comment
 *
 * FIchero para la creacion de los nuevos parametros de factura.
 * Creada en 2006-2007. Refractorizada en 2012
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
$html = Cni::mensajeError('Error en la solicitud');
Cni::chequeaSesion();
if (isset($_SESSION['usuario']) && isset($_POST['cliente']) && isset
($_POST['opcion'])
) {
    $tipoFacturacion = ($_POST['opcion'] == 0) ? 'mensual' : 'puntual';
    $fechaFactura = date('d-m-Y');
    $cliente = new Cliente($_POST['cliente']);
    ?>
    <table width='100%' class='tabla'>
    <tr>
        <th>
            Facturacion
            <?= $tipoFacturacion; ?>
            de
            <?= $cliente->nombre; ?>
        </th>
    </tr>
    <tr>
        <th>Datos Generales de la Factura</th>
    </tr>
    <tr>
        <td>
            <label for='fecha_factura'>Fecha Factura:</label>
            <input type='text' id='fecha_factura' name='fecha_factura'
                value='" . $fechaFactura . "'/>
            &nbsp;&nbsp;
            <button TYPE='button' class='calendario'
                    id='f_trigger_fecha_factura'></button>
            &nbsp;&nbsp;
            <label for='codigo'>Numero Factura:</label>
            <input type='text' id='codigo'
                   value='". Cni::codigoNuevaFactura() . "'  size='6'/>
            &nbsp;&nbsp;
            <label for='observaciones'>Observaciones:</label>
            <input type='text' id='observaciones'
                   name='observaciones' size='60px'/>
        </td>
    </tr>
    <?php
    if ($_POST['opcion'] == 1) {
        ?>
        <tr>
            <th>Datos especificos facturación puntual</th>
        </tr>
        <tr>
            <td>
            <label for='fecha_inicial_factura'>Fecha a Facturar:</label>
            <input type='text' id='fecha_inicial_factura'
                   name='fechaInicialFactura' size = '10' value='00-00-0000'/>
            &nbsp;&nbsp;
            <button TYPE='button' class='calendario'
                    id='f_trigger_fecha_inicial_factura'></button>
            &nbsp;&nbsp;
            <label for='fechaFinalFactura'>Fecha fin Rango:</label>
            <input type='text' id='fechaFinalFactura'
                   name='fecha_final_factura' size = '10' value='00-00-0000'/>
            &nbsp;&nbsp;
            <button TYPE='button' class='calendario'
                    id='f_trigger_fecha_final_factura'></button>
            </td>
        </tr>
    <?php
    }
    ?>
    <tr>
        <td align='left'>
            <input type='hidden' id='tipo' value='<?= $_POST['opcion']; ?>'/>
            <input type='button' onclick='generar_excel()'
                   value='>Informe Gestion'/>
            <input type='button' onclick='generaFactura(true)'
                   value='>Generar Proforma' />
            <input type='button' onclick='generaFactura(false)'
            value='>Generar Factura' />
        </td>
    </tr>
    </table>
    <?php
} else {
    echo Cni::mensajeError('Error en la solicitud: Faltan Parametros');
}

