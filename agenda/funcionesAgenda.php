<?php
require_once '../inc/configuracion.php';
if ( !isset( $_SESSION['usuario'] ) ) {
    notFound();
}

function agendaDespachos()
{
    $sql = "SELECT CAST(z.valor AS UNSIGNED) as despacho, c.Nombre as cliente ,
    c.id, c.Categoria
    FROM clientes as c  join z_sercont as z on c.id like z.idemp
    WHERE  Estado_de_cliente != 0 and
    c.Categoria like '%despacho%' and
    z.servicio like 'Codigo Negocio' order by z.valor asc";
    $resultados = consultaGenerica( $sql );
    $despachos = array_fill(1,36,'');
    $numDespacho = 0;
    foreach( $resultados as $resultado ) {
    
        $despachos[$resultado['despacho']] =
        array('id' => $resultado['id'],
                'cliente' => $resultado['cliente'] );
    }
    $html = "<tr>";
    foreach ( $despachos as $key => $despacho ) {
        $titulo = ($key == '23') ? "Sala De Juntas" : "Despacho ". $key;
        if (is_array( $despacho ) ) {
            $class = "class='despachoOcupado'";
            $nombreCliente =  $despacho['cliente'];
            $button = "<input class='informacionCliente' id='".$despacho['id']."' type='button'
            value='+ Info' />";
        } else {
            $class = "";
            $nombreCliente = "";
            $button = "";
        }
        $html.= "<td ".$class.">";
        $html.= $titulo."<br/>";
        $html.= $nombreCliente."<br/>";
        $html.= $button."<br/>";
        $html.= "</td>";
        $html.= ( ++$numDespacho % 6 == 0 ) ? "</tr><tr>" : "";
    }
    $html.= "</table>";
    return $html;
}

function tareasPendientes() {
	include_once 'tareas.php';
}
function agendaSemana(){
    include_once 'semanaNew.php';
}