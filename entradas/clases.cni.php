// <?php
// /* 
//  * To change this template, choose Tools | Templates
//  * and open the template in the editor.
//  */

// /**
//  * Description of clasescni
//  *
//  * @author Ruben Lacasa Mas - ruben@ensenalia.com
//  */

// class Entradas {
    
//     var $sql;
//     var $consulta;
//     var $tot_camp;
//     var $anterior;
//     var $j;
//     var $titulo;
//     var $inicial;
//     var $categorias;
//     var $dbname;
//     var $con;
    
//     function Entradas()
//     {
//         //En el constructor defino las categorias de clientes
//         include("../inc/variables.php");
//         $this->dbname = $dbname;
//         $this->con = $con;
//         $tabla = utf8_decode("categorías clientes");
//         $sql = "SELECT Nombre FROM `$tabla`";
//         $consulta = @mysql_db_query($this->dbname,$sql,$this->con);
//         while($resultado = @mysql_fetch_array($consulta))
//         {
//             $this->categorias[]=$resultado[0];
//         }

//     }
    
// /*
//  * Funcion ver_resultados: Muestra por pantalla los resultados ¿en uso?
//  */
//     function ver_resultados()
//     {
//         $this->consulta = @mysql_db_query($this->dbname,$this->sql,$this->con);
//         $this->tot_camp = @mysql_num_fields($this->consulta);
//         $cadena.="<div class='tabla_mes'><h3>".$this->titulo."</h3>";
//         echo $cadena;
//         if($this->j==1)
//         $this->inicial = $this->total();
//         echo "<h2>Entradas</h2><button id='boton_e_".$this->j."' onclick='ver_entradas(".$this->j.")'>Ver Detalles</button>".$this->total_entradas();
//         echo "<h2>Salidas</h2><button id='boton_s_".$this->j."' onclick='ver_salidas(".$this->j.")'>Ver Detalles</button>".$this->total_salidas();
//         echo "</div>";
//     }

// /*
//  * Funcion total: Muestra el total de resultados ¿en uso?
//  */
//     function total()
//     {
//          return @mysql_num_rows($this->consulta);

//     }
// /*
//  * Funcion total_entradas: Muestra el total de empresas que han entrado este mes ¿en uso?
//  */
//     function total_entradas() //Empresas que han entrado ese mes
//     {
//         $acu =O;
//         $can = substr($this->sql,-30,7);
//         //Vueltas por categorias
//         $cadenica.="<div id='entradas_".$this->j."' style='visibility:hidden;display:none;'>
// <button onclick='ocultar_entradas($this->j)'>Ocultar Detalles</button>";
//         foreach($this->categorias as $cat)
//         {
//         $k++;
//                 if($k%2==0)
//                     $tipo = "par";
//                 else
//                     $tipo = "impar";
//         $sql = "Select e.* from entradas_salidas as e inner join
//         clientes as c on e.idemp = c.Id
// where (month(e.entrada) like month('$can-00') and year(e.entrada) like year('$can-00')
// and (e.salida > '2007-12-31' or e.salida like '0000-00-00') and e.categoria like '$cat')";
//         $consulta = @mysql_db_query($this->dbname,$sql,$this->con);
      
//         while($resultado = @mysql_fetch_array($consulta))
//         {
//             $l++;
//             if($l%2==0)
//                 $ase = "par";
//             else
//                 $ase = "impar";
//             $cadenica.="<div class='linea_".$ase."'>".$this->nombre_empresa($resultado[1])."<br />".$this->fecha($resultado[2])." / ".$this->fecha($resultado[3])."</div>";
//         }
//         $resul.="<div class='linea_res_".$tipo."'>".$cat.":".mysql_numrows($consulta)."</div>";
//         $acu = $acu + mysql_numrows($consulta);

//         }
//         return $cadenica."</div>".$resul."<div class='total'>Total:".$acu."</div>";
       
//     }
// /*
//  * Funcion total_salidas: Muestras las empresas que se han dado de baja entre
//  * el dia 15 del mes anterior y el 15 del mes presente. ¿en uso?
//  */
//     function total_salidas()
//     {
        
//         $acu = 0;
//         $can = substr($this->sql,-30,7);
//         $cadenica.="<div id='salidas_".$this->j."' style='visibility:hidden;display:none;'>
// <button onclick='ocultar_salidas($this->j)'>Ocultar Detalles</button>";
       
//         foreach($this->categorias as $cat)
//         {
//             $k++;
//                 if($k%2==0)
//                     $tipo = "par";
//                 else
//                     $tipo = "impar";
//         $sql = "Select e.* from entradas_salidas as e inner join
//         clientes as c on e.idemp = c.Id
// where (e.salida <= '$can-15' and e.salida > DATE_SUB('$can-15',INTERVAL 1  month)
// and e.salida > '2007-12-31' and e.categoria like '$cat')";
//         $consulta = @mysql_db_query($this->dbname,$sql,$this->con);
//         while($resultado = @mysql_fetch_array($consulta))
//         {
//             $l++;
//             if($l%2==0)
//                 $ase = "par";
//             else
//                 $ase = "impar";
//             $cadenica.="<div class='linea_".$ase."'>".$this->nombre_empresa($resultado[1])."<br />".$this->fecha($resultado[2])." / ".$this->fecha($resultado[3])."</div>";
//         }
//         $resul.="<div class='linea_res_".$tipo."'>".$cat.":".mysql_numrows($consulta)."</div>";
//         $acu=$acu + mysql_numrows($consulta);

//         }
//         return $cadenica."</div>".$resul."<div class='total'>Total:".$acu."</div>";
//     }

// /*
//  * Funcion nombre_empresa($empresa): Devuelve el nombre de la empresa
//  * pasando como parametro el id de cliente sacado en la consulta
//  */
//     function nombre_empresa($empresa)
//     {
        
//         $sql = "Select Nombre,Categoria from clientes where id like $empresa";
//         $consulta = @mysql_db_query($this->dbname,$sql,$this->con);
//         $resultado = @mysql_fetch_array($consulta);
//         return $resultado[0]." ".$resultado[1];
//     }

// /*
//  * Funcion fecha($fecha): Cambia el formato de la fecha dependiendo de que tipo viene.
//  */
//     function fecha($fecha)
//     {
//         $nfecha = explode("-",$fecha);
//         $stamp = $nfecha[2]."-".$nfecha[1]."-".$nfecha[0];
//         return $stamp;
//     }
// /*
//  * Funcion anyo_cero_entradas($tipo):Devuelve las entradas a anyo 0 pasando como
//  * parametro la categoria
//  */
//     //Calculos parciales de entradas
//     function anyo_parcial_entradas($tipo,$anyo)
//     {
//             $ini = $anyo -1;
//             $res = 0;
//             for($i=$ini;$i>=2008;$i--)
//             {
//             $sql = "SELECT * FROM entradas_salidas WHERE categoria LIKE '%$tipo'
//             AND ( '$ini' LIKE year( entrada ) )";
//             $res = $res + $this->calculo_total($sql);
//             }
//             return $res;
//     }
//     function anyo_cero_entradas($tipo,$anyo)
//     {
//         $sql = "SELECT * FROM entradas_salidas where categoria like '%$tipo' and salida like '0000-00-00'";
//         //Iniciales a 2008
//         switch($tipo)
//         {
//             case('despachos'):$cadena = 129;break;
//             case('dom%ica'):$cadena = 99;break;
//             case('dom%tegral'):$cadena = 62;break;
//             case('dom%pecial'):$cadena = 29;break;
//             default: $cadena = $this->calculo_total($sql);break;
//         }
//         if($anyo > '2008')
//         {
//             $cadena = $cadena + $this->anyo_parcial_entradas($tipo,$anyo);
//         }
//         return $cadena;
//     }
// /*
//  * Funcion anyo_cero_salidas($tipo): Devuelve las salidas en anyo 0 pasando como
//  * parametro la categoria
//  */
//     function anyo_parcial_salidas($tipo,$anyo)
//     {
//             $ini = $anyo -1;
//             $ant = ($ini-1)."-12-15";
//             $fini = $ini."-12-15";
//             $res = 0;
//             for($i=$ini;$i>=2008;$i--)
//             {

//             $sql = "Select * from entradas_salidas where
//             categoria like '%$tipo'
//             and ((year(salida) <= '$ini') and (salida > '2007-12-31') and (salida > '$ant')
//             and (salida <= '$fini'))";
//             $res = $res + $this->calculo_total($sql);
//             }
//             return $res;
//     }
//     function anyo_cero_salidas($tipo,$anyo)
//     {
//         $sql = "SELECT * FROM entradas_salidas where categoria like '%$tipo' and (salida <= '2007-12-31' and salida not like '0000-00-00')";
//         switch($tipo)
//         {
//             case('despachos'):$cadena=-103;break;
//             case('dom%ica'):$cadena = -54;break;
//             case('dom%tegral'):$cadena = -40;break;
//             case('dom%pecial'):$cadena = -18;break;
//             default: $cadena = $this->calculo_total($sql);break;
//         }
//         if($anyo > 2008)
//         {
//             $cadena = ($this->anyo_parcial_salidas($tipo,$anyo) - $cadena)*-1;
//         }
//         return $cadena;
//     }
//  /*
//   * Acumulado 0 inicizializa el acumulado
//   */
//     function acumulado_cero($tipo,$anyo)
//     {
//         /*if($anyo > 2008)
//         {
//             switch($tipo)
//             {
//                 case('despachos'):$cadena = 129 + 103;break;
//                 case('dom%ica'):$cadena = 99 + 54;break;
//                 case('dom%tegral'):$cadena = 62 + 40;break;
//                 case('dom%pecial'):$cadena = 29 + 18;break;
//             }
//         }
//         else
//             {*/
//             switch($tipo)
//             {
//                 case('despachos'):$cadena = 26 ;break;
//                 case('dom%ica'):$cadena = 45;break;
//                 case('dom%tegral'):$cadena = 22;break;
//                 case('dom%pecial'):$cadena = 11;break;
//             }
//         //}
//        return $cadena;
//     }
//  /*
//   * Funcion despachos_entrantes($fecha,$tipo): Pasando como parametro la fecha
//   * y el tipo de cliente devuelve el total de despachos entrantes
//   */
//     function despachos_entrantes($fecha,$tipo)
//     {
//         $sql = "Select * from entradas_salidas where categoria like '%$tipo'
//         and (month('$fecha') like month(entrada) and year('$fecha') like year(entrada))";
//         return $this->calculo_total($sql);
//     }
//  /*
//   * Funcion despachos_salientes($fecha,$tipo): Pasando como parametro la fecha
//   * y el tipo de cliente devuelve el total de despachos salientes
//   */
//     function despachos_salientes($fecha,$tipo)
//     {

//         $sql = "Select * from entradas_salidas where
// (categoria like '%$tipo'
// and salida <= DATE_ADD('$fecha', INTERVAL 14 day)
// and salida > DATE_SUB(DATE_ADD('$fecha', INTERVAL 14 day),INTERVAL 1  month)
// and salida > '2007-12-31')";
//         return $this->calculo_total($sql);
       
//     }
// /*
//  * Function calculo_total($sql): Pasando como parametro la consulta sql devuelve
//  * el numero total
//  */
//     function calculo_total($sql)
//     {
//         $consulta = @mysql_db_query($this->dbname,$sql,$this->con);
//         $total = @mysql_numrows($consulta);
//         return $total;
//     }
// /*
//  * Funcion genera_tabla($des_en,$des_sa,$tipo,$descripcion):
//  * Pasamos como parametro los despachos entrantes, los salientes, el tipo de cliente
//  * y una descripcion
//  */
//     function genera_tabla($des_en,$des_sa,$tipo,$descripcion,$anyo0)
//     {
//         if($tipo != NULL)
//         {
            
//             $acu_en = $this->anyo_cero_entradas($tipo,$anyo0);
//             $acu_sa = $this->anyo_cero_salidas($tipo,$anyo0);

//             //$acu = $this->anyo_cero_entradas($tipo)-$this->anyo_cero_salidas($tipo);
//             if($anyo0 > 2008)
//             $acu = $this->acumulado_cero($tipo,$anyo0) + $this->anyo_parcial_entradas($tipo, $anyo0) - $this->anyo_parcial_salidas($tipo, $anyo0);
//             else
//             $acu = $this->acumulado_cero($tipo,$anyo0) ;

//             $tit_en = "Entradas";
//             $tit_sa= "Salidas";
//         }
//         else
//         {
//             if($des_sa != NULL)
//             {
//             $acu_en = 0;
//             $acu_sa = 0;
//             $acu = 0;
//             $tit_en = "JC";
//             $tit_sa = "MJ";
//             }
//             else
//             {
//                 $acu_en = 0;
//                 $acu_sa = 0;
//                 $acu = 0;
//                 $tit_en = "D/S Horas";
//             }
//         }
//         $cadena="<div id='tabla_entradas'>
//         <div class='celda_datos'>
//         <div class='linea_datos_c'>N&ordm;Servicios</div>
//         <div class='linea_datos_e'>$tit_en</div>";
//         if($des_sa != NULL)
//             $cadena.="<div class='linea_datos_s'>$tit_sa</div>";
//         $cadena.="<div class='linea_datos_t'>Total</div>
//         </div>";
//         if($tipo!=NULL)
//         {
//             $cadena.="<div class='celda_datos'>
//             <div class='linea_datos_c'>Acumulado</div>
//             <div class='linea_datos_e'><b>".$acu_en."</b></div>";
//             if($des_sa != NULL)
//                 $cadena.="<div class='linea_datos_s'><b>".$acu_sa."</b></div>";
//             $cadena.="<div class='linea_datos_t'><b>".$acu."</b></div>
//             </div>";
//         }
//         foreach($des_en as $key => $tot)
//         {
//             if($tipo!=NULL)
//             {
//                 $acu = $acu + $tot -$des_sa[$key];
//                 $acu_sa = $acu_sa - $des_sa[$key];
//             }
//             else
//             {
//                 $acu = $acu + $tot  + $des_sa[$key];
//                 $acu_sa = $acu_sa + $des_sa[$key];
//             }
//             $acu_en = $acu_en + $tot;
//             $cadena.= "<div class='celda_datos'>";
//             $cadena.= "<div class='linea_datos_c'>".$key."</div>";
//             $cadena.= "<div class='linea_datos_e'><a href=\"javascript:ver_datos('$key','$descripcion')\">".$tot."</a></div>";
//             if($tipo!=NULL && $des_sa != NULL)
//                 $cadena.= "<div class='linea_datos_s'><a href=\"javascript:ver_datos('$key','".$descripcion."_sa')\">".($des_sa[$key]*-1)."</a></div>";
//             else
//                 if($des_sa != NULL)
//                     $cadena.= "<div class='linea_datos_s'><a href=\"javascript:ver_datos('$key','".$descripcion."_MJ')\">".$des_sa[$key]."</a></div>";
//             $cadena.= "<div class='linea_datos_t'>".$acu."</div>";
//             $cadena.= "</div>";
//             //Si en la key esta diciembre muestro los acumulados
//             if (strstr($key,"Dic"))
//             {
//                 $cadena.= "<div class='celda_datos'>";
//                 $cadena.= "<div class='linea_datos_c'>Acumulado</div>";
//                 $cadena.= "<div class='linea_datos_e'><b>".$acu_en."</b></div>";
//                 if($des_sa != NULL)
//                     $cadena.= "<div class='linea_datos_s'><b>".$acu_sa."</b></div>";
//                 $cadena.= "<div class='linea_datos_t'><b>".$acu."</b></div>";
//                 $cadena.= "</div>";
//                     //Cierro la general y la vuelvo a abrir
//                 $cadena.= "</div><p/>";
//                 $cadena.= "<div id='tabla_entradas'>
//                            <div class='celda_datos'>
//                            <div class='linea_datos_c'>N&ordm;Servicios</div>
//                            <div class='linea_datos_e'>$tit_en</div>";
//                 if($des_sa != NULL)
//                     $cadena.="<div class='linea_datos_s'>$tit_sa</div>";
//                 $cadena.="<div class='linea_datos_t'>Total</div>
//                           </div>";
//                 if($tipo !=NULL)
//                 {
//                     $cadena.= "<div class='celda_datos'>";
//                     $cadena.= "<div class='linea_datos_c'>Acumulado</div>";
//                     $cadena.= "<div class='linea_datos_e'><b>".$acu_en."</b></div>";
//                     if($des_sa != NULL)
//                         $cadena.= "<div class='linea_datos_s'><b>".$acu_sa."</b></div>";
//                     $cadena.= "<div class='linea_datos_t'><b>".$acu."</b></div>";
//                     $cadena.= "</div>";
//                 }
//                 else {$acu_en = 0; $acu_sa=0; $acu=0;}

//               }
//             }
//            $cadena.="</div>";
//        return $cadena;
//     }

// /*
//  * Funcion puntual($fecha,$igual,$distinto): Devuelve los datos de ocupacion
//  * puntual pasandole como parametros la fecha, un array de datos para coincidencia
//  * en puntual y array con datos que sean distintos en el distinto
//  */
//     function puntual($fecha,$igual,$distinto)
//     {
//         $sql = "SELECT 1 FROM `detalles consumo de servicios` as d inner Join `consumo de servicios` as c
// on d.`Id Pedido` = c.`Id Pedido` inner Join `clientes` as l on c.`cliente` = l.id where
//         l.categoria like 'clientes externos' and (month(c.fecha) like month('$fecha') and year(c.fecha) like year('$fecha')) ";
//         if(count($igual)>=1)
//         {
//             $sql.=" and ( ";
//             foreach($igual as $var)
//             $sql .= " Locate('$var',d.servicio) and";
//             $sql = substr($sql,0,strlen($sql)-3);
//             $sql.= " ) ";
//         }
//         if(count($distinto)>=1)
//         {
//             $sql.="and ( ";
//             foreach($distinto as $var)
//             $sql .= " !Locate('$var',d.servicio) and";
//             $sql = substr($sql,0,strlen($sql)-3);
//             $sql.= " ) ";
//         }
//         return $this->calculo_total($sql);
//     }
// /*
//  * Funcion desaho($fecha,$solo): Calculo de despachos hora, se le pasa la fecha,
//  * el parametro solo, creo que es para depuracion, si se pone devuelve el sql generado
//  */
//     function desaho($fecha,$solo)
//     {
//         $sql = "SELECT 1 FROM `detalles consumo de servicios` as d
// inner Join `consumo de servicios` as c
// on d.`Id Pedido` = c.`Id Pedido`
// inner Join `clientes` as l on c.`cliente` = l.id
//         where
//         l.categoria like '%externo%'
//         and
//         (month(c.fecha) like month('$fecha') and year(c.fecha) like year('$fecha')) and
//         (Locate('Hora',d.servicio) and (Locate('Despacho',d.servicio) or Locate('Sala',d.servicio)))";
//         //var_dump($sql);
//         if(isset($solo))
//             return $sql;
//         return $this->calculo_total($sql);
//     }
//  /*
//   * funcion servicios(fecha,contiene,nocontiene)
//   */
//     function servicios($fecha,$contiene,$nocontiene)
//     {
//         $sql = "select 1 from `detalles consumo de servicios` as d
//         inner Join `consumo de servicios` as c
//         on d.`Id Pedido` = c.`Id Pedido`
//         where month(c.fecha) like month('$fecha') and year(c.fecha) like year('$fecha') and ";
//         if(is_array($contiene))
//         {
//             foreach($contiene as $var)
//             {
//              if($var == 'videopro')
//              {
//                 $sql.=" (Locate('videopro',d.servicio) or
//                         Locate('".utf8_decode('cañón')."',d.servicio)) and ";
//                         //var_dump($sql);
             
//              }
//              else
//                 $sql.=" Locate('".$var."',d.servicio) or ";
//             }
//         }
//         if(is_array($nocontiene))
//         {
//             foreach($nocontiene as $var)
//              $sql.=" !Locate('".$var."',d.servicio) and ";
//         }
//         $sql = substr($sql,0,strlen($sql)-4);
//         //var_dump($sql);
//         return $this->calculo_total($sql);
//         //return $sql;
//     }
// /*
//  * Funcion quien_cuando($fecha,$servicio): Funcion que devuelve el cliente y la
//  * fecha dependiendo el dato de la tabla que se marque
//  */
//     function quien_cuando($fecha,$servicio)
//     {
//        $cadena="<input type='button' class='boton' onclick=cierra_datos() value='[X]Cerrar' />";
//         switch($servicio)
//         {
//             case('despachos'):$ssql = "Select idemp,categoria,entrada from entradas_salidas where categoria like '%$servicio%'
//                                        and (month('$fecha') like month(entrada) and year('$fecha') like year(entrada)) order by entrada";break;
//             case('despachos_sa'):$ssql = "Select idemp,categoria,salida from entradas_salidas where
//                                         (categoria like '%despacho%'
//                                         and salida <= DATE_ADD('$fecha', INTERVAL 14 day)
//                                         and salida > DATE_SUB(DATE_ADD('$fecha', INTERVAL 14 day),INTERVAL 1  month)
//                                         and salida > '2007-12-31') order by salida";break;
//             case('dom_basica'):$ssql = "Select idemp,categoria,entrada from entradas_salidas where categoria like '%Dom%ica%'
//                                         and (month('$fecha') like month(entrada) and year('$fecha') like year(entrada)) order by entrada";break;
//              case('dom_basica_sa'):$ssql = "Select idemp,categoria,salida from entradas_salidas where
//                                         (categoria like '%Dom%ica%'
//                                         and salida <= DATE_ADD('$fecha', INTERVAL 14 day)
//                                         and salida > DATE_SUB(DATE_ADD('$fecha', INTERVAL 14 day),INTERVAL 1  month)
//                                         and salida > '2007-12-31') order by salida";break;
//             case('dom_integral'):$ssql = "Select idemp,categoria,entrada from entradas_salidas where categoria like '%Dom%gral'
//                                           and (month('$fecha') like month(entrada) and year('$fecha') like year(entrada)) order by entrada";break;
//             case('dom_integral_sa'):$ssql = "Select idemp,categoria,salida from entradas_salidas where
//                                         (categoria like '%Dom%gral'
//                                         and salida <= DATE_ADD('$fecha', INTERVAL 14 day)
//                                         and salida > DATE_SUB(DATE_ADD('$fecha', INTERVAL 14 day),INTERVAL 1  month)
//                                         and salida > '2007-12-31') order by salida";break;
//             case('dom_especial'):$ssql = "Select idemp,categoria,entrada from entradas_salidas where categoria like '%Dom%cial'
//                                           and (month('$fecha') like month(entrada) and year('$fecha') like year(entrada)) order by entrada";break;
//            case('dom_especial_sa'):$ssql = "Select idemp,categoria,salida from entradas_salidas where
//                                         (categoria like '%Dom%cial'
//                                         and salida <= DATE_ADD('$fecha', INTERVAL 14 day)
//                                         and salida > DATE_SUB(DATE_ADD('$fecha', INTERVAL 14 day),INTERVAL 1  month)
//                                         and salida > '2007-12-31') order by salida";break;
// 		    case('dom_integral_att'):$ssql = "Select idemp,categoria,entrada from entradas_salidas where categoria like '%Dom%gral%Aten%'
//                                           and (month('$fecha') like month(entrada) and year('$fecha') like year(entrada)) order by entrada";break;
//             case('dom_integral_att_sa'):$ssql = "Select idemp,categoria,salida from entradas_salidas where
//                                         (categoria like '%Dom%gral%Aten%'
//                                         and salida <= DATE_ADD('$fecha', INTERVAL 14 day)
//                                         and salida > DATE_SUB(DATE_ADD('$fecha', INTERVAL 14 day),INTERVAL 1  month)
//                                         and salida > '2007-12-31') order by salida";break;
//             case('dom_especial_att'):$ssql = "Select idemp,categoria,entrada from entradas_salidas where categoria like '%Dom%cial%Aten%'
//                                           and (month('$fecha') like month(entrada) and year('$fecha') like year(entrada)) order by entrada";break;
//            case('dom_especial_att_sa'):$ssql = "Select idemp,categoria,salida from entradas_salidas where
//                                         (categoria like '%Dom%cial%Aten%'
//                                         and salida <= DATE_ADD('$fecha', INTERVAL 14 day)
//                                         and salida > DATE_SUB(DATE_ADD('$fecha', INTERVAL 14 day),INTERVAL 1  month)
//                                         and salida > '2007-12-31') order by salida";break;
          	
           
           
//            case('att_telefonica'):$ssql = "Select idemp,categoria,entrada from entradas_salidas where categoria like 'Clientes Atencion Telefonica'
//                                           and (month('$fecha') like month(entrada) and year('$fecha') like year(entrada)) order by entrada";break;
//            case('att_telefonica_sa'):$ssql = "Select idemp,categoria,salida from entradas_salidas where
//                                         (categoria like 'Clientes Atencion Telefonica'
//                                         and salida <= DATE_ADD('$fecha', INTERVAL 14 day)
//                                         and salida > DATE_SUB(DATE_ADD('$fecha', INTERVAL 14 day),INTERVAL 1  month)
//                                         and salida > '2007-12-31') order by salida";break;
//            case('oficina_movil'):$ssql = "Select idemp,categoria,entrada from entradas_salidas where categoria like 'Clientes Oficina Movil'
//                                           and (month('$fecha') like month(entrada) and year('$fecha') like year(entrada)) order by entrada";break;
//            case('oficina_movil_sa'):$ssql = "Select idemp,categoria,salida from entradas_salidas where
//                                         (categoria like 'Clientes Oficina Movil'
//                                         and salida <= DATE_ADD('$fecha', INTERVAL 14 day)
//                                         and salida > DATE_SUB(DATE_ADD('$fecha', INTERVAL 14 day),INTERVAL 1  month)
//                                         and salida > '2007-12-31') order by salida";break;
           
//            case('punt_salas'):
//                 $sql = " l.categoria like 'clientes externos' and (Locate('Sala de Juntas',d.servicio) and Locate('Completa',d.servicio) and !Locate('Clientes',d.servicio)) ";break;
//             case('punt_salas_MJ'):
//                 $sql = " l.categoria like 'clientes externos' and (Locate('Sala de Juntas',d.servicio) and Locate('Media',d.servicio) and !Locate('Clientes',d.servicio)) ";break;
//             case('punt_despachos'):
//                 $sql = " l.categoria like 'clientes externos' and ((Locate('Despacho',d.servicio) or Locate('Sala',d.servicio)) and Locate('Completa',d.servicio) and !Locate('Clientes',d.servicio) and !Locate('Juntas',d.servicio)) ";break;
//             case('punt_despachos_MJ'):
//                 $sql = " l.categoria like 'clientes externos' and ((Locate('Despacho',d.servicio) or Locate('Sala',d.servicio)) and Locate('Media',d.servicio) and !Locate('Clientes',d.servicio) and !Locate('Juntas',d.servicio)) ";break;
//             case('desp/sala_horas'):
//                 $sql = "l.categoria like 'clientes externos' and (Locate('Hora',d.servicio) and
//                         (Locate('Despacho',d.servicio) or Locate('Sala',d.servicio)))";break;
//             case('Llamadas'):$sql = " (Locate('de llamadas',d.servicio) and
//                                     !Locate('anuncio',d.servicio) and !Locate('Desvio',d.servicio))";break;
//             case('Videoproyector'):$canyon = utf8_decode('cañón');
//             $sql = " (Locate('".$canyon."',d.servicio))";break;
//             case('Anuncios'):$sql = " (Locate('Anuncio en prensa',d.servicio) and
//                                     !Locate('llamadas',d.servicio))";break;
//             case('Mailing'):$sql = " (Locate('mailing',d.servicio))";break;

//             default: $cadena .= $servicio;break;
//         }
//         if(isset($sql))
//         {
//             $esql = "Select c.Cliente, d.Servicio, c.fecha
//                      from `detalles consumo de servicios` as d
//                      inner Join `consumo de servicios` as c
//                      on d.`Id Pedido` = c.`Id Pedido`
//                      inner Join `clientes` as l on c.`cliente` = l.id
//                      where month(c.fecha) like month ('$fecha')
//                      and year(c.fecha) like year ('$fecha')
//                      and ".$sql. " order by c.fecha ";
            
//             $consulta =  @mysql_db_query($this->dbname,$esql,$this->con);
//             if(@mysql_numrows($consulta)!=0)
//                 $cadena .= $this->ver_tabla($consulta);
//             else
//                 $cadena .= "<h3>No hay empresas en estas fechas</h3>";
//         }
//         if(isset($ssql))
//         {
//             $consulta = @mysql_db_query($this->dbname,$ssql,$this->con);
//             if(@mysql_numrows($consulta)!=0)
//                 $cadena .= $this->ver_tabla($consulta);
//             else
//                 $cadena .= "<h3>No hay empresas en estas fechas</h3>";
//         }
//         return $cadena;
//     }
// /*
//  * Funcion ver_tabla($consulta): Devuelve la tabla con los datos referidos en la consulta
//  * de quien cuando, pasando como parametro la consulta.
//  */
//     function ver_tabla($consulta)
//     {
//         $i=1;
//         $cadena.="<table class='tabla'><tr><th></th><th>Cliente</th><th>Servicio</th><th>Fecha</th></tr>";
//         while($resultado = @mysql_fetch_array($consulta))
//         {
//             if($i%2==0)
//                 $class = "class = 'par'";
//             else
//                 $class = "class = 'impar'";
//             $cadena.="<tr ".$class."><th>".$i++."</th><td>".utf8_encode($this->nombre_empresa($resultado[0]))."</td>";
//             $cadena.="<td>".utf8_encode($resultado[1])."</td>";
//             $cadena.="<td>".$this->fecha($resultado[2])."</td></tr>";
//         }
//         $cadena.="</table>";
//         return $cadena;
//     }
// }
// ?>
