// <? include("clases.cni.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- <html> -->
<!-- <head> -->

// <? if(isset($_GET["print"])) { ?>
<!-- <link href="estilo/print.css" rel="stylesheet" type="text/css"></link> -->
// <? } else { ?>
<!-- <link href="../estilo/cni.css" rel="stylesheet" type="text/css"></link> -->
<!-- <link href='estilo/estilo.css' rel='stylesheet' type='text/css'></link> -->
// <? } ?>
<!-- <title>Aplicacion Gestion Independencia Centro Negocios </title> -->
<!-- <script src='../js/prototype.js' type="text/javascript"></script> -->
<!-- <script src='js/entradas.js' type='text/javascript'></script> -->
<!-- </head> -->
<!-- <body> -->
 <?
// if(isset($_POST['rang0']))
//  {

//     $check0 = $_POST['rang0'];
//     $check1 = $_POST['rang1'];
//     if(isset($_POST['rangoo']))
//     {
//         for($k=$_POST['rang0'];$k<=$_POST['rang1'];$k++)
//         $anyos[]=$k;
//     }
//     else
//     {
//         if($_POST['rang0']==$_POST['rang1'])
//             $anyos = array($_POST['rang0']);
//         else
//             $anyos = array($_POST['rang0'],$_POST['rang1']);
//     }
//  }
//  else
//  {
//      $anyos = array(date(Y)-1,date(Y));
//      $check0 = date(Y)-1;
//      $check1 = date(Y);
//  }
//  ?>
    <h2>Cuadro de Entradas y comparativas <? foreach($anyos as $anyo) echo " ".$anyo; ?></h2>

// <?
//  if(!isset($_GET["print"]))
//  {
//     $form = "<form id='rango' name='rango' method='post' action=''>";
//     $form.="<select name='rang0'><optgroup label='Año inicial'>";
//     for($i=2008;$i<=date(Y);$i++)
//     {
//         if($i==$check0)
//             $form.="<option selected value='".$i."'>".$i."</option>";
//         else
//             $form.="<option value='".$i."'>".$i."</option>";
//     }
//     $form.="</select>";
//     $form.="<select name='rang1'><optgroup label='Año final'>";
//     for($i=2008;$i<=date(Y);$i++)
//     {
//         if($i==$check1)
//             $form.="<option selected value='".$i."'>".$i."</option>";
//         else
//             $form.="<option value='".$i."'>".$i."</option>";
//     }
//     $form.="</select>";
//     $form.="<label>¿Rango?</label><input type='checkbox' name='rangoo' checked />";
//     $form.="<input type='submit' value='Ver Años' /></form>";
//     echo $form;
//  }
//     $meses = array("","Ene","Feb","Mar","Abr","May","Jun",
//         "Jul","Ago","Sep","Oct","Nov","Dic");
//     $entrada = New Entradas();
//     foreach ($anyos as $i)
//     {
//         for ($j=1;$j<=12;$j++)
//         {
//             if($j<=9)
//                 $fecha = $i."-0".$j."-01";
//             else
//                 $fecha = $i."-".$j."-01";
//             //Despachos entradas y salidas
//             $des_en[$meses[$j]."/".$i] = $entrada->despachos_entrantes($fecha,"despachos");
//             $des_sa[$meses[$j]."/".$i] = $entrada->despachos_salientes($fecha,"despachos");
//             //Domiciliacion basica entradas y salidas
//             $dom_bas_en[$meses[$j]."/".$i] = $entrada->despachos_entrantes($fecha,"dom%ica");
//             $dom_bas_sa[$meses[$j]."/".$i] = $entrada->despachos_salientes($fecha,"dom%ica");
//             //Domiciliacion integral
//             $dom_in_en[$meses[$j]."/".$i] = $entrada->despachos_entrantes($fecha,"dom%tegral");
//             $dom_in_sa[$meses[$j]."/".$i] = $entrada->despachos_salientes($fecha,"dom%tegral");
//             //Domiciliacion integral + Atencion Telefonica
//             $dom_in_att_en[$meses[$j]."/".$i] = $entrada->despachos_entrantes($fecha,"dom%tegral%Aten%");
//             $dom_in_att_sa[$meses[$j]."/".$i] = $entrada->despachos_salientes($fecha,"dom%tegral%Aten%");
//           	//Domiciliacion especial
//             $dom_es_en[$meses[$j]."/".$i] = $entrada->despachos_entrantes($fecha,"dom%pecial");
//             $dom_es_sa[$meses[$j]."/".$i] = $entrada->despachos_salientes($fecha,"dom%pecial");
//             //Domiciliacion especial + Atencion Telefonica
//             $dom_es_att_en[$meses[$j]."/".$i] = $entrada->despachos_entrantes($fecha,"dom%pecial%Aten%");
//             $dom_es_att_sa[$meses[$j]."/".$i] = $entrada->despachos_salientes($fecha,"dom%pecial%Aten%");
            
//             //Clientes de atencion Telefonico
//             $att_tel_en[$meses[$j]."/".$i] = $entrada->despachos_entrantes($fecha,"Clientes Atencion Telefonica");
//             $att_tel_sa[$meses[$j]."/".$i] = $entrada->despachos_salientes($fecha,"Clientes Atencion Telefonica");
//             //Clientes de Oficina Movil
//             $ofi_movil_en[$meses[$j]."/".$i] = $entrada->despachos_entrantes($fecha,"Clientes Oficina Movil");
//             $ofi_movil_sa[$meses[$j]."/".$i] = $entrada->despachos_salientes($fecha,"Clientes Oficina Movil");
           
//             //Ocupaciones
//             $ocu_sa_jc[$meses[$j]."/".$i] = $entrada->puntual($fecha,array("Juntas","Completa"),array("Media","M.","Clientes"));
//             $ocu_sa_mj[$meses[$j]."/".$i] = $entrada->puntual($fecha,array("Juntas","Media","Jornada"),array("Completa","Clientes"));
//             $des_sa_jc[$meses[$j]."/".$i] = $entrada->puntual($fecha,array("Completa",),array("Juntas","Media","M.","Clientes"));
//             $des_sa_mj[$meses[$j]."/".$i] = $entrada->puntual($fecha,array("Media","Jornada"),array("Juntas","Completa","Clientes"));
//             //Despacho/sala horas
//             $des_sa_ho[$meses[$j]."/".$i] = $entrada->desaho($fecha,NULL);
//             //Seccion de otros servicios
//             //$serv_llamada[$meses[$j]."/".$i] = $entrada->servicios($fecha,array('de llamadas', 'telef%'),array('anuncio','Desvio'));
//             $serv_canyon[$meses[$j]."/".$i] = $entrada->servicios($fecha,array('videopro'),NULL);
//             //$serv_anuncios[$meses[$j]."/".$i] = $entrada->servicios($fecha,array('Anuncio en prensa'),array('llamadas'));
//             $serv_mailing[$meses[$j]."/".$i] = $entrada->servicios($fecha,array('mailing'),NULL);

//             $sql[$meses[$j]." de ".$i]=
//             "Select c.Nombre, e.entrada from clientes as c
//     inner join entradas_salidas as e on c.Id like e.idemp
//     where (entrada not like '0000-00-00' and c.Nombre != ''
//     and (salida like '0000-00-00' or salida > '".$fecha."') and entrada <= '".$fecha."'
// ) order by entrada";

//         }
//     }


//     //Estos son los que estan
//     $totales[0]=0;
//     $j=0;
//  if(!isset($_GET["print"]))
//  {
//   echo "<div id='detalles_mes'></div>";
//  }
//   //Tablica
//     //Recopilacion de datos

// ?>
<!-- <!-- Con divs por columna --> -->
<!-- <p/> -->
// <?  if(!isset($_GET["print"]))
//         echo "<a href='http://172.26.0.131/cni/entradas/index2.php?print=true'>Version para imprimir</a>";
//     else
//         echo "<a href='http://172.26.0.131/cni/entradas/index2.php'>Atras</a>";

//    //Echo de la tabla generada en la clase
//    echo "<h2>Despachos</h2>";
//    echo $entrada->genera_tabla($des_en,$des_sa,"despachos","despachos",$anyos[0]);
//    echo "<h2>Domiciliacion Basica</h2>";
//    echo $entrada->genera_tabla($dom_bas_en,$dom_bas_sa,"dom%ica","dom basica",$anyos[0]);
//    echo "<h2>Domiciliacion Integral</h2>";
//    echo $entrada->genera_tabla($dom_in_en,$dom_in_sa,"dom%tegral","dom integral",$anyos[0]);
//    echo "<h2>Domiciliacion Integral + Atencion Telefonica</h2>";
//    echo $entrada->genera_tabla($dom_in_att_en,$dom_in_att_sa,"dom%tegral + Aten%","dom integral att",$anyos[0]);
//    echo "<h2>Domiciliacion Especial</h2>";
//    echo $entrada->genera_tabla($dom_es_en,$dom_es_sa,"dom%pecial","dom especial",$anyos[0]);
//    echo "<h2>Domiciliacion Especial + Atencion Telefonica</h2>";
//    echo $entrada->genera_tabla($dom_es_att_en,$dom_es_att_sa,"dom%pecial + Aten%","dom especial att",$anyos[0]);
//    echo "<h2>Clientes Atencion Telefonica</h2>";
//    echo $entrada->genera_tabla($att_tel_en,$att_tel_sa,"Atencion Telefonica","att telefonica",$anyos[0]);
//    echo "<h2>Clientes Oficina Movil</h2>";
//    echo $entrada->genera_tabla($ofi_movil_en,$ofi_movil_sa,"Oficina Movil","oficina movil",$anyos[0]);
  
//    echo "<h2>Ocupacion Puntual Salas</h2>";
//    echo $entrada->genera_tabla($ocu_sa_jc,$ocu_sa_mj,NULL,"punt salas",NULL);
//    echo "<h2>Ocupacion Puntual Despachos</h2>";
//    echo $entrada->genera_tabla($des_sa_jc,$des_sa_mj,NULL,"punt despachos",NULL);
//    echo "<h2>Despacho/sala horas</h2>";
//    echo $entrada->genera_tabla($des_sa_ho,NULL,NULL,"desp/sala horas",NULL);
//    //echo "<h2>Atencion de Llamadas</h2>";
//    //echo $entrada->genera_tabla($serv_llamada,NULL,NULL,"Llamadas",NULL);
//    //echo "<h2>Alquiler de Ca&ntilde;&oacute;n</h2>";
//    //echo $entrada->genera_tabla($serv_canyon,NULL,NULL,"Videoproyector",NULL);
//    //echo "<h2>Anuncios</h2>";
//    //echo $entrada->genera_tabla($serv_anuncios,NULL,NULL,"Anuncios",NULL);
//    //echo "<h2>Mailing</h2>";
//    //echo $entrada->genera_tabla($serv_mailing,NULL,NULL,"Mailing",NULL);
//    //echo $entrada->genera_tabla($dom_bas_en,$dom_bas_sa,"Domiciliacion Basica");

// ?>



<!-- </body> -->
<!-- </html> -->