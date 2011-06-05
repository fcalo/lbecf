<?
	include("libs/session.php");
	//TODO
	$user=$_POST['user'];
	$pass=$_POST['pass'];
	$npass=$_POST['npass'];
	$npass2=$_POST['npass2'];
	$failed=false;
	if($user!="" && $pass!="")
	{
		

		include('./config/database.php');
		$rs=$db->get_row("select count(*) c from t_login where user='".$user."' and pass='".sha1($pass)."'",ARRAY_A);
		
		if ($rs['c']>0 )
			if ($npass==$npass2){
				if($db->query("update t_login set pass='".sha1($npass)."' where user='".$user."'")){
					$_SESSION[constant(USER.PROJECT)]=$user;
				}
			}else
				$error=1;
		else
			$failed=true;
	}
		
	if(!defined(USER.PROJECT) || isset($_SESSION[constant(USER.PROJECT)]) && $_SESSION[constant(USER.PROJECT)]!="")
	{
		header('Location: index.php');
		end;
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Acceso al Sistema</title>

<!--dependencias yui-->
<link rel="stylesheet" type="text/css" href="js/yui/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="js/yui/build/button/assets/skins/sam/button.css">


<script type="text/javascript" src="js/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="js/yui/build/element/element-min.js"></script>
<script type="text/javascript" src="js/yui/build/button/button-min.js"></script>


<style>
body {
	margin:0;
	padding:10;
	height:100%;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
input{
	background-color: #F7F7F7;
	border: #C0C0C0 1px solid;
	width:45%;
	
}
#loginbox{
	position:absolute;
	border: 3px solid #CCC;
	top:33%;
/*	height:100px;
	min-height:100px;*/
	left:27%;
	width:34%;
	min-width:20%;
	padding-bottom:10px;
}
#failedbox{
	border: 3px solid #F00;
	color:#F00;
	text-align:center;
	width:98%;
    display:none;
	margin-top:30px;
}
#failedbox2{
	border: 3px solid #F00;
	color:#F00;
	text-align:center;
	width:98%;
    display:none;
	margin-top:30px;
}
#boxuser{
	padding:5px;
	padding-top:10px;
	
}
#boxpass{
	padding:5px;
	padding-top:5px;
	
}
label {display:block;float:left;width:45%;clear:left; }

#button_ko{position:relative;float:right;}
#button_ok{position:relative;float:right;}

</style>
<script>
var oButtonOk = new YAHOO.widget.Button({  
    type: "link",  
    id: "buttonOk",  
    label: "Aceptar",  
    href: "javascript:aceptar()",  
    container: "button_ok"}); 
var oButtonCancel = new YAHOO.widget.Button({  
    type: "link",  
    id: "buttonKo",  
    label: "Cancelar",  
    href: "javascript:location.href='login.php'",  
    container: "button_ko"}); 


function press(){
		if (event.keyCode == 13)
			aceptar();
}
function press_ff(e) {
	if (e.which == 13)
		aceptar();
}
var clientPC = navigator.userAgent.toLowerCase();
var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1)); 
function init(){
	if (is_ie) {
		document.getElementById("user").onkeypress = press;
		document.getElementById("pass").onkeypress = press;
	}else {
		document.getElementById("user").onkeypress = press_ff;
		document.getElementById("pass").onkeypress = press_ff;
	}
	document.getElementById("user").focus();
	<?if ($failed){
		echo 'document.getElementById("failedbox").style.display="block";';
	}?>
	<?if ($error==1){
		echo 'document.getElementById("failedbox2").style.display="block";';
	}?>

}
function aceptar(){
	if (document.getElementById("user").value=='' || document.getElementById("pass").value==""){
		alert('Introduzca los datos para conectar.');
		document.getElementById("user").focus();
	}else
		document.getElementById("frmLogin").submit();
}


</script>
<body class="yui-skin-sam" onLoad="init()">
	<div id="loginbox">
		<form name="frmLogin" id="frmLogin" method="POST">
		<div id="boxuser"><label>Usuario</label><input type="text" name="user" id="user"/></div>
		<div id="boxpass"><label>Contrase&ntilde;a antigua</label><input type="password" name="pass" id="pass"/></div>
		<div id="boxpass"><label>Nueva Contrase&ntilde;a </label><input type="password" name="npass" id="pass"/></div>
		<div id="boxpass"><label>Repita Contrase&ntilde;a </label><input type="password" name="npass2" id="pass"/></div>
		<div id="boxbuttons">
			<span id="button_ok"></span>
			<span id="button_ko"></span>
		</div>
		</form>
		<div id="failedbox">Datos Incorrectos</div>
		<div id="failedbox2">Las contrase&ntilde;as no coinciden</div>
	</div>
	
</body>
</html>