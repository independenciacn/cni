<?php //generador de datos
include("../conexion.php");
if (isset($_POST[contrato]))
{
//1.-Cargamos datos y tablas necesarios para llenar el formulario de ese contrato	
$sql = "Select * from z_contratos where id like $_POST[contrato]";
$consulta = mysql_query($sql,$con);
$resultado = mysql_fetch_array($consulta);
$fichero = $resultado[2];
$totcamp = $resultado[3];
//buscamos los alias
$sql = "Select * from z_ccontratos where contrato like $_POST[contrato] order by orden";
$consulta = mysql_query($sql,$con);
$j=0;
while(true == ($resultado = mysql_fetch_array($consulta)))
{
	$muestra[$j]=$resultado[campof];
	$j++;
}
echo "<input type='hidden' id='fichero' value='".$fichero."' />";
echo "<input type='hidden' id='totcamp' value='".$totcamp."' />";
echo "<table id='tabla_contrato' cellpadding='1px' cellspacing='1px'>";
for ($i=0;$i<=$totcamp-1;$i++)
{
	
	
	echo "<tr><th>".$muestra[$i]."</th><td><input type='text' id='campo".$i."' size='40' /></td></tr>";
}
?>
</table>
<span  class='boton' id='genera' onclick = 'generalo()' />Generar Contrato</span>
<?php 
} 
?>