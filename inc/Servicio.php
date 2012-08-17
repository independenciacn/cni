<?php
/**
 *
 * @author ruben
 *        
 */
require_once 'Cni.php';
class Servicio
{
	// TODO - Insert your code here
	/**
	 */
	public $id = 0;
	public $nombre = null;
	public $precio = 0;
	public $iva = 0;
	public $fecha = false;
	private $_tabla = 'servicios2';
	private $_resultados = null;
	/**
	 * 
	 * @param unknown_type $fecha
	 */
	public function __construct($fecha = false)
	{
		if ($fecha) {
			$this->fecha = date_create(Cni::cambiaFormatoFecha($fecha));
		} else {
			$this->fecha = date_create();
		}
	}
	/**
	 * Busca el servicios por el id y establece los datos
	 * 
	 * @param unknown_type $id
	 */
	public function setServicioById($id)
	{
		$pars = array($id);
		$sql = "SELECT * 
			FROM ".$this->_tabla." WHERE Id like ?";
		$this->setDatosServicio($sql, $pars);
	}
	/**
	 * Busca el servicio por el nombre y establece los datos
	 * 
	 * @param unknown_type $name
	 */
	public function setServicioByName($name)
	{
		$pars = array('%'.$name.'%');
		$sql = "SELECT *
			FROM ".$this->_tabla." WHERE Nombre like ?";
		$this->setDatosServicio($sql, $pars);
	}
	/**
	 * Establece los datos del serivicio
	 * 
	 * @param unknown_type $sql
	 * @param unknown_type $pars
	 */
	private function setDatosServicio($sql, $pars)
	{
		$resultados =
		Cni::consultaPreparada(
				$sql,
				$pars,
				PDO::FETCH_CLASS,
				"Servicio"
		);
		if (Cni::totalDatosConsulta() == 1 ) {
			foreach ($resultados as $resultado) {
				$this->id = $resultado->id;
				$this->nombre = $resultado->Nombre;
				$this->precio = $resultado->PrecioEuro;
				$this->iva = $resultado->iva;
			}
			$this->setIva();
		}
	}
	/**
	 * 
	 */
	private function setIva()
	{
		foreach (Cni::$cambiosIva as $cambios) {
			if ($this->fecha >= date_create($cambios['fecha'])) {
				if ($cambios['ivaAnterior'] == $this->iva) {
					$this->iva = $cambios['ivaGenerico'];
				}
			}
		}
	}
    /**
     * Devuelve el listado de los servicios activos
     * @return bool|object
     */
    public function listadoServiciosActivos()
	{
		$sql = "SELECT * 
				FROM ".$this->_tabla." 
				WHERE `Estado_de_servicio` LIKE '-1' 
				OR `Estado_de_servicio` 
				LIKE 'on' 
				ORDER BY Nombre";
		return Cni::consultaPreparada($sql, array(), PDO::FETCH_CLASS);
	}
	/**
	 * Devuelve el listado de todos los servicios
	 * 
	 * @return boolean
	 */
	public function listadoServicios()
	{
		$sql = "SELECT *
				FROM ".$this->_tabla;
		return Cni::consultaPreparada($sql, array(), PDO::FETCH_CLASS);
	}
}

