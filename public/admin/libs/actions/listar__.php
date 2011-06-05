<?
include("../session.php");
include("../entity.php");
include("../field.php");
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // disable IE caching
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header('Content-Type: text/html; charset=UTF-8');

$idEntidad=$_GET["i"];
$parent=$_GET["p"];
$entities=unserialize($_SESSION[constant(CT_ENTITIES.PROJECT)]);

//definicion
if ($_GET["def"]=="1"){
	
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
					$parentTable=$entity->getTable();	
					foreach($entity->getFields() as $field)
					if ($field->getIsKey())
						$parentKey=$field->getName();	
					$entity=$tempEntity;
				}
				$find=false;
			}
			
		}
		
		if ($entity->getIdEntity()==$idEntidad){
			$i=0;
			foreach($entity->getFields() as $field){
				if ($field->getInList()){
					$arr=explode(".",$field->getEntityRef());
					if ($field->getEntityRef()>0 && $arr[0]!=$idEntidad){
						foreach($entities as $entityRef){
							if ($entityRef->getIdEntity()!=$field->getEntityRef() && $entityRef->getIdEntity()!=$idEntidad && $entityRef->getHasChilds()){
								foreach($entityRef->getEntitiesChilds() as $entityChild){
									foreach($entityChild->getFields() as $fieldRef){
										if ($fieldRef->getIsComboDescription()){
											$a[$i]['key']=$field->getName();
										}
									}
								}
							}
							
							
							if ($entityRef->getIdEntity()==$field->getEntityRef()){
								$primero=true;
								foreach($entityRef->getFields() as $fieldRef){
									if ($fieldRef->getIsComboDescription()){
										$a[$i]['key']=$fieldRef->getName();
									}
								}
							}
						}
						
					}else{
						$a[$i]['key']=$field->getName();
					}
					$a[$i]['label']=$field->getDescription();
					$a[$i]['isFile']=$field->getIsFile();
					$a[$i]['isPass']=$field->getIsPass();
					$i++;
				}
				if ($field->getIsKey()){
					$a[$i]['key']="_key";
					$a[$i]['label']="_key";
					$a[$i]['isFile']="N";
					$i++;
				}
				
			}
			$a[$i]['key']="_entity";
			$a[$i]['label']="_entity";
			$e['isChild']=$entity->getIsChild();
			$e['isSearchable']=$entity->getSearchable();
			$e['idEntity']=$idEntidad;
			$e['buttonNew']=$entity->getMaintanceType()<>3;
			$e['help_file']=$entity->getHelpFileList();
			$e['parent']['key']=$parentKey;
			$e['parent']['value']=$parent;
			
			//filtered
			if ($_SESSION["sea".$idEntidad]!=""){
				$sea=$_SESSION["sea".$idEntidad];
				foreach($sea  as $k=>$v){
					if ($k!="i" && $v!=""){
						if ($filtered!="")
							$filtered.=",";
						$filtered.=$k;
					}
				}
				if ($filtered!="")
					$filtered="Filtrado por ".$filtered;
			}
			$e['filtered']=$filtered;
			
			
			if ($entity->getMaxCount()>0){
				$sqlc="select count(*) c from ".$entity->getTable()." ";
				if ($parentKey!="")
					$sqlc.="where ".$parentKey."='".$parent."'";
				if ($entity->getByUser()){
					if ($parentKey=="")
						$sqlc.=" where ";
					else
						$sqlc.=" and ";
					$sqlc.=" user='".$_SESSION[constant(USER.PROJECT)]."'";
				}
				include("../../config/database.php");
				$r=$db->get_row($sqlc,ARRAY_A);
				$count=$r['c'];
				if ($count>=$entity->getMaxCount())
					$e['buttonNew']=false;
			}
				
		}
	}
	$rt[0]=$a;
	$rt[1]=$e;
	echo json_encode($rt);
}
else
{
	include("../../config/database.php");
	$sql="select '".$idEntidad."' _entity, ";
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
					$parentTable=$entity->getTable();
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
			foreach($entity->getFields() as $field){
				if ($field->getInList()){
					//Comprueba si está filtrado por la búsqueda
					if ($_SESSION["sea".$idEntidad]["sea_".$field->getName()]!=""){
						if($whereSearch!="")
							$whereSearch.=" and ";
						$whereSearch=$field->getName()." like '%".$_SESSION["sea".$idEntidad]["sea_".$field->getName()]."%'";
					}
					
				
					if (!$primero){
						$sql.=",";
						
					}
					$primero=false;
					$arr=explode(".",$field->getEntityRef());
					$entidadEncontrada=false;
					if ($field->getEntityRef()>0 && $arr[0]!=$idEntidad){
						foreach($entities as $entityRef){
							if ($entityRef->getIdEntity()==$field->getEntityRef()){
								$entidadEncontrada=true;
								foreach($entityRef->getFields() as $fieldRef){
									if ($fieldRef->getIsKey()){
										$keyCombo=$fieldRef->getName();
									}
									if ($fieldRef->getIsComboDescription()){
										
										if ($fieldRef->getIsMultilanguage()){
											$sql.=$entityRef->getTableLanguages().".".$fieldRef->getName();
											$tables.=",".$entityRef->getTableLanguages();
											if ($where=="")
												$where.=" where ";
											else
												$where.=" and ";
											$where.=$entity->getTable().".".$field->getName()."=".$entityRef->getTableLanguages().".".$field->getName();
											$where.=" and ".$entityRef->getTableLanguages().".id_idioma='1' ";
										}else{
											//$sql.=$entityRef->getTable().".".$fieldRef->getName().",".$entityRef->getTable().".".$field->getName();
											if($fieldRef->getName()==$field->getName())
												$sql.=$entityRef->getTable().".".$fieldRef->getName()." ".$field->getName();
											else
												$sql.=$entityRef->getTable().".".$fieldRef->getName().", ".$entityRef->getTable().".".$fieldRef->getName()." ".$field->getName();
											$tables.=",".$entityRef->getTable();
											if ($where=="")
												$where.=" where ";
											else
												$where.=" and ";
											$where.=$entity->getTable().".".$field->getName()."=".$entityRef->getTable().".".$keyCombo;
											if ($entityRef->getByUser()){
												$where.=" and ";
												$where.= $entityRef->getTable().".user='".$_SESSION[constant(USER.PROJECT)]."'";
											}
										}
									}
								}
							}
						}
						
						if (!$entidadEncontrada) {
							foreach($entities as $entityRef){
								if($entityRef->getHasChilds()){
									foreach($entityRef->getEntitiesChilds() as $entityChild){
										foreach($entityChild->getFields() as $fieldRef){
											if ($fieldRef->getIsKey()){
												$keyCombo=$fieldRef->getName();
											}
											if ($fieldRef->getIsComboDescription()){
												if ($fieldRef->getIsMultilanguage()){
													$sql.=$entityChild->getTableLanguages().".".$fieldRef->getName()." ".$field->getName();
													$tables.=",".$entityChild->getTableLanguages();
													if ($where=="")
														$where.=" where ";
													else
														$where.=" and ";
													$where.=$entity->getTable().".".$field->getName()."=".$entityChild->getTableLanguages().".".$field->getName();
													$where.=" and ".$entityChild->getTableLanguages().".id_idioma='1' ";
												}else{
													$sql.=$entityChild->getTable().".".$fieldRef->getName()." ".$field->getName();;
													$tables.=",".$entityChild->getTable();
													if ($where=="")
														$where.=" where ";
													else
														$where.=" and ";
													$where.=$entity->getTable().".".$field->getName()."=".$entityChild->getTable().".".$keyCombo;
												}
											}
										}
									}
								}
							}
						}
						
					}else{
						if ($field->getRef()!=null && $field->getRef()->getValues()!=null){
							$values=$field->getRef()->getValues();
							for($j=0;$j<sizeof($values);$j++){
								$sql.="if(".$field->getName()."=".$j.",'".$values[$j]."',";
			        		}
			        		$sql.="''";
			        		for($j=0;$j<sizeof($values);$j++){
								$sql.=")";
			        		}
			        		$sql.=" ".$field->getName();
			        		
						}else							if(strtolower($field->getType())=="date")								$sql.="date_format(".$field->getName().",'%d/%m/%Y') ".$field->getName();							else								$sql.=$field->getName();
					}
					
						
				}
				if ($field->getIsKey()){
					$key=$field->getName();
					if (!$primero)
						$sql.=",".$entity->getTable().".".$field->getName()." _key";
					else{
						$sql.=$entity->getTable().".".$field->getName()." _key";
						$primero=false;
					}
				}
			}
			
			if ($parentTable!=""){
				//$tables.=",".$parentTable;
				if ($where=="")
					$where.=" where ";
				else
					$where.=" and ";
				$where.=$entity->getTable().".".$parentKey."='".$parent."'";
				$whereParent.=" where ".$entity->getTable().".".$parentKey."='".$parent."'";
				
			}
			
			if ($entity->getByUser()){				if ($where=="")					$where.=" where ";				else					$where.=" and ";				if ($whereParent=="")					$whereParent.=" where ";				else					$whereParent.=" and ";									$where.= $entity->getTable().".user='".$_SESSION[constant(USER.PROJECT)]."'";				$whereParent.= $entity->getTable().".user='".$_SESSION[constant(USER.PROJECT)]."'";			}			if ($entity->getTable()=="t_login"){				if ($where=="")					$where.=" where ";				else					$where.=" and ";				if ($whereParent=="")					$whereParent.=" where ";				else					$whereParent.=" and ";				$where.="user<>'admin'";				$whereParent.="user<>'admin'";			}
			if($entity->getTableLanguages()!=""){
				$tables.=",".$entity->getTableLanguages()." i";
				if ($where=="")
					$where.=" where ";
				else
					$where.=" and ";
				$where.=" ".$entity->getTable().".".$key."= i.".$key;
				$where.=" and i.id_idioma=(select min(id_idioma) from t_idiomas)";
				
			}
			if($whereSearch!=""){
				if ($where=="")
					$where=" where ".$whereSearch;
				else
					$where.=" and ".$whereSearch;
			}
			
			$sql.=" from ".$entity->getTable().$tables.$where." order by ".$_GET['sort']." ".$_GET['dir']." LIMIT ".$_GET['startIndex'].",".$_GET['results'];

			$rs=$db->get_results($sql,ARRAY_A);
			
			$count=sizeof($rs);
			for($i=0;$i<$count;$i++){
				$count2=sizeof($rs[$i]);
				foreach($rs[$i] as $k=>$v){
					$rs[$i][$k]=htmlentities($rs[$i][$k]);
				}
			}
			$count=$db->get_row("select count(*) c from ".$entity->getTable().$whereParent);
			//if ($count->c=="")
			//	echo $sql."select count(*) c from ".$entity->getTable().$where;
			
			$c=$count->c;
			if ($c==""){
				var_dump($count);
				$c=0;
			}
			
			$rt='{"recordsReturned":'.$_GET['results'].',"totalRecords":'.$c.',"startIndex":'.$_GET['startIndex'].',"sort":"'.$_GET['sort'].'","dir":"'.$_GET['dir'].'","pageSize":'.$_GET['results'].',"records":';
			$rt.=json_encode($rs);
			$rt.="}";
			
		}
	}
	//'{"recordsReturned":2,"totalRecords":2,"startIndex":0,"sort":"des_cosa","dir":"asc","pageSize":25,"records":[{"des_cosa":"dddd"},{"des_cosa":"dds"},{"des_cosa":"2"}]}';	
	echo $rt;
}

?>
