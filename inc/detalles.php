<?php
/**
 * Detalles File Doc Comment
 * 
 * Visualiza los detalles de los estados de actividad y tambien la principal
 * con los cumpleaños
 * 
 * PHP Version 5.2.6
 * 
 * @category Detalles
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once 'variables.php';
$vars = $_POST;
array_walk( $vars, 'sanitize' ); // Saneamos todos los datos
switch ($vars['opcion']) {
    case 0:
        $cadena = frmObservaciones( $vars );
        break;
    case 1:
        $cadena = accion( $vars );
        break;
}
echo $cadena;
/**
 * Formulario de Observaciones
 * 
 * @param array $vars
 * @return string $cadena
 */
function frmObservaciones ($vars)
{
    global $con;
    switch ($vars['tipo']) {
        case 0:
            $sql = "Select * from desvios where id_cliente like " . $vars['cliente'] ;
            $tipo = "Desvios";
            break; //desvios
        case 1:
            $sql = "Select * from extranet where id_cliente like " . $vars['cliente'];
            $tipo = "Extranet";
            break;
    }
    $consulta = mysql_query( $sql, $con );
    if (mysql_numrows( $consulta ) != 0) //hay entradas actualizamos
    {
        $boton = "<input type='button' class='boton_actualizar' 
        onclick='ver_detalles(1,1," . $vars['tipo'] .", " . $vars['cliente'] . ")' 
        value='Actualizar'/>";
        $resultado = mysql_fetch_array( $consulta );
        $observacion = $resultado['observacion'];
    } else {
        $boton = "<input type='button' class='agregar' 
        onclick='ver_detalles(1,0, " . $vars['tipo'] ." ," . $vars['cliente'] ." )' 
        value='Agregar'/>";
        $observacion = "";
    }
    //formulario
    $cadena = "<input type='button' class='boton_cerrar' 
    onclick='cierra_frm_observaciones()' value='Cerrar'/><p/>Observaciones de " .
     $tipo .
     ":<p/>
	<textarea rows='6' id='detalles_obs' cols='100'> " .
     $observacion . "</textarea><p/>" . $boton .
     "<input type='button' class='boton_borrar2' 
     onclick='ver_detalles(1,2," . $vars['tipo'] ." ," . $vars['cliente'] . ")' 
     value='Borrar'/>";
    return $cadena;
}
/**
 * Ejecuta la accion
 * 
 * @param array $vars
 * @return string $cadena
 */
function accion ($vars)
{
    global $con;
    switch ($vars['tipo']) {
        case 0:
            $tabla = "desvios";
            break;
        case 1:
            $tabla = "extranet";
            break;
    }
    switch ($vars['accion']) {
        case 0:
            $sql = "Insert into " . $tabla ." (id_cliente,observacion) 
            values ('" . $vars['cliente'] . "','" . $vars['observacion'] . "')";
            $accion = 'Agregada';
            break;
        case 1:
            $sql = "Update " . $tabla ." set observacion='" . $vars['observacion'] ."' 
            where id_cliente like  " . $vars['cliente'];
            $accion = 'Actualizada';
            break;
        case 2:
            $sql = "Delete from " . $tabla ." where id_cliente like " . $vars['cliente'];
            $accion = 'Borrada';
            break;
    }
    if ( true == ($consulta = mysql_query( $sql, $con ) ) ) {
        $cadena = "Observacion " . $accion . "<p/>" . frmObservaciones( $vars );
    } else {
        $cadena = "No se ha realizado la accion" . $sql;
    }
    return $cadena;
}