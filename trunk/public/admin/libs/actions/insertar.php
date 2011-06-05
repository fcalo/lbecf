<?
include("../session.php");
include("../entity.php");
include("../field.php");
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
		if($entity->getMaintanceType()>=2)
			$listar="S";
		else{
			$listar="N";
		}
		if($entity->getLayout()==1)
			$listar="N";
			
			
		$bIdiomas=$entity->getTableLanguages()!="";
		$sql="insert into ".$entity->getTable()." (";
		if ($_SESSION[CT_LANGUAGES]){
			for($idi=0;$idi<$numIdiomas;$idi++){
				$sqlIdiomas[$idi]="insert into ".$entity->getTableLanguages()." (";
				$primeroIdioma[$idi]=true;
			}
		}
		if ($parentKey!=""){
			$sql.=$parentKey;
			$valores.="'".$_POST[$parentKey]."'";
			$primero=false;
		}
			
		foreach($entity->getFields() as $field){
			if (!$field->getIsAutoIncrement()){
				if ($field->getIsMultilanguage()){
					for($idi=0;$idi<$numIdiomas;$idi++){
						//Se asegura que al escaparlo quepa, problemas con el ruso
						$a=explode("(",$field->getType());
						$a=explode(")",$a[1]);
						$size=$a[0];
						if(is_numeric($size) && $size>0 && strlen($_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']])>$size)
							$db->query("alter table ".$entity->getTableLanguages()." modify column ".$field->getName()." text");
						if (!$primeroIdioma[$idi]){
							$sqlIdiomas[$idi].=",".$field->getName();
							$valoresIdiomas[$idi].=",'".$_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']]."'";
						}else{
							$sqlIdiomas[$idi].="id_idioma,".$keyIdiomas.",".$field->getName();
							$valoresIdiomas[$idi].=$idiomas[$idi]['id_idioma'].",'@@key@@','".$_POST[$field->getName()."-".$idiomas[$idi]['id_idioma']]."'";
							$primeroIdioma[$idi]=false;
						}
					}
				}else{
					if (!$field->getIsNull() && trim($_POST[$field->getName()])=="")
						die("KO#".$field->getName());
					if (!$primero){
						$sql.=",".$field->getName();
						/*if (strtolower($field->getType())=="char(1)")
							$valores.=",'".(($_POST[$field->getName()]!="")?'S':'N')."'";
						else*/
						if ($field->getIsPass())
							$valores.=",'".sha1($_POST[$field->getName()])."'";
						else
							if (strtolower($field->getType())=="date"){
								if($_POST[$field->getName()]!=""){
									$f=explode("/",$_POST[$field->getName()]);
									$valores.=",'".$f[2]."-".$f[1]."-".$f[0]."'";
								}
								else
									$valores.=",null";
							}else
								if($_POST[$field->getName()]!="")
									$valores.=",'".str_replace("|||||",chr(13).chr(10),$_POST[$field->getName()])."'";
								else
									$valores.=",null";
					}else{
						$sql.=$field->getName();
						if ($field->getIsPass())
							$valores.="'".sha1($_POST[$field->getName()])."'";
						else
							if (strtolower($field->getType())=="date"){
								if($_POST[$field->getName()]!=""){
									$f=explode("/",$_POST[$field->getName()]);
									$valores.="'".$f[2]."-".$f[1]."-".$f[0]."'";
								}
								else
									$valores.="null";
							}else
								if($_POST[$field->getName()]!="")
									$valores.="'".str_replace("|||||",chr(13).chr(10),$_POST[$field->getName()])."'";
								else
									$valores.="null";
						$primero=false;
					}
				}
			}else
				$keyIdiomas=$field->getName();
			
			
		}
		if ($entity->getByUser() && !strstr($sql,"user")){
			$sql.=",user";
			$valores.=",'".$_SESSION[constant(USER.PROJECT)]."'";
		}
		$sql.=") values (".$valores.")";
		
                        
                        if ($db->query($sql)){
                            $id=$db->insert_id;
                       echo "OK#".$db->insert_id."#".$idEntidad."#".$_POST[$parentKey]."#".$listar."#".$id."#".$_POST["_key"];
                            
                        
		}
		
		/*EXTRA*/
		/*if($idEntidad=10){
			$sql="update t_equipos set url_equipo=".amigableMySql("nom_equipo")." where id_equipo=".$db->insert_id;
			$db->query($sql);
		}
		/*FIN EXTRA*/
		$id=$db->insert_id;
		
		if ($bIdiomas){
			for($idi=0;$idi<$numIdiomas;$idi++){
				//echo $sqlIdiomas[$idi].") values (".str_replace("@@key@@",$id,$valoresIdiomas[$idi]).")---";
				$db->query($sqlIdiomas[$idi].") values (".str_replace("@@key@@",$id,$valoresIdiomas[$idi]).")");
			}
		}
		
		
		
		
	}
}


?>
