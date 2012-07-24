<?php
/**
 * procesa.php File Doc Comment
 *
 * Representacion del listado de los detallados cuando se hace clic en la vista
 * de acumulados
 *
 * PHP Version 5.2.6
 *
 * @category entradas
 * @package  cni/entradas
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once "clases/EntradasSalidas.php";
$entradas = new EntradasSalidas();
if (isset( $_POST['datos'] )) {
    //parametros tipo,categoria,año inicial, añofinal
    $datos = explode( "#", urldecode( $_POST['datos'] ) );
    $entradas->anyoInicial = $datos[2];
    $entradas->anyoFinal = $datos[3];
    $entradas->setAnyos();
    $descripcion = ($datos[0] != "servicios") ? $datos[0] . "s" : $datos[0];
    if ($datos[0] != "servicios") {
        echo "<table class='listaacumulada'>";
    } else {
        echo "<table class='listadetallada'>";
    }
    $mes = 1;
    $inicio = 0;
    $acum = 0;
    $cont = 0;
    if ($datos[0] != "servicios") {
        $listado = $entradas->detallesMovimientos( $datos[0], $datos[1] );
        $columnas = 4;
    } else {
        $listado = $entradas->detallesServiciosExternos( $datos[1] );
        $columnas = 3;
    }
    foreach ($listado as $entrada) {
        if ($datos[0] != "servicios") {
            $dia = Cni::verDia( $entrada[$datos[0]] );
            $dmes = Cni::verMes( $entrada[$datos[0]] );
            $anyo = Cni::verAnyo( $entrada[$datos[0]] );
            if ($datos[0] == 'salida') {
                if ($dia >= 16) {
                    if ($dmes == 12) {
                        $dmes = 1;
                        $anyo ++;
                    } else {
                        $dmes ++;
                    }
                }
            }
        } else {
            $dia = Cni::verDia( $entrada["fecha"] );
            $dmes = Cni::verMes( $entrada["fecha"] );
            $anyo = Cni::verAnyo( $entrada["fecha"] );
        }
        if ($dmes != $mes || $inicio == 0) {
            if ($inicio != 0) {
                echo "<tr class='ui-widget-content'>
                 <td colspan='{$columnas}'>
                 <strong>Total {$cont}</strong>
                 </td>
                 </tr>";
            }
            echo "<tr class=''>
             <th colspan='{$columnas}'>".Cni::$meses[$dmes]." {$anyo}</th>
             </tr>";
            if ($datos[0] != "servicios") {
                echo "<tr class=''>
                 <th></th>
                 <th class='acumulada'>Empresa</th>
                 <th class='datosacumulados'>Entrada</th>
                 <th class='datosacumulados'>Salida</th>
                 </tr>";
            } else {
                echo "<tr class=''>
                 <th></th>
                 <th class='acumulada'>Empresa</th>
                 <th class='datosacumulados'>Dia Consumo</th>
                 </tr>";
            }
            $inicio = 1;
            $mes = $dmes;
            $acum = $acum + $cont;
            $cont = 0;
        }
        $cont ++;
        $nombreEntrada = $entrada['Nombre'];
        if ($datos[0] != "servicios") {
            echo "<tr class='ui-widget-content'>
            <td>{$cont}</td>
            <td>{$nombreEntrada}</td>
            <td>".Cni::cambiaFormatoFecha($entrada["entrada"])."</td>
            <td>".Cni::cambiaFormatoFecha($entrada["salida"])."</td>
            </tr>";
        } else {
            echo "<tr class='ui-widget-content'>
            <td>{$cont}</td>
            <td>{$nombreEntrada}</td>
            <td>".Cni::cambiaFormatoFecha($entrada["fecha"])."</td>
            </tr>";
        }
    }
    if ($mes != 1) {
        echo "<tr class='ui-widget-content'>
         <td colspan='{$columnas}'><strong>Total {$cont}</strong></td>
         </tr>";
    }
    $total = $acum + $cont;
    echo "<tr class=''><th colspan='{$columnas}'>Total {$total}</th></tr>";
    echo "</table>";
    echo "<a href='#arriba' class='enlacedetallada'>Ir Arriba</a>";
}
if (isset( $_POST['servicio'] )) {
    $dato = explode( '#', $_POST['servicio'] );
    $html = "<table class='listaacumulada'>";
    if (! isset( $dato[3] )) {
        $html .= "<tr><td> No hay datos </td></tr>";
    } else {
        $detalles = $entradas->detallesServiciosExternos(
        	urldecode( $dato[1] ),
        	$dato[2],
        	$dato[3]
        );
        $html .= "
        <tr>
         <th class='acumulada'>Cliente</th>
         <th class='datosacumulados'>Fecha</th>
        </tr>";
        foreach ($detalles as $detalle) {
            $nombre = $detalle['Nombre'];
            $fecha = Cni::cambiaFormatoFecha( $detalle['fecha'] );
            $html .= "
            <tr class='celdadialog'>
             <td>".$nombre."</td>
             <td>".$fecha."</td>
            </tr>";
        }
    }
    $html .= "</table>";
    echo $html;
}
/**
 * @fixme no salen los datos de las categorias con acento 
 */
if (isset( $_POST['cliente'] )) {
    $dato = explode( '#', urldecode( $_POST['cliente'] ) );
    $html = "<table class='listaacumulada'>";
    $detalles = $entradas->detalleClienteMes( $dato[0], $dato[1], $dato[2],
    $dato[3] );
    if (count( $detalles ) > 0) {
        $html .= "
        <tr>
         <th class='acumulada'>Cliente</th>
         <th class='datosacumulados'>Entrada</th>
         <th>Salida</th>
        </tr>";
        foreach ($detalles as $detalle) {
            $nombre = $detalle['Nombre'];
            $fechaEntrada = Cni::cambiaFormatoFecha( $detalle['entrada'] );
            $fechaSalida = Cni::cambiaFormatoFecha( $detalle['salida'] );
            $html .= "
            <tr class='celdadialog'>
             <td>{$nombre}</td>
             <td>{$fechaEntrada}</td>
             <td>{$fechaSalida}</td>
            </tr>";
        }
    } else {
        $html .= "
        <tr>
         <td> No hay datos</td>
        </tr>";
    }
    $html .= "</table>";
    echo $html;
}
if (isset( $_POST['ocupacion'] )) {
    $anyoFinal = null;
    $dato = explode( '#', urldecode( $_POST['ocupacion'] ) );
    $class = "celdadialog";
    if ($dato[2] != 100) {
        if ($dato[2] > '11') {
            $mes = $dato[2] - 11;
            $anyo = $_POST['fin'];
        } else {
            $mes = $dato[2] + 1;
            $anyo = $_POST['inicial'];
        }
    } else {
        $mes = $dato[2];
        $anyo = $_POST['inicial'];
        $anyoFinal = $_POST['fin'];
        $class = "ui-widget-content";
    }
    $detalles = $entradas->detallesOcupacionHoras( $mes, $anyo, $dato[0],
    $dato[1], $anyoFinal );
    $i = 0;
    $mes = 0;
    $inicio = 0;
    $total = 0;
    $html = "<table class='listaacumulada'>";
    $html .= "
        <tr>
         <th class='datosacumulados'>&nbsp;</th>
         <th class='acumulada'>Cliente</th>
         <th class='acumulada'>Servicio</th>
         <th class='datosacumulados'>Fecha</th>
        </tr>";
    foreach ($detalles as $detalle) {
        $nombre = $detalle['Nombre'];
        $servicio = $detalle['Servicio'];
        $fecha = Cni::cambiaFormatoFecha( $detalle['fecha'] );
        if ($mes != Cni::verMes( $detalle["fecha"] )) {
            if ($inicio != 0) {
                $html .= "<tr class='ui-widget-content'>
                 <td colspan='4'>
                 <strong>Total {$i}</strong>
                 </td>
                 </tr>";
                $total += $i;
                $i = 0;
            }
            $mes = Cni::verMes( $detalle["fecha"] );
            $anyo = Cni::verAnyo( $detalle["fecha"] );
            $html .= "<tr class=''>
                 <th colspan='4'>".Cni::$meses[$mes]." {$anyo}</th>
                 </tr>";
            $inicio = 1;
        }
        ++ $i;
        $html .= "
            <tr class='{$class}'>
             <td>{$i}</td>
             <td>{$nombre}</td>
             <td>{$servicio}</td>
             <td>{$fecha}</td>
            </tr>";
    }
    $html .= "<tr class='ui-widget-content'>
                 <td colspan='4'>
                 <strong>Total {$i}</strong>
                 </td>
                 </tr>";
    $total += $i;
    $html .= "<tr class=''><th colspan='4'>Total {$total}</th></tr>";
    $html .= "</table>";
    $html .= "<a href='#arriba' class='enlacedetallada'>Ir Arriba</a>";
    echo $html;
}
 