<?
include("../session.php");
include("../entity.php");
include("../field.php");
include("../util/paths.php");
include("../../config/config.php");
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');

$idEntidad=$_GET["i"];
$parent=$_GET["p"];
$entities=unserialize($_SESSION[constant(CT_ENTITIES.PROJECT)]);

//definicion
include("../../config/database.php");

//Busca si aguna entidad depende de ella
foreach($entities as $entity){
	//busca en las hijas si tiene
	if ($entity->getIdEntity()!=$idEntidad && $entity->getHasChilds()){
		foreach($entity->getEntitiesChilds() as $entityChild){
			foreach($entityChild->getFields() as $field){
				if ($field->getEntityRef()==$idEntidad){
					//Comprueba si hay dependencias de datos
					$sql="select count(*) c from ".$entityChild->getTable()." where ".$field->getName()."='".$_GET["_key"]."'";
					$rs=$db->get_row($sql,ARRAY_A);
					if ($rs['c']>0){
						$dependencias=true;
						echo "No se puede borrar. Hay dependecias con ".$entityChild->getTitle()."\n";
					}
						
				}
					
			}
		}
		
	}
	foreach($entity->getFields() as $field){
		if ($field->getEntityRef()==$idEntidad){
			//Comprueba si hay dependencias de datos
			$sql="select count(*) c from ".$entity->getTable()." where ".$field->getName()."='".$_GET["_key"]."'";
			$rs=$db->get_row($sql,ARRAY_A);
			if ($rs['c']>0){
				$dependencias=true;
				echo "No se puede borrar. Hay dependecias con ".$entity->getTitle()."\n";
			}
				
		}
			
	}
}


if(!$dependencias){
	foreach($entities as $entity){
		//busca en las hijas si tiene
		if ($entity->getIdEntity()!=$idEntidad && $entity->getHasChilds()){
			$find=false;
			foreach($entity->getEntitiesChilds() as $entityChild){
				if ($entityChild->getIdEntity()==$idEntidad){
					$tempEntity=$entityChild;
					$find=true;
				}
				if ($find)
					$entity=$tempEntity;
				$find=false;
			}
			
		}
		if ($entity->getIdEntity()==$idEntidad){
			if ($entity->getTableLanguages()!=""){
				$primero=true;
				$sql="delete  from ".$entity->getTableLanguages()." ";
				foreach($entity->getFields() as $field){
					if ($field->getIsKey()){
						if($where=="")
							$where=" where ".$field->getName()."='".$_GET["_key"]."'";
						else
							$where=" and ".$field->getName()."='".$_GET["_key"]."'";
					}
				}
				$sql.=$where;
				$db->query($sql);
			}
			$primero=true;
			$where="";
			$sql="delete  from ".$entity->getTable()." ";
			foreach($entity->getFields() as $field){
				if ($field->getIsKey()){
					if($where=="")
						$where=" where ".$field->getName()."='".$_GET["_key"]."'";
					else
						$where=" and ".$field->getName()."='".$_GET["_key"]."'";
					
					$targetPath = "../../".UPLOAD_DIR.$entity->getTable()."/".$_GET["_key"]."/";
				}
			}
			
			$sql.=$where;
			if($db->query($sql)){
				//borra adjuntos si hubiera
				if (file_exists($targetPath))
					rmdirr($targetPath);
				if ($entity->getHasChilds()){
					foreach($entity->getEntitiesChilds() as $entityChild){
						$sql="delete  from ".$entityChild->getTable()." ";
						$db->query($sql.$where);
					}
					
				}
				echo "OK#".$idEntidad."#".$parent;
			}
			else
				echo $sql;
		}
	}
}