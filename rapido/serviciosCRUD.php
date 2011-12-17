<?php
/**
 * serviciosCRUD File Doc Comment
 *
 * Consultas de Insercion, actualizacion y borrado de la Gestion de Servicios
 *
 * PHP Version 5.2.6
 *
 * @category ServiciosCRUD
 * @package  cni/rapido
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
sanitize( $_POST );
$oper = array('edit','del','add');
/**
 * Actualizamos el Registro
 */
if ( isset( $_POST['oper'] ) && $_POST['oper'] =='edit' ) {
    $sql = "UPDATE `consumo de servicios` SET 
    Fecha = '" . cambiaf($_POST['fecha']) . "'
    WHERE `Id Pedido` LIKE '".$_POST['idPedido']."';";
    ejecutaConsulta( $sql );
    $sql = "UPDATE `detalles consumo de servicios` SET
    Servicio = '" . $_POST['servicio'] . "',
    Cantidad = '" . $_POST['cantidad'] . "',
    PrecioUnidadEuros = '" . $_POST['precio'] . "',
    ImporteEuro = '" . $_POST['importe'] ."',
    iva = '" . $_POST['iva'] ."',
    Observaciones = '" . $_POST['observaciones'] ."'
    WHERE id LIKE " . $_POST['id'];
}
/**
 * Agregamos el Registro
 */
if ( isset( $_POST['oper']) && $_POST['oper'] == 'add' ) {
    $sql = "Insert into `consumo de servicios` SET 
    Cliente = '".$_POST['idCliente']."',
    Fecha = '".cambiaf( $_POST['fecha'] )."'";
    ejecutaConsulta( $sql );
    $sql = "Insert into `detalles consumo de servicios` SET
    Servicio = '" . $_POST['servicio'] . "',
    Cantidad = '" . $_POST['cantidad'] . "',
    PrecioUnidadEuros = '" . $_POST['precio'] . "',
    ImporteEuro = '" . $_POST['importe'] ."',
    iva = '" . $_POST['iva'] ."',
    Observaciones = '" . $_POST['observaciones'] ."',
    `Id Pedido` = ( SELECT LAST_INSERT_ID() )";
}
/**
 * Borramos el Registro
 */
if ( isset( $_POST['oper']) && $_POST['oper'] == 'del' ) {
    $sql = "DELETE FROM c, d 
        USING `consumo de servicios` c,
       `detalles consumo de servicios` d 
        WHERE c.`Id Pedido` = d.`Id Pedido` 
        AND d.id = ".$_POST['id']; 
}
/**
 * Ejecutamos la sentencia SQL
 */
if ( isset( $_POST['oper']) && in_array( $_POST['oper'], $oper ) ) {
    ejecutaConsulta($sql);
}