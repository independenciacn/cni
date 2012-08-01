<?php
/**
 * envia.php File Doc Comment
 * 
 * Envia por email las facturas
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
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
require_once '../inc/Cni.php';
/**
 * Funcion encargada del envio de facturas
 * 
 * @param string $fichero
 * @param string $numeroFactura
 * @param string $dup
 * @return string
 */
function envia($fichero, $numeroFactura, $dup = false)
{
	$erroneas = '';
	$correctas = '';
	$fecha = false;
	$cliente = false;
	$direcciones = false;
	$duplicado="";
	$dupli="";
	$sql = "SELECT 
	DATE_FORMAT(r.fecha,'%d-%m-%Y') AS fecha,
	c.Nombre AS cliente,
	f.direccion AS direccion
	FROM regfacturas AS r
	INNER JOIN clientes AS c ON r.id_cliente = c.id
	INNER JOIN facturacion AS f ON r.id_cliente = f.idemp 
	WHERE r.codigo lIKE ?";
	$resultados = Cni::consultaPreparada(
		$sql,
		array($numeroFactura),
		PDO::FETCH_CLASS
		);
	foreach ($resultados as $resultado) {
		$fecha = $resultado->fecha;
		$cliente = $resultado->cliente;
		$direcciones = $resultado->direccion;
	}
	$direcciones = explode(";", $resultado['direccion']);
	foreach ($direcciones as $direccion) {
		if (filter_var($direccion, FILTER_VALIDATE_EMAIL)) {
			$correos[] = array(
				'destinatario' => $cliente,
				'email' => $direccion
			);
		} else {
			$erroneas .= "
			<div id='error'>Direccion " . $resultado['cliente']
			." Incorrecta o nula. No se enviara la factura - ".
			$direccion . "</div>";
		}
	}
	if ( $dup ) {
		$duplicado = "duplicado de";
		$dupli = "Duplicado ";
	}
	$htmlText = textoEmail($dupli, $duplicado, $fecha);
	$config = array(
		'auth' => 'login',
		'username' => 'admon%independenciacn.com',
		'password' => 'independencia');
	$transport = new Zend_Mail_Transport_Smtp(
			'mail.independenciacn.com',
			$config
			);
	foreach ($correos as $correo) {
		$mail = new Zend_Mail();
		$mail->setBodyHtml($htmlText);
		$mail->setFrom(
				'admon@independenciacn.com',
				'Independencia Centro de Negocios'
				);
		// $mail->addTo( $correo['email'], $correo['destinatario'] );
		$mail->addTo('ruben@ensenalia.com', 'Ruben Lacasa');
		$mail->setSubject( $dupli."Factura ".$numeroFactura." de ".$fecha);
		$mail->setDate(date('r'));
		$mail->createAttachment($fichero,
			'application/pdf',
			Zend_Mime::DISPOSITION_ATTACHMENT,
			Zend_Mime::ENCODING_BASE64,
		    "factura-".$numeroFactura.".pdf"
		);
		if ($mail->send($transport)) {
			 $correctas .= "<div class='span-24 success'>Factura " .
			 	$numeroFactura." Enviada</div>";
		}
	}
		echo $correctas."<br/>".$erroneas;
}
/**
 * [textoEmail description]
 * 
 * @param  [type] $dupli     [description]
 * @param  [type] $duplicado [description]
 * @param  [type] $fecha     [description]
 * 
 * @return [type]            [description]
 */
function textoEmail($dupli, $duplicado, $fecha)
{
	$htmlText = <<<EOF
	<html>
		<head>
			<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
			<title>".$dupli."Factura Independencia Centro de Negocios</title>
			<style>
				body {
					font-size:x-small;
					font-family:Arial, Helvetica, sans-serif;
				}
				a {
					color:#7d0063;
				}
				p {
					text-indent:1em;
				}
				
				.indep {
					color:#7d0063;
					font-weight:bold;
				}
				.pie {
					font-size:xx-small;
					color:#7d0063;
					padding:5px;
				}
			</style>
		</head>
	<body>
	<p>
		Buenas Tardes: Adjunto enviamos ".$duplicado." la factura con 
		fecha ".$fecha."
	</p>
	<p>
		Para cualquier consulta o aclaración no dude en ponerse en 
		contacto con nosotros.
	</p>
	<p>
		Sin otro particular le saluda atentamente:
	</p>
	<p>
		FDO: Departamento de Administración<br/>
		<span class='indep'>INDEPENDENCIA CENTRO DE NEGOCIOS</span><br/>
		<em>Paseo Independencia 8, Dpdo. 2&ordf; Planta</em><br/>
		<em>Tel. 976 79 43 60 - Fax 976 79 43 61</em><br/>
		<em>50004 ZARAGOZA</em><br/>
		<em>e-mail: 
			<a href='mailto:admon@independenciacn.com
			target='_blank'>admon@independenciacn.com</a>
		</em><br/>
		<em>
			<a href='www.independenciacn.com' 
			target='_blank'>www.independenciacn.com</a>
		</em><br/>
	</p>
	<hr>
	<div class='pie'>
	<p>
	Este mensaje se dirige exclusivamente a su destinatario y puede 
	contener información privilegiada o confidencial.
	</p>
	<p>
	Si no es Usted el destinatario indicado, habrá de saber que la 
	utilización, divulgación y/o copia sin autorización está prohibida 
	en virtud de la legislación vigente. Si ha recibido este mensaje 
	por error, le rogamos que nos lo comunique inmediatamente por esta 
	misma vía y proceda a su destrucción.
	</p>
	<p>
	De acuerdo con lo establecido en la Ley Orgánica 15/1999,
	de 13 de Diciembre, de Protección de Datos de Carácter
	Personal, le informamos de que sus datos pasarán a formar
	parte de los ficheros automatizados existentes en
	Independencia Centro de Negocios, que se conservarán en
	el mismo con carácter confidencial, con la finalidad de
	prestar el servicio solicitado. También nos facilitará;
	la gestión económica de nuestra entidad y nos permitirá
	poder enviarle información actualizada sobre nuestros productos
	y servicios en un futuro. Se le informa, así mismo, sobre la
	posibilidad que usted tiene de ejercitar los derechos de acceso, 
	rectificación y cancelación, en relación con sus datos personales,
	en los términos establecidos legalmente, solicitándolo a la 
	siguiente dirección:
	<em>Independencia Centro de Negocios, 
	P&ordm; Independencia, 8 duplicado 2&ordf; Planta 50004 Zaragoza.</em>
	Sus datos no serán cedidos a terceros, salvo en los casos en los 
	que la ley lo exija expresamente.
	</p>
	</div>
	</body>
	</html>
EOF;
	return $htmlText;
}
 
