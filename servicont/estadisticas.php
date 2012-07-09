<?php
/**
 * Generacion de consultas de estadisticas nuevas Julio 2008-Agosto 2008 DEBUG
 */
require_once '../inc/variables.php';
checkSession();
if ( isset( $_SESSION['usuario']) ) {
	if ( isset( $_POST['opcion'] ) ) {
		switch ( $_POST['opcion'] ) {
			case(0):
			    $cadena = formulario( $_POST );
			    break;//Generamos el formulario
			case(1):
			    $cadena = respuesta( $_POST );
			    break;//Generamos la respuesta
			case(2):
			    $cadena = comparativas( $_POST );
			    break;//Genera la pantalla de comparativa
		}
		echo $cadena;
	} else {
		echo "No se ha pasado opcion";
	}
} else {
	echo "No se ha iniciado sesion";
}
/*
 * Funciones Genericas
 */ 
 
/*
 * Cambio de Formato de Fecha
 */  
function cambiaf($stamp) {
	$fdia = explode ( "-", $stamp );
	$fdia2 = explode ( " ", $fdia [2] );
	$fecha = $fdia2 [0] . "-" . $fdia [1] . "-" . $fdia [0];
	return $fecha;
}
/*
 * Devuelve el nombre del cliente
 */
function nombre_cliente($cliente) {
	global $con;
	$sql = "Select Nombre from clientes where id like $cliente";
	$consulta = mysql_query ( $sql, $con );
	$resultado = mysql_fetch_array ( $consulta );
	return $resultado [0];
}
/*
 * Listado de Clientes
 */
function clientes() {
	global $con;
	if (isset ( $_GET ['emp'] )) {
		$_SESSION ['wcliente'] = $_GET ['emp'];
		$cliente = $_SESSION ['cliente'];
	} else {
		$cliente = 0;
	}
	$form = "<select id='cliente' name='cliente'>";
	$sql = "Select id, Nombre from clientes order by Nombre"; // mostramos
	                                                          // tambien los
	                                                          // inactivos
	$consulta = mysql_query ( $sql, $con );
	$form .= "<option value='0'>-Cliente-</option>";
	while ( true == ($resultado = mysql_fetch_array ( $consulta )) ) {
		$marcado = ($cliente == $resultado [0]) ? "selected" : "";
		if (trim ( $resultado [1] ) != "") {
			$form .= "<option value='$resultado[0]' $marcado >" . 
				$resultado [1] . " " . $resultado [2] . "</option>";
		}
	}
	$form .= "</select>";
	return $form;
}
/*
 * Select de categorias
 */
function categorias() {
	global $con;
	$form .= "<select id='categoria' name='categoria'>";
	$sql = "Select categoria from clientes group by categoria";
	$consulta = mysql_query ( $sql, $con );
	$form .= "<option value='0'>-Categorias-</option>";
	while ( true == ($resultado = mysql_fetch_array ( $consulta )) ) {
		if (trim ( $resultado [0] ) != "") {
			$form .= "<option value='" . $resultado [0] . "' >" . $resultado [0] . "</option>";
		}
	}
	$form .= "</select>";
	return $form;
}
 /*
  * Selector de fecha
  */
function fecha($modo) {
	$form = dias ( $modo ) . mes ( $modo ) . anyo ( $modo );
	return $form;
}
 /*
 * dias(): Devuelve 31 independientemente del mes marcado
 * Revisar:Generar funcion que dependiendo del mes y año de un valor u otro
 */
function dias($modo) {
	switch ($modo) {
		case 0 :
			$tipo = "dia";
			break;
		case 1 :
			$tipo = "diaf";
			break;
		case 2 :
			$tipo = "rdia";
			break;
		case 3 :
			$tipo = "rdiaf";
			break;
	}
	$select = "<select id='" . $tipo . "' name='" . $tipo . "'>";
	$select .= "<option value='0'>-Dia-</option>";
	for($i = 1; $i <= 31; $i ++) {
		$select .= "<option value='$i'>$i</option>";
	}
	$select .= "</select>";
	return $select;
}
/*
 * mes(): Opciones del campo select que muestran el mes
 */
function mes($modo) {
	switch ($modo) {
		case 0 :
			$tipo = "mes";
			break;
		case 1 :
			$tipo = "mesf";
			break;
		case 2 :
			$tipo = "rmes";
			break;
		case 3 :
			$tipo = "rmesf";
			break;
	}
	$select = "<select id='" . $tipo . "' name='" . $tipo . "'>";
	
	$meses = nombreMeses ();
	
	$select .= "<option value=0>-Mes-</option>";
	for($i = 1; $i <= 12; $i ++) {
		$select .= "<option value='" . $i . "'>" . $meses [$i] . "</option>";
	}
	$select .= "</select>";
	return $select;
}
/*
 * ano(): Funcion que muestra los anyos desde el 2000 hasta	
 */	
function anyo($modo) {
	$select = "";
	switch ($modo) {
		case 0 :
			$tipo = "ano";
			break;
		case 1 :
			$tipo = "anof";
			break;
		case 2 :
			$tipo = "rano";
			break;
		case 3 :
			$tipo = "ranof";
			break;
	}
	$select .= "<select id='" . $tipo . "' name='" . $tipo . "'>";
	$select .= "<option value='0'>-A&ntilde;o-</option>";
	$select .= "<option value='2007'>2007</option>";
	for($i = 8; $i <= 20; $i ++) {
		if ($i <= 9)
			$valor = "200" . $i;
		else
			$valor = "20" . $i;
		$select .= "<option value='" . $valor . "'>" . $valor . "</option>";
	}
	$select .= "</select>";
	return $select;
}
	/*
 * Select de servicios
 */
function servicios() {
	global $con;
	$form .= "<select id='servicios' name='servicios'>";
	$sql = "Select trim(servicio) from historico 
	group by trim(servicio) order by trim(servicio)";
	$consulta = mysql_query ( $sql, $con );
	$form .= "<option value='0'>-Servicios-</option>";
	while ( true == ($resultado = mysql_fetch_array ( $consulta )) ) {
		$form .= "<option value='" . trim ( $resultado [0] ) . "' >" . trim ( $resultado [0] ) . "</option>";
	}
	$form .= "</select>";
	return $form;
}
/*
 * Funciones Especificas Aplicacion
 */ 

 /**
  * Se genera el formulario para la consulta por cliente
  * 
  * @param array $vars
  */
function formulario($vars)
{
	//Este al ser entre fechas por cliente en formulario tenemos fechas y clientes
	//y devolvera servicios
	$cadena = "<form name='consulta' id='consulta' method='post' 
	onsubmit='procesa();return false'>";
	$cadena.="<input type='hidden' name='formu' id='formu' 
	value='".$vars['form']."'>";
	$inicioFin = "Inicio:".fecha(0)."Fin:".fecha(1);
	switch ( $vars['form'] ) {
		//Entre Fechas por cliente
		case(0):
		    $cadena.=clientes().$inicioFin;
		break;
		//Entre Fechas por categoria
		case(1):
		    $cadena.=categorias().$inicioFin;
		break;
		//Entre Fechas por servicios
		case(2):
		    $cadena.=servicios()."Inicio:".$inicioFin;
		break;
		//Entre Fechas por cliente/servicios
		case(3):
		    $cadena.=clientes().servicios()."<br/>".$inicioFin;
		break;
		//Entre Fechas por categoria/servicios
		case(4):
		    $cadena.=categorias().servicios()."<br/>".$inicioFin;
		break;
		//Servicios por volumen de facturacion entre fechas
		case(5):
		    $cadena.=servicios()."<br/>".$inicioFin;
		break;
		//Clientes por volumen de facturacion entre fechas
		case(6):
		    $cadena.=clientes()."<br/>".$inicioFin;
		break;
		//Comparativas
		case(7):
		    $cadena.= comparativas( $vars );
		break;
	}
	if ( $vars['form'] !=7 ) {
		$cadena.="<br /><input type='radio' name='tipo' value='acumulado' 
		    checked='checked'> Acumulado
		    <input type='radio' name='tipo' value='detallado'> Detallado
		    <input type='radio' name='tipo' value='comparativa'>Comparativa
		    &nbsp;&nbsp;Limite Comparativa:";
		$cadena.="<select name='limite'>";
		for ( $i=10; $i<=90; $i=$i+10) {
		    $cadena.="<option value=".$i.">".$i."</option>";
		}
	    $cadena.="<option value=0>Todos</option>";
	    $cadena.="</select>";
	    $cadena.="<input type='submit' class='boton' value='Buscar'>";
	}
	$cadena.="</form>";
	$cadena.="<div id='resultados'></div>";
	return $cadena;
}

/*
 * Funcion generadora de comparativas, iba la marcianada
 */
function comparativas($vars)
{
	if(!isset($vars['tipo']))
	{
		$cadena ="<input type='hidden' name='formu' id='formu' value='7' />";
		$cadena.="Comparacion de:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<select name='tipo_comparativa' id='tipo_comparativa' onchange='comparativa()'>";
		$cadena .="<option value='0'>-- Opcion --</option>";
		$cadena .="<option value='1'>Clientes</option>";
		$cadena .="<option value='2'>Servicio</option>";
		$cadena .="<option value='3'>Categoria</option>";
		$cadena .="</select>";
		$cadena.= "<div id='comparativas'></div>";
	}
	else
	{
		switch($vars['tipo'])
		{
			case(1):$cadena="Seleccione Cliente:
			    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".clientes().servicios();break;
			case(2):$cadena="Seleccione Servicio:&nbsp;&nbsp;&nbsp;".servicios();break;
			case(3):$cadena="Seleccione Categoria:&nbsp;".categorias().servicios();break;
		}
	$cadena_fechas ="
	<br />Inicio Rango:
	<input type='text' readonly size='10' id='fecha_inicio_a' name='fecha_inicio_a' />
	<button TYPE='button' class='calendario' id='boton_fecha_inicio_a'></button>
	Fin Rango:
	<input type='text' readonly size='10'id='fecha_fin_a' name='fecha_fin_a' />
	<button type='button' class='calendario' id='boton_fecha_fin_a' ></button>
	&nbsp;&nbsp;<strong><-- Frente a --></strong>&nbsp;&nbsp;
	Inicio Rango:<input type='text' readonly size='10' id='fecha_inicio_b' 
	name='fecha_inicio_b' />
	<button TYPE='button' class='calendario' id='boton_fecha_inicio_b'></button>
	Fin Rango:
	<input type='text' readonly size='10' id='fecha_fin_b' name='fecha_fin_b' />
	<button type='button' class='calendario' id='boton_fecha_fin_b'></button>";
	$cadena.=$cadena_fechas."<input type='submit' value='Comparar' />";
	}
	return $cadena;
}
/*
 * Generamos la respuesta dependiendo de los parametros que llegan
 */
function respuesta($vars) {
	switch ($vars ['formu']) {
		case (0) :
			$vars ['titulo'] = "Consumo mensual y acumulado entre fechas por cliente";
			$cadena = consulta ( $vars );
			break;
		case (1) :
			$vars ['titulo'] = "Consumo mensual y acumulado entre fechas por categoria";
			$cadena = consulta ( $vars );
			break;
		case (2) :
			$vars ['titulo'] = "Consumo mensual y acumulado entre fechas por servicios";
			$cadena = consulta ( $vars );
			break;
		case (3) :
			$vars ['titulo'] = "Consumo mensual y acumulados entre fechas por cliente/servicio";
			$cadena = consulta ( $vars );
			break;
		case (4) :
			$vars ['titulo'] = "Consumo mensual y acumulado entre fechas por categoria/servicio";
			$cadena = consulta ( $vars );
			break;
		case (5) :
			$vars ['titulo'] = "Servicios mas facturados";
			$cadena = consulta ( $vars );
			break;
		case (6) :
			$vars ['titulo'] = "Clientes con mas facturacion";
			$cadena = consulta ( $vars );
			break;
		case (7) :
			$vars ['titulo'] = "Comparativas";
			$cadena = consulta ( $vars );
			break;
	}
	return $cadena;
}
/*
 * Consultas en pruebas //O cogemos del global de globales
 */ 
 function consulta($vars)
 {
 	$print = true;
	$confecha=consulta_fecha($vars); //Lo paso directamente a una funcion para que saque las fechas
	if($confecha != "" && $vars['formu']!= 5)
		$con_fecha = " and ".$confecha;
	else
		if($vars['formu']==5)
			$con_fecha = $confecha;
		else
			$con_fecha = "";
	/*Limite de registros*/
	if($vars['limite']!=0)
			$limite = " LIMIT ".$vars['limite']." ";
			else
			$limite = " ";
	/*Filtro de la categoria de la consulta*/
	//Seleccion de tipo de consulta
	
//Consultas entre fechas de clientes	
	if($vars['formu']==0) {
		if($vars['tipo']=="acumulado") {
			$sql="SELECT trim(h.servicio) as Servicio, 
			sum(h.cantidad) as Unidades, sum(h.cantidad*h.unitario) as Importe, 
			sum(h.cantidad*h.unitario*h.iva/100) Iva, 
			sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
			FROM `historico` as h 
			INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
			INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
			WHERE c.id_cliente like '".$vars['cliente']."' ".$con_fecha." 
		    GROUP BY h.servicio";
		} else {
			$sql="SELECT c.fecha, trim(d.servicio) as Servicio, 
			trim(d.obs) as Observaciones, d.cantidad as Unidades, 
			d.unitario as Importe, d.iva, 
			((d.unitario*d.cantidad) + (d.unitario*d.cantidad)*d.iva/100) as Total
			FROM `historico` AS d
			INNER JOIN `regfacturas` AS c on c.`codigo` = d.`factura` 
			INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
			WHERE c.id_cliente like '".$vars['cliente']."' ".$con_fecha." order by c.fecha";
		}
		$subtitulo = nombre_cliente($vars['cliente']);
	}
//Consultas entre fechas de Categorias	
	if($vars['formu']==1) {
		if($vars['tipo']=="acumulado") {
			$sql="SELECT trim(h.servicio) as Servicio,
            sum(h.cantidad) as Unidades, sum(h.cantidad*h.unitario) as Importe,
            sum(h.cantidad*h.unitario*h.iva/100) Iva,
            sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
			FROM `historico` as h 
			INNER JOIN `regfacturas` AS c on h.factura = c.codigo
            INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
            INNER JOIN `entradas_salidas` AS e ON e.idemp like l.Id
            WHERE ( (e.salida >= c.fecha or e.salida like '0000-00-00')
            and e.entrada <= c.fecha 
            and e.Categoria like '".$vars['categoria']."'
            ".$con_fecha.") GROUP BY h.servicio";
		} else {
			/*$sql="SELECT c.fecha, s.Nombre as Cliente, trim(d.servicio) as Servicio,
            trim(d.obs) as Observaciones, d.cantidad as Unidades, d.unitario as Importe,
            d.iva, ((d.unitario*d.cantidad) + (d.unitario*d.cantidad)*d.iva/100) as Total
			FROM `historico` AS d
			INNER JOIN `regfacturas` AS c on c.`codigo` = d.`factura`
            INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
			WHERE s.Categoria like '".utf8_decode($vars[categoria])."' ".$con_fecha." order by c.fecha";
		*/
            $sql = "SELECT c.fecha, s.Nombre, trim(d.servicio) as Servicio,
            trim(d.obs) as Observaciones, d.cantidad as Unidades, 
            d.unitario as Importe, 
            d.iva, ((d.unitario*d.cantidad) + (d.unitario*d.cantidad)*d.iva/100) as Total 
            FROM entradas_salidas e
            inner join clientes as s on e.idemp like s.Id
            inner join regfacturas as c on e.idemp like c.id_cliente
            inner join historico as d on c.codigo like d.factura
            where  ((e.salida >= c.fecha or e.salida like '0000-00-00')
            and e.entrada <= c.fecha 
            and e.categoria like '".$vars['categoria']."' ".$con_fecha.")
            order by c.fecha, s.Nombre";
		}
        $subtitulo = $vars['categoria'];
	}
//Consumo mensual y acumulado entre fechas por servicios,control de almacenaje y fijos
	if($vars['formu']==2)
	{
		if($vars['tipo']=="acumulado") {
			$sql="SELECT sum(h.cantidad) as Unidades, 
			sum(h.cantidad*h.unitario) as Importe, 
			sum(h.cantidad*h.unitario*h.iva/100) Iva, 
			sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
			FROM `historico` as h 
			INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
			INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
			WHERE trim(h.servicio) like '".$vars['servicios']."' ".$con_fecha." 
		    GROUP BY h.servicio";
		} else {
			$sql="SELECT c.fecha, s.nombre as Cliente, d.obs as Observaciones, 
			d.cantidad as Unidades, d.unitario as Importe, d.iva, 
			((d.unitario*d.cantidad) + (d.unitario*d.cantidad)*d.iva/100) as Total
			FROM `historico` AS d
			INNER JOIN `regfacturas` AS c on c.`codigo` = d.`factura` 
			INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
			WHERE trim(d.servicio) LIKE '".$vars['servicios']."' ".$con_fecha." 
			order by c.fecha";
		}
		$subtitulo = $vars['servicios'];
	}
//Consumo mensual y acumulados entre fechas por cliente/servicio
	if($vars['formu']==3)
	{
		if($vars['tipo']=="acumulado") {
			$sql="SELECT sum(h.cantidad) as Unidades, 
			sum(h.cantidad*h.unitario) as Importe, 
			sum(h.cantidad*h.unitario*h.iva/100) Iva, 
			sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
			FROM `historico` as h 
			INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
			INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
			WHERE trim(h.servicio) like '".$vars['servicios']."' ".$con_fecha." 
		    and c.id_cliente LIKE '".$vars['cliente']."' GROUP BY h.servicio";
		} else {
			$sql="SELECT c.fecha, d.obs as Observaciones, d.cantidad as Unidades, 
			d.unitario as Importe, d.iva, 
			((d.unitario*d.cantidad) + (d.unitario*d.cantidad)*d.iva/100) as Total
			FROM `historico` AS d
			INNER JOIN `regfacturas` AS c on c.`codigo` = d.`factura` 
			INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
			WHERE trim(d.servicio) LIKE '".$vars['servicios']."' ".$con_fecha." 
			and c.id_cliente LIKE '".$vars['cliente']."' order by c.fecha";
		}
		$subtitulo = nombre_cliente($vars['cliente'])." / ".$vars['servicios'];
	}
//Consumo mensual y acumulado entre fechas por categoria/servicio
	if($vars['formu']==4) {
		if($vars['tipo']=="acumulado") {
			$sql="SELECT sum(h.cantidad) as Unidades, 
			sum(h.cantidad*h.unitario) as Importe, 
			sum(h.cantidad*h.unitario*h.iva/100) Iva, 
			sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
			FROM `historico` as h 
			INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
			INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
			WHERE trim(h.servicio) like '".$vars['servicios']."' ".$con_fecha." 
		    and l.Categoria LIKE '".$vars['categoria']."' GROUP BY h.servicio";
		} else {
			$sql="SELECT c.fecha, s.Nombre as Cliente, d.obs as Observaciones, 
			d.cantidad as Unidades, d.unitario as Importe, d.iva, 
			((d.unitario*d.cantidad) + (d.unitario*d.cantidad)*d.iva/100) as Total
			FROM `historico` AS d
			INNER JOIN `regfacturas` AS c on c.`codigo` = d.`factura` 
			INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
			WHERE trim(d.servicio) LIKE '".$vars['servicios']."' 
			".$con_fecha." and s.Categoria LIKE '".$vars['categoria']."' 
			order by c.fecha";
		}
		$subtitulo = nombre_cliente($vars['categoria'])." / ".$vars['servicios'];
	}
//Servicios por volumen de facturacion + facturados
	if($vars['formu']==5){
		if($vars['tipo']=="acumulado") {
			$sql="SELECT trim(h.servicio) as Servicio, sum(h.cantidad) as Unidades, 
			sum(h.cantidad*h.unitario) as Importe, sum(h.cantidad*h.unitario*h.iva/100) as Iva,
			sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
			FROM `historico` as h 
			INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
			INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
			".$con_fecha." GROUP BY trim(h.servicio) order by Total desc";
		} else {
			if ( $vars['tipo'] == "detallado" ) {
			    $sql="SELECT c.fecha, trim(d.servicio) as Servicio, 
			        trim(d.obs) as Observaciones, s.Nombre as Cliente, 
			        d.cantidad as Unidades, d.unitario as Importe, d.iva, 
			        ((d.unitario*d.cantidad) + (d.unitario*d.cantidad)*d.iva/100) as Total
			        FROM `historico` AS d
			        INNER JOIN `regfacturas` AS c on c.`codigo` = d.`factura` 
			        INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
			        ".$con_fecha." order by c.fecha";
			} else {
				if($vars['servicios']!="0") {
					$filtra_servicio = " and trim(h.servicio) 
					like trim('".$vars['servicios']."') ";
				} else {
					$filtra_servicio = "  ";
				}
				$rango=array_de_rangos($vars);
				foreach($rango as $rangillo) {
				    $esql[]="SELECT trim(h.servicio) as Servicio,
				        sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
				        FROM `historico` as h 
				        INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
				        INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
				        ".$rangillo." ".$filtra_servicio." GROUP BY trim(h.servicio) 
				        order by Total desc ".$limite;
				}
			//$cadena.=$vars[servicios];
			}
		}
		$subtitulo = "";
	}
	//Clientes por volumen de facturacion + facturados
	if($vars['formu']==6)
	{
		if($vars['tipo']=="acumulado")
		$sql="SELECT l.Nombre as Cliente, sum(h.cantidad) as Unidades, 
		sum(h.cantidad*h.unitario) as Importe, sum(h.cantidad*h.unitario*h.iva/100) as Iva,
		sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
		FROM `historico` as h 
		INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
		INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
		".$con_fecha." GROUP BY l.Nombre order by Total desc";
		else
			if($vars['tipo']=="detallado")
		$sql="SELECT c.fecha, s.Nombre as Cliente, 
		trim(d.servicio) as Servicio, trim(d.obs) as Observaciones,  
		d.cantidad as Unidades, d.unitario as Importe, d.iva, 
		((d.unitario*d.cantidad) + (d.unitario*d.cantidad)*d.iva/100) as Total
		FROM `historico` AS d
		INNER JOIN `regfacturas` AS c on c.`codigo` = d.`factura` 
		INNER JOIN `clientes` AS s ON c.id_cliente = s.Id
		".$con_fecha." order by c.fecha";
			else /*Comparativa entre rangos*/
			{
				if($vars['cliente']!=0)
					$filtra_cliente = " and c.id_cliente like ".$vars['cliente']." ";
				else
					$filtra_cliente = "";
				$rango=array_de_rangos($vars);
				foreach($rango as $rangillo)
					$esql[]="SELECT l.Nombre as Cliente, 
					sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
		            FROM `historico` as h 
		            INNER JOIN `regfacturas` AS c on 
		            h.factura = c.codigo INNER JOIN `clientes` AS l ON c.id_cliente = l.Id
		            ".$rangillo." ".$filtra_cliente." 
				    GROUP BY l.Nombre order by Total desc ".$limite;
			}
		$subtitulo = "";
	}
	//THIS IS THE MOTHER OF THE LAMB - Nueva seccion Comparativas
	if($vars['formu']==7) {
		switch($vars['tipo_comparativa']) {
			//Clientes
			case 1:
			$sql="SELECT l.Nombre as Cliente, 
			sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
		    FROM `historico` as h 
		    INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
		    INNER JOIN `clientes` AS l ON c.id_cliente = l.Id";
			if($vars['cliente']!=0) {
				$filtro = " and c.id_cliente like ".$vars['cliente']." ";
				$grupo = " GROUP BY l.Nombre";
			}
			else
			{	
				$sql = ereg_replace("l.Nombre as Cliente,"," 1 ,",$sql);
				$filtro = " ";
			}
			if($vars['servicios']!="0")
				$filtro .= " and trim(h.servicio) like trim('".$vars['servicios']."') ";	
			break;
			
			//Servicios
			case 2:
			$sql="SELECT trim(h.servicio) as Servicio, 
			    sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
				FROM `historico` as h 
				INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
				INNER JOIN `clientes` AS l ON c.id_cliente = l.Id";
			if($vars['servicios']!="0") {
				$filtro = " and trim(h.servicio) like trim('".$vars['servicios']."') ";
				$grupo = " GROUP BY trim(h.servicio)";
			} else {
			    $sql="SELECT 1, sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
				FROM `historico` as h 
				INNER JOIN `regfacturas` AS c on h.factura = c.codigo 
			    INNER JOIN `clientes` AS l ON c.id_cliente = l.Id";
			    $filtro = "  ";
			}
			break;		
			
			//Categorias
			case 3:
			$sql="SELECT 1, sum(h.cantidad*h.unitario+h.cantidad*h.unitario*h.iva/100) as Total
			FROM `historico` as h 
			INNER JOIN `regfacturas` AS c on h.factura = c.codigo INNER JOIN `clientes` AS l ON c.id_cliente = l.Id";
			if($vars['categoria']!="0")
			{
				$filtro = " and l.Categoria like '".$vars['categoria']."' ";
				$grupo = " GROUP BY l.Categoria";
			}
			else
			{
				$sql = ereg_replace("l.Categoria as Categoria,"," 1 ,",$sql);
				$filtro = "  ";
			}
			if($vars['servicios']!="0")
				$filtro .= " and trim(h.servicio) like trim('".$vars['servicios']."') ";	
			break;
		}
		//Rango de fechas de la comparativa
		$rangete_a = genera_consultas($vars['fecha_inicio_a'],$vars['fecha_fin_a']);
		//echo $sql." ".$filtro." ".$grupo.$vars[servicios];
		$subtitulo = "Datos del ".$vars['fecha_inicio_a']." al ".$vars['fecha_fin_a']." de ";
		if(is_array($rangete_a))
		foreach($rangete_a as $rango)
		{
			$rango.="-1";
			$esql[]=$sql." where year('$rango') like year(c.Fecha) 
			and month('$rango') like month(c.fecha)  ".$filtro.$grupo;
		}
		if($vars['fecha_inicio_b']!='' && $vars['fecha_fin_b']!='')
		{
			$rangete_b = genera_consultas($vars['fecha_inicio_b'],$vars['fecha_fin_b']);
			$subtitulo2 = "Datos del ".$vars['fecha_inicio_b']." al ".$vars['fecha_fin_b'];
			if(is_array($rangete_b))
			foreach($rangete_b as $rango)
			{
				$rango.="-1";
				$esql2[]=$sql." where year('$rango') like year(c.Fecha) 
				and month('$rango') like month(c.fecha)  ".$filtro.$grupo;
			}
		}
	}
//EJECUTAMOS LAS CONSULTAS
if($vars['formu']!=7)
{
	$_SESSION['consulta']=$sql;//Almacenamos la consulta como variable de sesion
	$_SESSION['titulo']=$vars['titulo']." - ".$subtitulo;
	if(is_array($esql))
	{
		$cadena.=genera_la_tabla_chunga($esql,$vars,$subtitulo);//multiarray
		$print = false;
	}
	else
		$cadena=genera_la_tabla($sql,$vars,$subtitulo);
// 	$cadena.="<br/><span class='boton' 
// 	onclick='window.open(\"print.php?sql=".urlencode($sql)."\",\"_self\")'>Imprimir</span>";
	$_SESSION['sqlQuery'] = $sql;
	if ( $print ) {
    	$cadena .= "<br/><span class='boton' 
        	onclick='window.open(\"print.php\",\"_self\")'>Imprimir</span>";
	}
}
else
{
	if(is_array($esql))
	{
		$cadena.=genera_la_tabla_comparativas($esql,$vars,$subtitulo);//multiarray
		$print = false;
		if(is_array($esql2))
		{
		$cadena.="</tr></table><br/>";
		$cadena.=genera_la_tabla_comparativas($esql2,$vars,$subtitulo2);//multiarray
		$print = false;
		}
	}
	$cadena.="</tr></table>";
	$_SESSION['sqlQuery'] = $sql;
// 	$cadena.="<br/><span class='boton' 
// 	onclick='window.open(\"print.php?sql=".urlencode($sql)."\",\"_self\")'>Imprimir</span>";
	if ( $print ) {
		$cadena .= "<br/><span class='boton'
			onclick='window.open(\"print.php\",\"_self\")'>Imprimir</span>";
	}
	//session_start();
	unset($_SESSION['acumulado']);
	unset($_SESSION['datos_ant']);
}
    return $cadena;
}

 /*
  * Funcion genera consultas, otra que genera bucles
  */
function genera_consultas($inicio,$fin)
{
	global $con;
	//cambio formato de fechas
	$inicio=cambiaf($inicio);
	$fin=cambiaf($fin);
	//miramos cuantos años hay en el rango inicial
	$sql = "Select year(fecha) from regfacturas 
	where (fecha>='".$inicio."' and fecha<='".$fin."') group by year(fecha) 
	order by year(fecha)";
	//echo $sql;
	$consulta = mysql_query($sql,$con);
	while(true == ($resultado = mysql_fetch_array($consulta)))
	$anyos_inicio[] = $resultado[0];
	//miramos los meses para cada año
	if (is_array($anyos_inicio))
	foreach($anyos_inicio as $anyo)
	{
	$sql = "Select month(fecha) from regfacturas where 
	(fecha>='".$inicio."' and fecha<='".$fin."' and year(fecha) like ".$anyo.") 
	group by month(fecha) order by month(fecha)";
	$consulta = mysql_query($sql,$con);
	while(true == ($resultado = mysql_fetch_array($consulta)))
		$mes_anyo[]=$anyo."-".$resultado[0];
	}
	return $mes_anyo;
}
 /*
  * Para las comparativas, una consulta por mes
  */
 function array_de_rangos($vars)
 {
 	//Rango de años
	global $con;
	//he marcado los años
	
	//No he marcado ningun año
	if($vars['ano']==0 && $vars['anof']==0) {
		$sql = "Select year(fecha) from regfacturas group by year(fecha)";
		$consulta = mysql_query($sql,$con);
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
		if($resultado[0]>='2008')
			$cadena[]=" where year(c.Fecha) like ".$resultado[0]." ";
		else	
			$cadena[]= " where month(c.Fecha)>=8 
		    and year(c.Fecha) like ".$resultado[0]." ";
		}
	}
	//he marcado años
	if($vars['ano']!=0 && $vars['anof']!=0)
	{
		for($i=$vars['ano'];$i<=$vars['anof'];$i++)
		{
			//if($vars[mes]!=0 && $vars[mesf]==0)//el mismo mes en 2 años
			//if($vars[mes]!=0 && $vars[mesf]!=0)//rango en 2 años
		if($i>=2008)
		    $sql = "Select month(fecha) from regfacturas 
		    where year(fecha) like ".$i." group by month(fecha) ";
		else
		    $sql = "Select month(fecha) from regfacturas 
		    where year(fecha) like ".$i." and month(fecha) >= 8 group by month(fecha) ";
		$consulta = mysql_query($sql,$con);
		while(true == ($resultado = mysql_fetch_array($consulta)))
			$cadena[]= " where month(c.Fecha) like ".$resultado[0]." 
		    and year(fecha) like $i";
		}
	}
	return $cadena;
 }
 /*
  * Generacion de la consulta con las fechas
  */
 function consulta_fecha($vars)
 {
 	if($vars['diaf']==0 && $vars['mesf']==0 && $vars['anof']==0) //sin limite
	{
		$check=0;
		if($vars['dia']!=0)
		{
			$cadena.= " day(c.Fecha) like ".$vars['dia']." ";
			$check = 1;
		}
		if($vars['mes']!=0)
		{
			if($check == 1)
			$cadena.= " and ";
			$cadena.=" month(c.Fecha) like ".$vars['mes']." ";
			$check=1;
		}
		if($vars['ano']!=0)
		{
			if($check == 1)
			$cadena.= " and ";
			$cadena.=" year(c.Fecha) like ".$vars['ano']." ";
			$check=1;
		}
	}
	else
	{
		if($vars['dia']!=0)
		{
			$cadena.= " day(c.Fecha) >= ".$vars['dia']." ";
			$check = 1;
		}
		if($vars['mes']!=0)
		{
			if($check == 1)
			$cadena.= " and ";
			$cadena.=" month(c.Fecha) >= ".$vars['mes']." ";
			$check=1;
		}
		if($vars['ano']!=0)
		{
			if($check == 1)
			$cadena.= " and ";
			$cadena.=" year(c.Fecha) >= ".$vars['ano']." ";
			$check=1;
		}
		if($vars['diaf']!= 0)
				$cadena.= " and day(c.Fecha) <= ".$vars['diaf']." ";	
		if($vars['mesf']!= 0)
				$cadena.= " and month(c.Fecha) <= ".$vars['mesf']." ";
		if($vars['anof']!= 0)
				$cadena.= " and year(c.Fecha) <= ".$vars['anof']." ";
	}
	if($vars['formu']== 5 && $cadena != "")
	    $cadena= " Where ".$cadena;
	return $cadena;
 }

/*
 * Generacion de la tabla simple para las partes normales
 */

 function genera_la_tabla($sql,$vars,$subtitulo)
 {
 	global $con;
	$consulta = mysql_query($sql,$con);

	$cadena ="<table id='tabla' width='100%'><tr>";
	$cadena.="<tr><th></th><th colspan='".mysql_num_fields($consulta)."'>
		".$vars['titulo']." - ".$subtitulo."</th></tr>";
	if(mysql_numrows($consulta)>=10000 || mysql_numrows($consulta)==0) {
		if(mysql_numrows($consulta)>=10000) {
			$cadena.="<tr><th colspan='".mysql_num_fields($consulta)."'>
	    		Demasiados Resultados. Filtre Mas</th></tr>";
		} else {
			$cadena.="<tr><th colspan='".mysql_num_fields($consulta)."'>
	    		No hay Resultados.</th></tr>";
		}
	} else {
		$cadena.="<th></th>";
    	for($i=0;$i<=mysql_num_fields($consulta)-1;$i++)
	    	$cadena.= "<th>".mysql_field_name($consulta,$i)."</th>";
			$cadena.="</tr>";
			$j=0;
			$aux = " ";
			$aux2 = "";
			$minitot = 0;
			while(true == ($resultado = mysql_fetch_array($consulta))) {
				$j++;
				$clase = ( $j % 2 == 0 ) ? "par" : "impar";
    			if(isset($resultado["Nombre"]) && isset($resultado["fecha"])) {
					if((($resultado["Nombre"]!=$aux)&&($aux != " ")) 
                		||(($resultado["fecha"]!=$aux2)&&($aux != " "))) {
                		$cadena.="<tr><th colspan='8'>Total ".$aux." ".cambiaf($aux2)."</th>
                		<th>".round($minitot,2)."&euro;</th></tr>";
                		$pal_final = $cadena;
                		$aux2 = $resultado["fecha"];
                		$aux = $resultado["Nombre"];
                		$minitot = 0;
            		} else {
                		if($aux == " ") {
                    		$aux = $resultado["Nombre"];
                    		$aux2 = $resultado["fecha"];
                		}
            		}
                	$minitot = $minitot + $resultado['Total'];
    			}
				$cadena.="<tr><th>".$j."</th>";
				for($i=0;$i<=mysql_num_fields($consulta)-1;$i++) {
					switch(mysql_field_type($consulta,$i)) {
						case "string":
							if(mysql_field_name($consulta,$i)=="Servicio") {
								$campo = $resultado[$i];
							} else {
								$campo = $resultado[$i];
							}
						break;
						case "real":
							$campo = number_format($resultado[$i],2,',','.');
							$tot[$i]=$tot[$i]+$resultado[$i];
						break;
						case "date":
							$campo = cambiaf($resultado[$i]);
						break;
						default:
							$campo = $resultado[$i];
							$tot[$i] ="";
						break;
					}
					$cadena.="<td class='".$clase."'>".$campo."</td>";
				}
				$cadena.="</tr>";
			}
			if(isset($aux) && isset($aux2) && isset($minitot)) {
				$cadena.="<tr><th colspan='8'>Total ".$aux." ".cambiaf($aux2)."</th>
				<th>".round($minitot,2)."&euro;</th></tr>";
			}
			$cadena.="<tr><th></th>";
			for($i=0;$i<=mysql_num_fields($consulta)-1;$i++) {
				switch(mysql_field_type($consulta,$i)) {
					case "string":
						$cadena.="<th></th>";
					break;
					case "real":
						$cadena.="<th>".number_format($tot[$i],2,',','.')."</th>";
					break;
					default:
						$cadena.="<th></th>";
					break;
				}
			}
	}
	$cadena.="</tr>";
	$cadena.="</table>";
	$cadena.="<div id='titulo'>Total Resultados: ".mysql_numrows($consulta)."</div>";
	return $cadena;
}
 
/*
 * Generacion de las tablas de las comparativas 
 */
function genera_la_tabla_chunga($sql,$vars,$subtitulo)
{
	global $con;
	//LLenamos el array multidimensional
	$i=0;
	foreach ($sql as $key => $esquel)
	{
		//echo $key."=>".$esquel;
		$titulo[]=generamos_titulo($esquel);
		$consulta = mysql_db_query($dbname,$esquel,$con);
		while(true == ($resultado = mysql_fetch_array($consulta)))
		{
			$subdatos[$resultado[0]]=$resultado[1];
		}
		$datos[$i]=$subdatos;
		$i++;
		unset($subdatos);
	}
	$cadena.="Tabla de comparativas ".$subtitulo;
	$cadena.="";
	$k=0;//Contador de titulo;
	
	foreach($datos as $key => $dato)
	{
	//llenamos la tabla de claves
		$claves[0]="";
		$datillos[0]="";
		if(is_array($dato))
		foreach($dato as $clave => $datillo)
		$claves[]=$clave;
		//Llenamos la tabla de datos
		if(is_array($dato))
		foreach($dato as $clave => $datillo)
		{
		$datillos[]=$datillo;
		$estad[$clave]=$datillo;
		}
	$cadena.="<tr><th colspan='10'height='2px'>".$titulo[$k]."</th></tr>";
	//A partir de aqui en columnas de 10
	$cadena.="<tr>";
	echo count($dato);
	//En el caso de las comparativas al mostrar solo 1 categoria
	//Solo sale columna, esto es lo que hay que arreglar
	for($j=1;$j<=count($dato);$j++)
	{
		$cadena.="<th>".$j."</th>";
		if($j%10==0 || $j==count($dato)) //Llegamos al valor 10 y saltamos
		{
		$cadena.="</tr><tr>";
		//Aqui recorremos 10 veces la tabla que almacena las claves;
			if($j==count($dato)&& $j%10!=0) //No son 10 calcular el resto
				$ciclos = count($dato)%10;
			else
				$ciclos = 10;
			for($l=$j-$ciclos;$l<=$j-1;$l++)
			{
				if($vars[formu]==6)
					$cadena.="<td class='par' valign='top'><b>".$claves[$l+1]."</b></td>";
				else
					$cadena.="<td class='par' valign='top'><b>".$claves[$l+1]."</b></td>";
			}
		$cadena.="</tr><tr>";
			for($l=$j-$ciclos;$l<=$j-1;$l++)
			{
				$cadena.="<td class='impar'><b>".round($datillos[$l+1],2)."&euro;</b></td>";
			}
		$cadena.="</tr><tr>";
		//Diferencia de facturacion
		for($l=$j-$ciclos;$l<=$j-1;$l++)
			{
				$posicion=diferencia($claves[$l+1],$estad_ant,$estad);
				$cadena.="<td class='par'>".$posicion."</td>";
			}
		//Diferencia de posicion
		$cadena.="</tr><tr>";
			for($l=$j-$ciclos;$l<=$j-1;$l++)
			{
				//AQUI NOS QUEDAMOS
				$posicion=posicion($l+1,$claves_ant,$claves[$l+1]);
				$cadena.="<td class='impar'>".$posicion."</td>";
			}
		$cadena.="</tr><tr>";
		}
	}
	$claves_ant=$claves; //Para la comparativa
	$datillos_ant = $datillos; //Para la comparativa
	$estad_ant = $estad; //Para la comparativa
	unset($claves);
	unset($datillos);
	unset($estad);
	$cadena.="</tr>";
	$k++;	
	}
	$cadena.="</tabla>";
	return $cadena;
}

/*
 * Posiciona los valores en el array para su comparacion
 */
function posicion($l,$claves,$clave)
{
	//$l posicion actual, $claves ->array de claves ant, $clave valor de clave en la pos
	if(is_array($claves))
	{
	$pos=array_search($clave,$claves)-$l;
	if($pos>0)
		$pos="<font color='green'><b><- ".$pos."</b></font>";
	else
		if($pos<0)
		{	
			$pos=$pos*-1;	
			if(array_search($clave,$claves))
			$pos="<font color='red'><b>-> ".$pos."</b></font>";
			else
			$pos="--Sin datos--";
		}
		else
			$pos="<b>=</b>";
	}
	else
	{
	$pos="--Sin datos--";
	}
	return $pos;
}

/*
 * Buscamos la aguja en el pajar
 */
function diferencia($aguja,$pajar_ant,$pajar)
{
	if(is_array($pajar_ant))
		if(array_key_exists($aguja,$pajar_ant))
			if(array_key_exists($aguja,$pajar))
			{
				$valor = $pajar[$aguja]-$pajar_ant[$aguja];
				if($valor>0)
					$valor="<font color='green'><b>".round($valor,2)."&euro;</b></font>";
				else
					if($valor<0)
						$valor="<font color='red'><b>".round($valor,2)."&euro;</b></font>";
					else
						$valor="<b>".round($valor,2)."&euro;</b>";
			}
			else
				$valor = "--Sin datos--";
		else
			$valor = "--Sin datos--";
	else
		$valor="--Sin datos--";
	return $valor;
}

/*
 * Se genera el titulo de la tabla ¡Modificar los de las comparativas!
 */
function generamos_titulo($sql)
{
	$meses = nombreMeses();
	$wave1=explode("where month(c.Fecha) like",$sql);
	if($wave1[1]!="")
	$wave2=explode("and year(fecha) like",$wave1[1]);
	else
	$wave2=explode("year(c.Fecha) like",$wave1[0]);
	$wave3=explode("GROUP BY",$wave2[1]);
	if($wave1[1]!="")
		$titulo = $meses[intval($wave2[0])]."-".$wave3[0];
	else
		$titulo = $wave3[0];
	return $titulo;
}
/*
 * Generamos la tabla de las comparativas tabla chunga 2.0
 */
 function genera_la_tabla_comparativas($sql, $vars, $subtitulo) {
	global $con;
	$i = 0;
	$j = 0;
	$l = 0;
	$acumulado = 0;
	$dato_ant = 0;
	foreach ( $sql as $key => $esquel ) {
		$titulo [] = generamos_titulo_comparativa ( $esquel );
		$consulta = mysql_query ( $esquel, $con );
		while ( true == ($resultado = mysql_fetch_array ( $consulta )) ) {
			$subdatos [$resultado [0]] = $resultado [1];
		}
		$datos [$i] = $subdatos;
		$i ++;
		unset ( $subdatos );
	}
	$cadena .= "<div class='nuevas_comparativas'><h3>Tabla de comparativas " . $subtitulo . "</h3>";
	foreach ( $titulo as $tit ) {
		$cadena .= "<div class='tit_compa'>
		<div class='titulo'>" . $tit . "</div>";
		$matriz = $datos [$j];
		if (is_array ( $matriz )) {
			foreach ( $matriz as $key => $dato ) {
				$cadena .= "<div class='dato_impar'>" . round ( $dato, 2 ) . " &euro;</div>";
				$diferencia = round ( $dato - $dato_ant, 2 );
				$acumulado = $acumulado + $dato;
				$dato_ant = $dato;
			}
		} else {
			$cadena .= "<div class='dato_impar'>--Sin datos--</div>";
			$dato = 0;
		}
		$datos_ant [] = $dato;
		
		if (isset ( $_SESSION ['datos_ant'] )) {
			
			if (($dato != 0) && ($_SESSION ['datos_ant'] [$l] != 0)) {
				$porcentaje = round ( ($dato * 100 / $_SESSION ['datos_ant'] [$l]) - 100, 2 );
				if ($porcentaje > 0)
					$mmi = "<font color='green'>" . $porcentaje . "%</font>";
				else if ($porcentaje == 0)
					$mmi = $porcentaje . "%";
				else
					$mmi = "<font color='red'>" . $porcentaje . "%</font>";
				$cadena .= "<div class='dato_par'>" . $mmi . "</div>";
			} else
				$cadena .= "<div class='dato_par'>--Sin Datos--</div>";
		}
		$l ++;
		$cadena .= "</div>";
		$j ++;
	}
	// Tabla totales
	$cadena .= "<div class='tit_compa'><div class='titulo'>Acumulado</div>
	<div class='dato_impar'>" . round ( $acumulado, 2 ) . " &euro;</div>";
	if (isset ( $_SESSION ['acumulado'] )) {
		$total = round ( $acumulado - $_SESSION ['acumulado'], 2 );
		if ($_SESSION ['acumulado'] != 0) {
			$porcentaje = round ( ($acumulado * 100 / $_SESSION ['acumulado']) - 100, 2 );
			if ($porcentaje > 0)
				$mmi = "<font color='green'>" . $porcentaje . "%</font>";
			else if ($diferencia == 0)
				$mmi = $porcentaje;
			else
				$mmi = "<font color='red'>" . $porcentaje . "%</font>";
		} else
			$mmi = "--Sin datos--";
		$cadena .= "<div class='dato_par'>" . $mmi . "</div>";
		$cadena .= "</div>";
	} else {
		// $cadena.="<div class='dato_par'>&nbsp;</div></div>";
		$_SESSION ['acumulado'] = $acumulado;
		$_SESSION ['datos_ant'] = $datos_ant;
	}
	
	$cadena .= "</div>";
	return $cadena;
}
function generamos_titulo_comparativa($esquel) {
	$cadena = explode ( "year('", $esquel );
	$cadena1 = explode ( "-1')", $cadena [1] );
	$cadena2 = cambiaf ( $cadena1 [0] );
	$cadena3 = explode ( "-", $cadena2 );
	$meses = nombreMeses ();
	return $meses [$cadena3 [1]] . " / " . $cadena3 [2];
}
/**
 * Funcion que devuelve el nombre de los meses del año
 * 
 * @return array $meses
 */
function nombreMeses() {
	$meses = array (
			1=>"Enero", "Febrero", "Marzo", "Abril", "Mayo", 
			"Junio", "Julio", "Agosto", "Septiembre", "Octubre", 
			"Noviembre", "Diciembre" );
	return $meses;
}
