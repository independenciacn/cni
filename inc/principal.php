<?php
/**
 * Principal File Doc Comment
 * 
 * Pagina principal de la aplicacion
 * 
 * PHP Version 5.2.6
 * 
 * @category Principal
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/ 
 * Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
if (isset( $_SESSION['usuario'] )) {
    include_once 'menu.php';
    echo "<div id='avisos' class='span-24 last'>";
    include_once 'inc/avisos.php';
    echo "</div>";
    echo "<div id='resultados'></div>";
    echo "<div id='formulario'></div>";
} else {
    ?>
    <div id="registro" class='span-14 prepend-5 append-5'><img
    src='imagenes/logotipo2.png' width='538px' alt='The Perfect Place' />
    <form id='login_name' name='login_name' method='post'
    action='inc/validacion.php'>
    <fieldset>
    	<?php
    if (isset( $_SESSION['error'] )) {
        echo "<div class='error span-12 prepend-1'>Usuario/contraseña incorrecto</div>";
    }
    ?>
    	<legend>Acceso Usuarios</legend>
    <p><label for='usuario' class='text span-3'>Usuario:</label> <input
    type='text' class='text' id='usuario' name='usuario' /></p>
    <p><label for='password' class='text span-3'>Contraseña:</label> <input
    type='password' class='text' id='password' name='password' /></p>
    <div class='prepend-3'><input type='submit' class='text'
    value='[->] Entrar' /></div>
    </fieldset>
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
    rel="cc:attributionURL">&copy;Rubén Lacasa::<?php
    echo date( 'Y' );
    ?>
    	</a></p>
    </form>
    </div>
    <?php
}