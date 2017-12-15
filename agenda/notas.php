<?php
    require_once '../inc/variables.php';
    require_once '../inc/classes/Connection.php';
    require_once 'funciones.php';
    
    $hoy=date('d-m-Y');
    $nota ="";
    $boton="Agregar Nota";
    $accion = "agrega_nota()";

    if (isset($_POST[nota])) {
        $sql ="Select * from notas where id like ".$_POST[nota]."";
        $consulta = @mysql_query($sql, $con);
        $resultado = @mysql_fetch_array($consulta);
        $hoy = cambiaf($resultado[fecha]);
        $nota = $resultado[nota];
        $boton = "Actualizar nota";
        $accion = "actualiza_nota(".$_POST[nota].")";
    }
    ?>
    <form id='notas' method='post' onsubmit='".$accion.";return false'>
    Fecha:<input type='text' name='vencimiento' id='semana' size='10' value='<?php echo $hoy ?>' />
        <button type='button' class='boton' id='f_trigger_semana' >...</button>
    <br>Nota:<br>
    <textarea name='nota' id='nota' cols='100' rows='10'><?php echo $nota ?></textarea>
    <div id='estado_nota'></div>
    <br><input type='submit' value='".$boton."' /></form>
    <br>Listado de Notas<br>
    <?php
    /*listado de las notas*/
    $sql="Select * from notas order by fecha desc";
    $consulta = @mysql_query($sql, $con);
    if(@mysql_numrows($consulta)==0)
        $cadena.="No hay notas";
    else
    {
        while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
        {
            
            $cadena.="&nbsp;&nbsp;<span class='fecha_nota'>".cambiaf($resultado[fecha])."&nbsp;&nbsp;&nbsp;&nbsp;<img src='imagenes/editar.png' alt='Editar Nota' onclick='editar_nota(".$resultado[0].")' /> &nbsp;|&nbsp;<img src='imagenes/borrar.png' alt='Borrar Nota' onclick='borra_nota(".$resultado[0].")' /></span>
            &nbsp;&nbsp;<div class='texto_nota'>".$resultado[nota]."</div><br>";
        }
    }
    
echo $cadena;
