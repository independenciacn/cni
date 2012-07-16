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
	        $_SESSION['titulo']
	    );
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="estilo/print.css" rel="stylesheet" type="text/css"></link>
<title>Aplicacion Gestion Independencia Centro Negocios </title>
<body>
	<span class='volver' onclick='window.history.back()'>&larr; Volver</span>
	<?php echo $tabla; ?>
</body>
</html>