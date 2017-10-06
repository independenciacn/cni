<?php
/**
 * Clase Connection conecta a la base de datos
 */
class Connection
{
    /**
     * @var null|Pdo
     */
    protected $conexion = null;
    private $host = "localhost";
    private $username = "cni";
    private $password = "inc";
    private $dbname = "centro";

    /**
     *
     */
    public function __construct()
    {
        if (getenv('MYSQL_HOSTNAME')) {
            $this->host = getenv('MYSQL_HOSTNAME');
        }
        $dsn = 'mysql:dbname='.$this->dbname.';host='.$this->host.';port=3306';
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
     * @param array $params
     * @param int $fetchMode
     * @return array
     */
    public function consulta($sql, $params = array(), $fetchMode = PDO::FETCH_ASSOC)
    {
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll($fetchMode);
    }
}
