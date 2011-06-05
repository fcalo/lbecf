<?
include("../session.php");
include("../entity.php");
include("../field.php");
include("../util/encode.php");
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');


$idEntidad=$_POST["i"];
$entities=unserialize($_SESSION[constant(CT_ENTITIES.PROJECT)]);

//definicion
include("../../config/database.php");

if ($_SESSION[CT_LANGUAGES]){
	$idiomas=$db->get_results("select id_idioma, des_idioma, des_corta from t_idiomas",ARRAY_A);
	$numIdiomas=sizeof($idiomas);
}

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
				if ($field->getIsKey())
					$parentKey=$field->getName();	
				$entity=$tempEntity;
			}
			$find=false;
		}
		
	}
	if ($entity->getIdEntity()==$idEntidad){
		$primero=true;
		$bIdiomas=$entity->getTableLanguages()!="";
		
		if($entity->getMaintanceType()>=2)
			$listar="S";
		else{
			$listar="N";
			if($entity->getByUser())
				$byUser="S";
			else
				$byUser="N";
		}
		
		if($entity->getLayout()==1)
			$listar="N";
			
		$sql="update ".$entity->getTable()." ";
		if ($bIdiomas)
			for($idi=0;$idi<$numIdiomas;$idi++){
				$sqlIdiomas[$idi]="insert into ".$entity->getTableLanguages()." (@campos) values (@valores) on duplicate key update ";
				$primeroIdiomas[$idi]=true;
			}
		foreach($entity->getFields() as $field){
                    if ($field->getIsKey()){
                        $keyField=$field->getName();
                    }
                }
		foreach($entity->getFields() as $field){
			if (!$field->getIsKey()){
				if ($field->getIsMultilanguage()){
					for($idi=0;$idi<$numIdiomas;$idi++){
						//Se asegura que al escaparlo quepa, problemas con el ruso
						$a=explode("(",$field->getType());
						$a=explode(")",$a[1]);
						$size=$a[0];
						if(is_numeric($size) && $size>0 && strlen($_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']])>$size)
							$db->query("alter table ".$entity->getTableLanguages()." modify column ".$field->getName()." text");
						if ($primeroIdiomas[$idi]){
							$sqlIdiomas[$idi].=" ".$field->getName()."='".($_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']])."'";
                                                        $sqlIdiomas[$idi]=str_replace("@campos",$field->getName().", ".$keyField." @campos",$sqlIdiomas[$idi]);
                                                        if ($entity->getIsChild())
                                                            $keyValue=$_POST["_key_".$idEntidad];
                                                        else
                                                            $keyValue=$_POST["_key"];
                                                        $sqlIdiomas[$idi]=str_replace("@valores","'".($_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']])."',{$keyValue}@valores",$sqlIdiomas[$idi]);
							$primeroIdiomas[$idi]=false;
						}else{
							$sqlIdiomas[$idi].=",".$field->getName()."='".($_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']])."'";
                                                        $sqlIdiomas[$idi]=str_replace("@campos",",".$field->getName()."@campos",$sqlIdiomas[$idi]);
                                                        $sqlIdiomas[$idi]=str_replace("@valores",",'".($_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']])."'@valores",$sqlIdiomas[$idi]);
						}	
					}
				}else{
					if (!$field->getIsNull() && trim($_POST[$field->getName()])=="")
						die("KO#".$field->getName());
					if (!$field->getIsFile() || $_POST[$field->getName()]!=""){
						if ($primero){
							if ($field->getIsPass())
								$sql.="set ".$field->getName()."='".sha1($_POST[$field->getName()])."'";
							else
								if (strtolower($field->getType())=="date"){
									if($_POST[$field->getName()]!=""){
										$f=explode("/",$_POST[$field->getName()]);
										$sql.="set ".$field->getName()."='".$db->escape($f[2]."-".$f[1]."-".$f[0])."'";
									}else
										$sql.="set ".$field->getName()."=null";
								}else
									if($_POST[$field->getName()]!="")
										$sql.="set ".$field->getName()."='".$db->escape(str_replace("|||||",chr(13).chr(10),$_POST[$field->getName()]))."'";
									else
										$sql.="set ".$field->getName()."=null";
							$primero=false;
						}else{
							if ($field->getIsPass())
								$sql.=",".$field->getName()."='".sha1($_POST[$field->getName()])."'";
							else
								if (strtolower($field->getType())=="date"){
									if($_POST[$field->getName()]!=""){
										$f=explode("/",$_POST[$field->getName()]);
										$sql.=",".$field->getName()."='".$db->escape($f[2]."-".$f[1]."-".$f[0])."'";
									}else
										$sql.=",".$field->getName()."=null";
								}else
									if($_POST[$field->getName()]!="")
										$sql.=",".$field->getName()."='".$db->escape(str_replace("|||||",chr(13).chr(10),$_POST[$field->getName()]))."'";
									else
										$sql.=", ".$field->getName()."=null";
						}	
					}
				}
			}
			else{
				if ($entity->getIsChild())
					$where=" where ".$field->getName()."='".$_POST["_key_".$idEntidad]."'";
				else{
					if($where=="")
						$where=" where ".$field->getName()."='".$_POST["_key"]."'";
					else
						$where=" and ".$field->getName()."='".$_POST["_key"]."'";
				}
			}
			
		}
		if($bIdiomas){
			for($idi=0;$idi<$numIdiomas;$idi++){
				//$sqlIdiomas[$idi].=$where." and id_idioma=".$idiomas[$idi]['id_idioma'];
                                $sqlIdiomas[$idi]=str_replace("@campos",",id_idioma",$sqlIdiomas[$idi]);
                                $sqlIdiomas[$idi]=str_replace("@valores",",".$idiomas[$idi]['id_idioma'],$sqlIdiomas[$idi]);
                                //die($sqlIdiomas[$idi]);
				$db->query($sqlIdiomas[$idi]);
			}
		}
		
		
		$sql.=$where;
		//echo $sql;

		if (strpos($sql,"set"))
			$db->query($sql);
		echo $db->last_error;
                echo "OK#".$idEntidad."#".$_POST[$parentKey]."#".$listar."#".$byUser."#".$_POST["_key_".$idEntidad]."#".$_POST["_key"];
                

                
	}
}