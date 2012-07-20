<?php
/**
 * Genera el formulario de las comparativas
 * 
 * @param array $vars
 * @return string
 */
function comparativas($vars)
{
    if (!isset($vars['tipo'])) {
        $cadena =
        "<input type='hidden' name='formu' id='formu' value='7' />
        <label for='tipo_comparativa'>Comparacion de:</label>
        <select name='tipo_comparativa' id='tipo_comparativa' class='span2' 
            onchange='comparativa()'>
            <option value='0'>-- Opcion --</option>
            <option value='1'>Clientes</option>
            <option value='2'>Servicio</option>
            <option value='3'>Categoria</option>
        </select>
        <div id='comparativas'></div>";
    } else {
        switch ($vars['tipo']) {
            case(1):
                $cadena="Seleccione Cliente:".clientes();
            break;
            case(2):
                $cadena="Seleccione Servicio:";
            break;
            case(3):
                $cadena="Seleccione Categoria:".categorias();
            break;
        }
        $cadena .= servicios();
        $cadenaFechas ="
        <br />
        <label for='fecha_inicio_a'>Inicio Rango:</label>
        <input type='text' readonly size='10' id='fecha_inicio_a' 
            name='fecha_inicio_a' />
            <button type='button' class='calendario' id='boton_fecha_inicio_a'>
            </button>
        <label for='fecha_fin_a'>Fin Rango:</label>
        <input type='text' readonly size='10'id='fecha_fin_a' 
            name='fecha_fin_a' />
        <button type='button' class='calendario' id='boton_fecha_fin_a' >
            </button>
        <strong>&laquo; Frente a &raquo;</strong>
        <label for='fecha_inicio_b'>Inicio Rango:</label>
        <input type='text' readonly size='10' id='fecha_inicio_b'
            name='fecha_inicio_b' />
        <button TYPE='button' class='calendario' id='boton_fecha_inicio_b'>
            </button>
        <label for='fecha_fin_b'>Fin Rango:</label>
        <input type='text' readonly size='10' id='fecha_fin_b' 
            name='fecha_fin_b' />
        <button type='button' class='calendario' id='boton_fecha_fin_b'>
            </button>";
        $cadena .= $cadenaFechas."<input type='submit' value='Comparar' />";
    }
    return $cadena;
}
