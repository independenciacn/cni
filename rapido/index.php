<?php
/**
 * index.php File Doc Comment
 *
 * Pagina principal de asigacion de servicios. 
 * Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2012
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
$tituloGeneral = APLICACION. " - ". VERSION;
/**
 * Muestra el select de la seleccion de Meses
 *
 * @param null $mesMarcado
 * @return string
 */
function selectMeses($mesMarcado = null)
{
    if ( $mesMarcado == null ) {
        $mesMarcado = date("m");
    }
    $html = "<option value='0'>--Mes--</option>";
    foreach (Cni::$meses as $key => $mes) {
        $marcado = ($key == $mesMarcado) ? "selected" : "";
        $html .= "<option value='".$key."' ".$marcado.">".$mes."</option>";
    }
    return $html;
}

/**
 * Muestra el select de los años
 *
 * @param null $anyoMarcado
 * @return string
 */
function selectAnyos($anyoMarcado = null)
{
    if ($anyoMarcado == null) {
        $anyoMarcado = date('Y');
    }
    $html = "";
    for ($i = 2007; $i <= date('Y') + 2; $i++) {
        $marcado = ( $i == $anyoMarcado ) ? "selected":"";
        $html.= "<option value='".$i."' ".$marcado.">".$i."</option>";
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<link  href="../estilo/cni.css" rel="stylesheet"/>
	<link  href="../estilo/calendario.css"  rel="stylesheet"/>
	<title>Servicios - <?= $tituloGeneral ?></title>
</head>
<body>
<form name ='seleccionCliente' id='seleccionCliente'>
    <button class='boton' onclick="window.close()">
        [X] Cerrar
    </button>
    <input type='hidden' id='idCliente' name='idCliente' />

    <div id='clientes'>
        <label for='cliente'>
            <img src='../iconos/personal.png' alt='cliente' />&nbsp;Cliente:
        </label>
        <input type='text' id='cliente' name='cliente' onkeyup='buscaCliente()'
            size='60'/>
        <div id='listadoClientes'></div>
    </div>
    <label for='meses'>
        <img src='../iconos/date.png' alt='Mes' />&nbsp;Mes:
    </label>
    <select id='meses' name='meses'>
        <?= selectMeses() ?>
    </select>
    <label for='anyo'>Año:</label>
    <select id='anyo' name='anyo'>
        <?= selectAnyos() ?>
    </select>
    <button class='ver_servicios' onclick='verServiciosContratados(false)'>
        Ver Servicios
    </button>
    <button type='reset' class='limpiar'>Limpiar</button>
    <input class='boton' type = 'button' onclick = 'clienteRango(0)'
           value = '>Facturacion Mensual' />
    <input class='boton' type = 'button' onclick = 'clienteRango(1)'
           value ='>Facturacion Puntual' />
    <input class='boton' type = 'button' onclick = 'gestionFacturas(0)'
           title = 'Muestra las facturas del cliente en el año marcado'
           value = '>Gesti&oacute;n Facturas'/>
    <input class='boton' type = 'button' onclick = 'oculta_parametros()'
           value = '>Ocultar Ventana' />
    <input class='boton' type = 'button' onclick = 'gestionFacturas(1)'
           title = 'Muestra todas las facturas en el año marcado'
           value = '>Listar todas las facturas' />
</form>
<div id='precargaDatos'></div>
<div id='parametros_facturacion'></div>
<br/>
<div id='tabla'></div>
<div id='observa'></div>
<div id='modificar'></div>
<div id='debug'></div>
<script src='../js/prototype.js'></script>
<script src="../js/calendar.js"></script>
<script src="../js/lang/calendar-es.js"></script>
<script src="../js/calendar-setup.js"></script>
<script src="js/rapido.js" ></script>
</body>
</html>
 