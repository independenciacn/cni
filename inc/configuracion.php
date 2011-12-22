<?php
/**
 * Configuracion File Doc Comment
 * 
 * Parametros de configuracion de la aplicacion
 * 
 * PHP Version 5.2.10
 * 
 * @category Configuracion
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
/**
 * Parametros de configuracion de la aplicacion MODIFICABLES SEGUN ENTORNO
 */
$conf['zonaHoraria'] = 'Europe/Madrid'; // Zona horaria por defecto
$ini = parse_ini_file("sample.ini");
$conf['sistema'] = $ini['sistema']; // Sistema Operativo Usado debian | otro | windows
$conf['estado'] = $ini['estado'];
$conf['system'] = $ini['system'];
$conf['version'] = $ini['version'];
$conf['root'] = $ini['root'];
//$conf['estado'] = 'DEVEL'; // Estado en el que esta la aplicacion DEVEL | PROD
//$conf['system'] = 'cloud'; // Donde estamos desarrollando local o cloud
//$conf['version'] = '2.1'; // Version de la aplicacion
//$conf['root'] = "http://sbarrat.my.phpcloud.com/cni";// Direccion Raiz de la aplicacion
// Cloud Configuration
if ( $conf['system'] == 'cloud' ) {
    $conf['dbHost'] = 'sbarrat-db.my.phpcloud.com'; // Servidor base de datos
    $conf['dbUser'] = 'sbarrat'; // Usuario de la base de datos
    $conf['dbPass'] = '769167226'; // Contraseña de la base de datos
    $conf['dbName'] = 'sbarrat'; // Nombre de la base de datos
} else {
// Local Configuration
    $conf['dbHost'] = 'localhost'; // Servidor base de datos
    $conf['dbUser'] = 'cni'; // Usuario de la base de datos
    $conf['dbPass'] = 'inc'; // Contraseña de la base de datos
    $conf['dbName'] = 'centro'; // Nombre de la base de datos
}
// Fin de las configuraciones de conexion a la base de datos

$conf['dbCharset'] = 'utf8'; // Charset de la base de datos
$conf['rutaBackup'] = '../copias/'; // carpeta copias dentro de la aplicacion 
/**
 * LAS MODIFICACIONES SE TERMINAN AQUI A PARTIR DE AQUI NO TOCAR
 */
/**
 * Iniciamos o regeneramos la session de la aplicacion
 */
$ssid = session_id();
if ( $ssid == null ) {
	session_start();
} else {
	session_regenerate_id();
}
/**
 * Establecemos el control de errores de la aplicacion
 */
if ( array_key_exists( 'estado', $conf ) ) {
	if ( $conf['estado'] == 'DEVEL' ) {
		error_reporting( E_ALL | E_DEPRECATED | E_STRICT );
	} else {
		error_reporting( 0 );
	}
}
/**
 * Establecemos las locales del sistema
 */
if ( array_key_exists( 'sistema', $conf ) ) {
	if ( $conf['sistema'] == 'debian' ) {
		setlocale( LC_ALL, 'es_ES.UTF-8' );
	} else {
		setlocale( LC_ALL, 'es_ES' );
	}
}
/**
 * Establecemos la configuracion Horaria
 */
if ( array_key_exists( 'zonaHoraria', $conf ) ) {
	ini_set( 'date.timezone', $conf['zonaHoraria'] );
}
/**
 * Establecemos las opciones de la base de datos
 */
if ( array_key_exists( 'dbHost', $conf ) ) {
	ini_set( 'mysql.default_host', $conf['dbHost'] );
}
if ( array_key_exists( 'dbUser', $conf ) ) {
	ini_set( 'mysql.default_user', $conf['dbUser'] );
}
if ( array_key_exists( 'dbPass', $conf ) ) {
	ini_set( 'mysql.default_password', $conf['dbPass'] );
}
/**
 * Conectamos a la base de datos
 */
try {
	$con = mysql_connect();
	if ( array_key_exists( 'dbName', $conf ) ) {
		mysql_select_db( $conf['dbName'], $con );
	}
	if ( array_key_exists( 'dbCharset', $conf ) ) {
		mysql_set_charset( $conf['dbCharset'], $con );
	}
} catch ( Exception $e ) {
	echo "No se ha podido establecer la conexion con el servidor
	de base de datos, por favor consulte con el desarrollador<br/>" .
	$e->getMessage();
}
/**
 * Establecemos los comandos y rutas de aplicaciones de la base de datos
 */
if ( array_key_exists( 'sistema', $conf ) ) {
	if ( $conf['sistema'] == 'windows' ) {
		$conf['binariosBD'] = 'C:\AppServ\MySQL\bin\\'; // Ruta de los binarios en windows
		$conf['mysqldumpExec'] = 'mysqldump.exe';
		$conf['mysqlExec'] = 'mysql.exe';
		$conf['borraExec'] = 'del';
	} else {
		$conf['binariosBD'] = '/usr/bin/';
		$conf['mysqldumpExec'] = 'mysqldump';
		$conf['mysqlExec'] = 'mysql';
		$conf['borraExec'] = 'rm';
	}
}
require_once 'variablesAuxiliares.php';
require_once 'funcionesAuxiliares.php';