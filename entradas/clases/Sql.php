<?php
/**
 * Sql File Doc Comment
 * 
 * Fichero de clase Sql
 * 
 * PHP Version 5.2.6
 * 
 * @category Sql
 * @package  cni/entradas/clases
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
/**
 * Sql Class Doc Comment
 * 
 * @category Class
 * @package  cni/entradas/classes
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @version  Release: 1.0
 * @link     https://github.com/independenciacn/cni
 *
 */
class Sql
{
    private $_conexion = null;
    private $_result = null;
    private $_dbname = "centro";
    /**
     * Constructor de Clase
     */
    function __construct ()
    {
        if ( is_null( $this->_conexion ) ) {
            ini_set( 'mysql.default_host', 'localhost' );
            ini_set( 'mysql.default_user', 'cni' );
            ini_set( 'mysql.default_password', 'inc' );
            $this->_conexion = mysql_pconnect();
            if (! $this->_conexion ) {
                die("Database connection failed: " . mysql_error());
            }
            if ( !mysql_set_charset( 'utf8', $this->_conexion ) ) {
                die( "Set charshet Failed: " . mysql_error() );
            }    
            if (! mysql_select_db( $this->_dbname, $this->_conexion ) ) {
                die("Database selection failed: " . mysql_error());
            } 
       }   
    }    
    
    /**
     * Consulta a la base de datos
     * 
     * @param string $sql
     */
    function consulta ( $sql )
    {
        $this->_result = mysql_query( $sql, $this->_conexion );
    }
    /**
     * Devuelve los datos de la consulta
     * 
     * @return array;
     */
    function datos ()
    {
        $rows = array();
        while (($row = mysql_fetch_array( $this->_result, MYSQL_ASSOC )) == true) {
            $rows[] = $row;
        }
        return $rows;
    }
    /**
     * Devuelve el numero total de datos de la consulta
     * 
     * @return integer
     */
    function totalDatos ()
    {
        return mysql_affected_rows();
    }
    /**
     * Destructor, cierra la conexion
     */
    function __destruct()
    {
        mysql_close( $this->_conexion );
    }
}