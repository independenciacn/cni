<?php
/**
 * Datos_gestion File Doc Comment
 * 
 * Funciones que devuelven los datos de la parte de gestion y realizan los procesos
 * 
 * PHP Version 5.2.6
 * 
 * @category Datos_gestion
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once 'configuracion.php';
if ( isset( $_POST['opcion'] ) ) {
	sanitize( $_POST );
	$tablasCategorias = array( 
		'1' => 'categoría servicios', 
		'2' => 'categorías clientes' 
	);
	switch( $_POST['opcion'] )
	{
		case 0:  $respuesta = listadoBackup();break;
		case 1:  $respuesta = hazBackup();break;
		case 2:  $respuesta = restauraBackup( $_POST['archivo'] );break;
		case 3:  $respuesta = borraBackup( $_POST['archivo'] );break;
		/*case 4:$respuesta = revisa_tablas();break;
		case 5:$respuesta = repara_tablas();break;
		case 6:$respuesta = optimiza_tablas();break;*/
		case 7:  $respuesta = listadoCategoria( $_POST );break;
		case 8:  $respuesta = detallesCategoria( $_POST );break;
		case 9:  $respuesta = actualizaCategoria( $_POST );break;
		case 10: $respuesta = listadoTelefonoIp();break;
		case 11: $respuesta = formularioTelefono();break;
		case 12: $respuesta = agregaTelefono( $_POST );break;
		case 13: $respuesta = rarita();break;
		case 14: $respuesta = listadoPersonalizado( $_POST );break;
		case 15: $respuesta = borraTelefonoAsignado( $_POST );break;
		case 16: $respuesta = editaTelefonoAsignado( $_POST );break;
		case 17: $respuesta = actualizaTelefonoAsignado( $_POST );break;
	}
	echo $respuesta;
}
/**
 * Realiza la copia de seguridad 
 * 
 * @todo Borrar de aqui esta ahora en funcionesGestion.php
 * @return string $html
 */
function hazBackup()
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
 * Restaura la copia de seguridad
 * 
 * 
 * @param string $archivo
 * @return string $html
 */
function restauraBackup( $archivo )
{
	global $conf;
	$html = "<span class='success span-10'>Copia Restaurada</span>";
	try {
		exec( $conf['binariosDB'] . $conf['mysqlExec'] . " 
		--user=" . $conf['dbUser'] . " --password=" . $conf['dbPass'] . " 
	 	" . $conf['dbName'] . " < " . $conf['rutaBackup'] . $archivo );
	} catch( Exception $e ) {
		$html = "<span class='error span-10'>Error " . $e->getMessage() . "</span>";
	}
	return $html;
}
/**
 * Muestra el listado de las copias de seguridad realizadas
 * 
 * @todo Borrar de aqui esta ahora en funcionesGestion.php
 * @return string $html
 */
function listadoBackup()
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
 * Borra la copia de seguridad
 * 
 * @param string $archivo
 * @return string $html
 */
function borraBackup( $archivo )
{
	global $conf;
	$html = "<span class='success span-10'>Copia Restaurada</span>";
	try {
		exec( $conf['borraExec'] . " " . $conf['rutaBackup'] . $archivo );
	} catch ( Exception $e ) {
		$html = "<span class='error span-10'>Error ". $e->getMessage() . "</span>";
	}
	return $html;
}
/*************REVISION DE TODAS LAS TABLAS - DEPRECATED************************************/
/*function revisa_tablas()
{
	global $con;
	$sql = "show tables";
	$consulta = mysql_query( $sql, $con );
	while($resultado = mysql_fetch_array($consulta))
	{
		$sql2 = "check table `$resultado[0]`";
		$consulta2 = mysql_db_query($dbname,$sql2,$con);
		$resultado2 = mysql_fetch_array($consulta2);
		$cadena .= "<br>Estado ".$resultado[0]." ->".$resultado2[3]; 
	}
	return $cadena;
}
/*************REVISION DE TODAS LAS TABLAS - DEPRECATED************************************/
/*function repara_tablas()
{
	global $con;
	$sql = "show tables";
	$consulta = mysql_query( $sql, $con );
	while($resultado = mysql_fetch_array($consulta))
	{
		$sql2 = "repair table `$resultado[0]`";
		$consulta2 = mysql_db_query($dbname,$sql2,$con);
		$resultado2 = mysql_fetch_array($consulta2);
		$cadena .= "<br>Estado ".$resultado[0]." ->".$resultado2[3]; 
	}
	return $cadena;
}
/*************REVISION DE TODAS LAS TABLAS - DEPRECATED************************************/
/*function optimiza_tablas()
{
	global $con;
	$sql = "show tables";
	$consulta = mysql_query( $sql, $con );
	while($resultado = mysql_fetch_array($consulta))
	{
		$sql2 = "optimize table `$resultado[0]`";
		$consulta2 = mysql_db_query($dbname,$sql2,$con);
		$resultado2 = mysql_fetch_array($consulta2);
		$cadena .= "<br>Estado ".$resultado[0]." ->".$resultado2[3]; 
	}
	return $cadena;
}*/

/**
 * Devuelve el listado de las categorias en formato tabla
 * 
 * @param array $vars
 * @return string $html
 */
function listadoCategoria( $vars )
{
	global $tablasCategorias;
	$sql = "SELECT * FROM `" . $tablasCategorias[$vars['categoria']] . "`";
	$resultados = consultaGenerica( $sql );
	$html = "<input type='hidden' id='categoria' 
		value='".$vars['categoria']."' />";
	$html .= "
	<table>
		<thead>
		<tr><th colspan='3'>
			Listado de " . ucfirst( $tablasCategorias[$vars['categoria']]) ."
		</th></tr>
		</thead>
		<tbody>";
	foreach ( $resultados as $resultado ) {
		$html .= "<tr>
		<td>". $resultado[1] ."</td>
		<td>" . $resultado[2] ."</td>
		<td><input type='button' 
		onclick='editar_categoria(".$resultado[0].")' value='Editar' />
		</td>
		</tr>";
	}
	$html .= "</tbody></table>
	<div id='detalles_categoria'></div>";
	return $html;
}
/**
 * Muestra los detalles de la categoria
 * 
 * @param array $vars
 * @return string $html
 */
function detallesCategoria( $vars )
{
	global $tablasCategorias;
	$sql = "SELECT * FROM `". $tablasCategorias[$vars['categoria']] . "`
	WHERE Id LIKE '". $vars['registro'] . "'";
	$resultado = consultaUnica( $sql );
	$html = "
	<form id='formulario_categorias' method='post' action=''>
	<input type='hidden' id='categoria' name='categoria' 
		value='" . $vars['categoria'] . "' />
	<input type='hidden' id='registro' name='registro' 
		value='" . $vars['registro'] . "' />
	<label for='nombre'>Categoria:</label> 
		<input type='text' name='nombre' 
			value='" . $resultado[0][1] . "' size='40'/>
	<br/>
	<label for='descripcion'>Descripcion:</label> 
	<textarea name='descripcion' cols='35'>" . $resultado[0][2] . "</textarea>
	<br/>
	<input type='submit' name='Actualizar' value='Actualizar' class='boton' />
	</form>
	<script type='text/javascript'>
	$('#formulario_categorias').submit( function(){
		actualiza_categoria();
		return false;
	});
	</script>";
/*
 * FIXME: NO DEJA AGREGAR CATEGORIAS
 */
//$cadena .= "<input type='submit' name='boton_envio' value='Borrar' class='boton' />";
	//$cadena .= "<input type='reset' value='Limpiar' class='boton'/>";
	//$cadena .= "<input type='submit' name='boton_envio' value='Agregar' class='boton' />";
	return $html;
	
}
/**
 * Actualiza la categoria
 * 
 * @param array $vars
 * @return string $html
 */
function actualizaCategoria( $vars )
{
	global $con;
	global $tablasCategorias;
	$html = "<span class='success span-10'>Datos Actualizados</span>";
	if ( isset( $vars['Actualizar'] ) ) {
		$sql = "Update `" . $tablasCategorias[$vars['categoria']] ."` 
		SET `Nombre` = '" . $vars['nombre'] . "',  
		`Descripción` = '" . $vars['Descripcion'] . "' 
		WHERE id like " . $vars['registro'];
	}
	if ( !ejecutaConsulta( $sql ) ) {
		$html = "<span class='error span-10'>Error en la Consulta</span>";
	}
	return $html;
}
/**
 * Listado de Telefonos del Centro
 * 
 * @return string $html
 */
function listadoTelefonoIp()
{
	$check = true;
	$sql = "Select c.Nombre, z.valor, z.servicio from clientes as c 
	join z_sercont as z on c.id like z.idemp 
	where z.servicio like 'Telefono' order by z.servicio desc, c.Nombre";
	$resultados = consultaGenerica( $sql );
	$html = "<table>
	<thead>
		<tr><th>Cliente</th><th>Telefono</th></tr>
	</thead>
	<tbody>";
	foreach ( $resultados as $resultado ) {
		if ( ( $resultado[2] == 'Direccion Ip' ) && ( $check === true ) ) {
			$html .= "</tbody><thead>
				<tr><th>Cliente</th><th>Direccion IP</th></tr>
				</thead><tbody>";
			$check = false;
		}
		$html .= "<tr><td>". $resultado[0] . "</td>
		<td>" . $resultado[1]."</td></tr>";
	}
	$html .="</tbody></table>";
	return $html;
}
/**
 * 
 * Muestra el formulario para agregar los numeros de telefono del centro
 * Mostrara los que estan asignados, pudiendo desasignarlos y dejara agregar
 * nuevos telefonos, modificarlos y borrarlos
 * 
 * @return string $html
 */
function formularioTelefono()
{
	$html = "<form id='frm_agrega_telefono' name='frm_agrega_telefono' 
	method='post' action=''>
	<label for='numero_telefono'>Telefono:</label>
		<input type='text' name='numero_telefono' size='12'/>
	<input type='submit' class='boton' name='agregar' 
	value='[+]Agregar Telefono' />
	</form>
	<div id='mensajes_estado'></div>";
	$html .= listadoTelefonosCentro();
	$html .= "<script type='text/javascript'>
	$('#frm_agrega_telefono').submit(function(){
		agrega_telefono();
		return false;
	});
	</script>";
	return $html;
	
}
//Tres parametros, telefono, asignado, y a quien
//Telipext sera la base de datos de telefonos donde estaran todos,
//se compara con la de z_sercont para ver quien lo tiene
// Esta es una fiesta
function listadoTelefonosCentro()
{
	global $con;
	$cadena = "";
	$asignados_despacho_telefono=array();
	$asignados_despacho_adsl=array();
	$asignados_despacho_fax=array();
	$asignados_telefono=array();
	$asignados_adsl=array();
	$asignados_fax=array();
	$no_asignados=array();
	$sql = "select DISTINCT direccion from telipext where tipo like 'telefono'";
	$consulta = mysql_query( $sql, $con );
	$asignados=array();
	$no_asignados=array();
	while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
		//Aqui comparo las de la base con las que tengo asignadas
		//el telefono en telipext esta siempre 976 12 34 56
		//en la base puede estar asi o no
		//quito los espacios en blanco
		$teljunto="";
		$telefono = explode(" ",$resultado['direccion']);
		foreach($telefono as $tele)
			$teljunto .= $tele;
		$sql2 = "Select * from z_sercont where 
		valor like '" . $resultado['direccion'] . "%' 
		or valor like '" . $teljunto . "%' ";
		
		$consulta2 = mysql_query( $sql2, $con );
		if(mysql_numrows($consulta2)>=1) {
			while ( true == ( $resultado2 = mysql_fetch_array( $consulta2 ) ) ) {
				$tipo = categoriaDelCliente( $resultado2['idemp'] );
				//echo "<p/>".$resultado[direccion]."-".$tipo."-".$resultado2[servicio];
				if($tipo == "OK") {
					switch( $resultado2['servicio'] )
					{
						case "Telefono":$asignados_despacho_telefono[] = $resultado['direccion']."-".$resultado2['idemp'];break;
						case "Adsl":$asignados_despacho_adsl[] = $resultado['direccion']."-".$resultado2['idemp'];break;
						case "Fax":$asignados_despacho_fax[] = $resultado['direccion']."-".$resultado2['idemp'];break;
					}
				}
				else
				{
					switch( $resultado2['servicio'] )
					{
						case "Telefono":$asignados_telefono[] = $resultado['direccion']."-".$resultado2['idemp'];break;
						case "Adsl":$asignados_adsl[] = $resultado['direccion']."-".$resultado2['idemp'];break;
						case "Fax":$asignados_fax[] = $resultado['direccion']."-".$resultado2['idemp'];break;
					}
				}
			}
		}
		else
			$no_asignados[] = $resultado['direccion'];
			
	}
	sort($asignados_despacho_telefono);
	sort($asignados_despacho_adsl);
	sort($asignados_despacho_fax);
	sort($asignados_telefono);
	sort($asignados_adsl);
	sort($asignados_fax);
	sort($no_asignados);
	//Nuevo diseño
	//$cadena.="<div id='tabla_asignacion_telefonos'>";
	//Telefonos de Despachos
	$cadena.="<input class='boton' value='[X] Cerrar' onclick='cierra_listado_copias()' ><br><div class='tabla'><div class='listado_1'>Telefonos Despachos</div>";
	$i=0;
	foreach($asignados_despacho_telefono as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
		$clase = "par";
		else
		$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".nombreCliente($asignacion[1])."</div>";
		
	}
	$cadena .="</div>";
	//Fin telefonos despachos
	//Telefonos Domiciliados
	$cadena.="<div class='tabla'><div class='listado_2'>Telefonos Domiciliados</div>";
	$i=0;
	foreach($asignados_telefono as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
		$clase = "par";
		else
		$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".nombreCliente($asignacion[1])."</div>";
	}
	$cadena .="</div>";
	//Fin telefonos Domiciliados
	//Telefonos del centro
	$cadena.="<div class='tabla'><div class='listado_3'>Telefonos del Centro</div>";
	$i=0;
	foreach($no_asignados as $no_asignado)
	{
		//listado de telefonos del centro
                /*
                 * TODO: Se tienen que poder agregar y modificar desde el programa
                 */
		$centro = array("976 30 11 82","976 79 43 60","976 79 43 61","976 79 43 62");
		$i++;
		$clase = ( $i % 2 == 0) ? 'par' : 'impar'; 	
		if(!in_array($no_asignado,$centro))
			$libres[]=$no_asignado;
		else
			$cadena .="<div class='".$clase."'>".$no_asignado . 
			"&nbsp;&nbsp;&nbsp;&nbsp;".descripcionTelefono( $no_asignado )."</div>";
	}
	$cadena .="</div>";
	//Fin telefonos Domiciliados
	//telefonos Libres
	$cadena .= "<div class='tabla'><div class='listado_4'>Telefonos Libres</div>";
	$i = 0;
	//if ( $opcion == 0 )
    //{
        if ( is_array( $libres ) ) {
            foreach( $libres as $libre ) {
                $i++;
                $clase = ( $i % 2 == 0) ? 'par' : 'impar'; 
            	$cadena .="<div class='".$clase."'>
            		<img src='imagenes/edittrash.png' alt='Borrar telefono' 
            		onclick='javascript:borrar_telefono_asignado(\"" . $libre . "\")'>
            		&nbsp;<img src='imagenes/kate.png' alt='Editar telefono' 
            		onclick='javascript:editar_telefono_asignado(\"" . $libre . "\")'>
            		&nbsp;" . $libre . "&nbsp;&nbsp;&nbsp;&nbsp;
            		<span id='edicion_" . $libre . "'>
            		" . descripcionTelefono( $libre ) . "</span></div>";
            }
        }
    //}
	$cadena .="</div>";
	//Fin telefonos libres
	//Faxes de Despachos
	$cadena.="<div class='tabla'><div class='listado_1'>Faxes Despachos</div>";
	$i=0;
	foreach($asignados_despacho_fax as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;". nombreCliente($asignacion[1])."</div>";
	}
	$cadena .="</div>";
	//Fin telefonos despachos
	//Faxes Domiciliados
	$cadena.="<div class='tabla'><div class='listado_2'>Faxes Domiciliados</div>";
	$i=0;
	foreach($asignados_fax as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".nombreCliente($asignacion[1])."</div>";
	}
	$cadena .="</div>";
	//Adsl de Despachos
	$cadena.="<div class='tabla'><div class='listado_1'>Adsl Despachos</div>";
	$i=0;
	foreach($asignados_despacho_adsl as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".nombreCliente($asignacion[1])."</div>";
	}
	$cadena .="</div>";
	//Fin adsl despachos
	//Adsl Domiciliados
	$cadena.="<div class='tabla'><div class='listado_2'>Adsl Domiciliados</div>";
	$i=0;
	foreach($asignados_adsl as $despacho_telefono)
	{
		$asignacion = explode("-",$despacho_telefono);
		$i++;
		if($i%2==0)
			$clase ="par";
		else
			$clase = "impar";
		$cadena .="<div class='".$clase."'>".$asignacion[0]."&nbsp;&nbsp;&nbsp;&nbsp;".nombreCliente($asignacion[1])."</div>";
	}
	$cadena .="</div>";
	$cadena .="<div class='tabla'><div class='listado_2'>Listado de IP's</div>";
	$cadena .= consultaDeIps();
	$cadena .="</div></div></div><p/><p/>";
	return $cadena;
}
/**
 * Devuelve el nombre del cliente pasandole el id como parametro
 * 
 * @param integer $id
 * @return string
 */
function nombreCliente( $id )
{
	$sql = "Select Nombre from clientes where id like " . $id;
	$resultado = consultaUnica( $sql );
	return $resultado[0][0];
}
/**
 * Devuelve la categoria del cliente pasandole el id de cliente
 * 
 * @param integer $id
 * @return string $valor
 */
function categoriaDelCliente( $id )
{
	$sql = "Select Categoria from clientes where id like " . $id;
	$resultado = consultaUnica( $sql );
	$valor = ( $resultado[0][0] == "Clientes despachos" ) ? 'OK' : 'KO';
	return $valor;
}
/**
 * Devuelve la descripcion del telefono
 * 
 * @param string $telefono
 * @return string
 */
function descripcionTelefono( $telefono )
{
	$sql = "Select descripcion from telipext 
	WHERE direccion like '" . $telefono . "'";
	$resultado = consultaUnica( $sql );
	return $resultado[0][0];
}
/**
 * Agrega el telefono
 * 
 * @param array $vars
 * @return string $html
 */
function agregaTelefono( $vars )
{
	$html = "<span class='success span-10'>Telefono Agregado</span>";
	$sql="Insert into telipext (tipo,direccion,asignada) 
	values ('telefono','" . $vars['numero_telefono'] . "','No')";
	if ( !ejecutaConsulta( $sql ) ) {
		$html = "<span class='success span-10'>Error no se ha agregado el </span>";
	}
	return $html;
		
}
//LISTADO ESPECIAL ¡¡TARDA MUCHO EN GENERARSE!!
function rarita()
{
	$sql = "SELECT DISTINCT(z.valor), c.Nombre, c.Categoria, f.observaciones 
	FROM `facturacion` as f join clientes as c on c.id like f.idemp join 
	z_sercont as z on z.idemp like c.id WHERE  Estado_de_cliente != 0 and 
	(c.Categoria like '%domiciliac%' or c.Categoria like '%despacho%' 
	or c.Categoria like 'Otros' or c.Categoria like '%Telefonica%' 
	or c.Categoria like '%oficina movil%') and
	z.servicio like 'Codigo Negocio' order by z.valor asc";
	$resultados = consultaGenerica( $sql );
	$html = "<input class='boton' value='[X] Cerrar' 
	onclick='cierra_listado_copias()' >
	<input type='button' class='boton' 
	onclick=window.open('inc/excel.php') value='Imprimir' />";
	$html .= "
	<table>
		<thead>
		<tr><th>Codigo</th>
			<th>Cliente</th>
			<th>Categoria</th>
			<th>Observaciones</th>
		</tr>
		</thead>
		<tbody>";
	foreach ( $resultados as $resultado ) {
		$color = 
			( preg_match( '#despacho#', $resultado[2] ) ) ? '#6699CC' : '#FF9900';
		$html .= "<tr>
		<td bgcolor='".$color."'>" . $resultado[0] . "</td>
		<td>" . $resultado[1] . "</td>
		<td>" . $resultado[2] . "</td>
		<td>" . $resultado[3] . "</td>
		</tr>";
	}
	$html .= "</table>";
	return $html;
}
/*
 * Funcion que genera el listado de ips asignadas y libres
 */
function consultaDeIps()
{
	global $con;
	$cogidas = "";
	$no_cogidas = "";
	$sql = "Select c.Nombre, z.valor from z_sercont as z join clientes as c 
	on z.idemp like c.id where z.servicio like 'Direccion IP' order by z.valor";
	$resultados = consultaGenerica( $sql );
	foreach ( $resultados as $resultado ) {
		$ipes = explode( ".", $resultado['valor'] );
		$ocupadas[intval($ipes[3])] = $resultado[0];
	}
	for( $i=1, $j = 0, $k = 0;$i<=254;$i++, $j++, $k++ ) {
		$clase = ( $i % 2 == 0) ? 'par' : 'impar'; 
		if ( array_key_exists( $i, $ocupadas ) ) {
			$clase = ( $j % 2 == 0) ? 'par' : 'impar'; 	
			$cogidas .= "<div class='" . $clase . "'>
			172.26.0." . $i . "&nbsp;&nbsp;&nbsp;&nbsp;". $ocupadas[$i] . "</div>";
		} else {	
			$clase = ( $k % 2 == 0) ? 'par' : 'impar'; 
			$no_cogidas .="<div class='" . $clase . "'>
			172.26.0.". $i ."&nbsp;&nbsp;&nbsp;&nbsp;</div>";
		}
	}
	return $cogidas."-".$no_cogidas;
}
//***********************************************************************************************/
function listadoPersonalizado( $vars )
{
	global $con;
	$cadena = "";
	//buscamos el nombre de la categoria_del_cliente
	$tabla = "categorías clientes";
	//$sql = "SELECT * FROM `$tabla`";
	
	if($vars['tipo']=='social')
	{
		$sql ="Select * from clientes where direccion not like '' and 
		Estado_de_cliente like '-1' order by Nombre";
	}
	else
	{
		if($vars['tipo']=='comercial')
		{
			$sql="Select * from clientes where dcomercial != '' and 
			Estado_de_cliente like '-1' order by Nombre";
		}
		else
		{
			if($vars['tipo']=='conserje') {
				$sql = "Select * from clientes 
				where (categoria like '%domicili%' 
				or categoria like '%despachos%' 
				or categoria like '%tencion telefo%') 
				and Estado_de_cliente like '-1' order by Nombre";
			} else {
				if ( $vars['tipo'] == 'independencia' ) {
					$sql ="Select * from clientes where direccion 
					like '%Independencia, 8 dpdo%' 
					and Estado_de_cliente like '-1' order by Nombre";
				} else {	
					$sql = "Select * from clientes as c join `" . $tabla . "` 
					as d on c.Categoria = d.Nombre where d.id 
					like " . $vars['tipo'] . " and 
					c.Estado_de_cliente like '-1' order by c.Nombre";
				}
			}
		}
	}
	$consulta = mysql_query( $sql, $con );
	$i=0;
	while( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
		$clase = ( $i % 2 ) ? 'listado_par' : 'listado_impar';
		$i++;
		$cadena.="<div class='".$clase."'>".$i." ".$resultado[1]."
		<span class='direccion_esp'>" . $resultado['Direccion'] ."</span>
		</div>";
	}
	$cadena .= "<div><input type='button' class='boton' 
	onclick=window.open('inc/excel.php?tipo=" . $vars['tipo'] . "') 
	value='Imprimir' /></div>";
	return $cadena;
}
/*
 * Borra el telefono que esta libre
 * 
 * @param array $vars
 * @return string $html
 */
function borraTelefonoAsignado( $vars )
{
	$html = "<span class='success span-10'>Telefono Borrado</span>";
	$sql = "Delete from telipext where direccion 
	like '" . $vars['telefono'] ."'";
	if ( !ejecutaConsulta( $sql ) ) {
		$html = "<span class='success span-10'>
		No se ha borrado el Telefono</span>";
	} 
	return $html;
}
/*
 * Edita la descripcion del telefono libre
 * 
 * @param array $vars
 * @return string $html
 */
 function editaTelefonoAsignado( $vars )
 {
	$sql = "SELECT * FROM `telipext` 
	WHERE direccion LIKE '". $vars['telefono'] ."'";
	$resultado = consultaUnica( $sql );
	$html = "<input type='text' id='descripcion_". $vars['telefono'] . "' 
	value='".$resultado[0]['descripcion']."'>
	<input type='hidden' id='identificador_" . $vars['telefono'] . "' 
	value='".$resultado[0]['id']."'>
	<input type='button' 
	onclick='actualiza_descripcion_telefono(\"" . $vars['telefono'] . "\")' 
	value='Actualizar'>";
 	return $html;
 }
 /*
  * Actualiza la descripcion del telefono libre
  * 
  * @param array $vars
  * @return string $html
  */
 function actualizaTelefonoAsignado( $vars )
 {
 	$html = "<span class='success span-10'>Telefono Actualizado</span>";
	$sql = "Update `telipext` set 
	descripcion = '" . $vars['descripcion'] ."' 
	where id like " . $vars['id'];
	if ( !ejecutaConsulta( $sql ) ) {
		$html = "<span class='success span-10'>
		No se ha actualizado el telefono</span>";
	}
	return $html;
 }