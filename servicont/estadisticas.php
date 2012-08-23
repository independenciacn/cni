<?php
/**
 * estadisticas.php File Doc Comment
 *
 * Generacion de consultas de estadisticas nuevas Julio 2008-Agosto 2008
 *
 * PHP Version 5.2.6
 *
 * @category servicont
 * @package  cni/servicont
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
require_once 'comparativas.php';
Cni::chequeaSesion();
/**
 * Muestra por pantalla la opcion de imprimir el listado
 *
 * @var string
 */
$imprimir = "<div class='pull-right'>
		<button class='btn btn-primary'
		onclick='window.open(\"print.php\",\"_self\")'>
		<i class='icon-print icon-white'></i>
		Imprimir</button></div>";

$cadena = "";
if (isset($_SESSION['usuario'])) {
    if (isset($_POST['opcion'])) {
        switch ($_POST['opcion']) {
            case (0):
                $cadena = formulario($_POST);
                break;
            //Generamos el formulario
            case (1):
                $cadena = respuesta($_POST);
                $cadena .= ($_POST['formu'] != 7) ? $imprimir : "";
                break;
            //Generamos la respuesta
            case (2):
                $cadena = opcionComparativas($_POST);
                break;
            //Genera la pantalla de comparativa
        }
        echo $cadena;
    } else {
        echo "No se ha pasado opcion";
    }
} else {
    echo "No se ha iniciado sesion";
}
/**
 * Devuelve el nombre del cliente
 *
 * @param  integer $cliente Id del Cliente
 *
 * @return string          Nombre del Cliente
 */
function nombreCliente($cliente)
{
    $nombreCliente = "Cliente no especificado";
    if ($cliente) {
        $sql = "SELECT Nombre FROM clientes WHERE id LIKE :idcliente";
        $params = array(':idcliente' => $cliente);
        $resultado = Cni::consultaPreparada($sql, $params);
        $nombreCliente = $resultado[0]['Nombre'];
    }

    return $nombreCliente;
}

/**
 * Listado de Clientes
 *
 * @return string Select de clientes
 */
function clientes()
{
    $sql = "SELECT id, Nombre FROM clientes ORDER BY Nombre";
    $resultados = Cni::consulta($sql);
    $form = "<select id='cliente' name='cliente' class='span4'>";
    $form .= "<option value='0'>-Cliente-</option>";
    foreach ($resultados as $resultado) {
        if (trim($resultado [1]) != "") {
            $form .= "<option value='" . $resultado[0] . "'>" .
                $resultado [1] . "</option>";
        }
    }
    $form .= "</select>";

    return $form;
}

/**
 * Listado de las categorias
 *
 * @return string
 */
function categorias()
{
    $sql = "SELECT categoria FROM clientes GROUP BY categoria";
    $resultados = Cni::consulta($sql);
    $form = "<select id='categoria' name='categoria' class='span4'>";
    $form .= "<option value='0'>-Categorias-</option>";
    foreach ($resultados as $resultado) {
        if (trim($resultado [0]) != "") {
            $form .= "<option value='" . $resultado [0] . "' >" .
                $resultado [0] . "</option>";
        }
    }
    $form .= "</select>";

    return $form;
}

/**
 * Select de los servicios
 *
 * @return string
 */
function servicios()
{
    $sql = "SELECT TRIM(servicio) FROM historico
    group by TRIM(servicio) ORDER BY TRIM(servicio)";
    $resultados = Cni::consulta($sql);
    $form = "<select id='servicios' name='servicios' class='span4'>";
    $form .= "<option value='0'>-Servicios-</option>";
    foreach ($resultados as $resultado) {
        $form .= "<option value='" . trim($resultado[0]) . "' >" .
            trim($resultado[0]) . "</option>";
    }
    $form .= "</select>";

    return $form;
}

/**
 * Devuelve el tipo de formulario solicitado
 *
 * @param integer $tipoFormulario   El tipo de formulario que queremos
 * @return string           El formulario solicitado
 */
function tipoFormulario($tipoFormulario)
{
    $formulario = "";
    switch ($tipoFormulario) {
        case 0:
            $formulario = clientes();
            break;
        case 1:
            $formulario = categorias();
            break;
        case 2:
            $formulario = servicios() . "Inicio:";
            break;
        case 3:
            $formulario = clientes() . servicios() . "<br/>";
            break;
        case 4:
            $formulario = categorias() . servicios() . "<br/>";
            break;
    }
    return $formulario;
}

/**
 * Se genera el formulario para la consulta por cliente
 * @param Array $vars
 * @return string
 */
function formulario($vars)
{
    $titulosFormulario = array(
        0 => 'Estadisticas Por Cliente',
        1 => 'Estadisticas Por Categoria',
        2 => 'Estadisticas Por servicios',
        3 => 'Estadisticas Por Cliente / Servicio',
        4 => 'Estadisticas Por Categoria / Servicio',
        5 => 'Estadisticas de Servicios por Volumen de Facturación',
        6 => "Estadisticas de Clientes por Volumen de Facturación",
        7 => "Comparativas",
    );
    //Este al ser entre fechas por cliente en formulario
    //tenemos fechas y clientes
    //y devolvera servicios
    $cadena = "
        <form name='consulta' class='form-inline' id='consulta' method='post'
            onsubmit='procesa();return false'>
            <fieldset>
            <legend>" . $titulosFormulario[$vars['form']] . "</legend>
            <input type='hidden' name='formu' id='formu' 
                value='" . $vars['form'] . "'>";
    $inicioFin = "
    		<label for='fechaInical'> Inicio:</label>
            <input type='text' class='span2 datepicker' readonly
                name='fechaInicial' id='fechaInicial' value='00-00-000'/>
    		<label for='fechaFinal'> Fin:</label>
            <input type='text' class='span2 datepicker' readonly
                name='fechaFinal' id='fechaFinal' value='00-00-0000' />";
    if ($vars['form'] != 7) {
        $cadena .= tipoFormulario($vars['form']) . $inicioFin;
        $cadena .= "
        		<div class='controls'>
        		<label class='radio'>
        		<input type='radio' name='tipo' value='acumulado'
            checked='checked'> Acumulado
        		</label>
        		<label class='radio'>
            	<input type='radio' name='tipo' value='detallado'> Detallado
           		</label>
        		<label class='select'>
        		Limitar Resultados:</label>";
        $cadena .= "<select name='limite' class='span1'>";
        for ($i = 10; $i <= 90; $i = $i + 10) {
            $cadena .= "<option value=" . $i . ">" . $i . "</option>";
        }
        $cadena .= "<option selected value=0>Todos</option>";
        $cadena .= "</select>";
        $cadena .= "
        		<button type='submit' class='btn btn-primary'>
        		<i class='icon-search icon-white'></i> 
                Buscar
        		</button>
                <button type='reset' class='btn btn-danger'>
                <i class='icon-remove icon-white'></i> 
                Limpiar
                </button>
                </div>";
    } else {
        $cadena .= formularioComparativas($vars);
        // $cadena .= "Las comparativas estan deshabilitadas";
    }
    $cadena .= "</fieldset></form>";
    $cadena .= "<div id='resultados'></div>";

    return $cadena;
}

/**
 * Generamos la respuesta dependiendo de los parametros que llegan
 *
 * @param array $vars
 * @return string
 */
function respuesta($vars)
{
    $titulo = "Consumo mensual y acumulado entre fechas por";
    $titulos = array(
        0 => $titulo . " cliente",
        1 => $titulo . " categoria",
        2 => $titulo . " servicios",
        3 => $titulo . " clientes/servicio",
        4 => $titulo . " categoria/servicio",
        5 => "Servicios mas facturados",
        6 => "Clientes con mas facturación",
        7 => "Comparativas",
    );
    $vars['titulo'] = $titulos[$vars['formu']];
    if ($vars['formu'] == 7) {
        return procesaComparativas($vars);
    } else {
        return procesaConsultas($vars);
    }
}

/**
 * Procesa y devuelve el subtitulo
 * @param Array $vars
 * @return string
 */
function procesaParams($vars)
{
    switch ($vars['formu']) {
        case 0:
            $params['titulo'] = nombreCliente($vars['cliente']);
            $params['vars'] = array($vars['cliente']);
            break;
        case 1:
            $params['titulo'] = $vars['categoria'];
            $params['vars'] = array($vars['categoria']);
            break;
        case 2:
            $params['titulo'] = $vars['servicios'];
            $params['vars'] = array($vars['servicios']);
            break;
        case 3:
            $params['titulo'] = nombreCliente($vars['cliente']) .
                " / " . $vars['servicios'];
            $params['vars'] = array($vars['servicios'], $vars['cliente']);
            break;
        case 4:
            $params['titulo'] = $vars['categoria'] . " / " . $vars['servicios'];
            $params['vars'] = array($vars['servicios'], $vars['categoria']);
            break;
        default:
            $params['titulo'] = "";
            $params['vars'] = array();
            break;
    }
    return $params;
}

/**
 * Genera los filtros de la consulta
 *
 * @param  Array $vars Array de valores
 *
 * @return String       Parte de la consulta donde salen los filtros
 */
function filtrosConsulta($vars)
{
    $filtro = "";
    $filtroFecha = consultaFecha($vars);
    if ($filtroFecha != "") {
        if ($vars['formu'] == 5) {
            $filtro .= " WHERE " . $filtroFecha;
        } else {
            $filtro .= " AND " . $filtroFecha;
        }
    }
    return $filtro;
}

/**
 * Procesa la consulta Sql y la genera
 * @param Array $vars
 * @return String
 */
function procesaConsultas($vars)
{
    $sql = "";
    $agrupamiento = "";
    $opcion = $vars['formu'];
    $params = procesaParams($vars);
    $options = array(
        0 => 'WHERE c.id_cliente LIKE ? ',
        1 => 'WHERE l.Categoria LIKE ? ',
        2 => 'WHERE TRIM(h.servicio) LIKE ? ',
        3 => 'WHERE TRIM(h.servicio) LIKE ? AND c.id_cliente LIKE ? ',
        4 => 'WHERE TRIM(h.servicio) LIKE ? AND l.Categoria LIKE ? ',
        5 => ' ',
        6 => ' '
    );
    if ($vars['tipo'] == 'acumulado') {
        $sql = "
        SELECT
        TRIM(h.servicio) AS Servicio,
        SUM(h.cantidad) AS Unidades,
        SUM(h.cantidad * h.unitario) AS Importe,
        SUM(h.cantidad * h.unitario * h.iva / 100) AS Iva,
        SUM( h.cantidad * h.unitario +
            h.cantidad * h.unitario * h.iva / 100
            ) AS Total
        FROM `historico` as h
        INNER JOIN `regfacturas` AS c
        ON h.factura = c.codigo
        INNER JOIN `clientes` AS l
        ON c.id_cliente = l.Id ";
        if ($opcion == 6) {
            $sql = preg_replace(
                '#TRIM\(h.servicio\) AS Servicio#',
                'l.Nombre AS Cliente',
                $sql
            );
            $agrupamiento = "GROUP BY TRIM(l.nombre)";
        } else {
            $agrupamiento = "GROUP BY TRIM(h.servicio)";
        }
    } elseif ($vars['tipo'] == 'detallado') {
        $sql = "
        SELECT
        TRIM(l.Nombre) AS Cliente,
        DATE_FORMAT(c.Fecha,'%d-%m-%Y') AS Fecha,   
        TRIM(h.servicio) AS Servicio,
        TRIM(h.obs) AS Observaciones,
        h.cantidad AS Unidades,
        h.cantidad * h.unitario AS Importe,
        h.cantidad * h.unitario * h.iva / 100 AS Iva,
        ( h.cantidad * h.unitario +
            h.cantidad * h.unitario * h.iva / 100
        ) AS Total
        FROM `historico` as h
        INNER JOIN `regfacturas` AS c
        ON h.factura = c.codigo
        INNER JOIN `clientes` AS l
        ON c.id_cliente = l.Id ";
    }
    $sql .= $options[$opcion];
    $sql .= filtrosConsulta($vars);
    $sql .= $agrupamiento;
    $sql .= ($vars['formu'] == 5 || $vars['formu'] == 6) ?
        "ORDER BY Total DESC " : " ";
    $sql .= ($vars['limite'] != 0) ? " LIMIT " . $vars['limite'] . " " : " ";
    /**
     * Comprobamos el tipo de consulta y la devolvemos preparada
     */
    $_SESSION['sqlQuery'] = $sql;
    $_SESSION['vars'] = $params['vars'];
    $_SESSION['titulo'] = $params['titulo'];
    return Cni::generaTablaDatos(
        $sql,
        $params['vars'],
        $params['titulo']
    );
}

/**
 * Generación de la consulta con las fechas
 *
 * @param array $vars
 * @return string
 */
function consultaFecha($vars)
{
    $sql = "";
    if ($vars['fechaInicial'] != '00-00-000') {
        $sql .= " AND (c.Fecha) >= 
        		STR_TO_DATE('" . $vars['fechaInicial'] . "','%d-%m-%Y') ";
    }
    if ($vars['fechaFinal'] != '00-00-0000') {
        $sql .= " AND (c.Fecha) <= 
        		STR_TO_DATE('" . $vars['fechaFinal'] . "','%d-%m-%Y') ";
    }
    $sql = substr($sql, 4);

    return $sql;
}
 