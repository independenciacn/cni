<?php
/**
 * graph.php File Doc Comment
 *
 * Grafica de movimientos de clientes
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
require_once 'jpgraph/jpgraph_line.php';
require_once 'jpgraph/jpgraph_bar.php';
require_once 'clases/EntradasSalidas.php';
$entradas = new EntradasSalidas();
$datos = explode( "#", urldecode( $_GET['datos'] ) );
$entradas->anyoInicial = $datos[2];
$entradas->anyoFinal = $datos[3];
$entradas->setAnyos();
$diferencia = $entradas->diferencia();
$meses = $entradas->mesesRango();
$arrayEntradas = $entradas->arrayEntradasClientes( $datos[1] );
$arraySalidas = $entradas->arraySalidasClientes( $datos[1] );
/*Construimos el grafico*/
$graph = new Graph( 930, 300 );
$graph->SetScale( 'textint' );
$graph->img->SetMargin( 80, 30, 30, 40 );
$graph->SetShadow();
$graph->SetFrame( false );
$graph->title->Set(
	"Cuadro de Entradas y Salidas {$datos[1]} de {$datos[2]} a {$datos[3]}"
);
$graph->title->SetFont( FF_DEFAULT, FS_BOLD, 10 );
$graph->xaxis->title->Set( '(Meses)' );
$graph->xaxis->SetTickLabels( $meses );
$graph->xaxis->SetFont( FF_DEFAULT, FS_NORMAL, 6 );
$graph->yaxis->title->Set( '(Totales)' );
$barplotA = new BarPlot( $arrayEntradas );
$barplotA->SetLegend( "Entradas" );
$barplotB = new BarPlot( $arraySalidas );
$barplotB->SetLegend( "Salidas" );
$gbplot = new GroupBarPlot( array($barplotA, $barplotB) );
$graph->Add( $gbplot );
$graph->Stroke();
 