<?php
require_once 'configuracion.php';
/**
 * Menu de la aplicacion
 * 
 * @return string $cadena
 */
function menu()
{
	global $imagen;
	$opciones = array(
		array(
			'nombre' => 'Avisos',
		 	'accion' => 'javascript:cambiaVisibilidad("avisos")',
		  	'imagen' => $imagen['avisos'] 
		),
		array(
			'nombre' => 'Buscar',
			'accion' => 'javascript:busqueda()',
			'imagen' => $imagen['buscar']
		),
		array(
			'nombre' => 'Clientes',
			'accion' => 'javascript:menu(1)',
			'imagen' => $imagen['clientes']
		),
		array(
			'nombre' => 'Servicios',
			'accion' => 'javascript:menu(2)',
			'imagen' => $imagen['servicios']
		),
		array(
			'nombre' => 'Empleados',
			'accion' => 'javascript:menu(3)',
			'imagen' => $imagen['empleados']
		),
		array(
			'nombre' => 'Proveedores',
			'accion' => 'javascript:menu(4)',
			'imagen' => $imagen['proveedores']
		),
		array(
			'nombre' => 'Listín',
			'accion' => 'javascript:menu(5)',
			'imagen' => $imagen['listin']
		),
		array(
			'nombre' => 'Estadisticas',
			'accion' => 'javascript:popUp("servicont/index.php")',
			'imagen' => $imagen['estadisticas']
		),
		array(
			'nombre' => 'Asignación',
			'accion' => 'javascript:popUp("rapido/index.php")',
			'imagen' => $imagen['asignacion']
		),
		array(
			'nombre' => 'Almacenaje',
			'accion' => 'javascript:popUp("almacen/index.php")',
			'imagen' => $imagen['almacenaje']
		),
		array(
			'nombre' => 'Agenda',
			'accion' => 'javascript:popUp("agenda/index.php")',
			'imagen' => $imagen['agenda']
		),
		array(
			'nombre' => 'Entradas',
			'accion' => 'javascript:popUp("entradas/index.php")',
			'imagen' => $imagen['entradas']
		),
		array(
			'nombre' => 'Gestión',
			'accion' => 'javascript:gestion()',
			'imagen' => $imagen['gestion']
		),
		array(
			'nombre' => 'Salir',
			'accion' => 'inc/logout.php',
			'imagen' => $imagen['cerrar']
		),
	
	);
	$menu = "
	<table>
	<thead><tr> ";
	foreach( $opciones as $opcion ) {
		$menu .= "<th>
		<a href='" . $opcion['accion'] . "' alt='". $opcion['nombre'] . "'>
		<img src='". $opcion['imagen'] . "' width='32px' 
			alt='" . $opcion['nombre'] . "' />
			<br/>" . $opcion['nombre'] . "</a>
		</th>"; 
	}
	$menu .= "</tr></thead>
	</table>
	";
	return $menu;	
}
/**
 * Muestra los avisos
 * 
 * @todo Establecer el estilo de alineacion de tabla superior y los estilos today, tomorrow
 * @return string $html
 */
function avisos() {
    global $imagen;
	$cumples = array();
	$renovaciones = array();
	$date = new DateTime();
	$today = $date->format('d-m');
	$date->add(new DateInterval('P1D') );
	$tomorrow =  $date->format('d-m');
	$sql = "Select date_format( curdate(), '%j'), 
	date_format( date_add(curdate(), interval 60 day), '%j' )";
	$resultado = consultaUnica($sql);
	$diaActual = $resultado[0];
	$diaFinal = $resultado[1];
	$html = "<table>
	<thead>
		<tr>
			<th colspan='2'>
			    <img src='".$imagen['avisos']."' width='32px' alt='Avisos' />
			     Avisos
			</th>
		</tr>
		<tr>
			<th>Cumpleaños</th>
			<th>Contratos</th>
		</tr>
	</thead>";
	// Personas de la central
	$sql = "SELECT  
		clientes.Nombre as empresa, 
		pcentral.persona_central as empleado, 	
		date_format( pcentral.cumple, '%d-%m' ) AS cumplea,
		date_format( pcentral.cumple, '%j' ) as dia
		FROM clientes INNER JOIN pcentral ON clientes.Id = pcentral.idemp 
		WHERE pcentral.cumple not like '0000-00-00'
		AND clientes.Estado_de_cliente != 0  
    	AND ( date_format( pcentral.cumple,'%j' ) >= date_format( curdate(), '%j' )
    	OR date_format( pcentral.cumple,'%j' ) <= date_format( date_add(curdate(), interval 60 day), '%j' ) )
    	order by dia";
	$cumples = array_merge($cumples, consultaGenerica($sql));
	// Cumpleaños Personal de la empresa
	$sql = "SELECT
        clientes.Nombre as empresa,
        CONCAT( pempresa.nombre, ' ', pempresa.apellidos) as empleado,
    	date_format( pempresa.cumple, '%d-%m' ) AS cumplea, 
    	date_format( pempresa.cumple, '%j' ) as dia
    	FROM clientes INNER JOIN pempresa ON clientes.Id = pempresa.idemp 
    	WHERE pempresa.cumple not like '0000-00-00' 
    	AND clientes.Estado_de_cliente != 0 
    	AND ( date_format( pempresa.cumple,'%j' ) >= date_format( curdate(), '%j' )
    	OR date_format( pempresa.cumple,'%j' ) <= date_format( date_add(curdate(), interval 60 day), '%j' ) )
    	order by dia";
	$cumples = array_merge($cumples, consultaGenerica($sql));
    // Cumpleaños de Empleados	
	$sql = "SELECT 
		'Centro' as empresa,
		Concat( Nombre, ' ', Apell1, ' ', Apell2 ) as empleado, 
    	date_format( FechNac,'%d-%m' ) as cumplea,
    	date_format( FechNac, '%j' ) as dia
    	FROM empleados 
    	WHERE FechNac not like '0000-00-00'
    	AND ( date_format( FechNac, '%j' ) >= date_format( curdate(), '%j' )
    	OR date_format( FechNac,'%j' ) <= date_format( date_add(curdate(), interval 60 day), '%j' ) )
    	order by dia";
    $cumples = array_merge($cumples, consultaGenerica($sql));
	$cumples = sortByKey( $cumples, 'dia', $diaActual, $diaFinal );
	// Finalizan contrato en los siguientes 60 dias
	$sql = "SELECT 
		clientes.Nombre as empresa,
		date_format( facturacion.renovacion, '%d-%m') as fecha,
		date_format( facturacion.renovacion, '%j' ) as dia 
		FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
		WHERE (CURDATE() <= renovacion) and 
		(DATE_ADD(CURDATE(),INTERVAL 60 DAY)) >= renovacion 
		and clientes.Estado_de_cliente != 0 
		order by Month(renovacion) asc, DAY(renovacion) asc";
	$renovaciones = array_merge( $renovaciones, consultaGenerica( $sql ) );
	$renovaciones = sortByKey( $renovaciones, 'dia', $diaActual, $diaFinal) ;
	// Mostramos los datos
    $html .= "<tbody><tr><td><table><tbody>";
    foreach( $cumples as $cumple ) {
    	$clase = ( $cumple['cumplea'] == $today ) ? 'today':( ( $cumple['cumplea'] == $tomorrow ) ? 'tomorrow' : '');
    	
    	$html .= "<tr >
    	<td class='". $clase ."'>". $cumple['cumplea'] . " 
    	" . ucwords( strtolower( $cumple['empleado'] ) )  . " de 
    	" . $cumple['empresa'] . "</td></tr>";
    }
    $html .= "</tbody></table></td><td><table><tbody>";
    foreach( $renovaciones as $renovacion ) {
    $clase = ( $renovacion['fecha'] == $today ) ? 'today': ( ( $renovacion['fecha'] == $tomorrow ) ? 'tomorrow' : '');
    	$html .= "<tr>
    	<td class='". $clase ."'>" . $renovacion['fecha'] . " 
    	" . ucwords( strtolower( $renovacion['empresa'] ) ) . "</td>
    	</tr>";
    }
    $html .= "</tbody></table></td></tr>";
    $html .= "</tbody></table>";
	return $html;
}
