<?php
require_once 'inc/variables.php';
require_once 'inc/Cni.php';
/**
 * index.php File Doc Comment
 *
 * Pagina principal de la aplicación
 *
 * PHP Version 5.2.6
 *
 * @category main
 * @package  cni
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
Cni::chequeaSesion();
$tituloGeneral = APLICACION. " - ". VERSION;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link href="estilo/cni.css" rel="stylesheet" />
    <link href="estilo/calendario.css" rel="stylesheet" />
    <script src='js/prototype.js'></script>
    <script src="js/calendar.js"></script>
    <script src="js/lang/calendar-es.js"></script>
    <script src="js/calendar-setup.js"></script>
    <script src='js/independencia.js'></script>
    <title>Principal - <?= $tituloGeneral; ?></title>
</head>
<body>
<div id='cuerpo'>
<?php
/*
 * TODO: Que se pueda modificar la contraseña de acceso
 * TODO: Agregar un nuevo campo a la factura: Nº Pedido
 */
if (!isset($_SESSION['usuario'])) {
    $mensaje = "";
    if (isset($_GET['exit'])) {
        $mensaje = Cni::mensajeExito('Sesion Cerrada');
    }
    if (isset($_GET['error'])) {
        $mensaje = Cni::mensajeError('Usuario/Contraseña Incorrecto');
    }
    ?>
    <div id='registro'>
        <img src='imagenes/logotipo2.png' alt='The Perfect Place' />
        <div id='mensajesEstado'>
            <?= $mensaje; ?>
        </div>
        <form id='login_usuario' method='post' action='inc/valida.php'>
            <label for='usuario'>Usuario:</label>
            <input type='text' id='usuario' name='usuario' tabindex="1" />
            <label for='passwd'>Contraseña:</label>
            <input type='password' id='passwd' name='passwd' tabindex="2" />
            <button type='submit' class='boton' tabindex="3">
                [&raquo;]Entrar
            </button>
        </form>
        <div class="etiqueta">
            Desarrollado por:
            <a href='http://www.ensenalia.com'>
                <img src='imagenes/ensenalia.jpg'/>
            </a>
        </div>
    </div>
    <?php
} else {
    require_once 'inc/menu.php';
    require_once 'inc/avisos.php';
    ?>
    <div id='menu_general'>
        <?= menu(); ?>
    </div>
    <div id='avisos'>
        <?= avisos(); ?>
    </div>
    <div id='resultados'></div>
    <div id='formulario'></div>
    <div id='datos_interesantes'></div>
    <div id='debug'></div>
    <?php
}
?>
</div>

</body>
</html>