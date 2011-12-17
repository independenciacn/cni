<?php
require_once '../inc/configuracion.php';
if ( !isset($_SESSION['usuario']) ) {
    notFound();
}
sanitize($_GET);
$sql = "SELECT a.id, 
CONCAT('\n\r',
'Despacho:', a.despacho, '\n\r',
'Cliente:',  c.Nombre, '\n\r' 
) as title,
UNIX_TIMESTAMP(CONCAT(a.finc, ' ', a.hinc)) as start,
UNIX_TIMESTAMP(CONCAT(a.ffin, ' ', a.hfin)) as end
FROM agenda as a INNER JOIN clientes as c 
ON a.id_cliente = c.Id
where UNIX_TIMESTAMP(a.finc) >= '".$_GET['start']."'
AND UNIX_TIMESTAMP(a.ffin) <= '".$_GET['end']."'
and a.tipo_ocupacion LIKE '".$_GET['ocupacion']."'
order by a.despacho ASC, a.hinc ASC";
//echo $sql;
$resultados = consultaGenerica($sql, MYSQL_ASSOC);
echo json_encode( $resultados );

?>