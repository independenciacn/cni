<?php
/**
 * Index File Doc Comment
 * 
 * Fichero principal de la aplicacion
 * 
 * PHP Version 5.2.6
 * 
 * @category Index
 * @package  cni
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
session_start(); 
error_reporting( 0 );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="estilo/cni.css" rel="stylesheet" type="text/css"></link>
<link href="estilo/calendario.css" rel="stylesheet" type="text/css"></link>
<script type="text/javascript" src='js/prototype.js'></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/lang/calendar-es.js"></script>
<script type="text/javascript" src="js/calendar-setup.js"></script>
<script type="text/javascript" src='js/independencia.js'></script>
<title>Aplicación Gestión Independencia Centro Negocios 2.0d</title>
</head>
<body>
<div id='cuerpo'>
<?php
if ( isset( $_SESSION['usuario'] ) ) {
    include 'inc/validacion.php';
    echo "<div id='menu_general'>";
    echo menu();
    echo "</div>";
} else {
    ?>
    <div id='registro'>
    <center> 
        <img src='imagenes/logotipo2.png'
        width='538px' alt='The Perfect Place' /> 
    </center>
    <p />
    <center>
    <?php
    if( isset( $_GET['exit'] ) ) {
        echo "<span class='ok'>Sesion Cerrada</span>";
    }
    if( isset( $_SESSION['error'] ) ) {
        echo "<span class='ko'>Usuario o Contrase&ntilde;a Incorrecta</span>";
    }
    ?>
    <form name='login_usuario' onsubmit='validar();'
    method='post'>
    <table width='30%' class="login">
    <tr>
        <td align='right'>Usuario:</td>
        <td><input type='text' id="usuario" accesskey="u" tabindex="1" />
        </td>
    </tr>
    <tr>
        <td align='right'>Contraseña:</td>
        <td><input type='password' id="passwd" accesskey="c"
            tabindex="2" /></td>
    </tr>
    <tr>
        <td align='center' colspan="2"><input type='submit'
            class='boton' accesskey="e" tabindex="3" value='[->]Entrar' />
        </td>
    </tr>
    <tr>
        <td colspan='2'></td>
    </tr>
    </table>
    </form>
    </center>
    <p />
    <center>
    <p><span class="etiqueta">Desarrollado por:</span></p>
    <p><a href='http://www.ensenalia.com'><img src='imagenes/ensenalia.jpg'
    width='128' /></a> <a rel="license"
    href="http://creativecommons.org/licenses/by-nc-nd/3.0/"> <img
    alt="Licencia Creative Commons" style="border-width: 0"
    src="http://i.creativecommons.org/l/by-nc-nd/3.0/88x31.png" /> </a>
    <br />
    <span xmlns:dct="http://purl.org/dc/terms/"
    href="http://purl.org/dc/dcmitype/Text" property="dct:title"
    rel="dct:type"> CNI 2.0d </span> por <a
    xmlns:cc="http://creativecommons.org/ns#"
    href="http://sbarrat.wordpress.com" property="cc:attributionName"
    rel="cc:attributionURL">&copy;Rubén Lacasa::<?php echo date( 'Y' ); ?>
    </a></p>
    </center>
    </div>
    <?php } ?>
</div>
<div id='datos_interesantes'></div>
<div id='debug'></div>
<?php 
if ( isset( $_SESSION['usuario'] ) ) {
    echo "<div id='avisos'>";
    include 'inc/avisos.php';
    echo "</div>";
    echo "<div id='resultados'></div>";
    echo "<div id='formulario'></div>";
}
?>
</body>
</html>