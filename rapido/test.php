// <?
// $Texto="Hola soy un texto de prueba";
// if(require_once("Mail.php"))
// {
// $recipients = 'ruben@ensenalia.com';//cuenta de correo al la que llega el mensaje
// $headers['MiME-Version'] = '1.0';
// $headers['Content-type'] = 'multipart/mixed; charset=iso-8859-15';
// $headers['Content-Disposition'] = 'inline';
// $headers['From']    = 'Ensenalia <ruben@ensenalia.com>';//quien envia, fijo
// $headers['To']      = 'ruben@ensenalia.com';//destinatario variable
// $headers['Subject'] = "Envio de factura";//Asunto, variable
// $params['host'] = 'mail.ensenalia.com';//fijo, no se toca
// $params['port'] = '25';//fijo no se toca
// $mail_object =& Mail::factory('smtp', $params);//configuracion del envio no se toca ok
// if($mail_object->send($recipients, $headers, $Texto))
// echo "Factura Enviada";
// else
// echo "No se ha enviado la factura";
// }
// else
// echo "Error en la carga";
// ?>