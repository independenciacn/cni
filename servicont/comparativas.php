<?php
/**
 * comparativas.php File Doc Comment
 * 
 * Fichero encargado de la seccion de las comparativas
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
/**
 * Selección de la comparativa a hacer
 * 
 * @return String Parte de seleccion de tipo de comparativa
 */
function formularioComparativas()
{
    $html = "
    <input type='hidden' name='formu' id='formu' value='7' />
        <label for='tipoComparativa'>Comparacion de:</label>
        <select name='tipoComparativa' id='tipoComparativa' class='span2' 
            onchange='comparativa()'>
            <option value='0'>-- Opción --</option>
            <option value='1'>Clientes</option>
            <option value='2'>Servicio</option>
            <option value='3'>Categoria</option>
        </select>
        <div id='comparativas'></div>";
    return $html;
}
/**
 * Dependiendo de la opcion marcada muestra un selector u otro
 * 
 * @param  Array $vars Datos Posteados
 * 
 * @return String      Resultado Final
 */
function opcionComparativas($vars)
{
    $fechas = "
    <div class='controls'>
        <label>Rango Inicial</label>
        <label for='fechaInicialA'>Fecha Inicial</label>
        <input type='text' id='fechaInicialA' name='fechaInicialA'
            class='datepicker span2' value='00-00-0000'/>
        <label for='fechaFinalA'>Fecha Final</label>
        <input type='text' id='fechaFinalA' name='fechaFinalA'
            class='datepicker span2' value='00-00-0000'/>    
    </div>
    <div class='controls'>
        <label>Rango Final</label>
        <label for='fechaInicialB'>Fecha Inicial</label>
        <input type='text' id='fechaInicialB' name='fechaInicialB'
            class='datepicker span2' value='00-00-0000'/>
        <label for='fechaFinalB'>Fecha Final</label>
        <input type='text' id='fechaFinalB' name='fechaFinalB'
            class='datepicker span2' value='00-00-0000'/> 
    </div>
    <div class='controls'>
        <button type='submit' class='btn btn-primary'>
            <i class='icon-resize-horizontal icon-white'></i>
            Comparar
        </button>
    </div>
    ";
    switch ($vars['tipo']) {
        case 1:
            $html = "Seleccione Cliente:".clientes().$fechas;
            break;
        case 2:
            $html = "Seleccione Servicio:".servicios().$fechas;
            break;
        case 3:
            $html = "Seleccione Categoria:".categorias().$fechas;
            break;
        default:
            $html = Cni::mensajeError("Opcion Incorrecta");
            break;
    }
    return $html;
}
/**
 * Segun los datos marcados ejecuta una comparativa u otra, y muestra
 * los resultados
 * 
 * @param  Array $vars  Datos posteados
 * 
 * @return String       Resultado Final
 */
function procesaComparativas($vars)
{
    if ($vars['fechaInicialA'] != '00-00-0000'
        && $vars['fechaFinalA'] != '00-00-0000'
        && $vars['fechaInicialB'] != '00-00-0000'
        && $vars['fechaFinalB'] != '00-00-0000') {
        switch ($vars['tipoComparativa']) {
            case 1:
                $html = comparativaClientes($vars);
                break;
            case 2:
                $html = comparativaServicios($vars);
                break;
            case 3:
                $html = comparativaCategorias($vars);
                break;
            default:
                $html = Cni::mensajeError('Opcion Incorrecta');
                break;
        }
    } else {
        $html = Cni::mensajeError('Debe especificar las fechas del rango');
    }
    return $html;
}
/**
 * Pasa las opciones correspondientes a la comparativa
 * de clientes y devuelve los resultados
 * 
 * @param  Array $vars  Array de valores posteados
 * 
 * @return String       Resultado final
 */
function comparativaClientes($vars)
{
    if ($vars['cliente'] != 0) {
        $html = procesaComparativa(
            'TRIM(h.servicio)',
            'l.Id',
            $vars,
            $vars['cliente'],
            'Cliente'
            );
    } else {
        $html = Cni::mensajeError('Debe seleccionar un cliente');
    }
    return $html;
}
/**
 * Pasa las opciones correspondientes a la comparativa de Servicios
 * 
 * @param  Array $vars  Array de valores pasados
 * 
 * @return String       Resultado final
 */
function comparativaServicios($vars)
{
    if ($vars['servicios'] != "0") {
        $html = procesaComparativa(
            'l.Nombre',
            'TRIM(h.servicio)',
            $vars,
            $vars['servicios'],
            'Cliente'
            );
    } else {
        $html = Cni::mensajeError('Debe seleccionar un servicio');
    }
    return $html;
}
/**
 * Pasa las opciones correspondientes a la comparativa de Categorias
 * 
 * @param  Array $vars  Array de valores pasados
 * 
 * @return String       Resultado final
 */
function comparativaCategorias($vars)
{
    if ($vars['categoria'] != "0") {
        $html = procesaComparativa(
            'TRIM(h.servicio)',
            'l.Categoria',
            $vars,
            $vars['categoria'],
            'Servicio'
            );
    } else {
        $html = Cni::mensajeError('Debe seleccionar un cliente');
    }
    return $html;
}
/**
 * Segun los parametros pasados genera las consultas Sql y procesa los datos
 * 
 * @param  String $primerCampo En formato sql el primer campo a mostrar 
 * 							   en la consulta
 * @param  String $igual       En formato sql el campo que sera Like
 * @param  Array $vars         Array de datos
 * @param  String $filtro      Valor sobre el que filtar
 * @param  String $titulo      Titulo del primer campo del listado
 * 
 * @return String              Resultado de todo
 */
function procesaComparativa($primerCampo, $igual, $vars, $filtro, $titulo)
{
    $sql = "
    SELECT 
    ". $primerCampo ." AS Servicio,
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
    ON c.id_cliente = l.Id
    WHERE 
    c.Fecha >= STR_TO_DATE(?, '%d-%m-%Y')
    AND
    c.Fecha <= STR_TO_DATE(?, '%d-%m-%Y')
    AND ".$igual." LIKE ?
    GROUP BY ".$primerCampo."
    ORDER BY ".$primerCampo."
    ";
    $rangoA = array($vars['fechaInicialA'], $vars['fechaFinalA'], $filtro);
    $rangoB = array($vars['fechaInicialB'], $vars['fechaFinalB'], $filtro);
    $datosRangoA = Cni::consultaPreparada($sql, $rangoA, PDO::FETCH_ASSOC);
    $datosRangoB = Cni::consultaPreparada($sql, $rangoB, PDO::FETCH_ASSOC);
    $html = comparaDatos($datosRangoA, $datosRangoB, $titulo);
    return $html;
}
/**
 * Procesa los datos y muestra la tabla de resultados
 * 
 * @param  Array $rangoA  Primer rango de valores
 * @param  Array $rangoB  Segundo rango de valores
 * @param  String $titulo Nombre del Primer campo de la tabla
 * 
 * @return String         Tabla con los datos procesados
 */
function comparaDatos($rangoA, $rangoB, $titulo)
{
    $datos = arrayDatos($rangoA, $rangoB);
    $datosFinales = array(
        'UnidadesA' => 0,
        'ImporteA' => 0,
        'IvaA' => 0,
        'TotalA' => 0,
        'UnidadesB' => 0,
        'ImporteB' => 0,
        'IvaB' => 0,
        'TotalB' => 0
    );
    $html = "
    <table class='table table-striped table-condensed'>
    <caption>Comparativa</caption>
    <thead>
    <tr>
        <th>&nbsp;</th>
        <th colspan='4'>Rango Inicial</th>
        <th colspan='4'>Rango Final</th>
        <th colspan='2'>Diferencia</th>
    </tr>    
    <tr>
        <th>".$titulo."</th>
        <th>Unidades</th>
        <th>Importe</th>
        <th>Iva</th>
        <th>Total</th>
        <th>Unidades</th>
        <th>Importe</th>
        <th>Iva</th>
        <th>Total</th>
        <th>Unidades</th>
        <th>Total</th>
    </tr>
    </thead>
    ";
    $html .= "<tbody>";
    foreach ($datos as $key => $dato) {
        $datosFinales['UnidadesA'] += $dato['A']['Unidades'];
        $datosFinales['ImporteA'] += $dato['A']['Importe'];
        $datosFinales['IvaA'] += $dato['A']['Iva'];
        $datosFinales['TotalA'] += $dato['A']['Total'];
        $datosFinales['UnidadesB'] += $dato['B']['Unidades'];
        $datosFinales['ImporteB'] += $dato['B']['Importe'];
        $datosFinales['IvaB'] += $dato['B']['Iva'];
        $datosFinales['TotalB'] += $dato['B']['Total'];
        $html .= "
        <tr>
        <td>".$key."</td>
        <td>".Cni::formateaNumero($dato['A']['Unidades'])."</td>
        <td>".Cni::formateaNumero($dato['A']['Importe'], true)."</td>
        <td>".Cni::formateaNumero($dato['A']['Iva'], true)."</td>
        <td>".Cni::formateaNumero($dato['A']['Total'], true)."</td>
        <td>".Cni::formateaNumero($dato['B']['Unidades'])."</td>
        <td>".Cni::formateaNumero($dato['B']['Importe'], true)."</td>
        <td>".Cni::formateaNumero($dato['B']['Iva'], true)."</td>
        <td>".Cni::formateaNumero($dato['B']['Total'], true)."</td>
        <td>".diferenciaValores($dato['A']['Unidades'], $dato['B']['Unidades']).
        "</td>
        <td>".diferenciaValores($dato['A']['Total'], $dato['B']['Total'], true).
        "</td>
        </tr>";
    }
    $html .= "</tbody>";
    $html .= "<tfoot>";
    $html .= "<tr>
    <th>Totales</th>";
    foreach ($datosFinales as $key => $dato) {
        $html .= "<th>";
        $html .= (preg_match('#Unidades#', $key)) ?
            Cni::formateaNumero($dato) :
            Cni::formateaNumero($dato, true);
        $html .= "</th>";
    }
    $html .= "<th>".
        diferenciaValores(
            $datosFinales['UnidadesA'],
            $datosFinales['UnidadesB']
            )
        ."</th>";
    $html .= "<th>".
        diferenciaValores(
            $datosFinales['TotalA'],
            $datosFinales['TotalB'],
            true
            )
        ."</th>";
    $html .= "</tr>";
    $html .= "</tfoot>";
    $html .= "</table>";
    return $html;
}
/**
 * Compara dos valores y devuelve la diferencia con la 
 * etiqueta correspondiente, si es mayor o menor
 * 
 * @param  Integer  $valorA Valor A
 * @param  Integer  $valorB Valor B
 * @param  boolean  $moneda Diferencia
 * 
 * @return String          Cadena Html formateada con la diferencia
 */
function diferenciaValores($valorA, $valorB, $moneda = false)
{
    $valor = $valorB - $valorA;
    $classValor = 'badge ';
    if ( $valor > 0 ) {
        $classValor .= 'badge-success';
    } elseif( $valor < 0 ) {
        $classValor .= 'badge-important';
    }
    $valor = ($moneda) ? Cni::formateaNumero($valor, true) : $valor;
    $html = "<span class='".$classValor."'>".$valor."</span>";
    return $html;
}
/**
 * Crea el array con los dos rangos en uno solo para la
 * comparativa
 * 
 * @param  Array $rangoA Datos del primer rango
 * @param  Array $rangoB Datos del rango final
 * 
 * @return Array         Datos combinados
 */
function arrayDatos($rangoA, $rangoB)
{
    $initArray = array(
    		'Unidades' => 0,
    		'Importe' => 0,
    		'Iva' => 0,
    		'Total' => 0
    		);
    foreach ($rangoA as $var) {
        $datos[$var['Servicio']]['A'] = $var;
        $datos[$var['Servicio']]['B'] = $initArray;
    }
    foreach ($rangoB as $var) {
        $datos[$var['Servicio']]['B'] = $var;
        if (!array_key_exists('A', $datos[$var['Servicio']])) {
            $datos[$var['Servicio']]['A'] = $initArray;
        }
    }
    ksort($datos);
    return $datos;
}
 