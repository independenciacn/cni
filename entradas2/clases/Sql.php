<?php
class Sql
{
    private $conexion = null;
    private $result = false;
    private $host = "127.0.0.1:3306";
    private $username = "cni";
    private $password = "inc";
    private $dbname = "centro";

    public function __construct()
    {
        $this->conexion =
        mysql_connect($this->host, $this->username, $this->password);
        mysql_set_charset('utf8', $this->conexion);
        if (! $this->conexion)
            die("Database connection failed: " . mysql_error());
        if (! mysql_select_db($this->dbname, $this->conexion))
            die("Database selection failed: " . mysql_error());
    }
    
    function consulta ($sql)
    {
    	$this->result = mysql_query($sql, $this->conexion);
    }
    
    function datos ()
    {
        $rows = array();
        if ( $this->result ) {
            while (($row = mysql_fetch_array($this->result, MYSQL_ASSOC)) == TRUE) {
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
        mysql_close($this->conexion);
    }
}