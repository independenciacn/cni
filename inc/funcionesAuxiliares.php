<?php
/**
 * Funciones Auxiliares
 */
/**
 * Se le puede pasar como parametro un array o una string y la sanea
 * 
 * @param mixed $vars
 * 
 */
function sanitize( &$vars ) {
	global $con;
	if ( is_array( $vars ) ) {
		foreach ( $vars as &$var ) {
			mysql_real_escape_string( $var, $con );
		}
	} elseif( is_string( $vars ) ) {
		mysql_real_escape_string( $vars, $con );
	}
}
/**
 * Convierte la cadena dada a modo capitalizado
 * 
 * @param mixed $vars
 */
function capitalize( &$vars ) {
	if ( is_array( $vars ) ) {
		foreach ( $vars as &$var ) {
			$var = ucwords( strtolower( $var ) );
		}
	} elseif( is_string( $vars ) ) {
		$vars = ucwords( strtolower( $vars ) );
	}
}
/**
 * Ejecuta la consulta y devuelve verdadero o falso, para inserts y update
 * 
 * @param string $sql
 * @return boolean
 */
function ejecutaConsulta( $sql ) {
	global $con;
	if ( mysql_query( $sql, $con ) ) {
		return true;
	} else {
		return false;
	}
}
/**
 * Nueva funcion que ejecuta la consulta y devuelve todos los resultados
 * 
 * @param string $sql
 * @return array $results
 */
function consultaGenerica( $sql , $type = MYSQL_BOTH ) {
	global $con;
	$results = array();
	$result = mysql_query( $sql, $con );
	if ( mysql_numrows( $result ) > 0 ) {
		while ( true == ($row = mysql_fetch_array( $result, $type ) ) ) {
			$results[] = $row;
		}
	}
	return $results;
}
/**
 * Cuando es una consulta de un solo resultado devolvemos solo ese
 * 
 * @param string $sql
 * @return array|boolean 
 */
function consultaUnica( $sql, $type = MYSQL_BOTH ) {
	global $con;
	$result = mysql_query( $sql, $con );
	if ( mysql_numrows( $result ) > 0 ) {
		return mysql_fetch_array( $result, $type );
	} else {
		return false;
	}
}
/**
 * Devuelve el numero de Celdas de la consulta
 * 
 * @param string $sql
 * @return number
 */
function totalCeldas( $sql ) {
    global $con;
    $result = mysql_query( $sql, $con );
    return mysql_numrows($result);
}
/**
 * Cambia la fecha en un sentido y en otro - OBSOLETA ???
 * 
 * @param string $fecha
 */
function cambiaf($stamp, $larga = null)
{
	global $meses;
	$fdia = explode("-",$stamp);
	$fecha = "--";
	if ( count( $fdia ) == 3 ) {
		if (!is_null( $larga ) ){
			$fdia[1] = $meses[$fdia[1]];
		}
		$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	}
	return $fecha;
}
/**
 * Establece la clase de la linea del listado - OBSOLETA
 * 
 * @param integer $k
 * @return string $clase
 * 
 */
function clase( $k )
{
	$clase = ( $k % 2 == 0 ) ? "par" : "impar";
	return $clase;
}
/**
 * Devuelve el precio formateado segun el entorno definido
 * 
 * @param float $precio
 * @return float
 */
function precioFormateado( $precio ) {
	return money_format( '%n', $precio );
}
/**
 * Devuelve el total del iva
 * 
 * @param float $iva
 * @param integer $iva
 * @return float $total
 */
function iva($importe,$iva)
{
	$total = round($importe + ($importe * $iva) / 100, 2 );
	return $total;
}
/**
 * Funcion que ordena las fechas de avisos
 * 
 * @param array $array
 * @param string $key
 * @param string $diaActual
 * @param string $diaFinal
 * @return array $newArray
 */
function sortByKey( $array, $key, $diaActual, $diaFinal ) {
	
	for( $j = $diaActual; $j<=366; $j++) {
		for ( $i = 0; $i < count( $array ); $i++ ) {
			if ( $array[$i][$key] == $j ) {
				$superior[] = $array[$i];
			}
		}	
	}
	for ( $j = 0; $j<=$diaFinal; $j++ ) {
		for ( $i = 0; $i < count( $array ); $i++ ) {
			if ( $array[$i][$key] == $j ) {
				$inferior[] = $array[$i];
			}
		}
	}
	$newArray = array_merge( $superior, $inferior );
	return $newArray;
}
/**
 * Redirige a una pagina no existente y sale que la pagina no se ha encontrado
 */
function notFound() {
    header("Location:notfound.html");
    exit(0);
}
/**
 * Listado de empleados
 */
function listadoEmpleados() {
	$sql = "SELECT Id,
	CONCAT(Nombre, ' ', Apell1, ' ', Apell2) as empleada 
	from empleados";
	$resultados = consultaGenerica( $sql );
	foreach( $resultados as &$resultado ) {
		capitalize($resultado);
	}
	return $resultados;
}
    