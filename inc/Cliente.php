<?php
/**
 *
 * @author ruben
 *        
 */
require_once 'Cni.php';
class Cliente {
	// TODO - Insert your code here
	/**
	 */
	private $_tabla = 'clientes';
	public $id = false;
	public $nombre = null;
	public function __construct($id = false)
	{
		if ( $id ) {
			$this->id = $id;
			$this->datosCliente();
		}
	}

	public function datosCliente()
	{
		if ( $this->id ) {
			$sql = "Select *
					FROM ".$this->_tabla."
					WHERE id LIKE ?";
			$resultados = Cni::consultaPreparada(
					$sql,
					array($this->id),
					PDO::FETCH_CLASS
					);
			foreach ($resultados as $resultado) {
				$this->nombre = $resultado->Nombre;
			}
		}
	}
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
}

