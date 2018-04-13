<?php require_once '../inc/variables.php';
// FIXME: Comprobar la autentificación
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        Informes - <?php echo APLICACION ?> - <?php echo VERSION ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'>
    <link href="../entradas2/css/custom-theme/jquery-ui-1.8.8.custom.css" rel="stylesheet" type="text/css">
    <link href="estilo/servicont.less" rel="stylesheet/less" type="text/css">
    <script src="../js/less.min.js" type="text/javascript"></script>
</head>
<body>
<div class='container'>
<div id='titulo'>
    Informes y busquedas de Consumos *Datos desde el
    1 de Julio de 2007 obtenidos de la facturación
</div>
<div id='botones'>
    <input type='button' class='boton' id='menu_0' value='Por cliente' />
    <input type='button' class='boton' id='menu_1' value='Por categoria de cliente' />
    <input type='button' class='boton' id='menu_2' value='Por servicios' />
    <input type='button' class='boton' id='menu_3' value='Por cliente / servicios' />
    <input type='button' class='boton' id='menu_4' value='Por categoria de cliente / servicios' />
    <input type='button' class='boton' id='menu_5' value='Servicios por volumen de facturación' />
    <input type='button' class='boton' id='menu_6' value='Clientes por volumen de facturación' />
    <input type='button' class='boton' id='menu_7' value='Comparativas' />
    <input type='button' value='Por cliente' />
    <input type='button' value='Limpiar' onclick='window.history.go(0)' />
    <input type='button' value='[X] Cerrar' onclick='window.close(this)' />
</div>
<div id='formulario'></div>
<div id='loader'>
    <center><img src='imagenes/loading.gif' alt='cargando' /></center>
</div>
<div id='resultados'></div>
</div>
</body>
<script src='../entradas2/js/jquery-1.4.4.min.js' type="text/javascript"></script>
<script src='../entradas2/js/jquery-ui-1.8.8.custom.min.js' type="text/javascript"></script>
<script src='js/servicont.js' type="text/javascript"></script>
</html>