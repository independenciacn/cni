<?php
/**
 * CniDB File Doc Comment
 *
 * Conexion a la base de datos
 *
 * Clase estatica standalone de conexion a la base de datos
 *
 * PHP Version 5.2.6
 *
 * @author  Ruben Lacasa <ruben@ensenalia.com>
 * @package cniEstable/inc
 * @license Creative Commons Atribución-NoComercial-SinDerivadas 3.0 Unported
 * @version 2.0e Estable
 * @link    https://github.com/sbarrat/cniEstable
 */
/**
 * CniDB Class Doc Comment
 *
 * Funciones estaticas de trabajo con la base de datos
 *
 */
final class CniDB
{
    private static $_handle = null;
    private static $_dsn = "mysql:dbname=centro;host=localhost;port=3306";
    private static $_user = 'cni';
    private static $_password = 'inc';
    private static $_options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        );
    /**
     * Deny Construct
     */
    private function __construct()
    {
    }
    /**
     * Deny Clone
     */
    private function __clone()
    {
    }
    /**
     * Funcion de conexion a la base de datos
     */
    public static function connect()
    {

        if ( is_null( self::$_handle ) ) {
            try {
                self::$_handle = new PDO(
                        self::$_dsn, 
                        self::$_user, 
                        self::$_password, 
                        self::$_options
                );
            } catch ( PDOException $error ) {
                var_dump ($error->getMessage());
            }
        }
        return self::$_handle;
    }
}