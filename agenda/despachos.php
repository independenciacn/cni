<?php
require_once '../inc/classes/Connection.php';
$conexion = new Connection();
$params = array('%despacho%', 'Codigo Negocio');
$sql = "SELECT z.valor, c.Nombre,c.id ,c.Categoria FROM clientes as c  join z_sercont as z on c.id like z.idemp 
    WHERE  Estado_de_cliente != 0 and 
        c.Categoria like ? and 
    z.servicio like ? order by z.valor asc";
$resultados = $conexion->consulta($sql, $params, PDO::FETCH_CLASS);
$despacho = 0;
$despachos = array();
$clase = array();
$cliente = array();
foreach ($resultados as $resultado) {
    var_dump($resultado);
    $despachos[intval($resultado->valor)]=$resultado->Nombre;
    $clase[intval($resultado->valor)]="despacho_ocupado";
    $cliente[intval($resultado->valor)]=$resultado->id;
}
?>
<table id='agenda' class='table table-bordered'>
<?php
for ($i=0; $i<=5; $i++) :
    ?>
    <tr>
    <?php
    for ($j=0; $j<=5; $j++) :
        $despacho++;
        $nombreDespacho = ($despacho == 23) ? "Sala de Juntas" : "Despacho ".$despacho;
        $boton = "<input type='hidden' id='cliente_despacho_".$despacho."' value='' /><div class='despacho_parcial'>&nbsp;</div>";
        if ($cliente[$despacho]!='') {
            $boton = "<div class='".$clase[$despacho]."'>".$despachos[$despacho]."</div>";
            $boton.="<p/><span class='mini_boton' onclick='informacion_cliente(".$cliente[$despacho].
            ")'>&nbsp;+Info&nbsp;</span>
            <input type='hidden' id='cliente_despacho_".$despacho."' value='".$cliente[$despacho]."' />";
        }
        ?>
        <td class='col-md-2' id='despacho_$despacho' valign='top'>
            <div class='cabezera_despacho'><?php echo $nombreDespacho ?></div>
            <?php echo $boton ?>
        </td>
    <?php
    endfor;
    ?>
    </tr>
    <?php
endfor;
?>
</table>