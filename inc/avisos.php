<?php
/**
* avisos File Doc Comment
*
* Genera el menu de la aplicacion
*
* PHP Version 5.2.6
*
* @category inc
* @package  CniInc
* @author   Ruben Lacasa Mas <ruben@ensenalia.com>
* @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ CC BY-NC-ND 3.0
* @version  GIT: Id$ In development. Very stable.
* @link     https://github.com/independenciacn/cni
*/
require_once 'variables.php';
require_once 'classes/Avisos.php';
require_once 'classes/Cni.php';
require_once 'classes/Connection.php';

$cni = new CNI();
$cni->checkSession();
$cadena = "";
if (isset($_POST['opcion']) && isset($_SESSION['usuario'])) {
    $opcion = filter_input(INPUT_POST, 'opcion', FILTER_SANITIZE_NUMBER_INT);
    $cadena = avisos();
    if ($opcion == 1) {
        $cadena = telefonos();
    }
} elseif (isset($_SESSION['usuario'])) {
    $cadena = avisos();
}
echo $cadena;

/**
 * Funcion que muestra los avisos
 *
 * @return string $cadena
 */
function avisos()
{
    $avisos = new Avisos();

    $texto="<input type='button' class='boton' value='[<]Ocultar Avisos'
    onclick='cerrar_avisos()'/>
    <table class='tabla'>
        <tr>
            <th colspan='2'>Cartel de Avisos</th></tr>
        <tr>
            <th>Cumpleaños</th><th>Contratos</th></tr>";

    $rangos = array(
        'hoy' => 'Hoy',
        'mañana' => 'Mañana',
        'mes' => 'Los proximos dias'
    );
    foreach ($rangos as $key => $rango) {
        $texto.= "
        <tr>
        <td valign='top'>
        <table width='100%'>
            <tr><th colspan='2'>".$rango." hacen los años</th></tr>";
        $resultados = array();
        $resultados = array_merge($resultados, $avisos->cumplesCentral($key));
        $resultados = array_merge($resultados, $avisos->cumplesEmpresa($key));
        $resultados = array_merge($resultados, $avisos->cumplesEmpleados($key));
        $cadena = "";
        // Ordenar por fecha solo en rango mes
        if ($key == 'mes') {
            foreach ($resultados as $result => $row) {
                $fecha[$result]  = $row['fecha'];
            }
            array_multisort($fecha, SORT_ASC, SORT_STRING, $resultados);
        }
        $clase = 0;
        foreach ($resultados as $resultado) {
            // Cambiar fecha a normal
            $empresa = ($resultado['empresa'] == 'Independenciacn')
                ? ""
                : " de
                <a href='javascript:muestra(".$resultado['id'].")'>" .
                $resultado['empresa'] ."</a>" ;
            $linea ="<tr class=".clase($clase)."><td colspan='2' >" .
                    $resultado['empleado']. $empresa.
                "</td></tr>";
            if ($key == 'mes') {
                $fecha = date_create($resultado['fecha']);
                $linea ="<tr class=".clase($clase).">
                    <td>" . $fecha->format('d-m') . "</td>
                    <td>" . $resultado['empleado'] . $empresa ."</td>
                    </tr>";
            }
            $cadena .= $linea;
            $clase++;
        }
        if (strlen($cadena) == 0) {
            $cadena = "<tr>
            <td colspan='2'>".$rango." nadie cumple los años</td>
            </tr>";
        }
        $texto .= $cadena;
    }

    $texto.="</table></td>";
    $texto.= "</tr></table></td></tr></table></td><td valign='top'>" . avisos_new() . "</td></tr></table>";
    return $texto;
}

/**
 * Genera el boton de ocultar telefono y el listado de telefonos
 *
 * @return string $cadena
 */
function telefonos()
{
    $cadena ="<input type='button' value='[v]Ocultar telefonos'
    onclick='cerrar_tablon_telefonos()'/>";
    $cadena .= listado('Telefono');
    $cadena .= listado('Fax');
    $cadena .= listado('Adsl');
    return $cadena;
}
/**
 * Devuelve el listado del servicio seleccionado
 *
 * @param string $servicio
 * @return string $cadena
 */
function listado($servicio)
{
    $connection = new Connection();
    $cadena ="<p/><u><b>".$servicio." del centro</b></u><p/>";
    $sql = "SELECT c.Id,c.Nombre, z.valor, z.servicio,
        (
        SELECT valor
        FROM z_sercont
        WHERE servicio LIKE 'Codigo Negocio'
        AND idemp LIKE z.idemp
        LIMIT 1
        ) as nombreServicio
    AS Despacho, c.Categoria
    FROM clientes AS c
    INNER JOIN z_sercont AS z ON c.Id = z.idemp
    WHERE z.servicio LIKE ?
    ORDER BY Despacho";
    $resultados = $connection->consulta($sql, array($servicio), PDO::FETCH_CLASS);
    $total = count($resultados);
    $cadena .="<table><tr>";
    $i=0;
    if ($total > 0) {
        foreach ($resultados as $resultado) {
            if (preg_match('#despacho#i', $resultado->nombreServicio)) {
                $color="#69C";
            } elseif (preg_match('#domicili#i', $resultado->nombreServicio)) {
                $color="#F90";
            } else {
                $color="#ccc";
            }
            if ($i%4 == 0) {
                $cadena .="</tr><tr>";
            }
            $cadena .= "<th bgcolor='".$color."' align='left'>
                <a href='javascript:muestra($resultado->Id)'>"
                .$resultado->nombreServicio."-".$resultado->Nombre."-
            <u><b>".$resultado->valor."</b></u></a></th>";
            $i++;
        }
    }
    $cadena .="</tr></table>";
    return $cadena;
}
/**
 * Muestra los avisos
 *
 * @return string
 */
function avisos_new()
{

    $connection = new Connection();
    $hnocump = 0;
    $k=0;
    $cadena ="<table width='100%'>";
//$cadena .= "<tr><th><span class='boton' onclick='cierralo()' onkeypress='cierralo()'>[X] Cerrar</span></th></tr>";
//$cadena .= "<tr><th colspan='2'>AVISOS</th></tr>";
//Clientes FInalizan HOY
    $cadena .= "<tr><th Colspan='2'>Hoy finalizan contrato</th></tr>";
    $sql = "SELECT  
        f.idemp,
        c.Nombre
        FROM facturacion as f INNER JOIN clientes as c ON f.idemp = c.Id
        WHERE date_format(renovacion,'%d %c %y') 
        LIKE date_format(curdate(),'%d %c %y') and c.Estado_de_cliente != ?";
    $resultados = $connection->consulta($sql, array(0), PDO::FETCH_CLASS);
    $total = count($resultados);
    if ($total >= 1) {
        foreach ($resultados as $resultado) {
            $cadena .="<tr><td class='".clase($k++)."'>
            <a href='javascript:muestra(".$resultado->idemp.")' >"
            . $resultado->Nombre."</a></td></tr>";
        }
    } else {
        $hnocump++;
        $cadena.="<tr><td class='".clase($k++)."' colspan='2'>
        Nadie Finaliza contrato hoy</td></tr>";
    }
    $cadena .= "</table>";
//return $cadena;
//Clientes que finalizan contrato este mes
//Clientes FInalizan este mes
    $cadena .= "<table width='100%'>";
    $cadena .= "<tr><th>Dia</th><th>Finalizan contrato este mes</th></tr>";
    $sql = "SELECT  
        f.idemp,
        DATE_FORMAT(f.renovacion, '%d-%m-%Y) as renovacion,
        c.Nombre
        FROM facturacion as f INNER JOIN clientes as c 
        ON f.idemp = c.Id
        WHERE month(f.renovacion) LIKE month(curdate()) 
        and year(f.renovacion) like year(curdate()) 
        and c.Estado_de_cliente != ? order by renovacion asc";
    $resultados = $connection->consulta($sql, array(0), PDO::FETCH_CLASS);
    $total = count($resultados);
    if ($total >= 1) {
        foreach ($resultados as $resultado) {
            $cadena .="<tr>
            <td class='".clase($k)."'>". $resultado->renovacion ."</td>
            <td class='".clase($k)."'>
            <a href='javascript:muestra(".$resultado->idemp.")' >".$resultado->Nombre."</a>
            </td>
            </tr>";
            $k++;
        }
    } else {
        $hnocump++;
        $cadena.="<tr><td colspan='2' class='".clase($k++)."'>
        Nadie Finaliza contrato este mes</td></tr>";
    }
    $cadena .= "</table>";
//Clientes que finalizan contrato dentro de los proximos 60 dias
//Clientes FInalizan este mes
    $cadena .= "<table width='100%'>";
    $cadena .= "<tr><th>Dia</th><th>Finalizan contrato en los proximos 60 dias</th></tr>";
    $sql = "SELECT 
        f.idemp,
        DATE_FORMAT(f.renovacion, '%d-%m-%Y) as renovacion,
        c.Nombre
        FROM facturacion as f INNER JOIN clientes as c ON f.idemp = c.Id
        WHERE (CURDATE() <= renovacion) and 
        (DATE_ADD(CURDATE(),INTERVAL 60 DAY)) >= renovacion 
        and c.Estado_de_cliente != 0 order by Month(renovacion) asc, 
        DAY(renovacion) asc";
    $resultados = $connection->consulta($sql, array(0), PDO::FETCH_CLASS);
    $total = count($resultados);
    if ($total >= 1) {
        foreach ($resultados as $resultado) {
            $cadena .="<tr>
            <td class='".clase($k)."'>".$resultado->renovacion."</td>
            <td class='".clase($k)."'>
            <a href='javascript:muestra(".$resultado->idemp.")' >".$resultado->Nombre."</a>
            </td></tr>";
            $k++;
        }
    } else {
        $hnocump++;
        $cadena.="<tr><td colspan='2' class='".clase($k++)."'>
        Nadie Finaliza contrato en los proximos 60 dias</td></tr>";
    }
    $cadena .= "</table>";
    return $cadena;
}
