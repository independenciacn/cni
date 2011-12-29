<?php
/**
 * Cumples File Doc Comment
 *
 * Fichero Gestion la seccion de Cumpleaños del menu
 *
 * PHP Version 5.2.6
 *
 * @category Cumples
 * @package  cni/inc
 * @author   Ruben Lacasa Mas <ruben@ensenalia.com>
 * @license  http://creativecommons.org/licenses/by-nc-nd/3.0/
 * 			 Creative Commons Reconocimiento-NoComercial-SinObraDerivada 3.0 Unported
 * @link     https://github.com/independenciacn/cni
 * @version  2.0e Estable
 */
/**
 * Cumples Class Doc Comment
 * 
 *
 */
class Cumples
{
    protected $db;
    function __construct()
    {
        require_once 'Zend/Loader/Autoloader.php';
        Zend_Loader_Autoloader::getInstance();
        $pdoParams = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8;'
        );
        $this->db = Zend_Db::factory('Pdo_Mysql',array(
                'host'  =>  '127.0.0.1',
                'username'  =>  'cni',
                'password'  =>  'inc',
                'dbname'    =>  'CENTRO',
                'driver_options' => $pdoParams
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
$cumple = new Cumples();
//Cumpleaños hoy
   //empleados
    $hoy[] = $cumple->consulta("Select
    concat(Nombre, ' ', Apell1, ' ', Apell2) as nombre
    from empleados where date_format(FechNac,'%d %c') 
    like date_format(curdate(),'%d %c')");
   //Clientes de la empresa
    $hoy[] = $cumple->consulta("SELECT pempresa.idemp,
	concat(pempresa.nombre, ' ',
	pempresa.apellidos) as nombre,
	pempresa.cumple,
	clientes.Nombre,
	clientes.id
    FROM pempresa INNER JOIN clientes ON pempresa.idemp = clientes.Id
    WHERE date_format(cumple,'%d %c') LIKE 
    date_format(curdate(),'%d %c') and clientes.Estado_de_cliente != 0");
   //Clientes hoy de la central
    $hoy[] = $cumple->consulta("SELECT pcentral.persona_central as nombre,
	clientes.Nombre,
	clientes.id
    FROM pcentral INNER JOIN clientes ON pcentral.idemp = clientes.Id
    WHERE date_format(cumple,'%d %c') LIKE date_format(curdate(),'%d %c') 
    and clientes.Estado_de_cliente != 0");
    foreach($hoy as $linea) {
    foreach($linea as $lin)
        $cumple_dia[]= array(
            $lin['nombre']." ".$lin['apellidos'],
            $lin['Nombre']
        );
    }
//Parte del mes
    //Empleados Mes
    $mes[]= $cumple->consulta("Select
     concat(Nombre, ' ', Apell1, ' ', Apell2) as nombre,
    Fechnac as cumple from empleados where MONTH(FechNac) 
    like MONTH(curdate())  order by DAY(FechNac)");
    //Clientes Mes Empresa
    $mes[] = $cumple->consulta("SELECT pempresa.idemp,
	pempresa.nombre,
	pempresa.apellidos,
	pempresa.cumple,
	clientes.Nombre,
	clientes.id
    FROM pempresa INNER JOIN clientes ON pempresa.idemp = clientes.Id
    WHERE month(cumple) LIKE month(curdate()) and 
    clientes.Estado_de_cliente != 0 order by DAY(cumple)");
    //Clientes Mes Central
    $mes[] = $cumple->consulta("SELECT pcentral.idemp,
    pcentral.persona_central as nombre,
	 pcentral.cumple,
	 clientes.Nombre,
	clientes.id
    FROM pcentral INNER JOIN clientes ON pcentral.idemp = clientes.Id
    WHERE month(cumple) LIKE month(curdate()) 
    and clientes.Estado_de_cliente != 0 order by DAY(cumple)");
    foreach($mes as $linea)
    {
        foreach($linea as $lin)
            $cumple_mes[]= array($cumple->cambiaf($lin['cumple']),$lin['nombre']." 
            ".$lin['apellidos'],$lin['Nombre']);
    }
//en $cumple_dia tenemos el array de los cumples del dia y en $cumple_mes los del mes
    sort($cumple_mes);
//presentacion
$k=0;
$cadena .="<table class='tabla'>";
$cadena .= "<tr><td colspan='3' align='left'>
<span class='boton' onclick='cierralo()' onkeypress='cierralo()'>
[X] Cerrar</span></td></tr>";
$cadena .= "<tr><th colspan='3'>Cumplea&ntilde;er@s</th></tr>";
//HOY
$cadena .= "<tr><th colspan='3'>Cumplen A&ntilde;os HOY</th></tr>";
$cadena .= "<tr><th></th><th>Nombre</th><th>Empresa</th></tr>";
if(count($cumple_dia)!=0)
{
    foreach($cumple_dia as $dia_de_hoy)
    {
        $cadena .="<tr><td class='".$cumple->clase($k)."'></td>
        <td class='".$cumple->clase($k)."'>".$dia_de_hoy[0]."</td>
        <td class='".$cumple->clase($k)."'>".$dia_de_hoy[1]."</td></tr>";
	    $k++;
    }
}
else
    $cadena.="<tr><td colspan='3'>Nadie cumple años hoy</td></tr>";
$cadena .= "<tr><th colspan='3'>Cumplen A&ntilde;os Este Mes</th></tr>";
$cadena .= "<tr><th>Dia</th><th>Nombre</th><th>Empresa</th></tr>";
if(count($cumple_mes)!=0) {
    foreach($cumple_mes as $dia_de_hoy)
    {
        $cadena .="<tr>
        <td class='".$cumple->clase($k)."'>".$dia_de_hoy[0]."</td>
        <td class='".$cumple->clase($k)."'>".$dia_de_hoy[1]."</td>
        <td class='".$cumple->clase($k)."'>".$dia_de_hoy[2]."</td></tr>";
        $k++;
    }
}
else
    $cadena.="<tr><td colspan='3'>Nadie cumple años este Mes</td></tr>";
$cadena.="</table>";
echo $cadena;