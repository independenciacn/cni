<?php
/**
 * graphservicios.php File Doc Comment
 *
 * Grafica de los servicios
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
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_pie.php';
require_once 'jpgraph/jpgraph_pie3d.php';
require_once 'clases/EntradasSalidas.php';
$entradas = new EntradasSalidas();
$datos = explode( "#", urldecode( $_GET['datos'] ) );
$entradas->anyoInicial = $datos[1];
$entradas->anyoFinal = $datos[2];
$entradas->setAnyos();
/*Grafico tarta*/
$graph = new PieGraph( 900, 800 );
$graph->SetShadow();
$graph->SetScale( 'textint' );
$graph->title->Set(
	"Servicios Consumidos por clientes externos de {$datos[1]} a {$datos[2]}"
);
$graph->title->SetFont( FF_DEFAULT, FS_BOLD, 10 );
foreach ($entradas->serviciosExternos( true ) as $servicios) {
    $totales[] = $servicios['Total'];
    $servicio[] = ucwords( strtolower( $servicios['Servicio'] ) );
}
$piePlot = new PiePlot3D( $totales );
$piePlot->SetLabelType( PIE_VALUE_ABS );
$piePlot->value->SetFormat( '%d' );
$piePlot->value->Show();
$piePlot->SetSize( 300 );
$piePlot->SetCenter( 0.5, 0.30 );
$piePlot->SetLegends( $servicio );
$graph->Add( $piePlot );
$graph->legend->SetColumns( 3 );
$graph->SetMargin( 10, 10, 10, 10 );
$graph->legend->Pos( 0.05, 0.60, 'left', 'top' );
$graph->Stroke();
 