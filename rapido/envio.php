// <?php
// /**
//  * Fichero antiguo de envio
//  * 
//  * @deprecated
//  */
// /*
//  * Funcion de cambio de fecha
//  */
// function cambiafecha($stamp) 
// {
// 	//formato en el que llega aaaa-mm-dd o al reves
// 	$fdia = explode("-",$stamp);
// 	$fecha = $fdia[2]." de ".dameElMes($fdia[1])." de ".$fdia[0];
// 	return $fecha;
// }
// /*
//  * Funcion general que controla el envio
//  */
// include("../inc/variables.php");
// $sql = "Select r.fecha,c.Nombre,f.direccion from regfacturas as r 
// inner join clientes as c on r.id_cliente = c.id 
// inner join facturacion as f
// on r.id_cliente = f.idemp where r.codigo like '$factura'";
// //echo $sql;
// $consulta = @mysql_db_query($dbname,$sql,$con);
// $resultado = @mysql_fetch_array($consulta);

// //Chequeo del correo separado por ;
// $correos = explode(";",$resultado[2]);
// //$correos = array("admon@independenciacn.com");
// $nombre_factura = "factura_".$factura.".pdf";
// $ruta_osx = "/Users/ruben/Desktop/facturas/";
// $ruta_wxp = "tmp/";
// $ruta = $ruta_wxp.$nombre_factura;
// if(isset($_POST['dup']))
// {
// 	$duplicado = "duplicado de ";
// 	$dupli = "Duplicado ";
// }
// else
// {
// 	$duplicado="";
// 	$dupli="";
// }
// $pie="<html><head><title>".$dupli."Factura Independencia Centro de Negocios</title></head>";
// $pie.="<body><style> 
// 	body{ 
// 		font-size:x-small; 
// 		font-family:Arial, Helvetica, sans-serif;
// 		}
// 	a
// 	{
// 		color:#7d0063;
// 	} 
// 	#direccion
// 	{
// 		font-family:Arial, Helvetica, sans-serif;
// 	}
// 	#indep
// 	{
// 		color:#7d0063;
// 		font-weight:bold;
// 	}
// 	#pie
// 	{
// 		font-size:xx-small;
// 		color:#7d0063;
// 		padding:5px;
// 	}
// </style>";
// $pie.="Buenas Tardes: Adjunto enviamos ".$duplicado."la factura con fecha ".cambiafecha($resultado[0]);
// $pie.="<br>Para cualquier consulta o aclaraci&oacute;n no dude en ponerse
// en contacto con nosotros.<br> 
// Sin otro particular le saluda atentamente
// <br>FDO: Departamento de Administraci&oacute;n<p>";
// $pie.="
// <div id='indep'>INDEPENDENCIA CENTRO DE NEGOCIOS</div>
// <div id='direccion'>Paseo Independencia 8, Dpdo. 2&ordf; Planta<br>
// Tel. 976 79 43 60 - Fax 976 79 43 61<br>
// 50004 ZARAGOZA<br>
// e-mail: <a href='mailto:admon@independenciacn.com'>
// admon@independenciacn.com
// </a><br>
// <a href='www.independenciacn.com' target='_blank'>
// www.independenciacn.com</a></div>

// "; 
// $pie.= "<hr><div id='pie'>Este mensaje se dirige exclusivamente a su destinatario 
// y puede contener informaci&oacute;n privilegiada o confidencial. 
// Si no es Usted el destinatario indicado, habr&aacute; de saber 
// que la utilizaci&oacute;n, divulgaci&oacute;n y/o copia sin 
// autorizaci&oacute;n est&aacute; prohibida en virtud de la legislaci&oacute;n 
// vigente. Si ha recibido este mensaje por error, le rogamos que 
// nos lo comunique inmediatamente por esta misma v&iacute;a y proceda 
// a su destrucci&oacute;n. 

// De acuerdo con lo establecido en la Ley Org&aacute;nica 15/1999, 
// de 13 de Diciembre, de Protecci&oacute;n de Datos de Car&aacute;cter 
// Personal, le informamos de que sus datos pasar&aacute;n a formar 
// parte de los ficheros automatizados existentes en 
// Independencia Centro de Negocios, que se conservar&aacute;n en 
// el mismo con car&aacute;cter confidencial, con la finalidad de 
// prestar el servicio solicitado. Tambi&eacute;n nos facilitar&aacute; 
// la gesti&oacute;n econ&oacute;mica de nuestra entidad y nos permitir&aacute; 
// poder enviarle informaci&oacute;n actualizada sobre nuestros productos
//  y servicios en un futuro. Se le informa, as&iacute; mismo, sobre la 
//  posibilidad que usted tiene de ejercitar los derechos de acceso, rectificaci&oacute;n
//   y cancelaci&oacute;n, en relaci&oacute;n con sus datos personales, 
//   en los t&eacute;rminos establecidos legalmente, solicit&aacute;ndolo a la siguiente direcci&oacute;n:
//    Independencia Centro de Negocios, P&ordm; Independencia, 8 duplicado 2&ordf; Planta 50004 Zaragoza. 
//    Sus datos nos ser&aacute;n cedidos a terceros, salvo en los casos en los que la ley lo exija expresamente.
// </div></body></html>";

// // Require Pear Mail Packages
// require_once ("Mail.php");
// require_once ("Mail/mime.php");
// foreach($correos as $correo)
// {
// 	$recipients  = $correo;//Destino
// // Additional headers
// 	$headers["From"] = 'Independencia Centro de Negocios <admon@independenciacn.com>';
// 	$headers["To"]=$correo; //Destino
// 	$headers["Subject"] = $dupli."Factura ".$factura." de ".cambiafecha($resultado[0]);
// 	$headers['Date'] = date("r");

// 	$crlf = "\n";
// 	$mime = new Mail_mime($crlf);
// 	$mime->addAttachment($ruta, 'application/pdf');
// 	$mime->setHTMLBody($pie);
// 	$message = $mime->get();
// 	$headers = $mime->headers($headers);
	
// 	$params['host'] = 'mail.independenciacn.com';//fijo, no se toca
// 	$params['port'] = '25';//fijo no se toca
// 	$params['auth'] = TRUE;
// 	$params['username'] = 'admon%independenciacn.com';
// 	$params['password'] = 'independencia';
// 	$params["debug"]    = "True"; 
// // create the mail object using the Mail::factory method
// 	$mail_message =& Mail::factory('smtp', $params);
// // create the mail object using the Mail::factory method
// //$mail_message =& Mail::factory('smtp', $params);
// 	if($mail_message->send ($recipients, $headers, $message))
// 	echo "Factura Enviada";
// 	else
// 	echo "No se ha enviado la factura";
// }
// unlink($ruta);
// ?>

