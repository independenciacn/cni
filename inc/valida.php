<?php
require_once 'variables.php';
require_once 'Cni.php';
/**
 * valida.php File Doc Comment
 *
 * Fichero encargado de la validación de usuarios
 *
 * PHP Version 5.2.6
 *
 * @category inc
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 *           Creative Commons Reconocimiento-NoComercial-SinObraDerivada
 *           3.0 Unported
 * @link     https://github.com/independenciacn/cni
 */
Cni::chequeaSesion();
if (isset( $_POST['usuario'] ) && isset( $_POST['passwd'])) {
    $sql = "SELECT nick
    FROM usuarios
    WHERE nick like ?
    AND contra like sha1(?)";
    $resultados = Cni::consultaPreparada(
        $sql,
        array($_POST['usuario'], $_POST['passwd']),
        PDO::FETCH_CLASS
    );
    if (Cni::totalDatosConsulta() == 1) {
        foreach ($resultados as $resultado) {
            $_SESSION['usuario'] = $resultado->nick;
        }
        header("Location:../index.php");
        exit(0);
    } else {
        header("Location:../index.php?error=1");
        exit(0);
    }
} else {
    header("Location:../index.php?error=1");
    exit(0);
}
