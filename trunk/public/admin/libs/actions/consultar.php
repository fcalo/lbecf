<?
include("../session.php");
include("../entity.php");
include("../field.php");
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');
function getSize($type){
	$l=getMaxlength($type);
	$r=0;
	if ($l>0) 
		$r=$l*1.5;
	if ($r>50)
		$r=50;
	
	return $r;
}

function getMaxlength($type){
	//Busca parentesis 
	$r=0;
	if($p1=strpos($type,"("))
		$r=substr($type,$p1+1,strpos($type,")")-$p1-1);
	return $r;
	
}

$idEntidad=$_GET["i"];
$parent=$_GET["p"];

$entities=unserialize($_SESSION[constant(CT_ENTITIES.PROJECT)]);

include("../../config/database.php");
if ($_SESSION[CT_LANGUAGES]){
	$idiomas=$db->get_results("select id_idioma, des_idioma, des_corta from t_idiomas",ARRAY_A);
	$numIdiomas=sizeof($idiomas);
}

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
		if ($_GET['val']!=""){
			$primero=true;
			foreach($entity->getFields() as $field){
				if ($field->getIsKey()){
					if (!$primero)
						$sql.=",".$entity->getTable().".".$field->getName()." _key";
					else{
						$sql.=$entity->getTable().".".$field->getName()." _key";
						$primero=false;
					}
					if ($where=="")
						$where.=" where ".$entity->getTable().".".$field->getName()."='".$_GET['val']."'";
					else
						$where.=" and ".$entity->getTable().".".$field->getName()."='".$_GET['val']."'";
					$key=$field->getName();
				}else{
					if (!$primero)
						$sql.=",";
					else{
						$primero=false;
					}
					if ($field->getIsMultilanguage()){
						for ($idi=0;$idi<$numIdiomas;$idi++){
							if ($idi>0)
								$sql.=",";
							$sql.="i".$idiomas[$idi]['id_idioma'].".".$field->getName()." ".$field->getName().$idiomas[$idi]['id_idioma'];
						}
					}else{
						if (strtolower($field->getType())=="date"){
							$sql.="date_format(".$field->getName().", '%d/%m/%Y') ".$field->getName();
						}else
							$sql.=$field->getName();
					}

				}
			}
			
			if($entity->getTableLanguages()!=""){
				/*if ($where=="")
					$where.=" where ";
				else
					$where.=" and ";*/
				$sql.=" from ".$entity->getTable();
				
				for ($idi=0;$idi<$numIdiomas;$idi++){
					/*if ($idi>0)
						$where.=" and ";
					$where.=" ".$entity->getTable().".".$key."= i".$idiomas[$idi]['id_idioma'].".".$key;
					$where.=" and i".$idiomas[$idi]['id_idioma'].".id_idioma=".$idiomas[$idi]['id_idioma'];*/
					$sql.=" left join ".$entity->getTableLanguages()." i".$idiomas[$idi]['id_idioma'];
                                        $sql.=" on ".$entity->getTable().".".$key."= i".$idiomas[$idi]['id_idioma'].".".$key;
                                        $sql.=" and i".$idiomas[$idi]['id_idioma'].".id_idioma=".$idiomas[$idi]['id_idioma'];
				}
				$sql.=$where;
				
			}else
				$sql.=" from ".$entity->getTable().$where;
			
			if ($entity->getByUser()){
				if ($where=="")
					$where.=" where ";
				else
					$where.=" and ";
				$where.=" user='".$_SESSION[constant(USER.PROJECT)]."'";
			}
                        //die($sql);
			$rs=$db->get_row($sql,ARRAY_A);

		}
		$i=0;
		foreach($entity->getFields() as $field){

			$a[$i]['file']="n";
			$a[$i]['entity_ref']="0";
			$a[$i]['multilanguage']="n";
			$a[$i]['ischeck']="n";
			if (strtolower($field->getType())=="char(1)")
				$a[$i]['ischeck']="s";
			$a[$i]['isdate']="n";
			if (strtolower($field->getType())=="date")
				$a[$i]['isdate']="s";
			$a[$i]['istext']="n";
			$a[$i]['iseditor']="n";
			if (strtolower($field->getType())=="text"){
				$a[$i]['istext']="s";	
				if ($field->getFckEditor())
					$a[$i]['iseditor']="s";
			}
			
			$a[$i]['isPass']="n";
			if ($field->getIsPass()){
				$a[$i]['isPass']="s";	
			}
			
			$a[$i]['locked']="n";
			if ($field->getIsLocked()){
				$a[$i]['locked']="s";	
			}
			if ($field->getIsKey()){
				$a[$i]['key']="_key";
				$a[$i]['label']="_key";
				$a[$i]['value']="";
				if ($_GET['val']!="")
					$a[$i]['value']=$rs["_key"];
				
			}
			else{
				if ($field->getIsMultilanguage()){
					for ($idi=0;$idi<$numIdiomas;$idi++){
						if ($idi>0){
							$a[$i]['istext']=$a[$i-$idi]['istext'];
							$a[$i]['iseditor']=$a[$i-$idi]['iseditor'];
						}
							
						$a[$i]['key']=$field->getName()."-".$idiomas[$idi]['id_idioma'];
						$a[$i]['label']=$field->getDescription();
						$a[$i]['value']="";
						
						if ($_GET['val']!="")
							$a[$i]['value']=utf8_encode($rs[$field->getName().$idiomas[$idi]['id_idioma']]);
						$a[$i]['entity_ref']=$field->getEntityRef();
						$a[$i]['multilanguage']="s";
						$a[$i]['maxlength']=getMaxLength($field->getType());
						$a[$i]['size']=getSize($field->getType());
						$i++;
					}
					$i--;
				}else{
					$a[$i]['key']=$field->getName();
					$a[$i]['label']=$field->getDescription();
					$a[$i]['value']="";
					if ($_GET['val']!="")
						$a[$i]['value']=utf8_encode($rs[$field->getName()]);
					$a[$i]['entity_ref']=$field->getEntityRef();
					if ($field->getIsFile())
						$a[$i]['file']="s";
					$a[$i]['maxlength']=getMaxLength($field->getType());
					$a[$i]['size']=getSize($field->getType());
				}
				
					
			}
			if ($field->getRef()!=null){
				if (sizeof($field->getRef()->getValues()==null)){
					$a[$i]['ref_table']=$field->getRef()->getTable();
					$a[$i]['ref_row']=$field->getRef()->getRow();
					$a[$i]['ref_row_depends']=$field->getRef()->getRowDepends();
					$a[$i]['ref_depends']=$field->getRef()->getDepends();
				}
			}
			else
				$a[$i]['ref_table']="";


                        if($field->getName()=="orden" && $a[$i]['value']==""){
                                $r=$db->get_row("select max(coalesce(orden,0)) m from ".$entity->getTable(),ARRAY_A);
                                $a[$i]['value']=$r['m']+1;
                        }

			$i++;
		}
		if ($entity->getHasChilds()){
			$i=0;
			foreach($entity->getEntitiesChilds() as $entityChild){
				$c[$i]['title']=$entityChild->getTitle();
				$c[$i]['id_entidad']=$entityChild->getIdEntity();
				$c[$i]['maintance_type']=$entityChild->getMaintanceType();
				$c[$i]['hook']=$entityChild->getHooked();
				$c[$i]['hookChild']=$entityChild->getHookChild();
				$c[$i]['layout']=$entityChild->getLayout();
				
				if($entityChild->getMaintanceType()==1){
					if($_GET['val']!=""){
						$sql="select ";
						foreach($entityChild->getFields() as $field){
							if ($field->getIsKey())
								$sql.=$field->getName();
						}
						$sql.=" k from ".$entityChild->getTable()." where ".$key."=".$_GET['val'];
						
						$r=$db->get_row($sql,ARRAY_A);
						$c[$i]['key_value']=$r['k'];
					}else{
						$c[$i]['key_value']="";
					}
				}
				$i++;
			}
		}
		$e['idEntity']=$entity->getIdEntity();
		$e['isChild']=$entity->getIsChild();
		$e['help_file']=$entity->getHelpFileDetail();
		$e['layout']=$entity->getLayout();
		$e['parent']['key']=$parentKey;
		$e['parent']['value']=$parent;
		
		$rt[0]=$a;
		$rt[1]=$c;
		$rt[2]=$e;		
		echo json_encode($rt);
	}
}
?>
