<?php
/**
 * Generator File Doc Comment
 * 
 * Pagina Generadora de distintos aspectos de la aplicacion
 * Original de Marzo de 2007
 * 
 * PHP Version 5.2.6
 * 
 * @category Generator
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once 'variables.php';
$opciones = array(0 => 'generador', 1 => 'cuca', 2 => 'formulario', 
3 => 'subformulario', 4 => 'actualiza', 5 => 'nuevo', 6 => 'agregaRegistro', 
7 => 'borraRegistro', 8 => 'frmSrvFijos', 9 => 'cambiaLosOtros', 
10 => 'agregaSrvFijo', 11 => 'borraSrvFijo', 12 => 'actualizaSrvFijo');
$respuesta = "";
if (isset( $_POST['opcion'] )) {
    $vars = $_POST;
    array_walk( $vars, 'sanitize' );
    $respuesta = $opciones[$_POST['opcion']]( $vars );
}
echo $respuesta;
/**
 * Generador de la pagina principal de gestion
 * 
 * @param array $vars
 */
function generador ($vars)
{
    global $con;
    $tabla = "";
    if ($vars['codigo'] == 6) {
        $tabla .= "
		<div class='gestion_app'>
		Gesti&oacute;n de Base de Datos:
		<span class='boton' onclick='hacer_backup()'>
		&nbsp;&nbsp;[H]Hacer copia&nbsp;&nbsp;</span>
		<span class='boton' onclick='lista_backup()'>
		&nbsp;&nbsp;[L]Listado de Copias realizadas&nbsp;&nbsp;</span>";
        $tabla .= "</div>
		<div class='gestion_app'>
		Datos Categorias:
		<span class='boton' onclick='categorias(1)'>Categorias Servicios</span>
		<span class='boton' onclick='categorias(2)'>Categorias Clientes</span>
		</div>
		
		<div class='gestion_app'>
		Telefonos Centro: 
		<span class='boton' onclick='formulario_telefonos()'>
		&nbsp;&nbsp;Gestion Telefonos Centro &nbsp;&nbsp;</span>
		</div>
		<div class='gestion_app'>
		Listado Despachos y Domiciliados:
		<input type='button' class='boton' onclick='consulta_especial()' 
		value='Ver Listado Completo' />
		<p><label>Ver listado filtrado de:</label>
		" . listadoCategorias() . "
		</p>
		</div>
		<div id='listado_copias'></div>
		<div id='estado_copia'></div>
		<div id='status_tablas'></div>
		</center>";
    } else {
        $sql = "Select pagina from menus where id like " . $vars['codigo'];
        $consulta = mysql_query( $sql, $con );
        $resultado = mysql_fetch_array( $consulta );
        $tabla .= "<div id='botoneria'>";
        $tabla = "&nbsp;&nbsp;<span class='titulo_categoria'>
		Seleccione " .
         ucfirst( $resultado[0] ) . ":</span>";
        $tabla .= "<input type='hidden' id='tabla' 
		value='" . $resultado[0] . "' />";
        $tabla .= "<input type='hidden' id='nuevo' 
		value='" . $vars['codigo'] . "' />";
        $tabla .= "<input type='text' id='texto' autocomplete='off' 
		onkeyup='busca()'/>&nbsp;<input class='boton' type='submit' 
		onclick='busca()' value='[M]Mostrar Busqueda'>";
        $tabla .= "&nbsp;<input class='boton' type='submit' 
		onclick='nuevo(" . $vars['codigo'] . ")' 
		value='[+] Nuevo " .
         ucfirst( $resultado[0] ) . "'>";
        if ($vars['codigo'] == 1) {
            $tabla .= "&nbsp;<input class='boton' type=submit 
			onclick=popUp('servicont/index.php') value = 'Estadisticas Servicios' />";
            $tabla .= "&nbsp;<input class='boton' type=submit 
			onclick=popUp('rapido/index.php') value = 'Asignacion de Servicios' />";
            $tabla .= "&nbsp;<input class='boton' type=submit 
			onclick=popUp('almacen/index.php') value = 'Almacenaje' />";
            $tabla .= "&nbsp;<input class='boton' type=submit 
			onclick=popUp('agenda/index.php') value = 'Agenda' />";
            $tabla .= "&nbsp;<input class='boton' type=submit 
			onclick=popUp('entradas/index.php') value = 'Entradas' />";
        }
        $tabla .= "</div>";
    }
    return $tabla;
}
/**
 * Funcion de buscador en la pagina de categoria
 * 
 * @param array $vars
 */
function cuca ($vars)
{
    global $con;
    $i = 0;
    $muestra = "";
    if ($vars['texto'] == "") {
        $muestra = "";
    } else {
        if ($vars['tabla'] == 'clientes') {
            $sql = "Select * from `" . $vars['tabla'] . "` 
		    where Nombre like '%" . $vars['texto'] . "%' 
		    or Contacto like '%" .
             $vars['texto'] . "%' order by Nombre";
        } else {
            $sql = "Select * from `" . $vars['tabla'] . "` 
		    where Nombre like '%" .
             $vars['texto'] . "%' order by Nombre";
        }
        $consulta = mysql_query( $sql, $con );
        $muestra .= "<input class='boton' type='button' 
		onclick='cierra_frm_busca()' value='[X]Cerrar'>";
        while (true == ($resultado = mysql_fetch_array( $consulta ))) {
            $i ++;
            $clase = ($i % 2 == 0) ? "pares" : "impares";
            $muestra .= "<div class='" . $clase . "'>
			<a href='javascript:muestra(" . $resultado[0] . ")' >
			" . preg_replace( 
            "/" . $vars['texto'] . "/", 
            "<span class='resalta'>" . strtoupper( $vars['texto'] ) . "</span>", 
            $resultado[1] ) . "</a></div>";
        }
    }
    return $muestra;
}
/**
 * Funcion que genera el color de la cabezera dependiendo del tipo de cliente
 * 
 * @param string $tabla
 * @param array $vars
 */
function colorCabezera ($tabla, $vars)
{
    switch ($tabla) {
        case "clientes":
            if (preg_match( "/despacho/", $vars['Categoria'] )) {
                $color = "#6699CC";
            } elseif (preg_match( "/domicili/", $vars['Categoria'] )) {
                $color = "#FF9900";
            } else {
                $color = "#7d0063";
            }
            break;
        default:
            $color = "#7d0063";
            break;
    }
    return $color;
}
/**
 * Devuelve el codigo de negocio
 * 
 * @param string $idemp
 * @return string
 */
function codigoNegocio ($idemp = null)
{
    global $con;
    $cadena = "";
    if ($idemp != null) {
        $sql = "Select * from z_sercont where idemp 
    	like " . $idemp .
         " and servicio like 'Codigo Negocio'";
        $consulta = mysql_query( $sql, $con );
        if (mysql_numrows( $consulta ) >= 0) {
            $resultado = mysql_fetch_array( $consulta );
            $cadena = "<font size='6'>" . $resultado['valor'] . "</font>";
        } else {
            $cadena = "";
        }
    }
    return $cadena;
}
/**
 * Generacion del formulario dependiendo de la seccion en la que estamos
 * 
 * @param array $vars
 */
function formulario ($vars)
{
    global $con;
    $desvio = "";
    $sql = "Select * from `" . $vars['tabla'] . "` where id like " .
     $vars['registro'];
    $consulta = mysql_query( $sql, $con );
    $numero_campos = mysql_num_fields( $consulta );
    $resultado = mysql_fetch_array( $consulta );
    $cadena = "
	<form id='formulario_actualizacion' action='#' method='post' 
	onSubmit='actualiza_registro(); return false'>
	<input type='hidden' id='opcion' value='0' />
	<input type='hidden' id='idemp' value='" .
     $resultado[0] . "' />
	<table cellpadding=0px cellspacing=1px class='formulario'>";
    // Cabezera nombre de empresa, desvio y activo y menu
    if ($vars['tabla'] == 'clientes') {
        $desvio = desvioActivo( $resultado['desvio'], 
        $resultado['Estado_de_cliente'], $resultado['extranet'], 
        $vars['registro'] );
    }
    $color_cabezera = colorCabezera( $vars['tabla'], $resultado );
    $cadena .= "<th height='24px' bgcolor='" . $color_cabezera . "' 
		color='#fff' align='left' width='100px'>
		<div id='edicion_actividad'></div>";
    $cadena .= $desvio . "</th><th height='24px' align='left' 
	bgcolor='" . $color_cabezera . "' colspan='2'>
	<font size='4'>" . $resultado['Nombre'] . " 
	" .
     codigoNegocio( $resultado['Id'] ) . "</font>
	<input type='hidden' name='nombre_tabla' id='nombre_tabla' 
	value='" . $vars['tabla'] . "' />
	<input type='hidden' name='numero_registro' id='numero_registro' 
	value='" . $resultado[0] . "' /></th>
	<th align='right' bgcolor='" . $color_cabezera . "'>
	<input class='boton' onclick='cierra_el_formulario()' value='[X] Cerrar' >
	</th></tr>";
    //submenus
    $cadena .= submenus( $vars );
    //Fin de los submenus
    //campo oculto con nombre de tabla
    for ($i = 1, $j = 0; $i <= $numero_campos - 1; $i ++, $j ++) //si empiezo desde 1 me salto el id, pero no el idepm
{
        if ($j % 2 == 0) {
            $cadena .= "</tr><tr>";
        }
        $cadena .= "<th align='left' valign='top' class='nombre_campo'>
		" .
         nombreCampo( mysql_field_name( $consulta, $i ), $vars['tabla'] ) . "
		</th>
		<td align='left' valign='top' class='valor_campo'>
		" . tipoCampo( mysql_field_name( $consulta, $i ), 
        $vars['tabla'], $resultado[$i], 'actualiza', $i ) . "</td>";
    }
    $cadena .= "</tr>";
    if (isset( $vars['principal'] )) {
        $cadena .= "<tr><th colspan='4' align='center'>
		<input class='boton' type='submit'  value='[+] Agregar' />";
        $cadena .= "<input class='boton' type='reset'  
		value='[L] Limpiar formulario' /></th></tr>";
    } else {
        $cadena .= "<tr><th colspan='4' align='center'><p/>
		<input class='boton' type='submit' 
		value='[*]Actualizar Datos' tabindex='" . $numero_campos .
         "'/>";
        $cadena .= "<input type='button' class='boton' 
		onclick='borrar_registro(" . $resultado[0] . ")' 
		value='[X]Borrar Datos' tabindex='" .
         $numero_campos . "'/></th></tr>";
    }
    $cadena .= "</table></form>";
    return $cadena;
}
/**
 * Devuelve el nombre del campo
 * 
 * @param string $campo
 * @param string $tabla
 */
function nombreCampo ($campo, $tabla)
{
    global $con;
    $sql = "Select campof from alias where tabla like '" . $tabla . "' 
	and `campoo` like '" . $campo . "'";
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    return $resultado[0];
}
//***********************************************************************************************/
//funciton tipo_campo(nombre_campo,nombre_tabla,valor_campo,opcion,orden_en_la_lista)
//***********************************************************************************************/
/**
 * Genera el campo en el formulario
 * 
 * @param string $campo
 * @param string $tabla
 * @param string $valor
 * @param string $opcion
 * @param string $orden
 */
function tipoCampo ($campo, $tabla, $valor, $opcion, $orden)
{
    global $con;
    $cadena = "";
    $i = 1;
    $sql = "Select * from alias where tabla like '" . $tabla . "' 
	and `campoo` like '" . $campo . "'";
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    switch ($resultado['tipo']) {
        case "text": //caso rarito de z_sercont valor
            if (($tabla == 'z_sercont') &&
             ($resultado['campoo'] == 'valor')) {
                $cadena = "<div id='tipo_teleco'><input type='text' size='" .
                 $resultado['size'] . "' id='" . $resultado['variable'] .
                 "' name='" . $resultado['campoo'] . "' value='" . $valor .
                 "' tabindex='" . $i . "' onkeyup='chequea_valor()'/></div>";
            } else {
                $cadena = "<input type='text' size='" . $resultado['size'] .
                 "' id='" . $resultado['variable'] . "' name='" .
                 $resultado['campoo'] . "' value='" . $valor . "' tabindex='" .
                 $i . "'/>";
            }
            break;
        case "textarea":
            $cadena = "<textarea id='" . $resultado['variable'] . "' name='" .
             $resultado['campoo'] . "' rows='" . $resultado['size'] .
             "' cols='46' tabindex='" . $i . "'>" . $valor . "</textarea>";
            break;
        case "checkbox":
            $chequeado = ($valor != 0) ? 'checked' : '';
            $cadena = "<input  type='checkbox' id='" . $resultado['variable'] .
             "' " . $chequeado . " name='" . $resultado['campoo'] .
             "' tabindex='" . $i . "'/>";
            break;
        case "date":
            $cadena = "<input type='text' id='" . $resultado['variable'] .
             "' name='" . $resultado['campoo'] . "' size = '" .
             $resultado['size'] . "'  value='" . cambiaf( $valor ) .
             "' tabindex='" . $i . "'/>";
            $cadena .= "&nbsp;&nbsp;<button TYPE='button' class='calendario' id='f_trigger_" .
             $resultado['variable'] . "' tabindex='" . $i . "'></button>";
            break;
        case "select":
            $sql = "Select * from `" . $resultado['depende'] . "` order by 2";
            $consulta = mysql_query( $sql, $con );
            if ($tabla == 'z_sercont') {
                $cadena = "<select id='" . $resultado['variable'] . "' name='" .
                 $resultado['campoo'] . "' tabindex='" . $i .
                 "' onchange='muestra_campo()'>";
            } else {
                $cadena = "<select id='" . $resultado['variable'] . "' name='" .
                 $resultado['campoo'] . "' tabindex='" . $i . "'>";
            }
            $cadena .= "<option value='0'>-::" . $resultado['campoo'] .
             ":-</option>";
            while (true == ($resultado = mysql_fetch_array( $consulta ))) {
                $marcado = ($resultado[1] == $valor) ? 'selected' : '';
                $cadena .= "<option " . $marcado . " value='" . $resultado[1] .
                 "'>" . $resultado[1] . "</option>";
            }
            $cadena .= "</select> " . $valor;
            break;
        default:
            $cadena = $valor;
            break;
    }
    // Generamos el enlace de la web o del mail
    switch ($resultado['enlace']) {
        case "web":
            $cadena .= "<a href='http://" . $valor . "' target='_blank'>
             <img src='iconos/package_network.png' width='14' alt='Abrir Web'/>
             </a>";
            break;
        case "mail":
            $cadena .= "<a href='mailto:" . $valor . "'><img src='iconos/mail_generic.png' width='14' 
             alt='Enviar Correo'/></a>";
            break;
    }
    return $cadena;
}
/**
 * Muestra si el cliente tiene no no desvio activo
 * 
 * @param string $desvio
 * @param string $estado
 * @param string $extranet
 * @param string $cliente
 */
function desvioActivo ($desvio, $estado, $extranet, $cliente)
{
    $cadena = "";
    if ($estado == 0) { // Cliente activo o no
        $cadena = "<img src='imagenes/noactivo.gif' 
		alt='Cliente Inactivo' width='24px'/>";
    } else {
        $cadena = "<img src='imagenes/activo.gif' 
		alt='Cliente Activo' width='24px'/>";
    }
    if ($desvio == 0) { // Desvio activo o no
        $cadena .= "<img src='imagenes/desvioi.gif' 
		alt='Desvio Inactivo' width='24px'/>";
    } else {
        $cadena .= "<span class='popup' 
		onclick='ver_detalles(0,0,0," . $cliente . ")'>
		<img src='imagenes/nudesvioa.gif' alt='Desvio Activo' width='24px' />
		</span>";
    }
    if ($extranet == 0) { //Extranet activa o inactiva
        $cadena .= "<img src='imagenes/extraneti.gif' 
		alt='Extranet Inactivo' width='24px'/>";
    } else {
        $cadena .= "<span class='popup' 
		onclick='ver_detalles(0,0,1," . $cliente . ")'>
		<img src='imagenes/extraneta.gif' alt='Extranet Activa' width='24px' />
		</span>";
    }
    return $cadena;
}
//***********************************************************************************************/
//submenus(nombre_tabla):Genera el submenu si la tabla pasada lo tiene si no no lo genera
//***********************************************************************************************/
/**
 * Genera el submenu si la tabla pasada lo tiene si no no
 * 
 * @param string $tabla
 */
function submenus ($tabla)
{
    global $con;
    $cadena = "";
    $sql = "Select id from menus where pagina like '" . $tabla['tabla'] . "'";
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    $sql = "Select * from submenus where menu like " . $resultado[0];
    $consulta = mysql_query( $sql, $con );
    $cadena = "<tr><th colspan='4' width='100%'height='26px'><table><tr>";
    while (true == ($resultado = mysql_fetch_array( $consulta ))) {
        if ($resultado[2] == "Principal") {
            $cadena .= "<th><span class='boton' 
		    onclick='muestra(" . $tabla['registro'] . ")' >
		    " . $resultado[2] . "</span></th>";
        } else {
            $cadena .= "<th><span class='boton' 
		    onclick='submenu(" . $resultado[0] . ")' >
		    " . $resultado[2] . "</span></th>";
        }
    }
    $cadena .= "</tr></table></th></tr>";
    return $cadena;
}
/**
 * Muestra el listado de la tabla que le hemos pasado dentro del array
 * 
 * @param array $vars
 * @return string $cadena
 */
function listado ($vars)
{
    global $con;
    $sql = "Select * from `" . $vars['tabla'] . "` 
	where idemp like " . $vars['registro'];
    $consulta = mysql_query( $sql, $con );
    $totdatos = mysql_num_rows( $consulta );
    $tot_columnas = mysql_num_fields( $consulta );
    $cadena = "<table class='listado'><tr>";
    for ($i = 2; $i <= $tot_columnas - 1; $i ++) {
        $cadena .= "<th>" . ucfirst( mysql_field_name( $consulta, $i ) ) .
         "</th>";
    }
    $cadena .= "</tr>";
    if ($totdatos == 0) {
        $cadena .= "<tr><td colspan = '" . $tot_columnas . "' align='center'>
		No hay registros</td></tr>";
    } else {
        while (true == ($resultado = mysql_fetch_array( $consulta ))) {
            $cadena .= "<tr>";
            for ($i = 2; $i <= $tot_columnas - 1; $i ++) {
                $cadena .= "<td>" . $resultado[$i] . "</td>";
            }
            $cadena .= "</tr>";
        }
    }
    $cadena .= "</table>";
    return $cadena;
}
/**
 * Comprobacion y cambio de valor en los campos check
 * 
 * @param string $tabla
 * @param string $campo
 * @param string $valor
 */
function compruebaCheck ($tabla, $campo, $valor)
{
    global $con;
    $sql = "Select tipo from alias 
	where tabla like '" . $tabla .
     "' and campoo like '" . $campo . "'";
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    switch ($resultado[0]) {
        case "checkbox":
            $valor = ($valor == 'on') ? '-1' : '0';
            break;
        case "date":
            $valor = cambiaf( $valor );
            break;
    }
    return $valor;
}
/**
 * Actualizacion de registros
 * 
 * @param array $vars
 * @return string
 */
function actualiza ($vars)
{
    //todos los valores estan serializados en el formulario 2 nos importan el nombre_tabla y el numero_registro
    //el resto pueden entrar en el bucle
    global $con;
    $sql = "Select * from `" . $vars['nombre_tabla'] . "`";
    $consulta = mysql_query( $sql, $con );
    $totcamp = mysql_numfields( $consulta );
    $sql = "Update `" . $vars['nombre_tabla'] . "` set ";
    for ($i = 1; $i <= $totcamp - 1; $i ++) {
        $sql .= " `" . mysql_field_name( $consulta, $i ) . "` = '" . compruebaCheck( 
        $vars['nombre_tabla'], mysql_field_name( $consulta, $i ), 
        $vars[mysql_field_name( $consulta, $i )] ) . "',";
    }
    $longitud = strlen( $sql );
    $sql = substr( $sql, 0, $longitud - 1 );
    $sql .= " where id like " . $vars['numero_registro'];
    //REASIGNACION DE SERVICIOS
    foreach ($vars as $key => $variable) {
        $cadenita .= $key . "=>" . $variable . "<br>";
    }
    if (($vars['nombre_tabla'] == "clientes") &&
     (! isset( $vars['Estado_de_cliente'] ))) {
        //Chequeo de tabla para asignacion directa por codigo de negocio
        $sql2 = "Select * from z_sercont 
			where idemp like " . $vars['numero_registro'];
        //$cadenita.=$sql2;
        $consulta = mysql_query( $sql2, $con );
        if (mysql_numrows( $consulta ) != 0) {
            while (true == ($resultado = mysql_fetch_array( $consulta ))) {
                //tomamos valor del codigo de negocio
                if ($resultado['servicio'] == "Codigo Negocio") {
                    $cod_despacho = intval( $resultado['valor'] );
                }
            }
            if ($cod_despacho == 23) {
                $code = "JUNTAS";
            } else {
                $code = $cod_despacho;
            }
            $sql3 = "Select id from clientes 
			where nombre like 'LIBRE " . $code . "'";
            $consulta = mysql_query( $sql3, $con );
            $resultado = mysql_fetch_array( $consulta );
            $sql4 = "Update z_sercont set idemp=" . $resultado[0] . " 
			where idemp like " . $vars['numero_registro'];
            $consulta = mysql_query( $sql4, $con );
            $sql5 = "Delete from z_sercont 
			where idemp like " . $resultado[0] . " 
			and servicio like 'Codigo_Negocio'";
            $consulta = mysql_query( $sql5, $con );
        }
    }
    if (mysql_query( $sql, $con )) {
        return "<img src='" . OK . "' alt='Registro Actualizado' width='24'/> 
		Registro Actualizado &nbsp;&nbsp;<p/>" . $sql5;
    } else {
        return "<img src='" . NOK . "' alt='ERROR' width='24'/> 
		ERROR&nbsp;&nbsp;<p/> " . $sql;
    }
}
/**
 * Agregamos un nuevo registro
 * 
 * @param array $vars
 */
function nuevo ($vars)
{
    global $con;
    //pasamos el codigo necesito el nombre de tabla
    $sql = "Select pagina from menus where id like " . $vars['tabla'];
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    //consulta vacia para nombre de las cabezeras de la tabla
    $sql = "Select * from `" . $resultado[0] . "`";
    $consulta = mysql_query( $sql, $con );
    $numero_campos = mysql_num_fields( $consulta );
    //se queda aqui es lo necesario para los nombres de campo
    $cadena .= "<form id='formulario_alta' action='#' 
	onsubmit='agrega_registro(); return false'>
	<table cellpadding=0px cellspacing=1px class='formulario'><tr>";
    $cadena .= "<input type='hidden' id='opcion' value='0' />";
    $cadena .= "<th align='left' bgcolor='#ccc' colspan='3'>" .
     $resultado['Nombre'] . "
	<input type='hidden' name='nombre_tabla' 
	id='nombre_tabla' value='" . $resultado[0] . "' />
	</th></tr>";
    //Fin de los submenus
    for ($i = 1, $j = 0; $i <= $numero_campos - 1; $i ++, $j ++) {
        if ($j % 2 == 0) {
            $cadena .= "</tr><tr>";
        }
        $cadena .= "<th align='right' valign='top' bgcolor='#7d0063'>
		<font color='#ffffff'>" .
         nombreCampo( mysql_field_name( $consulta, $i ), $resultado[0] ) . "
		</font></th><td align='left' valign='top'>" .
         tipoCampo( mysql_field_name( $consulta, $i ), $resultado[0], '', 
        'nuevo', $i ) . "</td>";
    }
    $cadena .= "</tr>";
    //parte de la botoneria
    $cadena .= "<tr><th colspan='4' align='center'>
	<input class='boton' type='submit'  name='boton_envio' value='Agregar' />";
    $cadena .= "&nbsp;<input class='boton' type='reset' 
	value='Limpiar formulario' /></th></tr>";
    $cadena .= "</table></form>";
    return $cadena;
}
/**
 * Agregamos el registro
 * 
 * NOTA MENTAL:
 * Tenemos ahora unos clientes que empiezan por el nombre LIBRE y que hacen
 * referencia a los despachos libres, estos clientes tienen asignadas unas 
 * caracteristicas la cuales son agregadas al cliente cuando se le asigna
 * el codigo de negocio que representa ese despacho, por lo tanto tengo que
 * chequear si se esta agregando a z_sercont y si el valor que se agrega
 * es codigo de negocio, en tal caso se le agregaran todos los datos que
 * tiene ese despacho
 * 
 * @param array $vars
 * @return string
 */
function agregaRegistro ($vars)
{
    global $con;
    $sql = "Select * from `" . $vars['nombre_tabla'] . "`";
    $campos = mysql_query( $sql, $con );
    $total = mysql_num_fields( $campos );
    if ($vars['boton_envio'] == "Agregar") {
        $sql = "Insert into `" . $vars['nombre_tabla'] . "` (";
        for ($i = 1; $i <= $total - 1; $i ++) {
            $sql .= "`" . mysql_field_name( $campos, $i ) . "`,";
            $sql2 .= "'" . codifica( 
            compruebaCheck( $vars['nombre_tabla'], 
            mysql_field_name( $campos, $i ), 
            $vars[mysql_field_name( $campos, $i )] ) ) . "',";
        }
        $longitud = strlen( $sql );
        $longitud2 = strlen( $sql2 );
        $sql = substr( $sql, 0, $longitud - 1 ) . ") 
		values (" .
         substr( $sql2, 0, $longitud2 - 1 ) . ")";
        if (($vars['nombre_tabla'] == 'z_sercont') &&
         ($vars['servicio'] == 'Codigo Negocio')) {
            $code = intval( $vars['valor'] );
            if ($code == 23) {
                $code = "JUNTAS";
            }
            $sql2 = "Select id from clientes where Nombre like 'LIBRE " . $code .
             "'";
            $consulta = mysql_query( $sql2, $con );
            $resultado = mysql_fetch_array( $consulta );
            $code_cli = $resultado[0];
            $sql2 = "Select * from z_sercont where idemp like " . $resultado[0];
            $consulta = mysql_query( $sql2, $con );
            if (mysql_numrows( $consulta ) != 0) {
                $sql3 = "Update z_sercont set idemp=" . $vars['idemp'] . " 
				where idemp like " . $code_cli;
                $consulta = mysql_query( $sql3, $con );
            }
        }
        $tipo = "Agregado";
    } else {
        //Caso de la baja reasignamos sus datos al despacho
        $sql = "Update `" . $vars['nombre_tabla'] . "` set ";
        for ($i = 1; $i <= $total - 1; $i ++) {
            $sql .= " `" . mysql_field_name( $campos, $i ) . "` = '" . compruebaCheck( 
            $vars['nombre_tabla'], mysql_field_name( $campos, $i ), 
            $vars[mysql_field_name( $campos, $i )] ) . "',";
        }
        $longitud = strlen( $sql );
        $sql = substr( $sql, 0, $longitud - 1 ); //eliminamos la , final
        if (($vars['nombre_tabla'] == 'facturacion') ||
         ($vars['nombre_tabla'] == 'z_facturacion') ||
         ($vars['nombre_tabla'] == 'cfm') ||
         ($vars['nombre_tabla'] == 'tllamadas')) {
            $sql .= " where idemp like " . $vars['id'];
        } else {
            $sql .= " where id like " . $vars['id'];
        }
        $tipo = "Actualizado";
    }
    if (true == ($consulta = mysql_query( $sql, $con ))) {
        return "<img src='" . OK . "' alt='Registro " . $tipo . "' width='24'/> 
	    	Registro " . $tipo . "&nbsp;&nbsp;<p/>";
    } else {
        return "<img src='" . NOK . "' alt='ERROR' width='24'/> 
			ERROR&nbsp;&nbsp;<p/>";
    }
}
/**
 * Borra el registro seleccionado
 * 
 * @param array $vars
 */
function borraRegistro ($vars)
{
    global $con;
    $sql = "Delete from `" . $vars['tabla'] . "` 
	where id like " . $vars['registro'];
    if (true == ($consulta = mysql_query( $sql, $con ))) {
        return "<img src='" . OK . "' alt='Registro Borrado' width='24'/> 
		Registro Borrado&nbsp;&nbsp;<p/>";
    } else {
        return "<img src='" . NOK . "' alt='ERROR' width='24'/>
		ERROR&nbsp;&nbsp;<p/>";
    }
}
/**
 * Generacion del subformulario
 * 
 * @param array $vars
 * @return string
 */
function subformulario ($vars)
{
    global $con;
    $cadena = "";
    $sql = "Select s.pagina, m.pagina, s.listado,s.nombre 
	from submenus as s join menus as m on s.menu = m.id 
	where s.id like " . $vars['codigo'];
    //echo $sql;
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    $tabla = Array("tabla" => $resultado[1], "registro" => $vars['registro']);
    // 2 casos de subformularios, proveedores y clientes
    if (isset( $vars['tabla'] )) {
        switch ($vars['tabla']) {
            case "pproveedores":
                $busca = "Select c.id from proveedores 
			    as c join `" . $vars['tabla'] . "` as t 
			    on c.id = t.idemp where t.id like " .
                 $vars['registro'];
                break;
            default:
                $busca = "Select c.id from clientes 
			    as c join `" . $vars['tabla'] . "` as t 
			    on c.id = t.idemp where t.id like " .
                 $vars['registro'];
                break;
        }
        $analiza = mysql_query( $busca, $con );
        $y_el_ganador_es = mysql_fetch_array( $analiza );
        $registro = $y_el_ganador_es[0];
        $tabla['registro'] = $registro;
    } else {
        $registro = $vars['registro'];
    }
    switch ($resultado[1]) {
        case "proveedores":
            $sql = "Select Nombre from proveedores where 
            id like " . $vars['registro'];
            $code = "";
            break;
        default:
            $sql = "Select Nombre from clientes where 
        	id like " . $registro;
            $code = codigoNegocio( $registro );
            break;
    }
    $consulta = mysql_query( $sql, $con );
    $resultado2 = mysql_fetch_array( $consulta );
    $cadena .= "<form id='formulario_alta' action='#' 
	onsubmit='agrega_registro(); return false'>
	<table cellpadding='0px' cellspacing='1px' class='formulario' ><tr>";
    $cadena .= "<th bgcolor='#7d0063' align='left'></th>
	<th bgcolor='#7d0063' colspan = '3' bgcolor='#ccc' align='left'>";
    $cadena .= "<font size='4'>" . ucfirst( $resultado[3] ) . " de 
	" .
     ucfirst( $resultado2[0] ) . " " . $code . "</font>";
    $cadena .= "<input type='hidden' id='id' name='id' 
	value='" . $vars['registro'] . "' />";
    $cadena .= "<input type='hidden' id='idemp' name='idemp' 
	value='" . $registro . "' />
	<input type='hidden' name='nombre_tabla' id='nombre_tabla' 
	value='" . $resultado[0] . "' /></th><th>
	<input class='boton' onclick='cierra_el_formulario()' 
	value='[X] Cerrar' ></th></tr>";
    $cadena .= submenus( $tabla );
    $formulario = "Select * from `" . $resultado[0] . "` where 
	id like " . $vars['registro'];
    $listado = "Select * from `" . $resultado[0] . "` where idemp like " .
     $registro;
    //Caso de telecos
    if ($resultado[0] == 'z_sercont') {
        $listado .= " order by servicio";
    }
    if (! array_key_exists( 'marcado', $vars )) {
        $vars['marcado'] = null;
    }
    switch ($resultado[0]) {
        case ("facturacion"):
            $cadena .= subform( $listado, $resultado[0], $registro, 
            $vars['marcado'] );
            $cadena .= "<input type='button' class='boton' 
			value='Parametros Factura' 
			onclick='parametros_factura(" . $registro . ")' />
			<div id='parametros_factura'></div>";
            $cadena .= serviciosFijos( $registro );
            break;
        case ("z_facturacion"):
            $cadena .= subform( $listado, $resultado[0], $registro, 
            $vars['marcado'] );
            break;
        case ("cfm"):
            $cadena .= subform( $listado, $resultado[0], $registro, 
            $vars['marcado'] );
            break;
        case ("tllamadas"):
            $cadena .= subform( $listado, $resultado[0], $registro, 
            $vars['marcado'] );
            break;
        default:
            $cadena .= subform( $formulario, $resultado[0], $registro, 
            $vars['marcado'] ) . "" . sublist( $listado, $resultado[0] );
            break;
    }
    $cadena .= "</table></form>";
    return $cadena;
}
/**
 * Genera el subformulario
 * 
 * @param string $sql
 * @param string $tabla
 * @param string $registro
 * @param string $marcado
 */
function subform ($sql, $tabla, $registro, $marcado)
{
    global $con;
    $cadena = "";
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    $numero_campos = mysql_numfields( $consulta );
    $numero_resultados = mysql_numrows( $consulta );
    //necesitamos un filtrado mejor, aqui puede suceder 2 cosas que sea nuevo
    //o bien que sea una actualizacion, si es nuevo aparece el boton de nuevo
    //si es una actualizacion entonces aparece el valor de actualizar.
    //Actualizar en: facturacion y z_facturacion cuando ya hay un registro de esa empresa agregado
    //Actualizar en todas las demas cuando se ha seleccionado un registro
    //Nuevo: cuando no hay registro en facturacion y z_facturacion y siempre que se entra en las demas
    //por lo tanto 1ª a filtrar la tabla
    //$sql2 = "Select c.id from clientes as c join `$tabla` as t on c.id like ";
    switch ($tabla) {
        case "facturacion":
            $cadena_opcion = "<input type='hidden' id='opcion' value='2' >";
            $tipo = chequeaEstadoTabla( $tabla, $registro );
            break;
        case "z_facturacion":
            $cadena_opcion = "<input type='hidden' id='opcion' value='8' >";
            $tipo = chequeaEstadoTabla( $tabla, $registro );
            break;
        case "cfm":
            $cadena_opcion = "<input type='hidden' id='opcion' value='9' >";
            $tipo = chequeaEstadoTabla( $tabla, $registro );
            break;
        case "tllamadas":
            $cadena_opcion = "<input type='hidden' id='opcion' value='10' >";
            $tipo = chequeaEstadoTabla( $tabla, $registro );
            break;
        default:
            $cadena_opcion = "";
            $tipo = (! is_null( $marcado )) ? "Actualizar" : "nuevo";
            break;
    }
    for ($i = 2; $i <= $numero_campos - 1; $i ++) {
        if ($tipo == "nuevo") {
            $cadena .= "<tr><th align='left' valign='top' bgcolor='#7d0063'>
			<font color='#ffffff'>" .
             nombreCampo( mysql_field_name( $consulta, $i ), $tabla ) . "
			</font></th>";
            $cadena .= "<td align='left' valign='top' width='100%' 
			bgcolor='#eeeeee'>" .
             tipoCampo( mysql_field_name( $consulta, $i ), $tabla, "", $tipo, 
            $i ) . "</td></tr>";
            $boton = "<input type='submit' class='boton' 
			name='boton_envio' value='Agregar'>";
        } else {
            $cadena .= "<tr><th align='left' valign='top' bgcolor='#7d0063'>
			<font color='#ffffff'>" .
             nombreCampo( mysql_field_name( $consulta, $i ), $tabla ) . "
			</font></th>";
            $cadena .= "<td align='left' valign='top' width='100%' 
			bgcolor='#eeeeee'>" . tipoCampo( mysql_field_name( $consulta, $i ), 
            $tabla, $resultado[$i], $tipo, $i ) . "</td></tr>";
            $boton = "<input class='boton' type='submit' class='boton' 
			name='boton_envio' value='Actualizar'>";
        }
    }
    $cadena .= $cadena_opcion;
    $cadena .= "<tr><th colspan='2'>" . $boton . "</th></tr>";
    return $cadena;
}
/**
 * Chequea el estado si hay registro sale actualizar si no sale agregar
 * 
 * @param string $tabla
 * @param string $registro
 * @return string $tipo
 */
function chequeaEstadoTabla ($tabla, $registro)
{
    global $con;
    $sql = "Select * from `" . $tabla . "` where idemp like " . $registro;
    $consulta = mysql_query( $sql, $con );
    $total = mysql_numrows( $consulta );
    $tipo = ($total == 0) ? 'nuevo' : 'Actualizar';
    return $tipo;
}
/**
 * Sublistado dentro del subformulario
 * 
 * @param string $sql
 * @param string $tabla
 */
function sublist ($sql, $tabla)
{
    global $con;
    //$cadena = $sql;
    //opcion en la que estamos
    $esecuele = "Select id from submenus where pagina like '" . $tabla .
     "'";
    $laconsulta = mysql_query( $esecuele, $con );
    $elresultado = mysql_fetch_array( $laconsulta );
    $cadena = "<tr><td colspan='2'><input type='hidden' 
	id='opcion' value='" . $elresultado[0] . "' />";
    //echo $sql;
    $consulta = mysql_query( $sql, $con );
    $totcampos = mysql_num_fields( $consulta );
    $cadena .= "<table width='100%' class='sublistado' cellspacing='0'>
	<tr><th align='center' bgcolor='#7d0063'></th>
	<th align='center' bgcolor='#7d0063'></th>";
    for ($i = 2; $i <= $totcampos - 1; $i ++) {
        $cadena .= "<th align='center' bgcolor='#7d0063'>
	    <font color='#ffffff'>" . ucfirst( mysql_field_name( $consulta, $i ) ) .
         "</font></th>";
    }
    $cadena .= "</tr>";
    $j = 0;
    while (true == ($resultado = mysql_fetch_array( $consulta ))) {
        $j ++;
        if ($j % 2 == 0) {
            $color = "par";
            $botoncico1 = "boton_borrar_par";
            $botoncico2 = "boton_editar_par";
        } else {
            $color = "impar";
            $botoncico1 = "boton_borrar_impar";
            $botoncico2 = "boton_editar_impar";
        }
        $cadena .= "<tr><td align='center' class='" . $color . "'>
		<input type='hidden' id='nombre_tabla' value='" . $tabla . "' />
		<input type='hidden' id='codigo' value='" .
         $elresultado[0] . "' />
		<input type='button' class='" . $botoncico2 . "' 
		onclick='muestra_registro(" . $resultado[0] . ")' /></td>
		<td align='center' class='" . $color . "'>
		<input type='button' class='" . $botoncico1 . "' 
		onclick='borrar_registro(" . $resultado[0] .
         ")' /></td>";
        for ($i = 2; $i <= $totcampos - 1; $i ++) {
            $cadena .= "<td align='center' class='" . $color . "'>" . ucfirst( 
            compruebaCheck( $tabla, mysql_field_name( $consulta, $i ), 
            $resultado[$i] ) ) . "holaquetal</td>";
        }
        $cadena .= "</tr>";
    }
    $cadena .= "</table></td></tr>";
    return $cadena;
}
/**
 * Servicios fijos en facturacion
 * 
 * @param string $cliente
 * @return string $cadena
 */
function serviciosFijos ($cliente)
{
    global $con;
    $cadena = "";
    $sql = "Select Id,ID_Cliente,Servicio,Imp_Euro,unidades,iva,observaciones 
	from `tarifa_cliente` where `ID_Cliente` like " . $cliente;
    $consulta = mysql_query( $sql, $con );
    $totcampos = mysql_num_fields( $consulta );
    $span = $totcampos - 2;
    $cadena .= "<tr><td colspan='2'>
		<table width='100%' class='sublistado' cellspacing='0'>";
    $cadena .= "<tr><th colspan='" . $span . "' bgcolor='#ccc'>
		Servicios Fijos Mensuales</th>";
    $cadena .= "<th align='center' bgcolor='#ccc'>
	<input type='button' class='agregar' onclick='frm_srv_fijo(" .
     $cliente . ")' />
	</th></tr>";
    $cadena .= "<tr><td colspan='4'><div id='frm_srv_fijos'></div></td></tr>";
    $cadena .= "<tr><th bgcolor='#7d0063'></th><th bgcolor='#7d0063'></th>";
    for ($i = 2; $i <= $totcampos - 2; $i ++) {
        $cadena .= "<th align='center' bgcolor='#7d0063'><font color='#ffffff'>
	    " .
         ucfirst( mysql_field_name( $consulta, $i ) ) . "</font></th>";
    }
    $cadena .= "</tr>";
    $j = 0;
    while (true == ($resultado = mysql_fetch_array( $consulta ))) {
        $j ++;
        if ($j % 2 == 0) {
            $color = "par";
            $botoncico1 = "boton_borrar_par";
            $botoncico2 = "boton_editar_par";
        } else {
            $color = "impar";
            $botoncico1 = "boton_borrar_impar";
            $botoncico2 = "boton_editar_impar";
        }
        $cadena .= "<tr>";
        //borrado y edicion
        $cadena .= "<td align='center' class='" . $color . "'>
		<input type='button'  class='" . $botoncico2 . "' 
		onclick='muestra_srv_fijo(" . $resultado[0] . ")' /></td>
		<td align='center' class='" . $color . "'>
		<input type='button' class='" . $botoncico1 . "' 
		onclick='borra_srv_fijo(" . $resultado[0] .
         ")' /></td>";
        $cadena .= "<td class='" . $color . "'>" . $resultado['Servicio'] . " " .
         $resultado['observaciones'] . "</td>";
        $cadena .= "<td class='" . $color . "' align='center'>" .
         $resultado['Imp_Euro'] . "</td>";
        $cadena .= "<td class='" . $color . "' align='center'>" .
         $resultado['unidades'] . "</td>";
        $cadena .= "<td class='" . $color . "' align='center'>" .
         $resultado['iva'] . "</td>";
        $cadena .= "</tr>";
    }
    $cadena .= "</table></td></tr>";
    return $cadena;
}
/**
 * Formulario de servicios Fijos
 * 
 * @param array $vars
 */
function frmSrvFijos ($vars)
{
    global $con;
    $cadena = "";
    $sql = "Select id,Nombre from `servicios2` where `Estado_de_servicio` like '-1' order by Nombre";
    $consulta = mysql_query( $sql, $con );
    if (isset( $vars['cliente'] )) {
        $cadena .= "</form>
		<form id='frm_srv_fijos' name='frm_srv_fijos' 
		action='#' onsubmit='agrega_srv_fijos(); return false'>";
        $cadena .= "
		<table id='tabla_srv_fijos' cellpadding='2px' cellspacing='2px'>
		<tr>
		<th>Servicio:</th><td>
		<input type='hidden' id='id_Cliente' name='id_Cliente' 
		value='" . $vars['cliente'] . "' />
		<select id='servicio' name='servicio' onchange='cambia_los_otros()'>
		<option value='0'>--Servicio--</option>";
        while (true == ($resultado = mysql_fetch_array( $consulta ))) {
            $cadena .= "<option value='" . $resultado[1] . "'>" . $resultado[1] .
             "</option>";
        }
        $cadena .= "</select></td>";
        $cadena .= "<th>Importe:</th><td>
		<input type='text' name='importe' id='importe' size='8'/>&euro;</td>";
        $cadena .= "<th>Unidades:</th><td>
		<input type='text' name='unidades' id='unidades' size='2' value='1' /></td>";
        $cadena .= "<th>Iva:</th><td>
		<input type='text' name='iva' id='iva' size='2'/></td></tr>";
        $cadena .= "<tr><th valign='top'>Observaciones:</th><td>
		<textarea name='observaciones' id='observaciones' cols='30'></textarea></td>";
        $cadena .= "<td colspan='4' align='center'>
		<input class='agregar' type='submit' name='agregar' value='Agregar' />
		</td></tr></table>";
    } else {
        $sql2 = "Select * from tarifa_cliente where id like " . $vars['id'];
        $consulta2 = mysql_query( $sql2, $con );
        $resultado2 = mysql_fetch_array( $consulta2 );
        $cadena .= "</form>
		<form id='frm_srv_fijos' name='frm_srv_fijos' action='#' 
		onsubmit='actualiza_srv_fijos(); return false'>";
        $cadena .= "<table id='tabla_srv_fijos' cellpadding='2px' cellspacing='2px'>
		<tr>
		<th>Servicio:</th><td>
		<input type='hidden' id='id' name='id' value='" .
         $resultado2[0] .
         "' />
		<input type='hidden' id='id_Cliente' name='id_Cliente' value='" .
         $resultado2[1] . "' />
		<select id='servicio' name='servicio' onchange='cambia_los_otros()'>
		<option value='0'>--Servicio--</option>";
        while (true == ($resultado = mysql_fetch_array( $consulta ))) {
            if ($resultado[1] == $resultado2['Servicio']) {
                $cadena .= "<option selected value='" . $resultado[1] . "'>
				" . $resultado[1] . "</option>";
            } else {
                $cadena .= "<option value='" . $resultado[1] . "'>
				" . $resultado[1] . "</option>";
            }
        }
        $cadena .= "</select></td>";
        $cadena .= "<th>Importe:</th><td><input type='text' name='importe' 
		id='importe' size='8'value='" .
         $resultado2['Imp_Euro'] . "'/>&euro;</td>";
        $cadena .= "<th>Unidades:</th><td><input type='text' name='unidades' 
		id='unidades' size='2' value='1' /></td>";
        $cadena .= "<th>Iva:</th><td><input type='text' name='iva' id='iva' 
		size='2'value='" . $resultado2['iva'] .
         "'/></td></tr>";
        $cadena .= "<tr><th valign='top'>Observaciones:</th><td>
		<textarea name='observaciones' id='observaciones' cols='30'>" .
         $resultado2['observaciones'] . "</textarea></td>";
        $cadena .= "<td colspan='4' align='center'>
		<input type='submit' class='boton_actualizar' name='actualizar' 
		value='Actualizar' /></td></tr></table>";
    }
    return $cadena;
}
/**
 * Cambia los otros
 * 
 * @param array $vars
 * @return string $cadena
 */
function cambiaLosOtros ($vars)
{
    global $con;
    $servicio = $vars['servicio'];
    $sql = "Select PrecioEuro, iva from servicios2 
	where Nombre like '" . $servicio . "'";
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array( $consulta );
    $cadena = $resultado[0] . ":" . $resultado[1];
    return $cadena;
}
/**
 * Agrega los servicios Fijos
 * 
 * @param array $vars
 * @return string
 */
function agregaSrvFijo ($vars)
{
    global $con;
    $sql = "Insert into tarifa_cliente 
	(`ID_Cliente`,`Servicio`,`Imp_Euro`,`iva`,`unidades`,`observaciones`) 
	values ('" .
     $vars['id_Cliente'] . "','" . $vars['servicio'] . "',
	'" . $vars['importe'] . "','" .
     $vars['iva'] . "',
	'" .
     $vars['unidades'] . "','" . $vars['observaciones'] . "')";
    if (mysql_query( $sql, $con )) {
        return "<img src='" . OK . "' alt='Servicio Agregado' width='64'/>
		Servicio Agregado&nbsp;&nbsp;<p/>";
    } else {
        return "<img src='" . NOK . "' alt='ERROR' width='64'/>
		ERROR&nbsp;&nbsp;<p/>" . $sql;
    }
}
/**
 * Borra el servicio fijo
 * 
 * @param array $vars
 * @return string
 */
function borraSrvFijo ($vars)
{
    global $con;
    $sql = "Delete from tarifa_cliente where id like " . $vars['id'];
    if (mysql_query( $sql, $con )) {
        return "<img src='" . OK . "' alt='Servicio Borrado' width='64'/>
		Servicio Borrado&nbsp;&nbsp;<p/>";
    } else {
        return "<img src='" . NOK . "' alt='ERROR' width='64'/>
		ERROR&nbsp;&nbsp;<p/>";
    }
}
/**
 * Actualiza el servicio fijo
 * 
 * @param array $vars
 * @return string
 */
function actualizaSrvFijo ($vars)
{
    global $con;
    $sql = "Update `tarifa_cliente` set `Servicio`='" . $vars['servicio'] . "',
	 `Imp_Euro`='" .
     $vars['importe'] . "', `iva`='" . $vars['iva'] . "', 
	 `unidades`='" . $vars['unidades'] . "',
	 `observaciones`='" . $vars['observaciones'] . "' 
	 where id like " . $vars['id'];
    if (mysql_query( $sql, $con )) {
        return "<img src='" . OK . "' alt='Servicio Actualizado' width='64'/>
		Servicio Actualizado&nbsp;&nbsp;<p/>";
    } else {
        return "<img src='" . NOK . "' alt='ERROR' width='64'/>
		ERROR&nbsp;&nbsp;<p/>" . $sql;
    }
}
/**
 * Genera el listado de categorias
 * 
 * @return string
 */
function listadoCategorias ()
{
    global $con;
    $sql = "SELECT * FROM `categorías clientes`";
    $consulta = mysql_query( $sql, $con );
    $cadena = "<select id='tipo_cliente' onchange='filtra_listado()'>
	<option value='0'>--Selecciona Tipo--</option>";
    while (true == ($resultado = mysql_fetch_array( $consulta ))) {
        $cadena .= "<option value='" . $resultado[0] . "'>" . $resultado[1] .
         "</option>";
    }
    $cadena .= "<option value='social'>Con direccion Facturaci&oacute;n</option>";
    $cadena .= "<option value='comercial'>Con direccion Contrato</option>";
    $cadena .= "<option value='independencia'>Con direccion Independencia</option>";
    $cadena .= "<option value='conserje'>Listado Conserje</option>";
    $cadena .= "</select>";
    return $cadena;
}