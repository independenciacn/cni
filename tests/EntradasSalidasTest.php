<?php
/**
 * Project: cni.
 * User: ruben
 * Date: 1/08/17
 * Time: 12:13
 */
require_once  __DIR__.'/../entradas2/clases/EntradasSalidas.php';
use PHPUnit\Framework\TestCase;

class EntradasSalidasTest extends TestCase
{
    public function testDetallesOcupacionHoras()
    {
        $entradas = new EntradasSalidas();
        $resultados = $entradas->DetallesOcupacionHoras(2,"2017", "Horas", "Completa");
        $this->assertTrue(is_array($resultados));
        $this->assertCount(1, $resultados);
        $resultados = $entradas->DetallesOcupacionHoras(2, "2017", "HorasClientes", "Completa");
        $this->assertCount(28, $resultados);
    }
    public function testCategorias()
    {
        $entradas = new EntradasSalidas();
        $resultados = $entradas->categorias();
        $this->assertTrue(is_array($resultados));
        $this->assertCount(6, $resultados);
        $this->assertArrayHasKey('Nombre', $resultados[0]);
    }
    public function testValoresCategoriasAnyoCero()
    {
        $entradas = new EntradasSalidas();
        $resultados = $entradas->valoresCategoriasAnyoCero('Clientes despachos', 'entradas');
        $this->assertEquals(128, $resultados);
        $resultados = $entradas->valoresCategoriasAnyoCero('Clientes domiciliación básica', 'salidas');
        $this->assertEquals(53, $resultados);
    }
    public function testMovimientosTotales()
    {
        $entradas = new EntradasSalidas();
        $resultados = $entradas->movimientosTotales();
        $this->assertCount(5, $resultados);
        $resultado = current($resultados);
        $this->assertArrayHasKey('categoria', $resultado);
        $this->assertArrayHasKey('total', $resultado);
        $this->assertEquals(1, $resultado['total']);
    }
    public function testTitulo()
    {
        $entradas = new EntradasSalidas();
        $resultados = $entradas->titulo();
        $year = date('Y');
        $antYear = $year - 1;
        $this->assertEquals("Consumo Servicios ".$antYear."-".$year, $resultados);
        $entradas->setTipoVista(1);
        $resultados = $entradas->titulo();
        $this->assertEquals("Consumo Servicios Acumulada " . $antYear . "-" . $year, $resultados);
    }
    public function testSetAnyos()
    {
        $entradas = new EntradasSalidas();
        $entradas->setAnyos();
        $this->assertCount(2, $entradas->anyos);
    }
    public function testSetTipoDato()
    {
        $entradas = new EntradasSalidas();
        $year = date('Y');
        $antYear = $year - 1;
        $entradas->setTipoDato(1);
        $titulo = $entradas->titulo();
        $this->assertEquals("Movimientos Clientes ".$antYear."-".$year, $titulo);
        $entradas->setTipoVista(2);
        $titulo = $entradas->titulo();
        $this->assertEquals("Movimientos Clientes Detallada " . $antYear . "-" . $year, $titulo);
    }
    public function testCuentaServiciosPorMes()
    {
        $entradas = new EntradasSalidas();
        $resultados = $entradas->cuentaServiciosPorMes('Despacho (una hora)', false, true);
        var_dump($resultados);
        $resultados = $entradas->cuentaServiciosPorMes('Despacho (una hora)');
        var_dump($resultados);
    }
}

