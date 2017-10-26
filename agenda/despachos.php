<table id='agenda' width='100%'>
<?php
	require_once '../inc/variables.php';
	include_once 'funciones.php';
	$sql = "SELECT z.valor, c.Nombre,c.id ,c.Categoria FROM clientes as c  join z_sercont as z on c.id like z.idemp 
		WHERE  Estado_de_cliente != 0 and 
		 c.Categoria like '%despacho%' and 
		z.servicio like 'Codigo Negocio' order by z.valor asc";
	$consulta = @mysql_query($sql,$con);
	$despachos=array();
	$clase=array();
	while( true == ( $resultado = mysql_fetch_array( $consulta ) ) )
	{
		 $despachos[intval($resultado[0])]=$resultado[1];
		 $clase[intval($resultado[0])]="despacho_ocupado";
		 $cliente[intval($resultado[0])]=$resultado[2];
	}
	for($i=0;$i<=5;$i++)
	{
		$cadena.="<tr>";
		for($j=0;$j<=5;$j++)
		{
			$despacho++;
			if($despacho == 23)
			$muestra = "Sala de Juntas";
			else
			$muestra = "Despacho ".$despacho;
			$cadena.="<td width='16.66%' id='despacho_$despacho' valign='top'><div class='cabezera_despacho'>".$muestra."
		";
			if($cliente[$despacho]!='')
			{
				$cadena.="</div>
				<div class='".$clase[$despacho]."' height='100%'>
		".$despachos[$despacho];
				$cadena.="<p/><span class='mini_boton' onclick='informacion_cliente($cliente[$despacho])'>&nbsp;+Info&nbsp;</span>
				<input type='hidden' id='cliente_despacho_$despacho' value='$cliente[$despacho]' />";
			}
			else
			{
				$cadena.="</div>";
				$cadena.="<input type='hidden' id='cliente_despacho_$despacho' value='' />";
				$cadena.="<div class='despacho_parcial'>&nbsp;</div>";
			}
		$cadena.="</div>
		</td>";
		}
		$cadena.="</tr>";
	}
	echo $cadena;

?>
</table>