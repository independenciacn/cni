<?php
    class conexion
	{
		private static $dbname="nuense";
		private static $host="localhost";
		private static $user="ense06";
		private static $password="60esne";
		var $con;
		var $sql;
		var $query;
	
	
	function conexion()
	{
		$this->con=mysql_connect($this->host,$this->user,$this->password) or die (mysql_error());	
	}
	
	public function sql($consulta)
	{
		$this->sql = mysql_db_query($this->dbname,$consulta,$this->con);
	}
	
	function consulta()
	{
		return $this->sql;
	}
	}
?>
