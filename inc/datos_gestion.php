<?php
/**
 * Datos_Gestion File Doc Comment
 * 
 * Genera los datos de la seccion de Gestion
 * 
 * PHP Version 5.2.6
 * 
 * @category Datos_Gestion
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once 'variables.php';
$vars = $_POST;
array_walk( $vars, 'sanitize' ); // Saneamos todos los datos
if (isset( $vars['opcion'] )) {
    switch ($vars['opcion']) {
        case 0:
            $respuesta = listadoCopias();
            break;
        case 1:
            $respuesta = hazBackup();
            break;
        case 2:
            $respuesta = restaura( $vars['archivo'] );
            break;
        case 3:
            $respuesta = borraBackup( $vars['archivo'] );
            break;
        case 4:
            $respuesta = revisaTablas();
            break;
        case 5:
            $respuesta = reparaTablas();
            break;
        case 6:
            $respuesta = optimizaTablas();
            break;
        case 7:
            $respuesta = listadoCategorias( $vars );
            break;
        case 8:
            $respuesta = detallesCategoria( $vars );
            break;
        case 9:
            $respuesta = actualizaCategoria( $vars );
            break;
        case 10:
            $respuesta = listadoTelefonos();
            break;
        case 11:
            $respuesta = formularioTelefonos();
            break;
        case 12:
            $respuesta = frmAgregaTelefono( $vars );
            break;
        case 13:
            $respuesta = rarita();
            break;
        case 14:
            $respuesta = listadoPersonalizado( $vars );
            break;
        case 15:
            $respuesta = borraTelefonoAsignado( $vars );
            break;
        case 16:
            $respuesta = editaTelefonoAsignado( $vars );
            break;
        case 17:
            $respuesta = actualizaTelefonoAsignado( $vars );
            break;
    }
    echo $respuesta;
}
/**
 * Hace la copia de seguridad
 * 
 * @return string $nombre_copia
 */
function hazBackup ()
{
    $stamp = date( "dmyHis" );
    //$ruta = "/Applications/MAMP/Library/bin/";//para mac
    $ruta = 'C:\AppServ\MySQL\bin\\'; //para windows
    exec( 
    $ruta .
     'mysqldump.exe --opt --user=cni --password=inc centro > ../copias/copia' .
     $stamp . '.sql' ); //windows
    //exec($ruta.'mysqldump --opt --user=cni --password=inc centro > ../copias/copia'.$stamp.'.sql');
    $nombre_copia = "<span class='avisok'>Copia Realizada</span>";
    return $nombre_copia;
}
/**
 * Restaura una copia de seguridad
 * 
 * @param string $archivo
 * @return string $nombre_copia
 */
function restaura ($archivo)
{
    //$ruta = "/Applications/MAMP/Library/bin/"; //para mac
    //$rutadir = "/Applications/MAMP/htdocs/cni/nueva/copias/".$archivo; //para mac
    $ruta = 'C:\AppServ\MySQL\bin\\'; //para windows
    $rutadir = 'C:\AppServ\www\cni\copias\\' . $archivo; //para windows
    exec( $ruta . "mysql.exe --user=cni --password=inc centro < " .
     $rutadir );
    //exec($ruta."mysql --user=cni --password=inc centro < ".$rutadir);
    $nombre_copia = "<span class='avisok'>Copia Restaurada</span>";
    //return $ruta.
    return $nombre_copia;
}
/**
 * Lista todas las copias de seguridad
 */
function listadoCopias ()
{
    //$ruta = '/Applications/MAMP/htdocs/cni/nueva/copias'; //para mac
    $ruta = 'C:\AppServ\www\cni\copias\\'; //para windows
    $i = 0;
    $cadena = "<table class='tabla'><tr><th colspan='3'>Listado de Copias Realizadas</th></tr>";
    if (($gestor = opendir( $ruta )) == true ) {
        while (false !== ($archivo = readdir( $gestor ))) {
            if ($archivo != "." && $archivo != ".." && $archivo != ".DS_Store") {
                $i ++;
                $clase = ( $i % 2 == 0) ? "par" : "impar";
                     //vamos a tratar el nombre para que salga de otra manera
                //el formato de fichero es copiaddmmaahhmmss.sql
                $nombre = substr( 
                $archivo, 5, 2 ) . "/" . substr( $archivo, 7, 2 ) . "/" .
                 substr( $archivo, 9, 2 ) . "-" . substr( $archivo, 11, 2 ) . ":" .
                 substr( $archivo, 13, 2 ) . ":" . substr( $archivo, 15, 2 );
                $cadena .= "<tr><td class='" . $clase . "'>" . $i .
                 "</td><td class='" . $clase . "'>" . $nombre .
                 "</td><td>
		   		<span class='boton' onclick=restaurar_backup('" .
                 $archivo .
                 "')>&nbsp;&nbsp;[R]Restaurar&nbsp;&nbsp;</span>
		   		<span class='boton' onclick=borrar_backup('" .
                 $archivo . "')>&nbsp;&nbsp;[B]Borrar&nbsp;&nbsp;</span>";
            }
        }
        closedir( $gestor );
        $cadena .= "</table>";
    }
    return $cadena;
}
/**
 * Borra la copia de seguridad
 * 
 * @param string $archivo
 */
function borraBackup ($archivo)
{
    //$ruta = '/Applications/MAMP/htdocs/cni/nueva/copias/'.$archivo; //Para mac
    $ruta = 'C:\AppServ\www\cni\copias\\' . $archivo; //para windows
    //$comando = "rm"; //para mac y linux
    $comando = "del"; //para windows
    exec( $comando . " " . $ruta );
    //return $comando." ".$ruta;
    return "<span class='avisok'>Copia Borrada</span>";
}
/**
 * Revisa todas las tablas de la base de datos
 * 
 * @return string $cadena;
 */
function revisaTablas ()
{
    global $con;
    $sql = "show tables";
    $consulta = mysql_query( $sql, $con );
    while ( true == ($resultado = mysql_fetch_array( $consulta ) ) ) {
        $sql2 = "check table `$resultado[0]`";
        $consulta2 = mysql_query( $sql2, $con );
        $resultado2 = mysql_fetch_array( $consulta2 );
        $cadena .= "<br>Estado " . $resultado[0] . " ->" . $resultado2[3];
    }
    return $cadena;
}
/**
 * Repara las tablas
 * 
 * @return string $cadena
 */
function reparaTablas ()
{
    global $con;
    $sql = "show tables";
    $consulta = mysql_query( $sql, $con );
    while ( true == ($resultado = mysql_fetch_array( $consulta ) ) ) {
        $sql2 = "repair table `$resultado[0]`";
        $consulta2 = mysql_query( $sql2, $con );
        $resultado2 = mysql_fetch_array( $consulta2 );
        $cadena .= "<br>Estado " . $resultado[0] . " ->" . $resultado2[3];
    }
    return $cadena;
}
/**
 * Optimizacion de las tablas
 * 
 * @return string $cadena
 */
function optimizaTablas ()
{
    global $con;
    $sql = "show tables";
    $consulta = mysql_query( $sql, $con );
    while ( true == ($resultado = mysql_fetch_array( $consulta ))) {
        $sql2 = "optimize table `$resultado[0]`";
        $consulta2 = mysql_query( $sql2, $con );
        $resultado2 = mysql_fetch_array( $consulta2 );
        $cadena .= "<br>Estado " . $resultado[0] . " ->" . $resultado2[3];
    }
    return $cadena;
}
/**
 * Listado de categorias
 * 
 * @param array $vars
 * @return string $cadena
 */
function listadoCategorias ($vars)
{
    global $con;
    $tabla1 = "categoría servicios";
    $tabla2 = "categorías clientes";
    switch ($vars['categoria']) {
        case 1:
            $listado = $tabla1;
            $sql = "SELECT * FROM `" . $tabla1 . "` ";
            break;
        case 2:
            $listado = $tabla2;
            $sql = "SELECT * FROM `" . $tabla2 . "` ";
            break;
    }
    $consulta = mysql_query( $sql, $con );
    $cadena .= "<input type='hidden' id='categoria' value='" . $vars['categoria'] .
     "' />";
    $cadena .= "<table class='tabla'>";
    $cadena .= "<tr><th colspan='3'>Listado de " . ucfirst( $listado ) . "</th></tr>";
    $i = 0;
    while (true == ($resultado = mysql_fetch_array( $consulta ) ) ) {
        $i ++;
        $clase = ( $i % 2 == 0) ? "par" : "impar";
        $cadena .= "<tr class='" . $clase . "'>";
        $cadena .= "<td>" . $resultado[1]  . "</td><td>" .
         $resultado[2] . "</td>";
        $cadena .= "<td><span class='boton' onclick='editar_categoria(" .
         $resultado[0] . ")'>Editar</span></td></tr>";
    }
    $cadena .= "</table><div id='detalles_categoria'></div>";
    return $cadena;
}
/**
 * Muestra los detalles de la categoria
 * 
 * @param array $vars
 * @return string $cadena
 */
function detallesCategoria ($vars)
{
    global $con;
    $tabla1 = "categoría servicios";
    $tabla2 = "categorías clientes";
    switch ($vars['categoria']) {
        case 1:
            $sql = "SELECT * FROM `" . $tabla1 .
             "` where Id like " . $vars['registro'];
            break;
        case 2:
            $sql = "SELECT * FROM `" . $tabla2 .
             "` where Id like " . $vars['registro'];
            break;
    }
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    $cadena .= "<form id='formulario_categorias' onsubmit='actualiza_categoria(); return false'>";
    $cadena .= "<input type='hidden' id='categoria' name='categoria' value='" .
     $vars['categoria'] . "' />";
    $cadena .= "<input type='hidden' id='registro' name='registro' value='" .
     $vars['registro'] . "' />";
    $cadena .= "Categoria: <input type='text' name='Nombre' value='" .
     $resultado[1]  . "' size='40'/>";
    $cadena .= "<br/>Descripcion: <textarea name='descripcion' cols='35'>" .
     $resultado[2]  . "</textarea>";
    $cadena .= "<br/><input type='submit' name='Actualizar' value='Actualizar' class='boton' />";
    $cadena .= "</form>";
    return $cadena;
}
/**
 * Actualiza la categoria
 * 
 * @param array $vars
 */
function actualizaCategoria ( $vars )
{
    global $con;
    switch ( $vars['categoria'] ) {
        case 1:
            $tabla = "categoría servicios";
            break;
        case 2:
            $tabla = "categorías clientes";
            break;
    }
    if (isset( $vars['Actualizar'] )) {
        $sql = "Update `" . $tabla . "` set `Nombre` = '" . $vars['Nombre'] ."',  
        `Descripción` = '" . $vars['Descripcion'] ."' 
        where id like " . $vars['registro'];
    }
    if ( true == ($consulta = mysql_query( $sql, $con ) ) ) {
        $cadena = "todo ok";
    } else {
        $cadena = $sql . "," . $vars['Actualizar'] . "," . $vars['Borrar'];
    }
    foreach ($vars as $key => $valores) {
        $cadena .= $key . "=>" . $valores . "<p/>";
    }
    return $cadena;
}
/**
 * Listado telefonos del centro
 */
function listadoTelefonos ()
{
    global $con;
    $sql = "Select c.Nombre, z.valor from clientes as c join z_sercont as z 
    on c.id like z.idemp where servicio like 'Telefono' order by c.Nombre";
    $consulta = mysql_query( $sql, $con );
    $cadena = "<input class='boton' value='[X] Cerrar' onclick='cierra_listado_copias()' ><table><tr>";
    $columnas = '4';
    for ($i = 1; $i <= $columnas; $i ++) {
        $cadena .= "<th class='impar'>Cliente</th><th class='par'>Telefono</th>";
    }
    $cadena .= "</tr><tr>";
    $i = 0;
    while (true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
        if ($i % $columnas == 0) {
            $cadena .= "</tr><tr>";
        }
        $cadena .= "<td class='impar'>" . $resultado[0]  .
         "</td><td class='par'>" . $resultado[1] . "</td>";
        $i ++;
    }
    $cadena .= "</tr></table>";
    $cadena .= listadoIp();
    return $cadena;
}
/**
 * Listado de Ip's del Centro
 * 
 * @return string $cadena
 */
function listadoIp ()
{
    global $con;
    $sql = "Select c.Nombre, z.valor from clientes as c join z_sercont as z 
    on c.id like z.idemp where servicio like 'Direccion IP' order by c.Nombre";
    $consulta = mysql_query( $sql, $con );
    $cadena = "<table><tr>";
    $columnas = '4';
    for ($i = 1; $i <= $columnas; $i ++) {
        $cadena .= "<th class='impar'>Cliente</th><th class='par'>Direccion IP</th>";
    }
    $cadena .= "</tr><tr>";
    $i = 0;
    while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
        if ($i % $columnas == 0) {
            $cadena .= "</tr><tr>";
        }
        $cadena .= "<td class='impar'>" . $resultado[0]  .
         "</td><td class='par'>" . $resultado[1] . "</td>";
        $i ++;
    }
    $cadena .= "</tr></table>";
    return $cadena;
}
/**
 * Muestra el formularo para agregar los numeros de telefon del centro
 * Mostrara los que estan asignados, pudiendo desasisgnarlos y dejara agregar
 * nuevos telefonos, modificar los existentes y borro
 * 
 * @return $cadena;
 */
function formularioTelefonos ()
{
    $cadena = "<form class='formulario' id='frm_agrega_telefono' 
    name='frm_agrega_telefono' onsubmit='agrega_telefono();return false' 
    method='post'>
    Telefono:<input type='text' name='numero_telefono' size='12'/>
    <input type='submit' class='boton' name='agregar' value='[+]Agregar Telefono' />
    </form>
    <div id='mensajes_estado'></div>";
    $cadena .= listadoTelefonosCentro();
    return $cadena;
}
/**
 * Lista los telefonos del centro
 * 
 * @return $cadena;
 */
function listadoTelefonosCentro ()
{
    global $con;
    $centro = array("976 30 11 82", "976 79 43 60", "976 79 43 61", 
        "976 79 43 62");
    $asignados_despacho_telefono = array();
    $asignados_despacho_adsl = array();
    $asignados_despacho_fax = array();
    $asignados_telefono = array();
    $asignados_adsl = array();
    $asignados_fax = array();
    $no_asignados = array();
    $sql = "select DISTINCT direccion from telipext where tipo like 'telefono'";
    $consulta = mysql_query( $sql, $con );
    $asignados = array();
    $no_asignados = array();
    while ( true == ($resultado = mysql_fetch_array( $consulta ) ) ) {
        //Aqui comparo las de la base con las que tengo asignadas
        //el telefono en telipext esta siempre 976 12 34 56
        //en la base puede estar asi o no
        //quito los espacios en blanco
        $teljunto = "";
        $telefono = explode( " ", $resultado['direccion'] );
        foreach ($telefono as $tele) {
            $teljunto .= $tele;
        }
        $sql2 = "Select * from z_sercont where valor 
        like '" . $resultado['direccion'] ."%' or valor like '" . $teljunto . "%' ";
        $consulta2 = mysql_query( $sql2, $con );
        if (mysql_numrows( $consulta2 ) >= 1) {
            while (true == ($resultado2 = mysql_fetch_array( $consulta2 ))) {
                $tipo = categoriaDelCliente( $resultado2['idemp'] );
                //echo "<p/>".$resultado[direccion]."-".$tipo."-".$resultado2[servicio];
                if ($tipo == "OK") {
                    switch ($resultado2['servicio']) {
                        case "Telefono":
                            $asignados_despacho_telefono[] = $resultado['direccion'] .
                             "-" . $resultado2['idemp'];
                            break;
                        case "Adsl":
                            $asignados_despacho_adsl[] = $resultado['direccion'] .
                             "-" . $resultado2['idemp'];
                            break;
                        case "Fax":
                            $asignados_despacho_fax[] = $resultado['direccion'] .
                             "-" . $resultado2['idemp'];
                            break;
                    }
                } else {
                    switch ($resultado2['servicio']) {
                        case "Telefono":
                            $asignados_telefono[] = $resultado['direccion'] . "-" .
                             $resultado2['idemp'];
                            break;
                        case "Adsl":
                            $asignados_adsl[] = $resultado['direccion'] . "-" .
                             $resultado2['idemp'];
                            break;
                        case "Fax":
                            $asignados_fax[] = $resultado['direccion'] . "-" .
                             $resultado2['idemp'];
                            break;
                    }
                }
            }
        } else {
            $no_asignados[] = $resultado['direccion'];
        }
    }
    sort( $asignados_despacho_telefono );
    sort( $asignados_despacho_adsl );
    sort( $asignados_despacho_fax );
    sort( $asignados_telefono );
    sort( $asignados_adsl );
    sort( $asignados_fax );
    sort( $no_asignados );
    //Nuevo diseño
    //$cadena.="<div id='tabla_asignacion_telefonos'>";
    //Telefonos de Despachos
    $cadena .= "<input class='boton' value='[X] Cerrar' onclick='cierra_listado_copias()' ><br><div class='tabla'><div class='listado_1'>Telefonos Despachos</div>";
    $i = 0;
    foreach ($asignados_despacho_telefono as $despacho_telefono) {
        $asignacion = explode( "-", $despacho_telefono );
        $i ++;
        $clase = ( $i % 2 == 0) ? "par" : "impar";
        $cadena .= "<div class='" . $clase . "'>" . $asignacion[0] .
         "&nbsp;&nbsp;&nbsp;&nbsp;" . 
        nombreCliente( $asignacion[1] ) . "</div>";
    }
    $cadena .= "</div>";
    //Fin telefonos despachos
    //Telefonos Domiciliados
    $cadena .= "<div class='tabla'><div class='listado_2'>Telefonos Domiciliados</div>";
    $i = 0;
    foreach ($asignados_telefono as $despacho_telefono) {
        $asignacion = explode( "-", $despacho_telefono );
        $i ++;
        $clase = ( $i % 2 == 0) ? "par" : "impar";
        $cadena .= "<div class='" . $clase . "'>" . $asignacion[0] .
         "&nbsp;&nbsp;&nbsp;&nbsp;" .  
        nombreCliente( $asignacion[1] ) . "</div>";
    }
    $cadena .= "</div>";
    //Fin telefonos Domiciliados
    //Telefonos del centro
    $cadena .= "<div class='tabla'><div class='listado_3'>Telefonos del Centro</div>";
    $i = 0;
    foreach ($no_asignados as $no_asignado) {
        $i ++;
        $clase = ( $i % 2 == 0) ? "par" : "impar";
        if (! in_array( $no_asignado, $centro )) {
            $libres[] = $no_asignado;
        } else {
            $cadena .= "<div class='" . $clase . "'>" . $no_asignado .
             "&nbsp;&nbsp;&nbsp;&nbsp;" . descripcionTelefono( $no_asignado ) .
             "</div>";
        }
    }
    $cadena .= "</div>";
    //Fin telefonos Domiciliados
    //telefonos Libres
    $cadena .= "<div class='tabla'><div class='listado_4'>Telefonos Libres</div>";
    $i = 0;
    // NO se de donde sale opcion
    //if ($opcion == 0) {
    if (is_array( $libres )) {
        foreach ( $libres as $libre ) {
            $i ++;
            $clase = ( $i % 2 == 0) ? "par" : "impar";
            $cadena .= "<div class='" . $clase .
             "'><img src='iconos/edittrash.png' alt='Borrar telefono' 
             onclick='javascript:borrar_telefono_asignado(\"" .
             $libre .
             "\")'>&nbsp;<img src='iconos/kate.png' alt='Editar telefono' 
             onclick='javascript:editar_telefono_asignado(\"" .
             $libre . "\")'>&nbsp;" . $libre .
             "&nbsp;&nbsp;&nbsp;&nbsp;<span id='edicion_" . $libre . "'>" .
             descripcionTelefono( $libre ) . "</span></div>";
        }
    }
    //}
    $cadena .= "</div>";
    //Fin telefonos libres
    //Faxes de Despachos
    $cadena .= "<div class='tabla'><div class='listado_1'>Faxes Despachos</div>";
    $i = 0;
    foreach ($asignados_despacho_fax as $despacho_telefono) {
        $asignacion = explode( "-", $despacho_telefono );
        $i ++;
        $clase = ( $i % 2 == 0) ? "par" : "impar";
        $cadena .= "<div class='" . $clase . "'>" . $asignacion[0] .
         "&nbsp;&nbsp;&nbsp;&nbsp;" .  
        nombreCliente( $asignacion[1] )  . "</div>";
    }
    $cadena .= "</div>";
    //Fin telefonos despachos
    //Faxes Domiciliados
    $cadena .= "<div class='tabla'><div class='listado_2'>Faxes Domiciliados</div>";
    $i = 0;
    foreach ($asignados_fax as $despacho_telefono) {
        $asignacion = explode( "-", $despacho_telefono );
        $i ++;
        $clase = ( $i % 2 == 0) ? "par" : "impar";
        $cadena .= "<div class='" . $clase . "'>" . $asignacion[0] .
         "&nbsp;&nbsp;&nbsp;&nbsp;" . 
        nombreCliente( $asignacion[1] )  . "</div>";
    }
    $cadena .= "</div>";
    //Adsl de Despachos
    $cadena .= "<div class='tabla'><div class='listado_1'>Adsl Despachos</div>";
    $i = 0;
    foreach ($asignados_despacho_adsl as $despacho_telefono) {
        $asignacion = explode( "-", $despacho_telefono );
        $i ++;
        $clase = ( $i % 2 == 0) ? "par" : "impar";
        $cadena .= "<div class='" . $clase . "'>" . $asignacion[0] .
         "&nbsp;&nbsp;&nbsp;&nbsp;" . 
        nombreCliente( $asignacion[1] )  . "</div>";
    }
    $cadena .= "</div>";
    //Fin adsl despachos
    //Adsl Domiciliados
    $cadena .= "<div class='tabla'><div class='listado_2'>Adsl Domiciliados</div>";
    $i = 0;
    foreach ($asignados_adsl as $despacho_telefono) {
        $asignacion = explode( "-", $despacho_telefono );
        $i ++;
        $clase = ( $i % 2 == 0) ? "par" : "impar";
        $cadena .= "<div class='" . $clase . "'>" . $asignacion[0] .
         "&nbsp;&nbsp;&nbsp;&nbsp;" .  
        nombreCliente( $asignacion[1] )  . "</div>";
    }
    $cadena .= "</div>";
    $cadena .= "<div class='tabla'><div class='listado_2'>Listado de IP's</div>";
    $cadena .= consultaDeIps();
    $cadena .= "</div></div></div><p/><p/>";
    return $cadena;
}
/**
 * Devuelve el nombre del cliente
 * 
 * @param string $id
 * @return string;
 */
function nombreCliente ($id)
{
    global $con;
    $sql = "Select Nombre from clientes where id like " . $id;
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    return $resultado[0];
}
/**
 * Devuelve la categoria del cliente
 * 
 * @param string $id
 * @return string $valor;
 */
function categoriaDelCliente ($id)
{
    global $con;
    $sql = "Select Categoria from clientes where id like " . $id;
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    switch ($resultado[0]) {
        case "Clientes despachos":
            $valor = 'OK';
            break;
        default:
            $valor = 'KO';
            break;
    }
    return $valor;
}
/**
 * Muestra la descripcion del telefono
 * 
 * @param string $telefono
 * @return string
 */
function descripcionTelefono ($telefono)
{
    global $con;
    $sql = "Select descripcion from telipext where direccion like '" . $telefono ."'";
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    return $resultado[0];
}
/**
 * Agrega el telefono
 * 
 * @param array $vars
 * @return string
 */
function frmAgregaTelefono ($vars)
{
    global $con;
    $sql = "Insert into telipext (tipo,direccion,asignada) values 
    ('telefono','" . $vars['numero_telefono'] ."','No')";
    if ( true == ($consulta = mysql_query( $sql, $con ))) {
        $texto = "Telefono Agregado";
    } else {
        $texto = "No se ha agregado el telefono";
    }
    return $texto;
}
//LISTADO ESPECIAL ¡¡TARDA MUCHO EN GENERARSE!!
/**
 * Listado Especial, Tarda mucho en generarse
 * 
 * @return string $cadena;
 */
function rarita ()
{
    global $con;
    $sql = "SELECT DISTINCT(z.valor), c.Nombre, c.Categoria, 
    f.observaciones FROM `facturacion` 
	as f join clientes as c on c.id like f.idemp join 
	z_sercont as z on z.idemp like 
	c.id WHERE  Estado_de_cliente != 0 and 
	(c.Categoria like '%domiciliac%' or c.Categoria like '%despacho%' or 
	c.Categoria like 'Otros' or c.Categoria like '%Telefonica%' or 
	c.Categoria like '%oficina movil%') and
	z.servicio like 'Codigo Negocio' order by z.valor asc";
    $consulta = mysql_query( $sql, $con );
    $cadena = "<input class='boton' value='[X] Cerrar' 
    onclick='cierra_listado_copias()' ><input type='button' class='boton' 
    onclick=window.open('inc/excel.php') value='Imprimir' />";
    $k = 0;
    $cadena .= "<table width='100%' class='tabla'>";
    $cadena .= "<tr><th>Codigo</th><th>Cliente</th><th>Categoria</th>
    <th>Observaciones</th></tr>";
    while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
        $color = ( preg_match( "/despacho/", $resultado[2] ) ) ? "#69C" : "#F90";
        $cadena .= "<tr><td bgcolor='" . $color .
         "'><font color='#fff' size='2'><b>" . $resultado[0] .
         "</b></font></td><td class='" . clase( $k ) . "'>" .
         $resultado[1] . "</td><td class='" . clase( $k ) . "'>" .
         $resultado[2] . "</td><td class='" . clase( $k ) . "'>" .
         $resultado[3] . "</td></tr>";
        $k ++;
    }
    $cadena .= "</table>";
    return $cadena;
}
/**
 * Funcion que genera el listado de ips asignadas y libres
 */
function consultaDeIps ()
{
    global $con;
    $j = 0;
    $k = 0;
    $sql = "Select c.Nombre,z.valor from z_sercont as z join clientes as 
    c on z.idemp like c.id where z.servicio like 'Direccion IP' order by z.valor";
    $consulta = mysql_query( $sql, $con );
    while ( true == ($resultado = mysql_fetch_array( $consulta ) ) ) {
        $ipes = explode( ".", $resultado['valor'] );
        $ocupadas[intval( $ipes[3] )] = $resultado[0];
    }
    for ($i = 1; $i <= 254; $i ++) {
        $clase = ($i % 2 == 0) ? "par" : "impar";
        if ($ocupadas[$i] != "") {
            $j ++;
            $clase = ($j % 2 == 0) ? "par" : "impar";
            $cogidas .= "<div class='" . $clase . "'>172.26.0." . $i .
             "&nbsp;&nbsp;&nbsp;&nbsp;" . $ocupadas[$i] . "</div>";
        } else {
            $k ++;
            $clase = ($k % 2 == 0) ? "par" : "impar";
            $no_cogidas .= "<div class='" . $clase . "'>172.26.0." . $i .
             "&nbsp;&nbsp;&nbsp;&nbsp;</div>";
        }
    }
    return $cogidas . "-" . $no_cogidas;
}
/**
 * Muestra el listado Personalizado
 * 
 * @param array $vars
 * @return string $cadena
 */
function listadoPersonalizado ($vars)
{
    global $con;
    //buscamos el nombre de la categoria_del_cliente
    $tabla = "categorías clientes";
    if ($vars['tipo'] == 'social') {
        $sql = "Select * from clientes where direccion 
        not like '' and Estado_de_cliente like '-1' order by Nombre";
    } else {
        if ($vars['tipo'] == 'comercial') {
            $sql = "Select * from clientes where 
            dcomercial != '' and Estado_de_cliente like '-1' order by Nombre";
        } else {
            if ($vars['tipo'] == 'conserje'){
                $sql = "Select * from clientes where 
                (categoria like '%domicili%' or categoria like '%despachos%' 
                or categoria like '%tencion telefo%') 
                and Estado_de_cliente like '-1' order by Nombre";
            } else { 
                if ($vars['tipo'] == 'independencia'){
                    $sql = "Select * from clientes where direccion 
                    like '%Independencia, 8 dpdo%' 
                    and Estado_de_cliente like '-1' order by Nombre";
                } else {
                    $sql = "Select * from clientes as c 
                    join `" . $tabla ."` as d on c.Categoria = d.Nombre 
                    where d.id like " . $vars['tipo'] . " and 
                    c.Estado_de_cliente like '-1' order by c.Nombre";
                }
            }
        }
    }
    $consulta = mysql_query( $sql, $con );
    $i = 0;
    while ( true == ($resultado = mysql_fetch_array( $consulta ))) {
        $clase = ($i % 2 == 0) ? 'listado_par' : 'listado_impar';
        $i ++;
        $cadena .= "<div class='" . $clase . "'>" . $i . " " .
         $resultado[1] . "<span class='direccion_esp'> " .
         $resultado['Direccion'] . "</span></div>";
         //$i++;
    }
    $cadena .= "<div><input type='button' class='boton' 
    onclick=window.open('inc/excel.php?tipo=" . $vars['tipo'] ."') value='Imprimir' />
    </div>";
    return $cadena;
}
/**
 * Borra el telefono Asignado
 * 
 * @param array $vars
 * @return string
 */
function borraTelefonoAsignado ($vars)
{
    global $con;
    $sql = "Delete from telipext where direccion like '" . $vars['telefono'] . "'";
    if ( true == ($consulta = mysql_query( $sql, $con ) ) ) {
        return "Telefono Borrado" . $sql;
    } else {
        return "No borrado" . $sql;
    }
}
/**
 * Edita la descripcion del telefono libre
 * 
 * @param array $vars
 * @return string $cadena
 */
function editaTelefonoAsignado ($vars)
{
    global $con;
    $sql = "SELECT * FROM `telipext` WHERE direccion LIKE '" . $vars['telefono'] ."'";
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    $cadena = "<input type='text' id='descripcion_" . $vars['telefono'] .
     "' value='" . $resultado['descripcion'] .
     "'><input type='hidden' id='identificador_" . $vars['telefono'] . "' value='" .
     $resultado['id'] .
     "'><input type='button' onclick='actualiza_descripcion_telefono(\"" .
     $vars['telefono'] . "\")' value='Actualizar'>";
    return $cadena;
}
/**
 * Actualiza la descripcion del telefono libre
 * 
 * @param array $vars
 * @return bool
 */
function actualizaTelefonoAsignado ($vars)
{
    global $con;
    $sql = "Update `telipext` set descripcion = '" . $vars['descripcion'] . "' 
    where id like " . $vars['id'];
    if (true == ($consulta = mysql_query( $sql, $con ))) {
        return true;
    } else {
        return false;
    }
}