<?php
require_once '../inc/configuracion.php';
if ( isset( $_SESSION['usuario'] ) && isset( $_REQUEST ) ) {
    sanitize( $_REQUEST );
} else {
    notFound();
}
$prioridades = array('Normal','Media','Alta','Urgente');
$page = $_REQUEST['page']; // get the requested page
$limit = $_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = $_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = $_REQUEST['sord']; // get the direction
if(!$sidx) $sidx =1;
$sql = "Select concat(e.Nombre, ' ', e.Apell1, ' ', e.Apell2) as empleada,
t.id as id, t.nombre as nombre,
date_format(t.vencimiento, '%d-%m-%Y') as vencimiento,
t.prioridad as prioridad, t.realizada as realizada
FROM tareas_pendientes as t
INNER JOIN empleados as e ON t.asignada = e.id";
$count = totalCeldas( $sql );
if( $count >0 ) {
    $total_pages = ceil($count/$limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages) { 
    $page = $total_pages; 
}
$start = $limit * $page - $limit; // do not put $limit*($page - 1)
/**
 * Seccion de parametros de busqueda
 * @var unknown_type
 */
//sopt: null // ['bw','eq','ne','lt','le','gt','ge','ew','cn'] 
//['equal','not equal', 'less', 'less or equal','greater','greater or equal', 
//'begins with','does not begin with','is in','is not in','ends with','does not end with',
//'contains','does not contain'] 
/*
bw - begins with ( LIKE val% )
eq - equal ( = )
ne - not equal ( <> )
lt - little ( < )
le - little or equal ( <= )
gt - greater ( > )
ge - greater or equal ( >= )
ew - ends with (LIKE %val )
cn - contain (LIKE %val% ) */
//prioridad, vencimiento, realizada, empleada => asignada, nombre
$campos = array('prioridad'=>'t.prioridad',
        'vencimiento'=>'t.vencimiento',
        'realizada' => 't.realizada', 
        'empleada' => 't.asignada',
        'nombre' => 't.nombre' );
if ( $_REQUEST['_search'] == 'true' ) {
    if (isset( $_REQUEST['vencimiento'] ) ) {
        $_REQUEST['vencimiento'] = cambiaf( $_REQUEST['vencimiento'] );
    }
    if (isset( $_REQUEST['nombre'] ) ) {
        $_REQUEST['nombre'] = "%".$_REQUEST['nombre']."%";
    }
    $sql .= " WHERE ";
    foreach( $campos as $key => $campo ) {
        if (array_key_exists($key, $_REQUEST ) ) {
            $sql .= " ".$campo." LIKE '".$_REQUEST[$key]."' AND ";
        }
    }
    $sql = substr($sql, 0, strlen($sql) - 4 );
}
/*if ( isset( $_REQUEST['searchField'] ) && isset( $_REQUEST['searchString'] ) ) {
    if ( $_REQUEST['searchField'] == 'vencimiento' ) {
        $_REQUEST['searchString'] = cambiaf($_REQUEST['searchString'] );
    }
    $sopt = array( 
            'bw' =>"LIKE '".$_REQUEST['searchString']."%'",
            'eq' =>" = '".$_REQUEST['searchString']."'",
            'ne' =>" <> '".$_REQUEST['searchString']."'",
            'lt' =>" < '".$_REQUEST['searchString']."'",
            'le' =>" <= '".$_REQUEST['searchString']."'",
            'gt' =>" > '".$_REQUEST['searchString']."'",
            'ge' =>" >= '".$_REQUEST['searchString']."'",
            'ew' =>" = '%".$_REQUEST['searchString']."'",
            'cn' =>" = '%".$_REQUEST['searchString']."%'"
            );
    if ( $_REQUEST['searchOper']!='' ){
        $sql .= " WHERE ".$_REQUEST['searchField']." 
        " . $sopt[$_REQUEST['searchOper']] . " ";
    }
}*/
$sql .= " ORDER BY $sidx $sord LIMIT $start , $limit";
$resultados = consultaGenerica( $sql, MYSQL_ASSOC );
$responce = new stdClass();
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i = 0;
foreach ( $resultados as $resultado ) {
    $responce->rows[$i]['id'] = $resultado['id'];
    $responce->rows[$i]['cell'] = array(
                    $resultado['id'],
                    $prioridades[$resultado['prioridad']],
                    $resultado['vencimiento'],
                    $resultado['realizada'],
                    ucwords( strtolower( $resultado['empleada'] )),
                    $resultado['nombre']
                    );
    $i++;
}
echo json_encode( $responce );
/*
// connect to the database
$db = mysql_connect($dbhost, $dbuser, $dbpassword)
or die("Connection Error: " . mysql_error());

mysql_select_db($database) or die("Error conecting to db.");
$result = mysql_query("SELECT COUNT(*) AS count FROM invheader a, clients b WHERE a.client_id=b.client_id");
$row = mysql_fetch_array($result,MYSQL_ASSOC);
$count = $row['count'];

if( $count >0 ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)
$SQL = "SELECT a.id, a.invdate, b.name, a.amount,a.tax,a.total,a.note FROM invheader a, clients b WHERE a.client_id=b.client_id ORDER BY $sidx $sord LIMIT $start , $limit";
$result = mysql_query( $SQL ) or die("Couldn t execute query.".mysql_error());

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    $responce->rows[$i]['id']=$row[id];
    $responce->rows[$i]['cell']=array($row[id],$row[invdate],$row[name],$row[amount],$row[tax],$row[total],$row[note]);
    $i++;
}        
echo json_encode($responce);
 */
/*$sql = "Select concat(e.Nombre, ' ', e.Apell1, ' ', e.Apell2) as Empleada,
t.id as id, t.nombre as nombre, 
date_format(t.vencimiento, '%d-%m-%Y') as vencimiento,
t.prioridad as prioridad, t.realizada as realizada
FROM tareas_pendientes as t
INNER JOIN empleados as e ON t.asignada = e.id  
order by t.prioridad desc ,t.vencimiento asc";
//var_dump( $sql );
$resultados = consultaGenerica( $sql, MYSQL_ASSOC );

echo json_encode($resultados);*/
/*
 * <caption>Listado de tareas <?php echo $tipo; ?></caption>
<colgroup>
<thead>
	<tr><th>Prioridad</th>
		<th>Fecha</th>
		<th>Realizada</th>
		<th>Asignada</th>
		<th>Tarea</th>
	</tr>
</thead>
<tbody>
	<?php 
		foreach( $resultados as $resultado ) {
			capitalize( $resultado['Empleada'] );
			echo "<tr>";
			echo "<td>" . $prioridades[$resultado['prioridad']] . "</td>";
			echo "<td>" . $resultado['vencimiento'] . "</td>";
			echo "<td><input type='checkbox' ";
			echo ( $resultado['realizada']=='Si' ) ? "checked" : "";
			echo "/></td>";
			echo "<td><img src='imagenes/borrar.png' alt='Borrar Tarea' /></td>";
			echo "<td>" . $resultado['Empleada']  . "</td>";
			echo "<td>" . $resultado['nombre'] . "</td></tr>";
		}
	?>
</tbody>
 */