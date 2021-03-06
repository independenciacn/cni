<?php
class Connection
{
    /**
     * @var null|Pdo
     */
    private $conexion = null;
    private $host = "localhost";
    private $username = "cni";
    private $password = "inc";
    private $dbname = "centro";

    /**
     * Constructor de clase
     */
    public function __construct()
    {
        if (getenv('MYSQL_HOSTNAME')) {
            $this->host = getenv('MYSQL_HOSTNAME');
        }
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
            var_dump($e->getTrace());
        }
    }

    /**
     * Ejecuta la consulta
     * @param $sql
     * @param null $params
     * @param int $mode
     * @return array
     */
    public function consulta($sql, $params = null, $mode = PDO::FETCH_BOTH)
    {
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll($mode);
    }
}
