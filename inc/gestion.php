<?php
require_once "configuracion.php";
require_once "funcionesGestion.php";
?>
<div id='gestion' class='span-24 last'>
    <h2>Opciones de gestión</h2>
        <div class='span-12 left'>
            <h3>Gestion de Base de Datos</h3>
            <input type='button' value='Hacer copias' onclick='doBackup()'/>
            <input type='button' value='Ver Listado Copias' onclick='listBackup()'/>
            <h3>Gestion de Usuarios</h3>
            <input type='button' value='Nuevo Usuario' />
            <input type='button' value='Ver Listado Usuarios' />
        </div>
        <div id='dialogoGestion' class='span-12 left last'></div>
</div>
<div id='listados' class='span-24 last'>
    <h2>Listados</h2>
    <h3>Datos Categorias:</h3>
    <input type='button' value='Categorias Servicios' onclick='categorias(1)'/>
    <input type='button' value='Categorias Clientes' onclick='categorias(2)'/>
    <h3>Telefonos Centro:</h3>
    <input type='button' value='Gestion telefonos Centro' onclick='formulario_telefonos()'/>
    <h3>Listado Despachos y Domiciliados:</h3>
    <input type='button' value='Ver Listado Completo' onclick='consulta_especial()'/>
    <label>Ver listado filtrado de:</label>
        <?php echo listadoCategoria(); ?>
    <div id='dialogoListados' class='span-24 last'></div>
</div>
<script type='text/javascript'>
function doBackup() {
	var respuesta = confirm("Hacer Copia de Seguridad?");
	if ( respuesta == true) {
	    url = "inc/funcionesGestion.php";
	    pars = "func=doBackup";
	    procesaAjax(url, pars, 'dialogoGestion', 'Haciendo Copia', false, false);
	    listBackup();
	} else {
	  alert("No se ha hecho copia de seguridad");   
	}
}
function listBackup() {
	url = "inc/funcionesGestion.php";
	pars = "func=listBackup";
	procesaAjax(url, pars, 'dialogoGestion', 'Generando Listado', false, false);
}
</script>