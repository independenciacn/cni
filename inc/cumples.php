<?php
require_once 'variables.php';
$html = "";
$cumple = "";
$cumple_dia = array();
$cumple_mes = array();
/*class Cumples
{
    protected $db;
    function __construct()
    {
        require_once 'Zend/Loader/Autoloader.php';
        Zend_Loader_Autoloader::getInstance();
        $this->db = Zend_Db::factory('Pdo_Mysql',array(
                'host'  =>  '127.0.0.1',
                'username'  =>  'cni',
                'password'  =>  'inc',
                'dbname'    =>  'CENTRO'
        ));
     }
    function consulta($sql)
    {
        return $this->db->fetchAll($sql);
    }
    function cambiaf($fecha)
    {
        $date = explode("-",$fecha);
        return $date[2]."-".$date[1];
    }
    function clase($k)
    {
	if($k%2==0)
		$clase = "par";
	else
		$clase = "impar";
    return $clase;
    }
}
$cumple = new Cumples();*/
//Cumpleaños hoy
   //empleados
    $hoy[] = consultaGenerica("Select
    concat(Nombre, ' ', Apell1, ' ', Apell2) as nombre
    from empleados where date_format(FechNac,'%d %c') like date_format(curdate(),'%d %c')");
   //Clientes de la empresa
    $hoy[] = consultaGenerica("SELECT pempresa.idemp,
	concat(pempresa.nombre, ' ',
	pempresa.apellidos) as nombre,
	pempresa.cumple,
	clientes.Nombre,
	clientes.id
    FROM pempresa INNER JOIN clientes ON pempresa.idemp = clientes.Id
    WHERE date_format(cumple,'%d %c') LIKE date_format(curdate(),'%d %c') and clientes.Estado_de_cliente != 0");
   //Clientes hoy de la central
	$hoy[] = consultaGenerica("SELECT pcentral.persona_central as nombre,
	clientes.Nombre,
	clientes.id
	FROM pcentral INNER JOIN clientes ON pcentral.idemp = clientes.Id
	WHERE date_format(cumple,'%d %c') LIKE date_format(curdate(),'%d %c') and clientes.Estado_de_cliente != 0");
	foreach ( $hoy as $linea ) {
    	foreach ( $linea as $lin ) {
        $cumple_dia[]= array(
        	$lin['nombre'] . " " . $lin['apellidos'],
        	$lin['Nombre']
        	);
		}
	}
//Parte del mes
    //Empleados Mes
    $mes[]= consultaGenerica("Select
     concat(Nombre, ' ', Apell1, ' ', Apell2) as nombre,
    Fechnac as cumple from empleados where MONTH(FechNac) like MONTH(curdate())  order by DAY(FechNac)");
    //Clientes Mes Empresa
    $mes[] = consultaGenerica("SELECT pempresa.idemp,
	pempresa.nombre,
	pempresa.apellidos,
	pempresa.cumple,
	clientes.Nombre,
	clientes.id
    FROM pempresa INNER JOIN clientes ON pempresa.idemp = clientes.Id
    WHERE month(cumple) LIKE month(curdate()) and clientes.Estado_de_cliente != 0 order by DAY(cumple)");
    //Clientes Mes Central
    $mes[] = consultaGenerica("SELECT pcentral.idemp,
	pcentral.persona_central as nombre,
	pcentral.cumple,
	clientes.Nombre,
	clientes.id
	FROM pcentral INNER JOIN clientes ON pcentral.idemp = clientes.Id
	WHERE month(cumple) LIKE month(curdate()) and clientes.Estado_de_cliente != 0 order by DAY(cumple)");

foreach($mes as $linea)
{
    foreach($linea as $lin)
        $cumple_mes[]= array( 
        	cambiaf($lin['cumple']), 
        	$lin['nombre'] ." ". $lin['apellidos'],
        	$lin['Nombre']
        	);
}
//en $cumple_dia tenemos el array de los cumples del dia y en $cumple_mes los del mes
sort($cumple_mes);


//presentacion
$k=0;
$html .="<table class='tabla'>";
$html .= "<tr><td colspan='3' align='left'>
<span class='boton' onclick='cierralo()' onkeypress='cierralo()'>[X] Cerrar</span></td></tr>";
$html .= "<tr><th colspan='3'>Cumplea&ntilde;er@s</th></tr>";
//HOY
$html .= "<tr><th colspan='3'>Cumplen A&ntilde;os HOY</th></tr>";
$html .= "<tr><th></th><th>Nombre</th><th>Empresa</th></tr>";

if ( count( $cumple_dia ) !=0 ) {
    foreach ( $cumple_dia as $dia_de_hoy ) {
    	$html .="<tr><td class='" . clase( $k ) . "'></td>
    	<td class='" . clase( $k ) . "'>".$dia_de_hoy[0]."</td>
    	<td class='" . clase( $k ) . "'>".$dia_de_hoy[1]."</td></tr>";
		$k++;
    }
} else {
    $html.="<tr><td colspan='3'>Nadie cumple años hoy</td></tr>";
}
$html .= "<tr><th colspan='3'>Cumplen Años Este Mes</th></tr>";
$html .= "<tr><th>Dia</th><th>Nombre</th><th>Empresa</th></tr>";
if ( count( $cumple_mes ) !=0 ) {
    foreach ( $cumple_mes as $dia_de_hoy ) {
        $html .="<tr>
        	<td class='" . clase( $k ) . "'>".$dia_de_hoy[0]."</td>
        	<td class='" . clase( $k ) . "'>".$dia_de_hoy[1]."</td>
        	<td class='" . clase( $k ) . "'>".$dia_de_hoy[2]."</td>
        </tr>";
        $k++;
    }
} else {
    $html.="<tr><td colspan='3'>Nadie cumple años este Mes</td></tr>";
}
$html.="</table>";
echo $html;