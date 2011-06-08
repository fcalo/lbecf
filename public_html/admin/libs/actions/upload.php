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

$idEntidad=$_POST["i"];
$parent=$_POST["p"];
$entities=unserialize($_SESSION[constant(CT_ENTITIES.PROJECT)]);

//definicion
include("../../config/database.php");

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
		}
		
	}
	if ($entity->getIdEntity()==$idEntidad){
		$id=$_POST["_key"];
		if ($id==""){
			$id=$_POST["_key_".$idEntidad];
		}
		if($entity->getByUser())
			$byUser=true;
		else
			$byUser=false;
		
		$listar=($entity->getMaintanceType()==2);
		if(!ensurePath("../../".UPLOAD_DIR.$entity->getTable()."/".$id."/"))
			die("No se pudo crear el dir ".UPLOAD_DIR.$entity->getTable()."/".$id."/");
			
		$primero=true;
		$upload=false;
		
		$sql="update ".$entity->getTable()." ";
		foreach($entity->getFields() as $field){
			if ($field->getIsFile()){
				if ($_FILES[$field->getName()]['name']!=""){
					$upload=true;
					$path="../../".UPLOAD_DIR."/".$entity->getTable()."/".$id."/";
					$file=basename($_FILES[$field->getName()]['name']);
					//$targetPath = "../../".UPLOAD_DIR."/".$entity->getTable()."/".$id."/".basename($_FILES[$field->getName()]['name']);
					$targetPath=$path.$file;
					$savePath = UPLOAD_DIR.$entity->getTable()."/".$id."/".basename($_FILES[$field->getName()]['name']);
					if(@!move_uploaded_file($_FILES[$field->getName()]['tmp_name'], $targetPath)) 
						$fallo=true;
						
					chmod($targetPath,0777);
					if ($primero){
						$sql.="set ".$field->getName()."='".$savePath."'";
						$primero=false;
					}else{
						$sql.=",".$field->getName()."='".$savePath."'";
					}	
					
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
			if ($field->getIsKey()){
				if($where=="")
					$where=" where ".$field->getName()."='".$id."'";
				else
					$where=" and ".$field->getName()."='".$id."'";
			}
		}
		$sql.=$where;
		if(!$fallo && $upload){
			$db->query($sql);
                }
	}
}

if($parent!="")
	$paramParent=",'".$parent."'";

if($byUser)
	$p=$_SESSION[constant(USER.PROJECT)];
else
	$p='1';



if($fallo){
?>  
	<script>alert("error adjuntando.<?=$sql.'--'.mysql_error().'__'.$msg?>")</script>	     
<?}else{
	if ($listar){?>
	<script>parent.listar('<?=$idEntidad?>'<?=$paramParent?>)</script>
	<?}else{?>
	<script>parent.consultar('<?=$idEntidad?>','<?=$p?>');</script>
<?}}?>