<?php
/*
 * Variables posteadas: inicio, fin, vista, datos
 * Valores de vista: 1 = Acumulada, 2 = Detallada, 3 = Grafica
 * Valores de datos: 1 = Movimientos de Clientes, 2 = Consumo de Servicios
 */
if (isset($_POST['inicio'])) {
    require_once 'clases/EntradasSalidas.php';
    $entradasSalidas = new EntradasSalidas();
    $entradasSalidas->anyoInicial = $_POST['inicio'];
    $entradasSalidas->anyoFinal = $_POST['fin'];
    $entradasSalidas->setAnyos();
    $entradasSalidas->setTipoVista($_POST['vista']);
    $entradasSalidas->setTipoDato($_POST['datos']);
    echo "<h3>{$entradasSalidas->titulo()}</h3>"; 
    switch ($_POST['vista']) {
        case 1:
            ($_POST['datos'] == '1') ? 
             $html = $entradasSalidas->listadoAcumuladoClientes() : 
             $html = $entradasSalidas->listadoAcumuladoServicios();
            break;
        case 2:
            ($_POST['datos'] == '1') ? 
             $html = $entradasSalidas->listadoDetalladoClientes() : 
             $html = $entradasSalidas->listadoDetalladoServicios();
            break;
        case 3:
            ($_POST['datos'] == '1') ? 
             $html = $entradasSalidas->graficaClientes() : 
             $html = $entradasSalidas->graficaServicios();
            break;
        default:
            $html = "Opcion no disponible";
            break;
    }
    echo $html;
}