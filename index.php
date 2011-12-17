<?php
/**
 * Index File Doc Comment
 * 
 * Pagina principal de la aplicacion
 * 
 * PHP Version 5.2.6
 * 
 * @category Index
 * @package  cni
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com> 
 * @license  http://creativecommons.org/licenses/by-nd/3.0/ 
 * 			 Creative Commons Reconocimiento-SinObraDerivada 3.0 Unported.
 * @link     https://github.com/independenciacn/cni
 */
require_once 'inc/configuracion.php';
echo $cabezeraHtml;
?>
<body>
<div id='cuerpo' class='container'>
<?php
if ( isset( $_SESSION['usuario'] ) ) {
	include_once 'inc/principal.php';
	echo "<div id='menu_general'>";
	echo menu();
	echo "</div>";
	echo "<div id='avisos' class='span-24 last'>";
	echo avisos();
	echo "</div>";
	echo "<div id='principal' class='span-24 last'></div>";
	echo "<div id='resultados'></div>";//linea de los resultados de busqueda
	echo "<div id='formulario'></div>";//linea del formulario
} else {
?>
<div id='registro' class='span-12 append-6 prepend-6 last'>
    <div class='span-12 last'>
        <img src='imagenes/logotipo2.png' width='470px' alt='The Perfect Place' />
	</div>
	<?php
	if(isset($_GET["exit"])) {
		echo "<div class='span-11  info last'>Sesion Cerrada</div>";
	}
	if(isset($_GET["error"])) {
		echo "<div class='span-11  error last'>Usuario o Contraseña Incorrecta</div>";
	}
	?>
	<form id='login_usuario' class='span-12 last' method='post' action='inc/validacion.php'>
    <fieldset>
        <legend>Acceso Usuarios:</legend>
        <p class='prepend-1'>
            <label for='usuario' class='title'>Usuario:</label><br/>
            <input class='title' type='text' id='usuario' name='usuario' 
            placeholder='Nombre Usuario' tabindex='1' />
        </p>
        <p class='prepend-1'>
            <label for='password' class='title' >Contraseña:</label><br/>
            <input type='password' class='title' id='passwd' name='passwd' 
            placeholder='Contraseña' tabindex='2' />
        </p>
        <p class='prepend-7'>
            <input type='submit' value='[->] Entrar' />
        </p>
    </fieldset>
    </form>
</div>
<?php } ?>
</div>
<?php echo $firmaAplicacion; ?>
</body>
</html>