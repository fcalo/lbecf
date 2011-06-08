<?include("../session.php");
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');

unset($_SESSION["sea".$_POST['i']]);
$_SESSION["sea".$_POST['i']]=$_POST;
/*foreach($_POST  as $k=>$v){
	if ($k!="i")
		$_POST[echo $k.":".$v;
}*/

echo "OK";
?>