<?php
require_once 'Cni.php';
/**
 * OpcionesPago.php File Doc Comment
 *
 * Clase encargada de la generacion y gestion de Facturas
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
class OpcionesPago
{
    public $idCliente = null;
    public $idFactura = null;
    public $formaPago = null;
    public $obsFormaPago = null;
    public $pedidoCliente = null;
    /**
     * Constructror, espera o el idCliente o el idFactura
     *
     * @param null | number $idCliente
     * @param null | number $idFactura
     */
    public function __construct($idCliente = null, $idFactura = null)
    {
        $this->idCliente = $idCliente;
        $this->idFactura = $idFactura;
        $this->estableceDatosPago();
    }
    /**
     * Dependiendo si se ha establecido o no el cliente o la factura devuelve
     * unos datos u otros
     */
    public function estableceDatosPago()
    {
        if (!is_null($this->idCliente)) {
            $resultados = $this->consultaDatosPagoPorCliente();
        } elseif (!is_null($this->idFactura)){
            $resultados = $this->consultaDatosPagoPorFactura();
        }

        if (!empty($resultados)) {
            foreach ($resultados as $resultado) {
                $this->formaPago = $resultado->formaPago;
                $this->obsFormaPago = $resultado->obsFormaPago;
                $this->pedidoCliente = $resultado->pedidoCliente;
            }
        }
        $pagoCC = array("Cheque", "Contado", "Tarjeta credito",
            "Liquidación");
        $pagoNCC = array("Cheque");
        if (!in_array($this->formaPago, $pagoCC)) {
            $this->formaPago = "Cuenta: ". $this->formaPago;
        } elseif (in_array($this->formaPago, $pagoNCC)) {
            $this->obsFormaPago = "Vencimiento: ".
                $this->obsFormaPago;
        }
    }

    /**
     * Devuelve los resultados de la forma de pago si se ha pasado el id de
     * factura
     *
     * @return bool|object
     */
    protected function consultaDatosPagoPorFactura()
    {
        $sql = "SELECT
			obs_alt AS observaciones,
    		fpago AS formaPago,
			obs_fpago AS obsFormaPago,
			pedidoCliente
			FROM regfacturas
			WHERE id LIKE ?";
        $resultados = Cni::consultaPreparada(
            $sql,
            array($this->idFactura),
            PDO::FETCH_CLASS
        );
        return $resultados;

    }
    /**
     * Devuelve los resultados de la forma de pago si se ha pasado el cliente
     *
     * @return bool|object
     */
    protected function consultaDatosPagoPorCliente()
    {
        $sql = "
        SELECT fpago AS formaPago,
        cc AS obsFormaPago,
        dpago AS pedidoCliente
        FROM facturacion
        WHERE idemp LIKE ?";
        $resultados = Cni::consultaPreparada(
            $sql,
            array($this->idCliente),
            PDO::FETCH_CLASS
        );
        return $resultados;
    }
}
