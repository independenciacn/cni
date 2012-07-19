<?php
/**
 * print.php File Doc Comment
 *
 * Genera la tabla con los resultados recibidos
 *
 * PHP Version 5.2.6
 *
 * @category servicont
 * @package  cni/servicont
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
require_once '../inc/variables.php';
require_once '../inc/Cni.php';
Cni::chequeaSesion();
$tabla = "";
if ( isset($_SESSION['titulo']) ) {
	$tabla = Cni::generaTablaDatos(
	        $_SESSION['sqlQuery'],
			$_SESSION['vars'],
	        $_SESSION['titulo']
	    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<link href='../bootstrap/css/bootstrap.min.css' rel="stylesheet" />
	<script src='../bootstrap/js/bootstrap.min.js'></script>
	<title>Aplicacion Gestion Independencia Centro Negocios </title>
	<body>
		<span class='volver' onclick='window.history.back()'>
			&larr; Volver
		</span>
	<?php echo $tabla; ?>
</body>
</html>
<?php
 