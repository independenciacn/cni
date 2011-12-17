<?php
require_once '../inc/configuracion.php';
if ( !isset( $_SESSION['usuario'] ) && !isset( $_POST ) ) {
    notFound();
} else {
    sanitize( $_POST );
    $actions = array('new','del','edit','update');
    if( in_array( $_POST['opt'],$actions ) ) {
    
    }
}

?>
<form class='inline' id='frmAlta' name='frmAlta' method='post' action='' >
	<fieldset>
	    <legend>Alta de Servicio</legend>
	   <p>
	        <label for='fecha'>Fecha:</label>
	        <input type='text' name='fecha' id='fecha' />
	    
	        <label for='servicio'>Servicio:</label>
	        ". servicios()."
	    
	        <label for='precio'>Precio:</label>
	        <input type='text' name='precio' id='precio' />
	    
	        <label for='cantidad'>Cantidad:</label>
	        <input type='text' name='cantidad' id='cantidad' size='3' />
	   
	        <label for='iva'>Iva:</label>
	        <input type='text' name='iva' id='iva' size='3' />
	    
	        <label>Importe:</label>
	        <span id='importe'></span>
	    
	        <label>Total:</label>
	        <span id='total'></span>
	    </p>
	    <p>
	        <label for='observacion'>Observacion:</label><br/>
	        <textarea id='observacion' name='observacion' rows='1' cols='50'>
	        </textarea>
	    </p>
	    <input type='submit' value='Agregar Servicio' />
	</fieldset> 
</form>