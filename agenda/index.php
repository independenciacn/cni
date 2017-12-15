<?php
/*
 * FIXME: Si agregamos una repeticion a martes y miercoles, sale el domingo y tambien el sabado
 * FIXME: Segun como agregamos cosas a la hora de borrar no se borran del todo.
 */
?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Ocupación de Despachos</title>
    <link href="estilo/agenda.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src='../js/prototype.js'></script>
    <script type="text/javascript" src="../js/calendar.js"></script>
    <script type="text/javascript" src="../js/lang/calendar-es.js"></script>
    <script type="text/javascript" src="../js/calendar-setup.js"></script>
    <script type="text/javascript" src="js/agenda.js"></script>
</head>
<body>
    <div id='mensajes_estado'></div>
    <h1>Agenda Despachos</h1>
    <label for='tipo_vista'>Seleccionar Vista:</label>
    <select id='tipo_vista' onchange='cambia_vista()'>
        <option selected value=''>--Opcion--</option>
        <option value='0'>Despachos</option>
        <option value='1'>Semana</option>
        <option value='2'>Interna</option>
        <option value='3'>Tareas Pendientes</option>
        <option value='4'>Notas</option>
    </select>
    <hr/>
<p>&nbsp;</p>
<div id='vista'></div>
<div id='informacion_despacho'></div>
<div id='formulario_agenda'></div>
</body>
</html>-->
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Ruben Lacasa Mas ruben@ensenalia.com">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Ocupación de Despachos</title>
    <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Agenda Despachos</h1>
                <div class="form-group">
                    <label for="tipoVista">Seleccionar Vista</label>
                    <select id="tipoVista" name="tipoVista">
                        <option selected value='' disabled>--Opción--</option>
                        <option value='0'>Despachos</option>
                        <option value='1'>Semana</option>
                        <option value='2'>Interna</option>
                        <option value='3'>Tareas Pendientes</option>
                        <option value='4'>Notas</option>
                    </select>
                </div>
            </div>
            <div class="col-md-10" id="vista"></div>
            <div class="col-md-2" id="informacion">
            <!--<div id='mensajes_estado'></div>
            <div id='informacion_despacho'></div>
            <div id='formulario_agenda'></div>-->
        </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../bootstrap/js/jquery-1.12.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jAgenda.js"></script>
  </body>
</html>
