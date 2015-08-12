<?php
/**
 * Avisos File Doc Comment
 *
 * Muestra el cartel de avisos
 *
 * PHP Version 5.2.6
 *
 * @category Avisos
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
require_once 'variables.php';
require_once 'classes/Avisos.php';

checkSession();
if (isset($_POST['opcion']) && isset($_SESSION['usuario'])) {
    $opcion = filter_input(INPUT_POST, 'opcion', FILTER_SANITIZE_NUMBER_INT);
    $cadena = avisos();
    if ($opcion == 1) {
        $cadena = telefonos();
    }
} elseif (isset($_SESSION['usuario'])) {
    $cadena = avisos();
} else {
    $cadena = "";
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
    $texto.= "<td valign='top'>".avisos_new()."</td></tr></table>";
    return $texto;
}
/**
 * Funcion del cambio de fecha 
 * 
 * @deprecated
 * @param string $stamp
 * @return string $fecha
 */
function cambiaf($stamp) //funcion del cambio de fecha
{
    //formato en el que llega aaaa-mm-dd o al reves
    $fdia = explode("-",$stamp);
    $fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
    return $fecha;
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
    global $con;
    $cadena ="<p/><u><b>".$servicio." del centro</b></u><p/>";
    $sql = "SELECT c.Id,c.Nombre, z.valor, z.servicio,
        (
        SELECT valor
        FROM z_sercont
        WHERE servicio LIKE 'Codigo Negocio'
        AND idemp LIKE z.idemp
        LIMIT 1
        )
    AS Despacho, c.Categoria
    FROM clientes AS c
    INNER JOIN z_sercont AS z ON c.Id = z.idemp
    WHERE z.servicio LIKE '".$servicio."'
    ORDER BY Despacho";
    $consulta = mysql_query($sql,$con);
    $cadena .="<table><tr>";
    $i=0;
    if (mysql_numrows($consulta)!=0) {
        while(true == ($resultado = mysql_fetch_array($consulta)))
        {
            if ( preg_match('#despacho#i',$resultado[5])) {
                $color="#69C";
            } elseif ( preg_match('#domicili#i', $resultado[5])) {
                $color="#F90";
            } else {
                $color="#ccc";
            }
            if($i%4==0) {
                $cadena .="</tr><tr>";
            }
            $cadena .= "<th bgcolor='".$color."' align='left'>
                <a href='javascript:muestra($resultado[0])'>"
                .$resultado[4]."-".$resultado[1]."-
            <u><b>".$resultado[2]."</b></u></a></th>";
            $i++;
        }
    }
    $cadena .="</tr></table>";
    return $cadena;
}
/**
 * que queremos avisar principalmente fin de contratos en el dia y 
 * en el mes tanto de clientes como de proveedores. 
 * De donde se coge ese dato, de la tabla facturacion 
 * AUDITAREMOS campos finicio, duracion, valores de duracion 
 * dias-espacio es decir 1-H, 1-D, 1-S, 1-M, 1-A 
 * Clientes (facturacion)
 * 1.- Fecha inicio + duracion
 * 2.- Dia de Pago - Si es hoy el dia del mes de pago
 * Proveedores (z_facturacion)
 * 1.- Fecha inicio + duracion
 * 2.- Dia de Pago - Si es hoy el dia del mes de pago
 * 
 * @return string
 */
function avisos_new()
{

    global $con;
    $hnocump = 0;
    $k=0;
    $cadena ="<table width='100%'>";
//$cadena .= "<tr><th><span class='boton' onclick='cierralo()' onkeypress='cierralo()'>[X] Cerrar</span></th></tr>";
//$cadena .= "<tr><th colspan='2'>AVISOS</th></tr>";
//Clientes FInalizan HOY
    $cadena .= "<tr><th Colspan='2'>Hoy finalizan contrato</th></tr>";
    $sql = "SELECT facturacion.id, 
        facturacion.idemp,
        facturacion.finicio,
        facturacion.duracion,
        facturacion.renovacion,
        clientes.Nombre
        FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
        WHERE date_format(renovacion,'%d %c %y') 
        LIKE date_format(curdate(),'%d %c %y') and clientes.Estado_de_cliente != 0";
    $consulta = mysql_query($sql,$con);
    $total = mysql_numrows($consulta);
    if ($total >= 1) {
        while(true == ($resultado = mysql_fetch_array($consulta))) {
            $cadena .="<tr><td class='".clase($k++)."'>
            <a href='javascript:muestra(".$resultado[1].")' >"
            .$resultado[5]."</a></td></tr>";
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
    $sql = "SELECT facturacion.id, 
        facturacion.idemp,
        facturacion.finicio,
        facturacion.duracion,
        facturacion.renovacion,
        clientes.Nombre
        FROM facturacion INNER JOIN clientes 
        ON facturacion.idemp = clientes.Id
        WHERE month(renovacion) LIKE month(curdate()) 
        and year(renovacion) like year(curdate()) 
        and clientes.Estado_de_cliente != 0 order by renovacion asc";
    $consulta = mysql_query($sql,$con);
    $total = mysql_numrows($consulta);
    if ($total >= 1) {
        while(true == ($resultado = mysql_fetch_array($consulta))) {
            $cadena .="<tr>
            <td class='".clase($k)."'>".cambiaf($resultado[4])."</td>
            <td class='".clase($k)."'>
            <a href='javascript:muestra(".$resultado[1].")' >".$resultado[5]."</a>
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
    $sql = "SELECT facturacion.id, 
        facturacion.idemp,
        facturacion.finicio,
        facturacion.duracion,
        facturacion.renovacion,
        clientes.Nombre
        FROM facturacion INNER JOIN clientes ON facturacion.idemp = clientes.Id
        WHERE (CURDATE() <= renovacion) and 
        (DATE_ADD(CURDATE(),INTERVAL 60 DAY)) >= renovacion 
        and clientes.Estado_de_cliente != 0 order by Month(renovacion) asc, 
        DAY(renovacion) asc";
    $consulta = mysql_query($sql,$con);
    $total = mysql_numrows($consulta);
    if ($total >= 1) {
        while(true == ($resultado = mysql_fetch_array($consulta))) {
            $cadena .="<tr>
            <td class='".clase($k)."'>".cambiaf($resultado[4])."</td>
            <td class='".clase($k)."'>
            <a href='javascript:muestra(".$resultado[1].")' >".$resultado[5]."</a>
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
