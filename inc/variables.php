<? //aqui pondremos las diferencias de variables entre windows mac y linux
//nombre de la base de datos
$con = mysql_connect ("localhost","cni","inc") or die (mysql_error()); //cadena de conexion a la base de datos
mysql_set_charset('utf8', $con);
//$dbname = "CENTRO"; //para Windows
$dbname = "centro"; //mara mac y linux
DEFINE("OK","imagenes/clean.png"); //imagen en el mensaje de correcto
DEFINE("NOK","imagenes/error.png"); //imagen en el mensaje de fallo
//DEFINE("SISTEMA","*nix");
DEFINE("SISTEMA","windows");
//$sql = "SET NAMES 'utf8'";
//$sql = "SET NAMES 'latin1'";
//$consulta = mysql_db_query($dbname,$sql,$con);

?>
