<?php
    /*include("../inc/variables.php");
	
	$sql = "Select id,factura,servicio,obs from historico where servicio like 'Bultos Almacenados%'";
	$consulta = @mysql_db_query($dbname,$sql,$con);
	while($resultado = @mysql_fetch_array($consulta))
	{
		
		echo "<br>".$resultado[0]."-".$resultado[1]."-".$resultado[2]."-".$resultado[3];
		$nueva = explode('Bultos Almacenados ',$resultado[2]);
		if(count($nueva)>=2)
		{
			$sql2 = "Update historico set servicio='Bultos Almacenados',obs='$nueva[1]' where id like $resultado[0]"; 
			if($consulta2=mysql_db_query($dbname,$sql2,$con)) 
			echo"<font color=green>actualizado</font>";
			else
			echo"<font color=red>no actualizado</font>".$sql2;
		}
	}*/
	echo urldecode("Pagan%20mediante%20TR%20antes%20del%20d%C3%ADa%205%20de%20cada%20mes.%0A**%20SEPTIEMBRE%3A%20abono%2034%20euros%20por%20cambio%20de%20dom.%20especial%20a%20integral%20el%2001%2F08%2F08.%0A***%20Devolver%20diferencia%20fianza%20cuando%20firmen%20contrato");
?>
