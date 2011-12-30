<?php
/**
 * Logout File Doc Comment
 *
 * Cierra la sesion y redirige a la principal
 *
 * PHP Version 5.2.6
 *
 * @category Logout
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
session_start();
session_destroy();
header("Location:../index.php?exit=0");
exit(0);

