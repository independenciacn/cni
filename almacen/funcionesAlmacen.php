<?php
/**
 * FuncionesAlmacen File Doc Comment
 *
 * Funciones de Borrado, Insercion, Actualizacion del Almacenaje
 *
 * PHP Version 5.2.6
 *
 * @category FuncionesAlmacen
 * @package  cni/almacen
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/configuracion.php';
if ( isset($_SESSION['usuario']) && isset( $_POST ) ) {
    sanitize( $_POST );
} else {
    notFound();
}
/**
 * Borramos el registro
 */
if ( isset( $_POST['opcion']) && $_POST['opcion'] == 'del' ) {
    $opcion = "Borrado";
    $sql = "Delete from z_almacen where id like ". $_POST['item'];
}
/**
 * Agregamos el registro
 */
if ( isset( $_POST['opcion']) && $_POST['opcion'] == 'add' ) {
    $opcion = "Agregado";
    $sql = "Insert into z_almacen (`cliente`,`bultos`,`inicio`,`fin`) 
    values ('". $_POST['idcliente'] ."','". $_POST['bultos']."',
    STR_TO_DATE('" . $_POST['fechaInicio'] ."','%d-%m-%Y'),
    STR_TO_DATE('" . $_POST['fechaFin']."','%d-%m-%Y')
    )";
}
/**
 * Actualizamos el registro
 */
if ( isset( $_POST['opcion']) && $_POST['opcion'] == 'update' ) {
    $opcion = "Actualizado";
    $sql = "Update z_almacen set `bultos` = '".$_POST['bultos']."',
    `inicio` = STR_TO_DATE('" . $_POST['fechaInicio'] ."','%d-%m-%Y'),
    `fin` = STR_TO_DATE('" . $_POST['fechaFin']."','%d-%m-%Y') 
    where id like ". $_POST['item'];
}
/**
 * Ejecutamos la consulta y devolvemos la respuesta
 */
if ( isset( $_POST['opcion'] )  && isset( $opcion ) && isset( $sql ) ) {
    if ( ejecutaConsulta( $sql ) ) {
        echo "<div class='success'>Registro ".$opcion."</div>";
    } else {
        echo "<div class='error'>No se ha ".$opcion." el Registro</div>";
    }
}
