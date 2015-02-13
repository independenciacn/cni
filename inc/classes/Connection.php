<?php
class Connection
{
    /**
     * @var null|Pdo
     */
    private $conexion = null;
    private $host = "127.0.0.1:3306";
    private $username = "cni";
    private $password = "inc";
    private $dbname = "centro";

    /**
     *
     */
    public function __construct()
    {
        $dsn = 'mysql:dbname='.$this->dbname.';host='.$this->host;
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");
        try {
            $this->conexion = new PDO(
                $dsn,
                $this->username,
                $this->password,
                $options
            );
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    /**
     * Ejecuta la consulta
     * @param $sql
     * @param null $params
     * @return array
     */
    public function consulta($sql, $params = null)
    {
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
