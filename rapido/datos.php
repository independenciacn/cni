<?php
/**
 * datos.php File Doc Comment
 *
 * Muestra los datos del cliente en el mes actual
 * Realizado por Ruben Lacasa Mas ruben@ensenalia.com 2006-2012
 *
 * PHP Version 5.2.6
 *
 * @category rapido
 * @package  cni/rapido
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
require_once '../inc/Servicio.php';
require_once '../inc/Cliente.php';
switch ($_POST['opcion'])
{
	case 1:$cadena = buscaCliente($_POST);
	break;
	case 2:$cadena = dameNombreCliente($_POST);
	break;
	case 3:$cadena = verServiciosContratados($_POST);
	break;
	case 4:$cadena = borraServicioContratado($_POST);
	break;
	case 5:$cadena = frmModificacionServicio($_POST);
	break;
	case 6:$cadena = modificacionServicio($_POST);
	break;
	case 7:$cadena = frmAltaServicio($_POST);
	break;
	case 8:$cadena = valorDelServicio($_POST);
	break;
	case 9:$cadena = agregaServicio($_POST);
	break;
	case 10:$cadena = ventanaObservaciones($_POST);
	break;
	case 11:$cadena = listadoFacturas($_POST);
	break;
	case 13:$cadena = borrarFactura($_POST);
	break;
	case 14:$cadena = filtros($_POST);
	break;
	case 15:$cadena = casos($_POST);
	break;
}
echo $cadena;
/**
 * Calcula lo que hay en el almacen y si hay que cobrarlo
 * 
 * @param string $cliente
 * @param string $mes
 * @param string $anyo
 * @return Array
 */
function almacenaje($cliente, $mes, $anyo)
{
	$sql = "SELECT bultos, 
			DATEDIFF(fin,inicio) as dias 
			FROM z_almacen 
			WHERE cliente LIKE ? 
			AND MONTH(fin) LIKE ? 
			AND YEAR(fin) LIKE ?";
	$resultados = Cni::consultaPreparada(
			$sql,
			array($cliente, $mes, $anyo),
			PDO::FETCH_CLASS
		);
	return $resultados;
}
/**
 * Devuelve el array con los datos de los servicios contratados por el cliente
 * 
 * @param string $cliente
 * @param string $mes
 * @param string $anyo
 * @return Array
 */
function servicioContratados($cliente, $mes, $anyo)
{
	$sql = "SELECT
			d.Servicio AS Servicio,
			d.Cantidad AS Cantidad,
			date_format(c.fecha,'%d-%m-%Y') AS Fecha,
			d.PrecioUnidadEuros AS Precio,
			d.ImporteEuro AS Importe,
			d.iva AS Iva,
			c.`Id Pedido` AS IdPedido,
			d.Observaciones AS Observaciones,
			d.Id AS IdServicio
			FROM `detalles consumo de servicios` AS d
			INNER JOIN `consumo de servicios` AS c
			ON c.`Id Pedido` = d.`Id Pedido`
			WHERE c.Cliente like ?
			AND ? LIKE DATE_FORMAT(c.fecha,'%c')
			AND ? LIKE DATE_FORMAT(c.fecha,'%Y')
			ORDER BY c.fecha ASC";
	$resultados = Cni::consultaPreparada(
			$sql,
			array($cliente, $mes, $anyo),
			PDO::FETCH_CLASS
	);
	return $resultados;
}
/**
 * Listado de servicios disponibles
 * 
 * @return string $texto
 */
function servicios()
{
	$servicios = New Servicio();
	$resultados = $servicios->listadoServiciosActivos();
	$html = "<select name='servicios' id='servicios' 
			onchange='dame_el_valor()'>";
	$html .= "<option value=0>--Seleccione Servicio--</option>";
	foreach ($resultados as $resultado) {
		$html .= "<option value='".$resultado->id."'>".
			$resultado->Nombre."</option>";
	}
	$html .= "</select>";
	return $html;
}
/**
 * Funcion que busca y muestra el nombre del cliente
 * 
 * @param array $vars
 * @return string $html
 */
function buscaCliente($vars)
{
	$html = "";
	if ($vars['texto'] != "") {
		$cliente = new Cliente();
		$resultados = $cliente->buscaCliente($vars['texto']);
		$html .="<ul>";
		foreach ($resultados as $resultado) {
			$texto = preg_replace(
				"#".$vars['texto']."#i",
				"<b><u>".strtoupper($vars['texto'])."</u></b>",
				$resultado->Nombre
			);
			$html .="
				<li>
				<span class='lbl_clientes' 
					onclick='marca(".$resultado->Id.")'
					onmouseout='quitar_color(".$resultado->Id.")'
					onmouseover='cambia_color(".$resultado->Id.")'
					id='linea_".$resultado->Id."'>".$texto."
				</span>
				</li>";
		}
		$html .="</ul>";
	}
	return $html;
}
/**
 * Funcion que devuelve el nombre del cliente y lo pone en el campo de texto
 * 
 * @param array $vars
 * @return string $cadena
 */
function dameNombreCliente($vars)
{
	$cliente = New Cliente($vars['cliente']);
	$html = $cliente->id.";".$cliente->nombre;
	return $html;
}
/**
 * Funcion que muestra los datos de los servicios contratados del cliente en 
 * el mes seleccionado
 * 
 * @param array $vars
 * @return string $cadena
 */
function verServiciosContratados($vars)
{
	$acumuladoSubtotal = 0;
	$acumuladoTotal = 0;
	$acumuladoCantidad = 0;
	$subtotal = 0;
	$total = 0;
	$celda = 0;
	$html = "
		<span class='agregar' 
			onclick='ver_frm_agregar_servicio(".$vars['cliente'].")'>
			Agregar Servicio</span>
		<div id='form_agregar'></div>
		<table class='tabla' width='100%'>
		<tr>
			<th>Fecha</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>Servicio</th>
			<th>Cantidad</th>
			<th>Precio Unidad</th>
			<th>Importe</th>
			<th>Iva</th>
			<th>Total</th>
		</tr>";
	/**
	 * Almacenaje
	 */
	$servicio = new Servicio($vars['anyo']."-".$vars['mes']."-01");
	$servicio->setServicioByName('Almacenaje');
	$resultados = almacenaje($vars['cliente'], $vars['mes'], $vars['anyo']);
	foreach ($resultados as $resultado) {
		$subtotal = $resultado->bultos * $resultado->dias * $servicio->precio;
		$total = Cni::totalconIva($subtotal, $servicio->iva);
		$html .= "
			<tr class='".Cni::clase($celda++)."'>
			<td>".$resultado->dias." Días</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>Almacenaje</td>
			<td>".Cni::formateaNumero($resultado->bultos)."</td>
			<td>".Cni::formateaNumero($servicio->precio, true)." Bultos Dia</td>
			<td>".Cni::formateaNumero($subtotal, true)."</td>
			<td>".$servicio->iva."</td>
			<td>".Cni::formateaNumero($total, true)."</td>
			</tr>";
		$acumuladoSubtotal += $subtotal;
		$acumuladoTotal += $total;
		$acumuladoCantidad += $resultado->bultos;
	}
	/**
	 * Resto Servicios
	 */
	$resultados = servicioContratados(
			$vars['cliente'],
			$vars['mes'],
			$vars['anyo']
			);
	foreach ($resultados as $resultado) {
		$subtotal = $resultado->Precio * $resultado->Cantidad;
		$total = Cni::totalconIva($subtotal, $resultado->Iva);
		$clase = Cni::clase($celda++);
		$html .= "
			<tr class='".$clase."'>
			<td>".$resultado->Fecha."</td>
			<td>
				<input type='button' 
					onclick='borra(".$resultado->IdServicio.")'
					class='boton_borrar_".$clase."' />
			</td>
			<td>
				<input type='button' 
					onclick='modificar(".$resultado->IdServicio.")'
					class='boton_editar_".$clase."' />
			</td>
			<td>".$resultado->Servicio." ".$resultado->Observaciones."</td>
			<td>".Cni::formateaNumero($resultado->Cantidad)."</td>
			<td>".Cni::formateaNumero($resultado->Precio, true)."</td>
			<td>".Cni::formateaNumero($subtotal, true)."</td>
			<td>".$resultado->Iva."</td>
			<td>".Cni::formateaNumero($total, true)."</td>
			</tr>";
		$acumuladoSubtotal += $subtotal;
		$acumuladoTotal += $total;
		$acumuladoCantidad += $resultado->Cantidad;
	}
	/**
	 * Linea de Totales
	 */
	$html.= "
			<tr>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>".Cni::formateaNumero($acumuladoCantidad)."</th>
			<th>&nbsp;</th>
			<th>".Cni::formateaNumero($acumuladoSubtotal, true)."</th>
			<th>&nbsp;</th>
			<th>".Cni::formateaNumero($acumuladoTotal, true)."</th>
			</tr>";
	$html.= "</table>";
	return $html;
}
/**
 * Borra el servicio contratado
 * 
 * @param array $vars
 * @return boolean
 */
function borraServicioContratado($vars)
{
	$sql = "DELETE FROM `detalles consumo de servicios`, 
			`consumo de servicios` 
			USING `detalles consumo de servicios`
			INNER JOIN `consumo de servicios` 
			WHERE `detalles consumo de servicios`.`Id Pedido` = 
			`consumo de servicios`.`Id Pedido` 
			AND `detalles consumo de servicios`.`Id`= ?";
	if (Cni::consultaPreparada($sql, array($vars['servicio']))) {
		return true;
	} else {
		return false;
	}
}
/**
 * Formulario de modificacion de servicio
 * 
 * @param array $vars
 * @return string
 */
function frmModificacionServicio($vars)
{
	global $con;
	$sql = "Select * from `detalles consumo de servicios` 
	where `Id` like ".$vars['servicio'];
	$resultados = Cni::consultaPreparada(
			$sql,
			array($vars['servicio']),
			PDO::FETCH_CLASS
		);
	foreach ($resultados as $resultado) {
		$html = "
			<input type='button' class='boton_cerrar' 
				onclick='cierra_frm_modificacion()' value='Cerrar'/>
			<form id='modificacion' name='modificacion' method='post' 
				onSubmit='modif(".$resultado->Id."); return false'>
			<table cellpadding='1px' cellspacing='1px' width='100%'>
			<tr>
				<th align='center' colspan='6'>
					Servicio:".$resultado->Servicio." 
				</th>
			</tr>
			<tr>
				<th align='left'>Precio:</th>
				<td>
					<input type='text' id='precio' name='precio' 
						onkeyup='recalcula()' value='".
						Cni::formateaNumero($resultado->PrecioUnidadEuros).
						"' size='8'/>&euro;
				</td>
				<th align='left'>Cantidad:</th>
				<td>
					<input type='text' id='cantidad' name='cantidad'  
						onkeyup='recalcula()' value='".
						Cni::formateaNumero($resultado->Cantidad).
						"' size='3'/>
				</td>
				<th align='left'>IVA:</th>
				<td>
					<input type='text' id='iva'  name='iva' 
						onkeyup='recalcula()' value='".
						$resultado->iva.
						"' size='5'/>
				</td>
			</tr>
			<tr>
				<th align='left'>Importe:</th>
				<td>
					<span id='importe'>".
						Cni::formateaNumero($resultado->ImporteEuro).
					"</span>&euro;
				</td>
				<th align='left'>Total:</th>
				<td>
					<span id='total'>".
						Cni::formateaNumero(
							Cni::totalconIva(
								$resultado->ImporteEuro,
								$resultado->iva
								)
							)."</span>&euro;
				</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th valign='top' align='left'>Observaciones:</th>
				<td colspan='3'>
					<textarea id='observacion' name='observacion' 
						cols='40' rows='2' >".
						$resultado->observaciones.
					"</textarea>
				</td>
				<th colspan='2'>
					<input class='boton_actualizar' type='submit' 
						name='Modificar' value='Modificar' />
				</th>
			</tr>
		</table>
	</form>";
	}
	return $html;
}
/**
 * Modificamos los datos recibidos
 * 
 * @param array $vars
 */
function modificacionServicio($vars)
{
	global $con;
	$subtotal = $vars['cantidad'] * $vars['precio'];
	$subtotal = Cni::formateaNumero($subtotal);
	$sql = "Update `detalles consumo de servicios` 
		SET `Cantidad` = ?,
			`PrecioUnidadEuros` = ?,
			`ImporteEuro` = ?,
			`iva` = ?,
			`observaciones` = ? 
		WHERE `Id` like ?";
	$params = array(
			$vars['cantidad'],
			$vars['precio'],
			$subtotal,
			$vars['iva'],
			$vars['observacion'],
			$vars['servicio']
			);
	if (Cni::consultaPreparada($sql, $params)) {
		return Cni::mensajeExito("Servicio Modificado");
	} else {
		return Cni::mensajeError("Servicio No Modificado");
	}
}
/**
 * Formulario de Agregar el servicio al cliente
 * 
 * @param array $vars
 * @return string
 */
function frmAltaServicio($vars)
{
	$html ="<br/>
	<form id='frm_alta' class='formulario'  method='post' 
	onSubmit='agrega_servicio(); return false'>
	<table width='100%' class='tabla'>
	<tr>
	<th>Fecha:</th>
	<td colspan='2'>
		<input type='text' name='fecha' size='10' value='".date("d-m-Y")."' />
	</td>	
	<th>Servicio:</th>
	<td colspan='2'>".servicios()."</td>
	</tr>
	<tr>
	<th>Precio:</th>
	<td>
		<input type='text' name='precio' id='precio' size='8' 
		value='0' onkeyup='recalcula()' tabindex='2'/>
		&euro;
	</td>
	<th>Cantidad:</th>
	<td>
		<input type='text' name='cantidad' id='cantidad' size='3' value='1' 
		onkeyup='recalcula()' tabindex='3'/>
	</td>
	<th>IVA:</th>
	<td><input type='text' name='iva' id='iva' size='3' 
		value='0' onkeyup='recalcula()' tabindex='4'/>
	</td>
	</tr>
	<tr>
	<th>Importe:</th>
	<td colspan='2'>
		<span id='importe'></span>&euro;
	</td>
	<th>Total:</th>
	<td colspan='2'>
		<span id='total'></span>&euro;
	</td>
	</tr>
	<tr>
	<th colspan='2'>Observaciones:</th>
	<td colspan='2'>
		<textarea id='observacion' name='observacion' rows='1' 
			cols='50' tabindex='5'></textarea>
	</td>
	<th colspan='2'>
		<input type='submit' class='agregar' id='agregar' accesskey='a' 
			value='Agregar' tabindex='6'/>
	</th>
	</tr>
	</table>";
	return $html;
}
/**
 * Funcion que devuelve el precio y el iva del servicio seleccionado
 * 
 * @todo pasar la fecha
 * @param array $vars
 * @return string
 */
function valorDelServicio($vars)
{
	$sql = "Select PrecioEuro,iva from servicios2 
	where id like ?";
	$resultados = Cni::consultaPreparada($sql, array($vars['servicio']));
	foreach ($resultados as $resultado) {
		$iva = (IVAVIEJO == $resultado[1]) ? IVANUEVO : $resultado[1];
		$servicio = Cni::formateaNumero($resultado[0]).";".$iva;
	}
	return $servicio;
}

/**
 * Agrega el servicio
 * 
 * @todo Falla la insercion
 * @todo No se muestra el mensaje
 * @param array $vars
 */
function agregaServicio($vars)
{
	$servicio = new Servicio();
	$servicio->setServicioById($vars['servicios']);
	$sql = "INSERT INTO `consumo de servicios` 
			(`Cliente`,`Fecha`) VALUES 
			(?, STR_TO_DATE(?,'%d-%m-%Y')";
	$params = array($vars['cliente'], $vars['fecha']);
	if ( Cni::consultaPreparada($sql, $params)) {
		$sql = "INSERT INTO `detalles consumo de servicios`
			(`Id Pedido`,`Servicio`,`Cantidad`,`PrecioUnidadEuros`,
			`ImporteEuro`,`iva`,`observaciones` ) VALUES 
			(LAST_INSERT_ID(), ?, ?, ?, ?, ?, ?)";
		$params = array(
				$servicio->nombre,
				$vars['cantidad'],
				$vars['precio'],
				$vars['precio'] * $vars['cantidad'],
				$vars['iva'],
				$vars['observacion']
				);
		if ( Cni::consultaPreparada($sql, $params)) {
			return Cni::mensajeExito("Servicio Agregado");
		} else {
			return Cni::mensajeError("No se ha Agregado el servicio");
		}
	} else {
		return Cni::mensajeError("No se ha Agregado el servicio");
	}
}
/**
 * Visualizacion de la ventana de observaciones
 * 
 * @param array $vars
 * @return string $cadena
 */
function ventanaObservaciones($vars)
{
	$sql = "Select observaciones from `detalles consumo de servicios` 
	where `Id Pedido` like ?";
	$resultados = Cni::consultaPreparada(
			$sql,
			array($vars['servicio']),
			PDO::FETCH_CLASS
			);
	foreach ($resultados as $resultado) {
		$html = "<input type='button' class='boton_cerrar' 
			onclick='cierra_ventana_observaciones()' value='Cerrar' /><br/>".
			$resultado->observaciones;
	}
	return $html;
}
/**
 * Para borrar una factura seleccionada
 * 
 * @param array $vars
 * @return string $cadena
 */
function borrarFactura($vars)
{
	global $con;
	$sql = "Select codigo from regfacturas where id like ".$vars['factura'];
	$consulta = mysql_query($sql,$con);
	$resultado = mysql_fetch_array($consulta);
	$codigo = $resultado[0];
	$sql = "Delete from regfacturas where id like ".$vars['factura'];
	if( mysql_query( $sql, $con ) ) {
		$sql = "Delete from historico where factura like ".$codigo;
		$consulta = mysql_query($sql,$con);
		$cadena = "Factura Borrada<p/>";
	} else {
		$cadena = "No se ha borrado la factura<p/>";
	}
	$cadena .= listadoFacturas($vars);
	return $cadena;
}
/**
 * Gestion del listado de facturas, funcion de generacion
 * 
 * @param array $vars
 * @return string $cadena
 */
function listadoFacturas($vars)
{
	//Las vars marcaran que hay ordenado y que no
	$cadena =cabezera_pantalla(0,0,0,0,1);
	if($vars['tipo'] == 1 ) {
		$sql = "Select r.id as id,r.codigo as codigo,r.fecha as fecha,
		r.importe as importe ,r.obs_alt as obs_alt, 
		c.Nombre as nombre from regfacturas as r join clientes as c 
		on r.id_cliente like c.id order by r.fecha desc";
	} else {
		$sql = "Select r.id as id,r.codigo as codigo,r.fecha as fecha,
		r.importe as importe ,r.obs_alt as obs_alt, 
		c.Nombre as nombre from regfacturas as r join clientes as c 
		on r.id_cliente like c.id where r.id_cliente 
		like '".$vars['cliente']."' order by r.fecha desc";
	}
	$cadena .="<div id='tabla_resultados'>";
	$cadena .= dibuja_pantalla($sql,0,0,0,0);
	$cadena .= "</div>";
	return $cadena;
}
/**
 * Genera la cabezera del listado
 * Medidas Fijas cliente='285px,factura='50px,70px,70px,100px'
 * 
 * @param string $marca_cliente
 * @param string $marca_factura
 * @param string $marca_fecha
 * @param string $marca_importe
 * @param string $tipo
 * @return string $cadena
 */
function cabezera_pantalla($marca_cliente,$marca_factura,$marca_fecha,$marca_importe,$tipo)
{
	$cadena = "
	<table width='100%' class='tabla'>
		<tr>
			<th colspan='6'>Listado de Facturas</th>
		</tr>
		<tr>
			<th width='280px'>
				<input type='text' id='filtro_0' autocomplete='off' 
					onkeyup='filtro(0)' size='50'/>
			</th>
			<th width='50px'>
				<input type='text' id='filtro_1' autocomplete='off' 
					onkeyup='filtro(1)' size='6'/>
			</th>
			<th width='70px'>
				<input type='text' id='filtro_2' autocomplete='off' 
					onkeyup='filtro(2)' size='10'/>
			</th>
			<th width='70px' >
				<input type='text' id='filtro_3' autocomplete='off' 
					onkeyup='filtro(3)' size='10'/>
			</th>
			<th width='100px' >&nbsp;</th>
			<th >&nbsp;</th>
		</tr>";
	//Cabezeras con filtro
	//Valores de las marcas
	//0=asc y 1=desc
	$cadena .= "
		<tr>
			<th>
				<a href='javascript:sort(0)'>Cliente</a>
			</th>
			<th>
				<a href='javascript:sort(1)'>Factura</a>
			</th>
			<th>
				<a href='javascript:sort(2)'>Fecha</a>
			</th>
			<th>
				<a href='javascript:sort(3)'>Importe</a>
			</th>
			<th>Observacion</th>
			<th>&nbsp;</th>
		</tr>
	</table>";
	return $cadena;
}
/**
 * Muestra la seleccion x cliente o x mes y deja imprimirlas desde aqui
 * @param array $vars
 * @return string $cadena
 */
function casos($vars)
{
		//habra que filtrar los sorts aqui
		switch ( $vars['seccion'] ) {
			case 0:
				$orden = " order by c.Nombre ";
				if ( $vars['marca_cliente']==0 ) {
					$orden .= " asc ";
					$marca_cliente=1;
					$marca_factura=0;
					$marca_fecha=0;
					$marca_importe=0;
				} else {
					$orden .= " desc ";
					$marca_cliente=0;
					$marca_factura=0;
					$marca_fecha=0;
					$marca_importe=0;
				}
			break;
			case 1:
				$orden = " order by r.codigo ";
				if ($vars['marca_factura']==0 ) {
					$orden .= " asc ";
					$marca_cliente=0;
					$marca_factura=1;
					$marca_fecha=0;
					$marca_importe=0;
				} else {
					$orden .= " desc ";
					$marca_cliente=0;
					$marca_factura=0;
					$marca_fecha=0;
					$marca_importe=0;
				}
			break;
			case 2:
				$orden = " order by r.fecha ";
				if ( $vars['marca_fecha'] == 0 ) {
					$orden .= " asc ";
					$marca_cliente=0;
					$marca_factura=0;
					$marca_fecha=1;
					$marca_importe=0;
				} else {
					$orden .= " desc ";
					$marca_cliente=0;
					$marca_factura=0;
					$marca_fecha=0;
					$marca_importe=0;
				}
			break;
			case 3:
				$orden = " order by r.importe ";
				if ( $vars['marca_importe'] == 0 ) {
					$orden .= " asc ";
					$marca_cliente=0;
					$marca_factura=0;
					$marca_fecha=0;
					$marca_importe=1;
				} else {
					$orden .= " desc ";
					$marca_cliente=0;
					$marca_factura=0;
					$marca_fecha=0;
					$marca_importe=0;
				}
			break;
			default:
				$orden = " order by r.codigo";
			break;
		}
		$sql = "Select r.id as id,r.codigo as codigo,r.fecha as fecha,
			r.importe as importe ,r.obs_alt as obs_alt, 
			c.Nombre as nombre from regfacturas as r 
			join clientes as c on r.id_cliente like c.id ";
		$sql .= $orden;
		$pantalla .= dibuja_pantalla($sql,$marca_cliente,$marca_factura,$marca_fecha,$marca_importe);
	return $pantalla;
}
/**
 * Filtros de texto
 * @param array $vars
 * @return string $pantalla
 */
function filtros($vars)
{
	switch ( $vars['filtro'] ) {
		case 0:
			$cacho=" where c.Nombre like '".$vars['texto']."%'";
		break;
		case 1:
			$cacho=" where r.codigo like '".$vars['texto']."%'";
		break;
		case 2:
			$fecha = cambiab($vars['texto']);
			$cacho=" where r.fecha like '".$fecha."%'";
		break;
		case 3:
			$cacho=" where r.importe like '".$vars['texto']."%'";
		break;
	}
	$sql = "Select r.id as id,r.codigo as codigo,r.fecha as fecha,
		r.importe as importe ,r.obs_alt as obs_alt, 
		c.Nombre as nombre from regfacturas as r join clientes as c 
		on r.id_cliente like c.id";
	$sql .= $cacho;
	$pantalla .= dibuja_pantalla($sql,0,0,0,0);
	return $pantalla;
}
/**
 * Cambia el formato de fecha especial para el filtro
 * 
 * @param string $stamp
 * @return string $fecha
 */
function cambiab( $stamp )
{
	//formato en el que llega aaaa-mm-dd o al reves
	$fdia = explode("-",$stamp);
	if($fdia[2]=='') { $fdia[2]='%'; }
	if($fdia[1]=='') { $fdia[1]='%'; }
	$fecha = $fdia[2]."-".$fdia[1]."-".$fdia[0];
	return $fecha;
}
/**
 * Aqui llega la sentencia la ejecuta y la visualiza
 * 
 * @param string $sql
 * @param string $marca_cliente
 * @param string $marca_factura
 * @param string $marca_fecha
 * @param string $marca_importe
 * @return string $cadena
 */
function dibuja_pantalla($sql,$marca_cliente,$marca_factura,$marca_fecha,$marca_importe)
{
	global $con;
	//Ordenes
	$cadena ="
	<input type='hidden' id='marca_cliente' value='".$marca_cliente."' />
	<input type='hidden' id='marca_factura' value='".$marca_factura."' />
	<input type='hidden' id='marca_fecha' value='".$marca_fecha."' />
	<input type='hidden' id='marca_importe' value='".$marca_importe."' />";
	$cadena .="<table width='100%' class='tabla'>";
	$consulta = mysql_query($sql,$con);
	if ( mysql_numrows( $consulta ) !=0 ) {
		$k=0;
		while ( true == ( $resultado = mysql_fetch_array( $consulta ) ) ) {
			if($resultado['codigo'] == 0) {
				$codigo = intval($resultado['id']) + 99;
			} else {
				$codigo = $resultado['codigo'];
			}
			$cadena .="<tr>
				<td class='".clase($k)."' width='280px'>".
					$resultado['nombre']."</td>
				<td class='".clase($k)."' width='50px'>".
					$codigo."</td>
				<td class='".clase($k)."' width='70px'>".
					cambiaf($resultado['fecha'])."</td>
				<td class='".clase($k)."' width='70px'>".
					number_format($resultado['importe'],2,',','.')."</td>
				<td class='".clase($k)."' width='100px'>&nbsp;".
					$resultado['obs_alt']."</td>
				<td class='".clase($k)."'> 
				<input class='boton' type='button' 
					onclick='borrar_factura(".$resultado['id'].")' value='Borrar' />
				<input class='boton' type='button' 
					onclick='ver_factura(".$resultado['id'].")' value='Factura' /> 
				<input class='boton' type='button' 
					onclick='duplicado_factura(".$resultado['id'].")' value='Duplicado' />
				<input class='boton' type='button' 
					onclick='genera_recibo(".$resultado['id'].")' value='Recibo' />
				<input class='boton' type='button' 
					onclick='window.open(\"facturapdf.php?factura=".$codigo."\",\"_blank\")' 
					value='PDF' />
				<input class='boton' type='button' 
					onclick='window.open(\"facturapdf.php?factura=".$codigo."&dup=1\",\"_blank\")' 
					value='Duplicado PDF' />
				<input type='checkbox' name='code' id='code' value='".$codigo."' />
				<br/>
				<div id='modificaciones_".$resultado['id']."'></td></tr>";
				$k++;
		}
	} else {
		$cadena .= "<tr><th colspan='5'>No hay facturas</th></tr>";
	}
	$cadena .= "</table>
	<div class='linea_checks'>
	<span class='boton' onclick='check_all()'>Marcar Todos</span>
	<span class='boton' onclick='uncheck_all()'>Desmarcar Todos</span>
	<span class='boton' onclick='guarda_check_pdf(0)'>Guardar seleccionados como PDF</span>
	<span class='boton' onclick='envia_check_pdf(0)'>Enviar PDF's por email</span>
	<span class='boton' onclick='guarda_check_pdf(1)'>Guardar seleccionados como Duplicados PDF</span>
	<span class='boton' onclick='envia_check_pdf(1)'>Enviar Duplicados PDF's por email</span>
	</div>
	</div>
	<div id='linea_generacion'></div>";
	return $cadena;
}
