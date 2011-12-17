<?php require_once 'configuracion.php'; 
if( isset( $_POST ) && isset( $_SESSION['usuario'] ) ) {
    sanitize( $_POST );
    $datosRegistro = array();
    $desvio = "";
    if ( isset( $_POST['registro'] ) ) {
        $sql = "Select * from `" . $_POST['tabla'] . "`
        where id like " . $_POST['registro'] ;
        $datosRegistro = consultaUnica($sql, MYSQL_ASSOC );
    }
    $sql = "Select * from alias where tabla like '".$_POST['tabla']."'
    AND mostrar like 'Si' order by orden";
    $campos = consultaGenerica( $sql, MYSQL_ASSOC );
?>
<div id='resultadoAccion'></div>
<form  id='registro' method='post' action=''>
<fieldset>
<legend>Formulario</legend>
<input type='hidden' name='tableform' value='<?php echo $_POST['tabla']; ?>' />
<?php
    foreach ( $campos as $campo ) {
        if ( array_key_exists($campo['campoo'], $datosRegistro) ){
            $dato = $datosRegistro[$campo['campoo']];
        } else {
            $dato = "";
        }
        if ( $campo['tipo'] != "hidden" ) {
            echo "<div class='span-11 left'>
            <label class='text' for='".$campo['campoo']."'>
            " . $campo['campof'] . "
            </label><br/>";
        }
        echo dibujaCampo( $campo, $dato );
        if ( $campo['tipo'] != "hidden" ) {
            echo "</div>";
        }
    }
    echo "<div class='span-22 left last'>";
    if ( count($datosRegistro) != 0 ) {
        echo "
        <input id='update' name='update' type='button' value='Actualizar Registro' />
        <input id='delete' name='delete' type='button' value='Borrar Registro' />";
    } else {
        echo "
        <input id='add' name='add' type='button' value='Agregar Registro' />
        <input type='reset' value='Limpiar Formulario' />";
    }
    echo "</div>";
?>
</fieldset>
</form>
<div id='sublistado'>
<?php 
sublistado($_POST);
?>
</div>
<script type='text/javascript'>
/**
* Se carga el datepicker con la configuracion en castellano
*/
$(function(){
$(".datepicker").datepicker( { dateFormat: 'dd-mm-yy' } );
$(".datepicker").datepicker( $.datepicker.regional[ "es" ] );

});

$('#update').click(function(){
var url = "inc/funcionesRegistro.php";
var pars = "opt=update&" +  $('form').serialize();
procesaAjax(url, pars, "resultadoAccion", "Actualizando Registro", false, false);
});

$('#delete').click(function(){
if ( confirm('Desea borrar este registro?') ) {
var url = "inc/funcionesRegistro.php";
var pars = "opt=delete&" +  $('form').serialize();
procesaAjax(url, pars, "resultadoAccion", "Borrando Registro", false, false);
} else {
alert("No se ha borrado el registro");
}
});

$('#add').click(function(){
var url = "inc/funcionesRegistro.php";
var pars = "opt=add&" +  $('form').serialize();
procesaAjax(url, pars, "resultadoAccion", "Agregando Registro", false, false);
});
</script>
<?php
} else {
    header("Status: 404 Not Found");
    exit(0);
}
 
function dibujaCampo( $campo, $dato )
{
    $html = "";
    switch( $campo['tipo'] )
    {
        case "hidden":
            $html .= "<input type='hidden' readonly size='".$campo['size']."'
             name='" . $campo['campoo'] . "'
            value='" . $dato . "' />";
        break;    
        case "text": //caso rarito de z_sercont valor
            /*if (($tabla =='z_sercont') && ($resultado['campoo']=='valor'))
                $cadena ="<div id='tipo_teleco'>
            <input type='text' size='".$resultado['size']."' 
            id='".$resultado['variable']."' 
            name='".$resultado['campoo']."' 
            value='".$valor."' tabindex='".$i."' onkeyup='chequea_valor()'/>
            </div>";
            else*/
            $html .= "<input type='text' size='".$campo['size']."' 
             name='" . $campo['campoo'] . "' 
            value='" . $dato . "' />";
        break;
        case "textarea":
            $html .= "<textarea  
            name='" . $campo['campoo'] . "' rows='" . $campo['size'] . "' 
            cols='46' >" . $dato . "</textarea>";
        break;
        case "checkbox": 
            $chequeado = ( $dato == 0 ) ? '':'checked';
            $html .= "<input  type='checkbox'  
            " . $chequeado . " name='" . $campo['campoo'] . "'/>";
        break;
        case "date":
            $html .= "<input class='datepicker' type='text' 
             name='" . $campo['campoo'] . "' 
            size='" . $campo['size'] . "'  value='" . cambiaf( $dato ) . "' />";
        break;
        case "select":
            $sql = "Select * from `" . $campo['depende'] . "` order by 2";
                $resultados = consultaGenerica( $sql );
               /* if ($tabla =='z_sercont') //caso del z_sercont
                    $cadena ="<select id='".$resultado['variable']."' name='".$resultado['campoo']."' tabindex='".$i."' onchange='muestra_campo()'>";
                else
                    $cadena ="<select id='".$resultado['variable']."' name='".$resultado['campoo']."' tabindex='".$i."'>";
                */
            $html .="<select  name='".$campo['campoo']."'>";
            $html .="<option value='0'>-::". $campo['campoo'] .":-</option>";
            foreach( $resultados as $resultado ) {
                    $marcado = ( $resultado[1] == $dato )? 'selected':'';
                    $html .= "<option ".$marcado." value='".$resultado[1]."'>
                    ". $resultado[1] ."</option>";
                }
                $html .= "</select> ";
        break;
        default: $html = $dato;
        break;
    
    }
    //generamos el enlace de conexion o bien a web o envio de correo
    switch( $resultado['enlace'] )
    {
        case "web":
            $html .="<a href='http://" . $dato . "' target='_blank'>
            <img src='imagenes/package_network.png' width='14' alt='Abrir Web'/></a>";
        break;
        case "mail":
            $html .="<a href='mailto:".$dato."'>
            <img src='imagenes/mail_generic.png' width='14' alt='Enviar Correo'/></a>";
        break;
    }
    return $html;
}
function submenus( $tabla ) {
    $html = "";
    $sql = "Select submenus.* from submenus INNER JOIN menus on menus.id = submenus.menu
    WHERE menus.pagina like '".$tabla."'";
    $resultados = consultaGenerica( $sql, MYSQL_ASSOC );
    foreach( $resultados as $resultado ) {
        $html .= "<input type='button' value='".$resultado['nombre']."' />";
    }
    /*$html = "<tr><th colspan='4' width='100%'height='26px'><table><tr>";
    while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
        if($resultado[2] == "Principal") {
            $html .= "<th><span class='boton'
            onclick='muestra(".$tabla['registro'].")' >" . $resultado[2] .
            "</span></th>";
        } else {
            $html .= "<th><span class='boton'
            onclick='submenu(".$resultado[0].")' >". $resultado[2] .
            "</span></th>";
        }
    }
    $html .= "</tr></table></th></tr>";*/
    return $html;
}

function sublistado( $vars ) {
    var_dump ( $vars );
    $sql = "SELECT * FROM `".$vars['tabla']."` 
    WHERE idemp like '".$vars['registro']."'";
    $resultados = consultaGenerica( $sql, MYSQL_ASSOC );
    if ( count($resultados) > 0 ) {
        foreach( $resultados as $resultado ) {
            print_r( $resultado );
        }
    }
}
/*function formulario( $vars )
{
	global $con;
	if ( isset( $vars['registro'] ) ) {
	    $sql = "Select * from `" . $vars['tabla'] . "` 
	    where id like " . $vars['registro'] ;
	    $datosRegistro = consultaUnica($sql);
	}
	
	$consulta = mysql_query( $sql, $con );
	$numero_campos = mysql_num_fields( $consulta ); 
	$resultado = mysql_fetch_array( $consulta );
	$cadena = "
	<form id='formulario_actualizacion' action='#' method='post' 
	onSubmit='actualiza_registro(); return false'>
	<input type='hidden' id='opcion' value='0' />
	<input type='hidden' id='idemp' value='".$resultado[0]."' />
	<table cellpadding=0px cellspacing=1px class='formulario'>";
	//cabezera nombre de empresa, desvio y activo y menu
	switch ( $vars['tabla'] ) {
		case "clientes": $desvio = 
			desvio_activo(
				$resultado['desvio'],
				$resultado['Estado_de_cliente'],
				$resultado['extranet'],
				$vars['registro']
				);
		//$code = codigo_negocio($resultado[Id]);
		break;
		default: $desvio = "";
		break;//$code="";break;
	}
	$color_cabezera = color_cabezera( $vars['tabla'], $resultado );
	$codigoNegocio = (in_array('Id',$resultado)) ? codigo_negocio( $resultado['Id'] ) : codigo_negocio();
	$cadena .= "<th height='24px' bgcolor='".$color_cabezera."' color='#fff' 
	align='left' width='100px'><div id='edicion_actividad'></div>";
	$cadena .= $desvio."</th><th height='24px' align='left' 
	bgcolor='".$color_cabezera."' colspan='2'>
	<font size='4'>" . $resultado['Nombre'] ." 
	" . $codigoNegocio . "
	</font>
	<input type='hidden' name='nombre_tabla' id='nombre_tabla' 
	value='" . $vars['tabla'] . "' />
	<input type='hidden' name='numero_registro' id='numero_registro' 
	value='".$resultado[0]."' /></th>
	<th align='right' bgcolor='".$color_cabezera."'>
	<input type='button' class='boton' onclick='cierra_el_formulario()' 
	value='[X] Cerrar' ></th></tr>";

	//submenus
	$cadena .= submenus( $vars );
	//Fin de los submenus
	//campo oculto con nombre de tabla
	for ($i=1, $j=0 ;$i<=$numero_campos-1;$i++, $j++) {
		if( $j % 2 == 0 ) {
			$cadena .= "</tr><tr>";
		}
		$cadena .= "<th align='left' valign='top' 
		class='nombre_campo'>" 
		. nombre_campo( mysql_field_name( $consulta,$i ), $vars['tabla'] ) .
		"</th><td align='left' valign='top' class='valor_campo'> " 
		. tipo_campo( 
			mysql_field_name($consulta,$i), 
			$vars['tabla'],
			$resultado[$i],
			'actualiza',
			$i
			) .
		"</td>";
	}
	$cadena .= "</tr>";
	//parte de la botoneria ya empezamos con los casos particulars
	//o actualizo o creo
	if(isset($vars['principal'])) //de momento indicativo de subformulario
	{
		$cadena .= "<tr><th colspan='4' align='center'>
		<input class='boton' type='submit'  value='[+] Agregar' />";
		$cadena .= "<input class='boton' type='reset'  
		value='[L] Limpiar formulario' /></th></tr>";
	}
	else
	{
		$cadena .= "<tr><th colspan='4' align='center'><p/>
		<input class='boton' type='submit' value='[*]Actualizar Datos' 
		tabindex='".$numero_campos."'/>";
		$cadena .= "<input type='button' class='boton' 
		onclick='borrar_registro(".$resultado[0].")' value='[X]Borrar Datos' 
		tabindex='".$numero_campos."'/></th></tr>";
	}
	$cadena .= "</table></form>";
	return $cadena;
}


function dibujaCampo( $campo, $valor ) {
    
}
function tipo_campo( $campo, $tabla, $valor, $opcion, $orden )
{
    global $con;
    $i = 0;
    $sql = "Select * from alias where tabla like '" . $tabla ."' and `campoo` like '" . $campo . "'";
    $consulta = mysql_query( $sql, $con );
    $resultado = mysql_fetch_array($consulta);
    switch($resultado['tipo'])
    {
        case "text": //caso rarito de z_sercont valor
            if (($tabla =='z_sercont') && ($resultado['campoo']=='valor'))
                $cadena ="<div id='tipo_teleco'><input type='text' size='".$resultado['size']."' id='".$resultado['variable']."' name='".$resultado['campoo']."' value='".$valor."' tabindex='".$i."' onkeyup='chequea_valor()'/></div>";
            else
                $cadena = "<input type='text' size='".$resultado['size']."' id='".$resultado['variable']."' name='".$resultado['campoo']."' value='".$valor."' tabindex='".$i."'/>";break;
        case "textarea":$cadena = "<textarea id='".$resultado['variable']."' name='".$resultado['campoo']."' rows='".$resultado['size']."' cols='46' tabindex='".$i."'>".$valor."</textarea>";break;
        case "checkbox": 	{
            if ($valor!= 0)
                $chequeado = 'checked';
            else
                $chequeado = '';
            $cadena = "<input  type='checkbox' id='".$resultado['variable']."' ".$chequeado." name='".$resultado['campoo']."' tabindex='".$i."'/>";
        }
        break;
        case "date": 	{
            $cadena = "<input type='text' id='".$resultado['variable']."' name='".$resultado['variable']."' size = '".$resultado['size']."'  value='".cambiaf($valor)."' tabindex='".$i."'/>";
            //$cadena .= "&nbsp;&nbsp;<button TYPE='button' class='calendario' id='f_trigger_".$resultado['variable']."' tabindex='".$i."'></button>";
        }
        break;
        case "select":
            {//hay que hacer una consulta a la tabla dependiente de los valores
                $sql = "Select * from `" . $resultado['depende'] . "` order by 2";
                $consulta = mysql_query( $sql, $con );
                if ($tabla =='z_sercont') //caso del z_sercont
                    $cadena ="<select id='".$resultado['variable']."' name='".$resultado['campoo']."' tabindex='".$i."' onchange='muestra_campo()'>";
                else
                    $cadena ="<select id='".$resultado['variable']."' name='".$resultado['campoo']."' tabindex='".$i."'>";
                $cadena .="<option value='0'>-::". $resultado['campoo'] .":-</option>";
                while (true == ( $resultado = mysql_fetch_array( $consulta ) ) )
                {
                    if ( $resultado[1] == $valor)
                        $marcado = 'selected';
                    else
                        $marcado = "";
                    $cadena .= "<option ".$marcado." value='".$resultado[1]."'>". $resultado[1] ."</option>";
                }
                $cadena .= "</select> ". $valor;
            }break;
        default: $cadena = $valor;break;

    }
    switch($resultado['enlace'])//generamos el enlace de conexion o bien a web o envio de correo
    {
        case "web":$cadena .="<a href='http://".$valor."' target='_blank'><img src='imagenes/package_network.png' width='14' alt='Abrir Web'/></a>";break;
        case "mail":$cadena .="<a href='mailto:".$valor."'><img src='imagenes/mail_generic.png' width='14' alt='Enviar Correo'/></a>";break;
    }
    return $cadena;
}


function desvio_activo( $desvio, $estado, $extranet, $cliente )
{
    if($estado == 0) //Cliente activo o no
        $cadena = "<img src='imagenes/noactivo.gif' alt='Cliente Inactivo' width='24px'/>";
    else
        $cadena = "<img src='imagenes/activo.gif' alt='Cliente Activo' width='24px'/>";

    if($desvio == 0) //Desvio activo o no
        $cadena .= "<img src='imagenes/desvioi.gif' alt='Desvio Inactivo' width='24px'/>";
    else
        $cadena .= "<spam class='popup' onclick='ver_detalles(0,0,0,".$cliente.")'>
    <img src='imagenes/nudesvioa.gif' alt='Desvio Activo' width='24px' /></spam>";

    if($extranet == 0) //Extranet activa o inactiva
        $cadena .= "<img src='imagenes/extraneti.gif' alt='Extranet Inactivo' width='24px'/>";
    else
        $cadena .= "<spam class='popup' onclick='ver_detalles(0,0,1,".$cliente.")'>
    <img src='imagenes/extraneta.gif' alt='Extranet Activa' width='24px' /></spam>";
    return $cadena;
}*/
 // FIN FICHERO