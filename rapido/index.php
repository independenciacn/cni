<?php
/**
 * Index File Doc Comment
 * 
 * Pagina principal de asigacion de servicios
 * 
 * PHP Version 5.2.6
 * 
 * @category Index
 * @package  cni/rapido
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
error_reporting( E_ALL );
require_once '../inc/variables.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Servicios</title>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="../js/prototype17.js"></script>
<script type="text/javascript" src="../js/calendar.js"></script>
<script type="text/javascript" src="../js/lang/calendar-es.js"></script>
<script type="text/javascript" src="../js/calendar-setup.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<link href="../estilo/cni.css" rel="stylesheet" type="text/css"></link>
<link href="../estilo/calendario.css" rel="stylesheet" type="text/css"></link>
</head>
<?php 
/**
 * Devuelve el listado de clientes
 * 
 * @param string $cliente
 * @return string $texto
 */
function clientes($cliente)
{
    global $con;
    $sql = "Select Id,Nombre from clientes where `Estado_de_cliente` like '-1' 
	or `Estado_de_cliente` like 'on' order by Nombre";
    $consulta = mysql_query( $sql, $con );
    while (true == ($resultado = mysql_fetch_array( $consulta ))) {
        if ($cliente == $resultado[0]) {
            $seleccionado = "selected";
        } else {
            $seleccionado = "";
        }
        $texto .= "<option " . $seleccionado . " value='" . $resultado[0] . "'>
		" . $resultado[1] . "</option>";
    }
    return $texto;
}
/**
 * Para mostrar las facturas por meses, se marca por defecto el mes en el que
 * estamos. Funcion de la seleccion de meses para ver los servicios asignados ese mes
 * 
 * @param string $mes
 * @return string $cadena
 */
function seleccionMeses( $mes = null )
{
    global $meses;
    $mes = (!is_null( $mes )) ? $mes : date( "m" );
    $cadena = "<select name='meses' id='meses'>";
    $cadena .= "<option value='0'>--Mes--</option>";
    for ($i = 1; $i <= 12; $i ++) {
        $marcado = ($mes == $i) ? "selected" : "";
        $cadena .= "<option value='" . $i . "' " . $marcado . ">" . $meses[$i] .
         "</option>";
    }
    $cadena .= "</select>";
    echo $cadena;
}
?>
<body>
<form id='seleccion_cliente' name='seleccion_cliente'>
<table class='tabla'>
    <tr>
        <td align='left' valign='top' colspan='4'><input type='button'
            class='boton' onclick='window.close()' value='[X] Cerrar' />
        </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th valign='top'><input type='hidden' id='id_cliente'
            name='id_cliente' /> <img src='../iconos/personal.png'
            alt='cliente' />&nbsp;Cliente:</th>
        <td><input type='text' name='cliente' id='cliente'
            autocomplete='off' onkeyup='busca_cliente()' size='60' /></td>
        <th><img src='../iconos/date.png' alt='Mes' />&nbsp;Mes:</th>
        <td><?php echo seleccionMeses(); ?></td>
        <td><select id='anyo'>
<?php 
$anyoActual = date( 'Y' );
for ($i = 2007;$i <= $anyoActual + 2;$i ++ ) {
    if ($anyoActual == $i) {
        echo "<option selected value='" . $i . "'>" . $i . "</option>";
    } else {
        echo "<option value='" . $i . "'>" . $i . "</option>";
    }
}
?>
</select></td>
        <td><input type='button' class='ver_servicios'
            onclick='ver_servicios_contratados()' value='Ver Servicios' /></td>
        <td><input type='reset' class='limpiar' value='Limpiar' /></td>

    </tr>
    <tr>
        <td colspan='2'><input type='button' onclick='cliente_rango(0)'
            value='>Facturacion Mensual' /> <input type='button'
            onclick='cliente_rango(1)' value='>Facturacion Puntual' /> <input
            type='button' onclick='gestion_facturas(0)'
            value='>Gesti&oacute;n Facturas' /> <input type='button'
            onclick='oculta_parametros()' value='>Ocultar Ventana' /> <input
            type='button' onclick='gestion_facturas(1)'
            value='>Listar todas las facturas' /></td>
    </tr>
</table>
</form>
<div id='parametros_facturacion'></div>
<div id='listado_clientes'></div>
<div id='tabla'></div>
<div id='observa'></div>
<div id='modificar'></div>
<div id='debug'></div>
</body>
</html>