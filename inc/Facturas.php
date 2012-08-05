<?php
require_once 'Cni.php';

class Facturas {
    public $factura = null;
    public $servicio = null;
    public $cantidad = null;
    public $unitario = null;
    public $iva = null;
    public $obs = null;
    public $resultados = false;
    private $totalGlobal = 0;
    private $totalBruto = 0;
    private $totalCantidad = 0;
    private $historico = false;
    /**
     * 
     * @param unknown_type $factura
     */
    public function __construct($factura = null) 
    {
        if (!is_null($factura)) {
            $this->numeroFactura = $factura;
            $this->historico = true;
        }
    }
    /**
     * Consulta los datos del historico
     */
    private function datosHistorico() 
    {
        $sql = "SELECT * 
                FROM historico
				WHERE factura 
                LIKE ?";
        $resultados = Cni::consultaPreparada(
                $sql,
                array($this->factura),
                PDO::FETCH_CLASS
        );
        if (Cni::totalDatosConsulta() > 0 ) {
            $this->resultados = $resultados;
        } else {
            $this->resultados = false;
        }
    }
    private function serviciosFijos()
    {
        
    }
    private function serviciosAlmacenaje()
    {
        
    }
    private function serviciosNoAgrupados()
    {
        
    }
    private function serviciosAgrupados()
    {
        
    }
    private function serviciosDescuento()
    {
        
    }
    /**
     * Agrega los datos al historico
     * 
     * @return boolean
     */
    private function agregaHistorico()
    {
        $sql = "INSERT 
                INTO historico 
                (factura, servicio, cantidad, unitario, iva, obs)
				VALUES (?, ?, ?, ?, ?, ?)";
        $params = array(
                $this->factura, 
                $this->servicio, 
                $this->cantidad, 
                $this->unitario, 
                $this->iva, 
                $this->obs
                );
        Cni::consultaPreparada($sql, $params);
        return true;
    }
    /**
     * Devuelve la cabezera de la factura
     * 
     * @return string
     */
    private function cabezeraFactura()
    {
        $html = "";
        return $html;
    }
    /**
     * Devuelve la linea de la factura
     * 
     * @return string
     */
    private function lineaFactura()
    {
        $importe = $this->unitario * $this->cantidad;
        $total = Cni::totalconIva($importe, $this->iva);
        $html = "
 		<tr>
		<td>".$this->servicio." ".$this->obs."</td>
		<td>".Cni::formateaNumero($this->cantidad)."</td>
		<td>".Cni::formateaNumero($this->unitario, true)."</td>
		<td>".Cni::formateaNumero($importe, true)."</td>
		<td>".Cni::formateaNumero($this->iva)."%</td>
		<td>".Cni::formateaNumero($total, true)."</td>
		</td>
		</tr>";
        $this->totalBruto += $importe;
        $this->totalCantidad += $this->cantidad;
        $this->totalGlobal += $total;
        if (!$this->historico) {
            $this->agregaHistorico();
        }
        return $html;
    }
    /**
     * Devuelve el pie de la factura
     * 
     * @return string
     */
    private function pieFactura()
    {
        $html = "";
        return $html;
    }
    /**
     * Devuelve la factura formateada
     * 
     * @return unknown
     */
    public function verFactura() {
        $html = $this->cabezeraFactura();
        foreach ($this->resultados as $resultado) {
            $this->servicio = ucfirst(trim($resultado->servicio));
            $this->cantidad = $resultado->cantidad;
            $this->unitario = $resultado->unitario;
            $this->iva = $resultado->iva;
            $this->obs = $resultado->obs;
            $html .= $this->lineaFactura();
        }
        $html .= $this->pieFactura();
        return $html;
    }
}