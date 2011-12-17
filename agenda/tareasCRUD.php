<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
sanitize( $_REQUEST );
/*$sql = "Select concat(e.Nombre, ' ', e.Apell1, ' ', e.Apell2) as empleada,
t.id as id, t.nombre as nombre,
date_format(t.vencimiento, '%d-%m-%Y') as vencimiento,
t.prioridad as prioridad, t.realizada as realizada
FROM tareas_pendientes as t
INNER JOIN empleados as e ON t.asignada = e.id";*/

$oper = array('edit','del','add');
/**
 * Actualizamos el Registro
 */
if ( isset( $_POST['oper'] ) && $_POST['oper'] =='edit' ) {
    $sql = "Update tareas_pendientes SET
    nombre = '" . $_POST['nombre'] . "',
    vencimiento = '" . cambiaf( $_POST['vencimiento'] ) . "',
    prioridad = '" . $_POST['prioridad'] . "',
    realizada = '" . $_POST['realizada'] . "',
    asignada = '" . $_POST['empleada'] ."'
    where id like " . $_POST['id'];
}
/**
 * Agregamos el Registro
 */
if ( isset( $_POST['oper']) && $_POST['oper'] == 'add' ) {
    $sql = "Insert into tareas_pendientes SET 
    nombre = '" . $_POST['nombre'] . "',
    vencimiento = '" . cambiaf( $_POST['vencimiento'] ) . "',
    prioridad = '" . $_POST['prioridad'] . "',
    realizada = '" . $_POST['realizada'] . "',
    asignada = '" . $_POST['empleada'] ."'";
}
/**
 * Borramos el Registro
 */
if ( isset( $_POST['oper']) && $_POST['oper'] == 'del' ) {
    $sql = "Delete from tareas_pendientes where id like ".$_POST['id'];
}
/**
 * Ejecutamos la sentencia SQL
 */
if ( isset( $_POST['oper']) && in_array( $_POST['oper'], $oper ) ) {
    ejecutaConsulta($sql);
}