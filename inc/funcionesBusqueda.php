<?php
/**
 * FuncionesBusqueda File Doc Comment
 *
 * Funciones que realizan la busqueda en la base de datos y devuelven los
 * resultados
 *
 * PHP Version 5.2.6
 *
 * @category FuncionesBusqueda
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nd/3.0/
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once 'configuracion.php';
if ( isset( $_POST['texto'] ) && isset( $_SESSION['usuario'] ) ) {
    sanitize( $_POST );
    if ( strlen( $_POST['texto']) >= 2  ) {
        echo buscar( $_POST ); 
    } else {
        echo "<div class='error'>Debe usar como minimo 2 caracteres</div>";
    }  
} else {
   notFound();
}
/**
 * Function de busqueda avanzada en todas las tablas
 * 
 * @param array $vars
 */
function buscar( $vars ) {
    
    $html = "<table>";
    // Buscamos en clientes
    $sql = "SELECT id,
    CONCAT( Nombre, ' - ', Contacto ) as texto,
    CONCAT(
        REPLACE(Tfno1, ' ', ''), ' - ', 
        REPLACE(Tfno2, ' ', ''), ' - ', 
        REPLACE(Tfno3, ' ', ''), ' - '
    ) as telefono
    FROM clientes
    WHERE ( 
        Nombre like '%" . $vars[ 'texto' ] ."%' 
        OR Contacto like '%". $vars[ 'texto' ] ."%'
        OR REPLACE(Tfno1, ' ', '') like '%". $vars[ 'texto' ] ."%'
        OR REPLACE(Tfno2, ' ', '') like '%". $vars[ 'texto' ] ."%'
        OR REPLACE(Tfno3, ' ', '') like '%". $vars[ 'texto' ] ."%'
    )
    AND Estado_de_cliente = '-1' order by Nombre";
    $html .= muestraBusqueda( consultaGenerica( $sql ),'Clientes' );
    // Buscamos en Personal de la Empresa
    $sql = "SELECT c.id as id,
    CONCAT( c.Nombre, ' ', p.nombre, ' ', p.apellidos) as texto,
    REPLACE( p.telefono, ' ', '' ) as telefono
    FROM clientes as c INNER JOIN pempresa as p ON c.id = p.idemp
    WHERE (
        c.nombre like '%" . $vars[ 'texto' ] ."%' 
        OR p.nombre like '%". $vars[ 'texto' ] ."%'
        OR p.apellidos like '%". $vars[ 'texto' ] ."%'
        OR CONCAT( p.nombre, ' ', p.apellidos ) like '%". $vars[ 'texto' ] ."%'
        OR REPLACE( p.telefono, ' ', '' ) like '%". $vars[ 'texto' ] ."%'
    ) AND c.Estado_de_cliente = -1 order by c.Nombre";
    $html .= muestraBusqueda( consultaGenerica( $sql ),'Personal Empresa' );
    // Buscamos en Personal de la central
    $sql = "SELECT c.id as id,
    CONCAT( c.Nombre, ' ', p.persona_central) as texto,
    REPLACE( p.telefono, ' ', '' ) as telefono
    FROM clientes as c INNER JOIN pcentral as p ON c.id = p.idemp
    WHERE (
    c.nombre like '%" . $vars[ 'texto' ] ."%'
    OR p.persona_central like '%". $vars[ 'texto' ] ."%'
    OR REPLACE( p.telefono, ' ', '' ) like '%". $vars[ 'texto' ] ."%'
    ) AND c.Estado_de_cliente = -1 order by c.Nombre";
    $html .= muestraBusqueda( consultaGenerica( $sql ),'Personal Central' );
    // Buscamos en proveedores
    $sql = "SELECT id, 
    CONCAT( Nombre, ' ', nocor, ' ', contacto ) as texto,
    CONCAT(
        REPLACE(Tfno1, ' ', ''), ' - ', 
        REPLACE(Tfno2, ' ', ''), ' - ', 
        REPLACE(Tfno3, ' ', ''), ' - '
    ) as telefono
    FROM proveedores 
    WHERE ( 
        Nombre LIKE '%" . $vars['texto'] ."%'
        OR nocor LIKE '%" . $vars['texto'] ."%'
        OR contacto LIKE '%" . $vars['texto'] ."%'
        OR REPLACE(Tfno1, ' ', '') like '%". $vars[ 'texto' ] ."%'
        OR REPLACE(Tfno2, ' ', '') like '%". $vars[ 'texto' ] ."%'
        OR REPLACE(Tfno3, ' ', '') like '%". $vars[ 'texto' ] ."%'
	    ) order by Nombre
    ";
    $html .= muestraBusqueda( consultaGenerica( $sql ), 'Proveedores' );
    // Buscamos en personal de Proveedores
    $sql = "SELECT c.id as id,
    CONCAT( c.Nombre, ' ', p.nombre, ' ', p.apellidos) as texto,
    REPLACE( p.telefono, ' ', '' ) as telefono
    FROM proveedores as c INNER JOIN pproveedores as p ON c.id = p.idemp
    WHERE (
    c.nombre like '%" . $vars[ 'texto' ] ."%'
    OR p.nombre like '%". $vars[ 'texto' ] ."%'
    OR p.apellidos like '%". $vars[ 'texto' ] ."%'
    OR CONCAT( p.nombre, ' ', p.apellidos ) like '%". $vars[ 'texto' ] ."%'
    OR REPLACE( p.telefono, ' ', '' ) like '%". $vars[ 'texto' ] ."%'
    ) order by c.Nombre";
    $html .= muestraBusqueda( consultaGenerica( $sql ),'Personal Proveedores' );
    // Cerramos la tabla de resultados
    $html .="</table>";
    // Marcamos las ocurrencias de la busqueda 
    $html = destacados( $vars['texto'], $html );
    return $html;
}
/**
 * Funcion que marca los destacados
 * 
 * @param string $needle
 * @param string $string
 * @return string
 */
function destacados( $needle, $string )
{
    return preg_replace('#' . $needle . '#i', '<span class="found">$0</span>', $string );
}
/**
 * Funcion que presenta los resultados
 * 
 * @param array $resultados
 * @param string $titulo
 * @return string $html
 */
function muestraBusqueda( $resultados, $titulo )
{
    $html = "
    <thead>
    <tr><th>Resultados ". $titulo. " - " . count( $resultados ) ."</th></tr>
    </thead>
    <tbody>";
    foreach( $resultados as $resultado ) {
        $html .= "<tr><td>" . $resultado['id'] . " - " . $resultado['texto'] . "
        - Telefonos: " . $resultado['telefono'] . "</td></tr>";
    }
    $html .="</tbody>";
    return $html;
}