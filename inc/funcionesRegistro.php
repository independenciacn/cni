<?php
/**
 * FuncionesRegistro File Doc Comment
 *
 * Funciones que se encargan de Insertar, Borrar y Modificar Registros en las tablas
 *
 * PHP Version 5.2.10
 *
 * @category FuncionesRegistro
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once 'configuracion.php';
if ( isset( $_SESSION['usuario'] ) && $_POST['opt'] ) {
    sanitize($_POST);
    $crudFunctions = array('add','delete','update');
    $blackFields = array('opt','tabla','Id','texto','tableform');
    if ( in_array( $_POST['opt'], $crudFunctions ) ) {
       $camposFecha = camposFecha($_POST['tableform']);
       $camposCheck = camposCheck($_POST['tableform']); 
       echo $_POST['opt']($_POST);
    }
}
/**
 * Agrega el registro
 * 
 * @param array $vars
 * @return string
 */
function add( $vars ) {
    global $blackFields;
    $sql = "INSERT INTO " . $vars['tableform'] . " SET (";
    foreach($vars as $key => $var ) {
        if ( !in_array( $key, $blackFields ) ) {
            $sql .= checkCampos( $key, $var );
        }
    }
    $sql = substr( $sql, 0, strlen($sql)-1 );
    return ejecutaCrud($sql, 'Agregado');
}
/**
 * Borra el registro
 * 
 * @param array $vars
 * @return string
 */
function delete( $vars ) {
    $sql = "DELETE FROM " . $vars['tableform'] . " where id like " . $vars['Id'];
    return ejecutaCrud($sql, 'Borrado');
}
/**
 * Actualiza el registro
 * 
 * @param array $vars
 * @return string
 */
function update( $vars ) {
    var_dump($vars);
    global $blackFields;
    $sql = "UPDATE " . $vars['tableform'] . " SET ";
    foreach($vars as $key => $var ) {
        if ( !in_array( $key, $blackFields ) ) {
            $sql .= checkCampos( $key, $var );
        }
    }
    $sql = substr($sql, 0, strlen($sql)-1);
    $sql .= " WHERE id like ". $vars['Id'];
    return ejecutaCrud( $sql, 'Actualizado' );
}
/**
 * Ejecuta la consulta y devuelve la respuesta
 * 
 * @param string $sql
 * @param string $seccion
 */
function ejecutaCrud( $sql, $seccion ) {
    if ( ejecutaConsulta( $sql ) ) {
        return "<div class='success'>Registro ". $seccion. ".</div>";
    } else {
        return "<div class='error'>No se ha ". $seccion. " el Registro.".$sql."</div>";
    }
}
/**
 * Checkea los campos para convertirlos segun su formato
 * 
 * @param string $key
 * @param string $var
 * @return string
 */
function checkCampos( $key, $var ) {
    global $camposFecha, $camposCheck;
    if ( in_array( $key, $camposFecha ) ) {
        $var = cambiaf( $var );
    } elseif( in_array( $key, $camposCheck ) ) {
        $var = ( $var == 'on' ) ? '-1': '0';
    }
    $sql = " ". $key ." = '" . $var ."',";    
    return $sql;
}
/**
 * Selecciona de la tabla alias que campos son de tipo fecha
 * 
 * @param string $tabla
 * @return array $campos
 */
function camposFecha( $tabla ) {
    $campos = array();
    $sql = "Select campoo from alias where tabla like '".$tabla."' 
    and tipo like 'date'";
    $resultados = consultaGenerica( $sql, MYSQL_ASSOC );
    foreach( $resultados as $resultado ) {
        $campos[] = $resultado['campoo'];
    }
    return $campos;
}
/**
 * Selecciona de la tabla alias que campos son checkbox
 * 
 * @param string $tabla
 * @return array
 */
function camposCheck( $tabla ) {
    $campos = array();
    $sql = "Select campoo from alias where tabla like '".$tabla."'
    and tipo like 'checkbox'";
    $resultados = consultaGenerica( $sql, MYSQL_ASSOC );
    foreach( $resultados as $resultado ) {
        $campos[] = $resultado['campoo'];
    }
    return $campos;
}