<?php
/*
 * Variables posteadas: inicio, fin, vista, datos
 * Valores de vista: 1 = Acumulada, 2 = Detallada, 3 = Grafica
 * Valores de datos: 1 = Movimientos de Clientes, 2 = Consumo de Servicios
 */
$html = "";
if (isset($_POST['inicio'])) {
    require_once 'clases/EntradasSalidas.php';
    $entradasSalidas = new EntradasSalidas();
    $params = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    $entradasSalidas->anyoInicial = $params['inicio'];
    $entradasSalidas->anyoFinal = $params['fin'];
    $entradasSalidas->setAnyos();
    $entradasSalidas->setTipoVista($params['vista']);
    $entradasSalidas->setTipoDato($params['datos']);
    echo "<h3>{$entradasSalidas->titulo()}</h3>"; 
    switch ($params['vista']) {
        case 1:
            $html = ($params['datos'] == '1') ?
                $entradasSalidas->listadoAcumuladoClientes() : $entradasSalidas->listadoAcumuladoServicios();
            break;
        case 2:
            $html = ($params['datos'] == '1') ?
                $entradasSalidas->listadoDetalladoClientes() : $entradasSalidas->listadoDetalladoServicios();
            break;
        case 3:
            $html = ($params['datos'] == '1') ?
                $entradasSalidas->graficaClientes() : $entradasSalidas->graficaServicios();
            break;
        default:
            $html = "Opcion no disponible";
            break;
    }
}
echo $html;