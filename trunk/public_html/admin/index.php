<?php

	include("libs/session.php");
	include("libs/parser.php");
	include("libs/config_database.php");
	
	if ($_REQUEST['logout']==1){
		unset($_SESSION[constant(USER.PROJECT)]);
		//$_SESSION['force']=1;
		//header('Location: /admin/');
		//end;
	}

	
	if(!defined(USER.PROJECT) || !isset($_SESSION[constant(USER.PROJECT)]) || $_SESSION[constant(USER.PROJECT)]=="")
	{
		header('Location: login.php');
		end;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title></title>

<!--dependencias yui-->
<link rel="stylesheet" type="text/css" href="js/yui/build/reset-fonts-grids/reset-fonts-grids.css">
<link rel="stylesheet" type="text/css" href="js/yui/build/menu/assets/skins/sam/menu.css">
<link rel="stylesheet" type="text/css" href="js/yui/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="js/yui/build/paginator/assets/skins/sam/paginator.css" />
<link rel="stylesheet" type="text/css" href="js/yui/build/datatable/assets/skins/sam/datatable.css" />
<link rel="stylesheet" type="text/css" href="js/yui/build/button/assets/skins/sam/button.css">
<link rel="stylesheet" type="text/css" href="js/yui/build/container/assets/skins/sam/container.css" />
<link rel="stylesheet" type="text/css" href="js/yui/build/tabview/assets/skins/sam/tabview.css" />
<link rel="stylesheet" type="text/css" href="js/yui/build/calendar/assets/skins/sam/calendar.css" />
<link rel="stylesheet" type="text/css" href="js/yui/build/editor/assets/skins/sam/simpleeditor.css" />

<script type="text/javascript" src="js/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="js/yui/build/container/container_core.js"></script>
<script type="text/javascript" src="js/yui/build/menu/menu.js"></script>
<script type="text/javascript" src="js/yui/build/connection/connection-min.js"></script>
<script type="text/javascript" src="js/yui/build/json/json-min.js"></script>
<script type="text/javascript" src="js/yui/build/element/element-min.js"></script>
<script type="text/javascript" src="js/yui/build/paginator/paginator-min.js"></script>
<script type="text/javascript" src="js/yui/build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="js/yui/build/datatable/datatable-min.js"></script>
<script type="text/javascript" src="js/yui/build/button/button-min.js"></script>
<script type="text/javascript" src="js/yui/build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="js/yui/build/container/container-min.js"></script>
<script type="text/javascript" src="js/yui/build/tabview/tabview-min.js"></script>
<script type="text/javascript" src="js/yui/build/calendar/calendar-min.js"></script>
<script type="text/javascript" src="js/yui/build/editor/simpleeditor.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>

<!--fin dependencias yui-->
<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript" src="js/json.js"></script>
<script type="text/javascript" src="hook/hook.js"></script>
<?include("libs/actions.php");?>
<script type="text/javascript" src="js/main.js"></script>







<link rel="stylesheet" type="text/css" href="css/cuerpo.css">
<link rel="stylesheet" type="text/css" href="css/marco.css">
<link rel="stylesheet" type="text/css" href="css/mymenu.css">
<link rel="stylesheet" type="text/css" href="css/detalle.css">
<link rel="stylesheet" type="text/css" href="css/hooks.css">
<link rel="stylesheet" type="text/css" href="css/comun.css">


<style>
#bottom{
	margin:20px 0;
	bottom:0;
	
}
</style>
</head>
<body class="yui-skin-sam" onresize="resize()">
<div id="loading"></div>
<div id="panel_ayuda"></div>
<div id="panel_ayuda_content">
	<div id="panel_ayuda_content_cerrar"><a href="javascript:closeHelp()">Cerrar</a></div>
	
	<div id="panel_ayuda_content_text"></div>
	
</div>
<div id="top">
	<div id="top-bd">
	<div id="titulo-top"></div>
        <div id="enlaces-top">
            <a href="/admin/index.php?logout=1">Desconectarse</a>
        </div>

	</div>
</div>

<div id="izq">
<div id="menu"><?if (!$bloqueado) include('libs/menu.php')?></div>
</div>	
<div style="float:left;width:77%;">
	<div id="dcuerpo">
		<div id="estado"><?=PROJECT_WELCOME?></div>
		<div id="panel_hook"></div>
		<div id="panel_listado">
			<div id="button_nuevo"></div>
			<div id="button_search"></div>
			<div id="label_filtered"></div>
			<div id="listado"></div>
			<div id="dialog_detail">
				<div class="hd">Please enter your information</div>
				<div class="bd">
				<form method="POST" id="dialog_detail_fields" action="libs/actions/insertar.php"></form>
				</div>
			</div>
			<div id="dialog_search">
				<div class="hd">B&uacute;squeda</div>
				<div class="bd">
				<form method="POST" id="dialog_search_fields" action="libs/actions/search.php"></form>
				</div>
			</div>
		</div>
		<div id="panel_detalle">
			<div id="panel_detalle_fields"></div>
			<div>
				<div id="button_aceptar_detalle"></div>
				<div id="button_cancelar_detalle"></div>
			</div>
			
		</div>
	</div>
</div>



<iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
</body>