<?

//require_once "./PEAR/PEAR/Mail.php";
//$server="http://".$_SERVER['HTTP_HOST'];
//if(strpos($server,"localhost")>0){
	require_once "Mail.php";
	require_once ('Mail/mime.php'); 
//}else{
//	require_once "/home/miequipodeportivo/www/PEAR/PEAR/Mail.php";
//}



function sendMail($para,$asunto,$cuerpo){
	
	$server="http://".$_SERVER['HTTP_HOST'];
	if(strpos($server,"localhost")>0){
		$host = "mail.miequipodeportivo.com";
	}
	else
		$host = "localhost";
	$from="Mi Equipo Deportivo <miequipodeportivo@miequipodeportivo.com>";
	$username = "miequipodeportivo@miequipodeportivo.com";
	$password = "kulgfn";
	$port=25;
	

	$smtp = Mail::factory('smtp',
	array ('host' => $host,
	'auth' => true,
	'port' => $port,
	'username' => $username,
	'password' => $password));

	if (PEAR::isError($mail))
		die("error");
	$headers = array ('From' => $from,
					'MIME-Version' => '1.0',
					'Content-type' => 'text/html',
					'charset' => 'UTF8',
					'Subject' => $asunto);

	$mail = $smtp->send($para, $headers, $cuerpo);
	if (PEAR::isError($mail)){
		die($mail->getMessage());
	}else{
		return true;
	}
}

if(isset($_GET['test'])){
	if (sendmail("fernando.calo.sanchez@gmail.com","test","test"))
		echo "Enviado";
}
?>