<?php
class Sql
{
    private $_conexion = null;
    private $_result = false;
    private $_host = "127.0.0.1:3306";
    private $_username = "cni";
    private $_password = "inc";
    private $_dbname = "centro";
    function __construct ()
    {
        $this->_conexion = 
        mysql_connect($this->_host, $this->_username, $this->_password);
        mysql_set_charset('utf8', $this->_conexion);
        if (! $this->_conexion)
            die("Database connection failed: " . mysql_error());
        if (! mysql_select_db($this->_dbname, $this->_conexion))
            die("Database selection failed: " . mysql_error());
    }
    
    function consulta ($sql)
    {
    	$this->_result = mysql_query($sql, $this->_conexion);
    }
    
    function datos ()
    {
        $rows = array();
        if ( $this->_result ) {
            while (($row = mysql_fetch_array($this->_result, MYSQL_ASSOC)) == TRUE) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    function totalDatos ()
    {
        return mysql_affected_rows();
    }
    
    function close ()
    {
        mysql_close($this->_conexion);
    }
}