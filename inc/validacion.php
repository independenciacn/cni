<?php 
//validacion.php solo se encarga de validar el usuario y iniciar la session
//parte principal de mis funciones
require_once 'configuracion.php'; // Cabezera obligatoria
//Lanzamos la funcion se sanitize
if ( isset( $_POST['usuario'] ) ) {
	sanitize( $_POST );
	$contra = sha1( $_POST['passwd'] ); 
	$sql = "Select nick,contra from usuarios 
	where nick like '" . $_POST['usuario'] ."' 
	and contra like '" . $contra ."'";
	$resultados = consultaGenerica($sql);
	if ( count( $resultados ) != 1 ) {
		header("Location:../index.php?error=1");
		exit(0);
	} else {
		if( ( $_POST['usuario'] == $resultados[0][0] ) && ( $contra == $resultados[0][1] ) ) {
			$_SESSION['usuario'] = $_POST['usuario'];
			header("Location:../index.php");
			exit(0);
		} else {
			header("Location:../index.php?error=1");
			exit(0);
		}
	}
}
