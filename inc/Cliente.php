<?php
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
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once 'Cni.php';
class Cliente {
	private $_tabla = 'clientes';
	public $idCliente = false;
	public $nombre = null;
	public $nif = null;
	public $direccion = null;
	public $ciudad = null;
	public $cp = null;
	public $pais = null;

	/**
	 * [__construct description]
	 * 
	 * @param boolean $idCliente [description]
	 */
	public function __construct($idCliente = false)
	{
		if ( $idCliente ) {
			$this->idCliente = $idCliente;
			$this->datosCliente();
		}
	}
	/**
	 * [datosCliente description]
	 * 
	 * @return [type] [description]
	 */
	public function datosCliente()
	{
		if ( $this->idCliente ) {
			$sql = "Select *
					FROM ".$this->_tabla."
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
			}
		}
	}
	/**
	 * [buscaCliente description]
	 * 
	 * @param  [type] $var [description]
	 * 
	 * @return [type]      [description]
	 */
	public function buscaCliente($var)
	{
		$sql = "SELECT * FROM ".$this->_tabla."
			WHERE (Nombre LIKE :texto
			or Contacto LIKE :texto)
			AND `Estado_de_cliente`
			LIKE '-1' ORDER by Nombre ";
		$resultados = Cni::consultaPreparada(
				$sql,
				array(':texto' => '%'.$var.'%'),
				PDO::FETCH_CLASS
		);
		return $resultados;
	}
	/**
	 * [formaPagoCliente description]
	 * 
	 * @return [type] [description]
	 */
	public function formaPagoCliente()
	{
		if ($this->idCliente) {
			$sql = "SELECT fpago 
			FROM facturacion 
			WHERE idemp LIKE ?";
			$resultados = Cni::consultaPreparada(
				$sql,
				array($this->idCliente),
				PDO::FETCH_CLASS
				);
			foreach ($resultados as $resultado) {
				$formaPago = $resultado->fpago;
			}
			return $formaPago;
		}
	}
}

