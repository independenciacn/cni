<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
	notFound();
}
function envia( $fichero, $numeroFactura, $dup ) {
	$sql = "Select date_format(r.fecha,'%d-%m-%Y') as fecha,c.Nombre,f.direccion 
	from regfacturas as r 
	inner join clientes as c on r.id_cliente = c.id 
	inner join facturacion as f
	on r.id_cliente = f.idemp where r.codigo like '".$numeroFactura."'";
//echo $sql;
	$resultado = consultaUnica($sql,MYSQL_ASSOC);

//Chequeo del correo separado por ;
//$correos = explode(";",$resultado['direccion']);
//$correos = array("ruben@ensenalia.com");
	$correo = "ruben@ensenalia.com";
	$destinatario = 'Ruben';
//$ruta = "tmp" . DIRECTORY_SEPARATOR . "factura_".$numeroFactura.".pdf";
	if ( $dup ) {
		$duplicado = "duplicado de ";
		$dupli = "Duplicado ";
	} else {
		$duplicado="";
		$dupli="";
	}
	$htmlText="<html><head><title>".$dupli."Factura Independencia Centro de Negocios</title></head>";
	$htmlText.="<body><style> 
	body{ 
		font-size:x-small; 
		font-family:Arial, Helvetica, sans-serif;
		}
	a
	{
		color:#7d0063;
	} 
	#direccion
	{
		font-family:Arial, Helvetica, sans-serif;
	}
	#indep
	{
		color:#7d0063;
		font-weight:bold;
	}
	#pie
	{
		font-size:xx-small;
		color:#7d0063;
		padding:5px;
	}
</style>";
	$htmlText.="Buenas Tardes: Adjunto enviamos ".$duplicado."la factura con fecha ".$resultado['fecha'];
	$htmlText.="<br>Para cualquier consulta o aclaraci&oacute;n no dude en ponerse
	en contacto con nosotros.<br> 
	Sin otro particular le saluda atentamente
	<br>FDO: Departamento de Administraci&oacute;n<p>";
	$htmlText.="
	<div id='indep'>INDEPENDENCIA CENTRO DE NEGOCIOS</div>
	<div id='direccion'>Paseo Independencia 8, Dpdo. 2&ordf; Planta<br>
	Tel. 976 79 43 60 - Fax 976 79 43 61<br>
	50004 ZARAGOZA<br>
	e-mail: <a href='mailto:admon@independenciacn.com'>
	admon@independenciacn.com
	</a><br>
	<a href='www.independenciacn.com' target='_blank'>
	www.independenciacn.com</a></div>
"; 
	$htmlText.= "<hr><div id='pie'>Este mensaje se dirige exclusivamente a su destinatario 
y puede contener informaci&oacute;n privilegiada o confidencial. 
Si no es Usted el destinatario indicado, habr&aacute; de saber 
que la utilizaci&oacute;n, divulgaci&oacute;n y/o copia sin 
autorizaci&oacute;n est&aacute; prohibida en virtud de la legislaci&oacute;n 
vigente. Si ha recibido este mensaje por error, le rogamos que 
nos lo comunique inmediatamente por esta misma v&iacute;a y proceda 
a su destrucci&oacute;n. 

De acuerdo con lo establecido en la Ley Org&aacute;nica 15/1999, 
de 13 de Diciembre, de Protecci&oacute;n de Datos de Car&aacute;cter 
Personal, le informamos de que sus datos pasar&aacute;n a formar 
parte de los ficheros automatizados existentes en 
Independencia Centro de Negocios, que se conservar&aacute;n en 
el mismo con car&aacute;cter confidencial, con la finalidad de 
prestar el servicio solicitado. Tambi&eacute;n nos facilitar&aacute; 
la gesti&oacute;n econ&oacute;mica de nuestra entidad y nos permitir&aacute; 
poder enviarle informaci&oacute;n actualizada sobre nuestros productos
 y servicios en un futuro. Se le informa, as&iacute; mismo, sobre la 
 posibilidad que usted tiene de ejercitar los derechos de acceso, rectificaci&oacute;n
  y cancelaci&oacute;n, en relaci&oacute;n con sus datos personales, 
  en los t&eacute;rminos establecidos legalmente, solicit&aacute;ndolo a la siguiente direcci&oacute;n:
   Independencia Centro de Negocios, P&ordm; Independencia, 8 duplicado 2&ordf; Planta 50004 Zaragoza. 
   Sus datos nos ser&aacute;n cedidos a terceros, salvo en los casos en los que la ley lo exija expresamente.
</div></body></html>";
	$mail = new Zend_Mail();
	$config = array(
		'auth' => 'login', 
		'username' => 'admon%independenciacn.com', 
		'password' => 'independencia');
	$transport = new Zend_Mail_Transport_Smtp('mail.independenciacn.com', $config);
	$mail = new Zend_Mail();
	$mail->setBodyHtml($htmlText);
	$mail->setFrom('admon@independenciacn.com', 'Independencia Centro de Negocios');
	$mail->addTo( $correo, $destinatario);
	$mail->setSubject( $dupli."Factura ".$numeroFactura." de ".$resultado['fecha']);
	$mail->setDate(date('r'));
	$mail->createAttachment($fichero,
		'application/pdf',
		Zend_Mime::DISPOSITION_ATTACHMENT,
		Zend_Mime::ENCODING_BASE64);
	if ( $mail->send() ) {
		echo "<div class='span-24 success'>Factura ".$numeroFactura." Enviada</div>";
	} else {
		echo "<div class='span-24 error'>No se ha enviado la Factura</div>";
	}
}



