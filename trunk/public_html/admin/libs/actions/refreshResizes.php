<?php
include("../session.php");
include("../entity.php");
include("../field.php");
include("../util/paths.php");
include("../util/images.php");
include("../../config/config.php");
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');

$entities=unserialize($_SESSION[constant(CT_ENTITIES.PROJECT)]);



function fields($entity){
	foreach($entity->getFields() as $field){
		if($field->getIsKey())
			$key=$field->getName();
		
		if ($field->getIsFile()){
			include("../../config/database.php");
			$sql="select ".$key." id,".$field->getName()." file from ".$entity->getTable();
			$rs=$db->get_results($sql,ARRAY_A);
			$count=sizeof($rs);
			for($i=0;$i<$count;$i++){
				$path="../../".UPLOAD_DIR.$entity->getTable()."/".$rs[$i]['id']."/";
				$file=basename($rs[$i]['file']);
				
				echo "...".$path.$file."<br>";
				if (is_array($field->getSizes())){
					foreach($field->getSizes() as $size){
						
						$width=$size->getWidth();
						$height=$size->getHeight();
						
						$pathRes=$path.$width."x".$height."/";
						
						if (!ensurePath($pathRes)){
							$fallo=true;
						}
						
						if($width=="")
							$width=$height*10;
							
						if($height=="")
							$height=$width*10;
						
						
						if(!resizeImage($path, $pathRes, $file, $width,$height)){
							$msg="RESIZE";
							$fallo=true;
						}
					}
				}
			}
		}
	}
}

foreach($entities as $entity){
	if ($entity->getHasChilds()){
		foreach($entity->getEntitiesChilds() as $entityChild){
			fields($entityChild);
		}
	}
	fields($entity);
}

if(!$fallo)
	echo "OK";
?>