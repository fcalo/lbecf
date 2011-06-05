<?
include("../session.php");
include("../entity.php");
include("../field.php");
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');

if (isset($_GET["i"])){
	if (strstr($_GET["i"],".")){
		$a=explode(".",$_GET["i"]);
		$idEntidad=$a[0];
		$numeral=$a[1]-1;
	}else{
		$idEntidad=$_GET["i"];
		$numeral=0;
	}
	$entities=unserialize($_SESSION[constant(CT_ENTITIES.PROJECT)]);
	
	include("../../config/database.php");
	$sql="select ";
	foreach($entities as $entity){
		//busca en las hijas si tiene
		if ($entity->getIdEntity()!=$idEntidad && $entity->getHasChilds()){
			$find=false;
			foreach($entity->getEntitiesChilds() as $entityChild){
				if ($entityChild->getIdEntity()==$idEntidad){
					$tempEntity=$entityChild;
					$find=true;
				}
				
				if ($find){
					foreach($entity->getFields() as $field)
					{
						if ($field->getIsKey())
							$parentKey=$field->getName();
					}
						
					$entity=$tempEntity;
				}
				$find=false;
			}
			
		}
		if ($entity->getIdEntity()==$idEntidad){
			
			
			$primero=true;
			$hasLanguages=false;
			$cargado=false;
			foreach($entity->getFields() as $field){
				if(!$cargado){
					if ($field->getIsKey()){
						$key=$field->getName();
						if (!$primero)
							$sql.=",".$entity->getTable().".".$field->getName()." _key";
						else{
							$sql.=$entity->getTable().".".$field->getName()." _key";
							$primero=false;
						}
					}
					if ($field->getIsComboDescription() && $numeral==0){
						
						if ($field->getRef()!=null && $field->getRef()->getValues()!=null){
							$values=$field->getRef()->getValues();
							$a[0]=$_GET['val'];
							$a[1]=$_GET['combo'];
							for($j=0;$j<sizeof($values);$j++){
			        			$v[$j]["_key"]=$j;
			        			$v[$j]["value"]=$values[$j];
			        		}
							$a[2]=$v;
							$rt=json_encode($a);
							$cargado=true;
						}else{
							if ($field->getIsMultilanguage()){
								$tab=$entity->getTableLanguages();
								$hasLanguages=true;
							}else
								$tab=$entity->getTable();
							if (!$primero)
								$sql.=",".$tab.".".$field->getName()." value";
							else{
								$sql.=$tab.".".$field->getName()." value";
								$primero=false;
							}
							$order=$field->getName();
						}
					}else{
						if($field->getIsComboDescription())
							$numeral--;
					}
				}
				
			}
			if(!$cargado){
				$sql.=" from ".$entity->getTable();
				$where=false;
				if($hasLanguages){
					$sql.=", ".$entity->getTableLanguages();
					$sql.=" where ".$entity->getTable().".".$key."= ".$entity->getTableLanguages().".".$key;
					$sql.=" and ".$entity->getTableLanguages().".id_idioma=1";
					$where=true;
				}
				if ($entity->getByUser()){
					if ($where)
						$sql.=" and ";
					else
						$sql.=" where ";
					$sql.="  user='".$_SESSION[constant(USER.PROJECT)]."'";
				}
				$sql.=" order by ".$order;
				
				$rs=$db->get_results($sql,ARRAY_A);
				$count=sizeof($rs);
				for($i=0;$i<$count;$i++){
					$count2=sizeof($rs[$i]);
					foreach($rs[$i] as $k=>$v){
						$rs[$i][$k]=utf8_encode($rs[$i][$k]);
					}
				}
				
				$a[0]=$_GET['val'];
				$a[1]=$_GET['combo'];
				$a[2]=$rs;
				$rt=json_encode($a);
			}
		}
		
	}
	//'{"recordsReturned":2,"totalRecords":2,"startIndex":0,"sort":"des_cosa","dir":"asc","pageSize":25,"records":[{"des_cosa":"dddd"},{"des_cosa":"dds"},{"des_cosa":"2"}]}';	
	echo $rt;
}else{
	include("../../config/database.php");
	if ($_GET['depends']!="undefined")
		$sql="select distinct ".$_GET['row']." value, ".$_GET['row']." _key from ".$_GET['t']." where ".$_GET['depends']."='".$_GET['dependsValue']."' order by ".$_GET['row'];
	else
		$sql="select distinct ".$_GET['row']." value, ".$_GET['row']." _key from ".$_GET['t']." order by ".$_GET['row'];

	$rs=$db->get_results($sql,ARRAY_A);
	$count=sizeof($rs);
	for($i=0;$i<$count;$i++){
		$count2=sizeof($rs[$i]);
		foreach($rs[$i] as $k=>$v){
			$rs[$i][$k]=utf8_encode($rs[$i][$k]);
		}
	}
	//By Table
	$a[0]=$_GET['val'];
	$a[1]=$_GET['combo'];
	$a[2]=$rs;
	$rt.=json_encode($a);
	echo $rt;
}



?>
