<?php
require_once 'Cni.php';
/**
 * Cliente.php File Doc Comment
 *
 * [Descripcion]
 *
 * PHP Version 5.2.6
 *
 * @category inc
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
class Cliente
{
    private $tabla = 'clientes';
    public $idCliente = false;
    public $nombre = null;
    public $nif = null;
    public $direccion = null;
    public $ciudad = null;
    public $cp = null;
    public $pais = null;
    public $diaFacturacion = null;

    /**
     * Contructor de la clase
     * @param bool $idCliente
     */
    public function __construct($idCliente = false)
    {
        if ($idCliente) {
            $this->idCliente = $idCliente;
            $this->datosCliente();
            $this->diaFacturacionCliente();
        }
    }

    /**
     * Establece los datos de el cliente si su id se ha establecido
     *
     * @return bool
     */
    public function datosCliente()
    {
        if ($this->idCliente) {
            $sql = "Select *
					FROM " . $this->tabla . "
					WHERE id LIKE ?";
            $resultados = Cni::consultaPreparada(
                $sql,
                array($this->idCliente),
                PDO::FETCH_CLASS
            );
            foreach ($resultados as $resultado) {
                $this->id = $resultado->Id;
                $this->nombre = $resultado->Nombre;
                $this->nif = $resultado->NIF;
                $this->direccion = $resultado->Direccion;
                $this->ciudad = $resultado->Ciudad;
                $this->cp = $resultado->CP;
                $this->pais = $resultado->Pais;
                $this->razon = $resultado->razon;
                $this->diaFacturacionCliente();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Busca el cliente por nombre o contacto y devuelve los resultados
     *
     * @param string $var
     * @return array|bool
     */
    public function buscaCliente($var)
    {
        $sql = "SELECT * FROM " . $this->tabla . "
			WHERE (Nombre LIKE :texto
			or Contacto LIKE :texto)
			AND `Estado_de_cliente`
			LIKE '-1' ORDER by Nombre ";
        $resultados = Cni::consultaPreparada(
            $sql,
            array(':texto' => '%' . $var . '%'),
            PDO::FETCH_CLASS
        );
        return $resultados;
    }

    /**
     * Establece el dia de facturacion del cliente si tiene
     */
    private function diaFacturacionCliente()
    {
        if ($this->idCliente) {
            $sql = "SELECT *
				FROM agrupa_factura
				WHERE concepto LIKE 'dia'
				AND idemp LIKE ?
				AND valor NOT LIKE '' ";
            $resultados = Cni::consultaPreparada(
                $sql,
                array($this->idCliente),
                PDO::FETCH_CLASS
            );
            if (Cni::totalDatosConsulta() > 0) {
                foreach ($resultados as $resultado) {
                    $this->diaFacturacion = $resultado->valor;
                }
            }
        }
    }
}

