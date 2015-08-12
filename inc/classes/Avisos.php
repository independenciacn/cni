<?php
require_once 'Connection.php';


class Avisos
{

    /**
     * @var Connection|null
     */
    private $conexion = null;

    /**
     *
     */
    public function __construct()
    {
        $this->conexion = new Connection();
    }

    /**
     * Devuelve los cumpleaños de los empleados de la central
     * @param $cuando
     * @return array
     */
    public function cumplesCentral($cuando)
    {
        $between = $this->filterBetween($cuando);
        $sql = "Select clientes.id, clientes.Nombre as empresa,
        pcentral.persona_central as empleado,
        @anyo:= IF (MONTH(CURDATE()) = 12,
                    IF (MONTH(pcentral.cumple) = 1,
                        YEAR(date_add(curdate(), INTERVAL 1 YEAR)),
                        YEAR(curdate())
                    ),
                    YEAR(curdate())
        ),
        @month:= MONTH(pcentral.cumple),
        @day:= DAY(pcentral.cumple),
        STR_TO_DATE(CONCAT(@anyo, '-', @month, '-', @day), '%Y-%m-%d') as fecha
        FROM clientes INNER JOIN pcentral ON clientes.Id = pcentral.idemp
        WHERE clientes.Estado_de_cliente != 0
        HAVING fecha $between
        ORDER BY fecha";
        return $this->conexion->consulta($sql);
    }

    /**
     * Devuelve los cumpleaños de los empleados de la empresa
     * @param string $cuando
     * @return array
     */
    public function cumplesEmpresa($cuando = 'hoy')
    {
        $between = $this->filterBetween($cuando);
        $sql = "Select clientes.id, clientes.Nombre as empresa,
        CONCAT(pempresa.nombre, ' ', pempresa.apellidos) as empleado,
        @anyo:= IF (MONTH(CURDATE()) = 12,
                    IF (MONTH(pempresa.cumple) = 1,
                        YEAR(date_add(curdate(), INTERVAL 1 YEAR)),
                        YEAR(curdate())
                    ),
                    YEAR(curdate())
        ),
        @month:= MONTH(pempresa.cumple),
        @day:= DAY(pempresa.cumple),
        STR_TO_DATE(CONCAT(@anyo, '-', @month, '-', @day), '%Y-%m-%d') as fecha
        FROM clientes INNER JOIN pempresa ON clientes.Id = pempresa.idemp
        WHERE clientes.Estado_de_cliente != 0
        HAVING fecha $between
        ORDER BY fecha";
        return $this->conexion->consulta($sql);
    }

    /**
     * Devuelve los cumpleaños de los empleados
     * @param string $cuando
     * @return array
     */
    public function cumplesEmpleados($cuando = 'hoy')
    {
        $between = $this->filterBetween($cuando);
        $sql = "SELECT Id as id, 'Independenciacn' as empresa,
        CONCAT(Nombre, ' ', Apell1, ' ', Apell2) as empleado,
        @anyo:= IF (MONTH(CURDATE()) = 12,
                    IF (MONTH(FechNac) = 1,
                        YEAR(date_add(curdate(), INTERVAL 1 YEAR)),
                        YEAR(curdate())
                    ),
                    YEAR(curdate())
        ),
        @month:= MONTH(FechNac),
        @day:= DAY(FechNac),
        STR_TO_DATE(CONCAT(@anyo, '-', @month, '-', @day), '%Y-%m-%d') as fecha
        FROM empleados
        HAVING fecha $between
        ORDER BY fecha";
        return $this->conexion->consulta($sql);
    }

    /**
     * @param $cuando
     * @return string
     */
    private function filterBetween($cuando)
    {
        if ($cuando == 'hoy') {
            $between = " = CURDATE()";
            return $between;
        } elseif ($cuando == 'mañana') {
            $between = " = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
            return $between;
        } else {
            $between = "BETWEEN
                DATE_ADD(CURDATE(), INTERVAL 2 DAY) AND
                DATE_ADD(CURDATE(), INTERVAL 1 MONTH)";
            return $between;
        }
    }
}
