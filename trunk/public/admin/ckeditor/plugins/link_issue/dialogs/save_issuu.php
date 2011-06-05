<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$server="http://".$_SERVER['HTTP_HOST'];
if(strpos($server,".dev")>0){
	DEFINE ("CT_SERVIDOR" , "192.168.0.1");
	DEFINE ("CT_USER" ,"root") ;
	DEFINE ("CT_PASS" ,"r00t") ;
	DEFINE ("CT_DB" , "bd_alcantara");
}else{
	DEFINE ("CT_SERVIDOR" , "llde250.servidoresdns.net");
	DEFINE ("CT_USER" ,"qgc341") ;
	DEFINE ("CT_PASS" ,"Alc4nt4r410") ;
	DEFINE ("CT_DB" , "qgc341");
}

include_once "../../../../ezsql/shared/ez_sql_core.php";
include_once "../../../../ezsql/mysql/ez_sql_mysql.php";


$db = new ezSQL_mysql(CT_USER,CT_PASS,CT_DB,CT_SERVIDOR);

$sql=" INSERT IGNORE INTO t_issuu (md5, text) VALUES";
$sql.=" ('".$_POST['k']."','".$_POST['t']."')";

$db->query($sql);
?>
