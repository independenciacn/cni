<?php
require_once 'configuracion.php';
if ( isset( $_POST['func'] ) && isset( $_SESSION['usuario'] ) ) {
    sanitize( $_POST );
    if ( function_exists($_POST['func'] ) ) {
        echo $_POST['func']();
    } else {
        echo "Function Not Found";
    }
} else {
   header("Status: 404 Not Found");
   exit(0);
}
/**
 * Hace la copia de seguridad
 */
function doBackup()
{
    global $conf;
    $stamp = date("dmyHis");
    $html = "<span class='success span-10'>Copia Realizada</span>";
    try {
        exec( $conf['binariosBD'] . $conf['mysqldumpExec'] . "
                --opt --user=" . $conf['dbUser'] . "  --password=". $conf['dbPass'] . "
                " . $conf['dbName'] . " > " . $conf['rutaBackup'] .
                "copia" . $stamp . ".sql" );
    } catch( Exception $e ) {
        $html = "<span class='error span-10'>Error " . $e->getMessage() . "</span>";
    }
    return $html;
}
/**
 * Muestra el listado de las copias de seguridad realizadas
 *
 * @return string $html
 */
function listBackup()
{
    global $conf;
    $i = 0;
    $html =  "
    <table>
    <thead>
    <tr>
    <th colspan='3'>Listado de Copias Realizadas</th>
    </tr>
    </thead>
    <tbody>";
    if ( true == ($gestor = opendir( $conf['rutaBackup'] ) ) ) {
        while (false !== ( $archivo = readdir($gestor) ) ) {
            if ( preg_match('#.sql$#', $archivo ) ){
                $nombre = substr( $archivo, 5, 2 ) . "/" . substr( $archivo, 7, 2 ) .
                "/" . substr( $archivo, 9, 2) . "-" . substr( $archivo, 11, 2 ) .
                ":" . substr( $archivo, 13, 2) . ":" . substr( $archivo, 15, 2);
                $html .= "<tr><td>" . ++$i . "</td><td>" . $nombre . "</td><td>
                <input type='button' value='Restaurar'
                onclick='restaurar_backup(\"" . $archivo . "\")' />
                <input type='button' value='Borrar'
                onclick='borrar_backup(\"" . $archivo . "\")' />
                </td></tr>";
            }
        }
        closedir( $gestor );
    }
    if ( $i == 0) {
        $html .= "<tr><td colspan='3'>No hay copias guardadas</td></tr>";
    }
    $html .= "</tbody></table>";
    return $html;
}
/**
 * Genera el listado de categorias -- REFRACTORIZAR A AUXILIARES?
 */
function listadoCategoria()
{
    $cadena = "";
    $tabla = "categorías clientes";
    $sql = "SELECT * FROM `$tabla`";
    $resultados = consultaGenerica( $sql );
    $cadena.="<select id='tipo_cliente' onchange='filtra_listado()'>
    <option value='0'>--Selecciona Tipo--</option>";
    foreach( $resultados as $resultado ) {
        $cadena.="<option value='$resultado[0]'>" . $resultado[1] . "</option>";
    }
    $cadena.="<option value='social'>Con direccion Facturación</option>";
    $cadena.="<option value='comercial'>Con direccion Contrato</option>";
    $cadena.="<option value='independencia'>Con direccion Independencia</option>";
    $cadena.="<option value='conserje'>Listado Conserje</option>";
    $cadena.="</select>";
    return $cadena;
}