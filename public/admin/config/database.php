<?

$server="http://".$_SERVER['HTTP_HOST'];
if(strpos($server,".dev")>0){
	DEFINE ("CT_SERVIDOR" , "192.168.1.2");
        DEFINE ("CT_USER" ,"root") ;
	DEFINE ("CT_PASS" ,"r00t") ;
	DEFINE ("CT_DB" , "bd_lbe");
	
}else{
	DEFINE ("CT_SERVIDOR" , "localhost");
	DEFINE ("CT_USER" ,"lovetuning") ;
	DEFINE ("CT_PASS" ,"tun1ng11") ;
	DEFINE ("CT_DB" , "lovetuning");
}

/***************************************/
@include_once "./ezsql/shared/ez_sql_core.php";
@include_once "./ezsql/mysql/ez_sql_mysql.php";
@include_once "../ezsql/shared/ez_sql_core.php";
@include_once "../ezsql/mysql/ez_sql_mysql.php";
@include_once "../../ezsql/shared/ez_sql_core.php";
@include_once "../../ezsql/mysql/ez_sql_mysql.php";
@include_once "./admin/ezsql/shared/ez_sql_core.php";
@include_once "./admin/ezsql/mysql/ez_sql_mysql.php";
@include_once "../admin/ezsql/shared/ez_sql_core.php";
@include_once "../admin/ezsql/mysql/ez_sql_mysql.php";


$db = new ezSQL_mysql(CT_USER,CT_PASS,CT_DB,CT_SERVIDOR);

?>