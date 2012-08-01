<?
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Prueba</title>
    <script src='../js/jquery-1.7.2.min.js'></script>
</head>
<body>
<?php
    function print_line($fd, $events, $arg)
{
    static $max_requests = 0;

    $max_requests++;

    if ($max_requests == 10) {
        // Salir del bucle después que escriba 10 veces
        event_base_loopexit($arg[1]);
    }

    // Imprimir o mostrar la línea actual
    echo  fgets($fd);
}

// crear la base y el evento
    $base = event_base_new();
    $event = event_new();

    $fd = STDIN;

// Colocar las banderas del evento
    event_set($event, $fd, EV_READ | EV_PERSIST, "print_line", array($event, $base));
// Colocar la base del evento
    event_base_set($event, $base);

// Habilitar el evento
    event_add($event);
// Iniciar el bucle del evento
    event_base_loop($base);
?>
</body>
</html>