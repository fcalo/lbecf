<?php
include('./config/config.php');
include('./config/database.php');


if (!PRODUCTION){
	$tables=$db->get_results("SHOW TABLES",ARRAY_N);
	$idiomas=false;
	
	foreach ($entities as $entity){
		if ($entity->getTable()!=""){
			$count=sizeof($tables);
			$existe=false;
			for($i=0;$i<$count;$i++){
				if ($tables[$i][0]==$entity->getTable())
					$existe=true;
			}
			if (!$existe)
			{
				$key="";
				$sql="create table `".$entity->getTable()."`(";
				$sqlIdiomas="create table `".$entity->getTableLanguages()."`( id_idioma INT(11) NOT NULL,";
				foreach ($entity->getFields() as $field){
					$campo="";
					$campo.="`".$field->getName()."` ";
					$campo.=$field->getType()." ";
					if ($field->getIsNull())
						$campo.="NULL ";	
					else
						$campo.="NOT NULL ";	
					if ($field->getIsAutoIncrement())
						$campo.="auto_increment ";	
					if ($field->getIsKey()){
						if ($key!="")
							$key.=",";
						$key.="`".$field->getName()."`";	
					}
					$campo.=",";
					
					if (!$field->getIsMultilanguage()){
						$sql.=$campo;
						if ($field->getIsKey())
							$sqlIdiomas.=str_replace("auto_increment","",$campo);
					}else{
						$sqlIdiomas.=$campo;
					}
					
				}
				if ($entity->getByUser() && $entity->getMaintanceType()!=1)
					$sql.="user varchar(50) NOT NULL,";
				$sql.="primary key (".$key.")) CHARSET=utf8 COLLATE=utf8_unicode_ci";
				$sqlIdiomas.="primary key (id_idioma,".$key.")) CHARSET=utf8 COLLATE=utf8_unicode_ci";
				$db->query($sql);
				if($entity->getTableLanguages()!=""){
					$idiomas=true;
					$db->query($sqlIdiomas);
				}
			}
			if ($entity->getByUser() && $entity->getMaintanceType()>0){
				$sql="delete from ".$entity->getTable()." where not exists (select 1 from t_login where user=".$entity->getTable().".user)";
				$db->query($sql);
			}
			
			if ($entity->getMaintanceType()==1){
				if ($entity->getByUser()){
					$sql="select user from t_login";
					$rs=$db->get_results($sql,ARRAY_A);
					$count=sizeof($rs);
					for($i=0;$i<$count;$i++){
						//crea un registro para cada login
						$sql="select count(*) c from ".$entity->getTable()." where user='".$rs[$i]['user']."'";
						$rsC=$db->get_row($sql,ARRAY_A);
						if ($rsC['c']==0){
							$sql="insert into ".$entity->getTable()."(user) values('".$rs[$i]['user']."')";
							$db->query($sql);
						}
					}
				}else{
					//crea el registro unico
					$sql="select count(*) c from ".$entity->getTable();
					$rs=$db->get_row($sql,ARRAY_A);
					if ($rs['c']==0){
						$sql="insert into ".$entity->getTable()."(".$key.") values(1)";
						$db->query($sql);
					}
				}
			}
				
			
		}
		//Entidades hijas
		if ($entity->getHasChilds()){
			foreach ($entity->getEntitiesChilds() as $entityChild){
				if ($entityChild->getTable()!=""){
					$count=sizeof($tables);
					$existe=false;
					for($i=0;$i<$count;$i++){
						if ($tables[$i][0]==$entityChild->getTable())
							$existe=true;
					}
					if (!$existe)
					{
						$keyChild="";
						$sql="create table `".$entityChild->getTable()."`(";
						$sqlIdiomas="create table `".$entityChild->getTableLanguages()."`( id_idioma INT(11) NOT NULL,";
						foreach ($entityChild->getFields() as $field){
							$campo="";
							$campo.="`".$field->getName()."` ";
							$campo.=$field->getType()." ";
							if ($field->getIsNull())
								$campo.="NULL ";	
							else
								$campo.="NOT NULL ";	
							if ($field->getIsAutoIncrement())
								$campo.="auto_increment ";	
							if ($field->getIsKey()){
								if ($keyChild!="")
									$keyChild.=",";
								$keyChild.="`".$field->getName()."`";	
							}
							$campo.=",";
							
							if (!$field->getIsMultilanguage()){
								$sql.=$campo;
								if ($field->getIsKey())
									$sqlIdiomas.=str_replace("auto_increment","",$campo);
							}else{
								$sqlIdiomas.=$campo;
							}
							
						}
						//añade la clave del padre
						foreach ($entity->getFields() as $field){
							if ($field->getIsKey()){
								$key=$field->getName()." ".$field->getType();
							}
						}
						$sql.=$key." not null ,";
						
						$sql.="primary key (".$keyChild.")) CHARSET=utf8 COLLATE=utf8_unicode_ci";
						$sqlIdiomas.="primary key (id_idioma,".$keyChild.")) CHARSET=utf8 COLLATE=utf8_unicode_ci";
						$db->query($sql);
						if($entityChild->getTableLanguages()!=""){
							$idiomas=true;
							$db->query($sqlIdiomas);
						}
					}
				}
			}
		}
		
	}
	if ($idiomas){
		$count=sizeof($tables);
		$existe=false;
		for($i=0;$i<$count;$i++){
			if ($tables[$i][0]=="t_idiomas")
				$existe=true;
		}
		if (!$existe){
			$sql="create table `t_idiomas`( ";
			$sql.="id_idioma INT(11) NOT NULL,";
			$sql.="des_idioma varchar(255) NOT NULL,";
			$sql.="des_corta varchar(15) NOT NULL,";
			$sql.="primary key (id_idioma)) CHARSET=utf8 COLLATE=utf8_unicode_ci";
			$db->query($sql);
			$sql="insert into t_idiomas (id_idioma, des_idioma, des_corta) values('1','Español','ESP')";
			$db->query($sql);
			$sql="insert into t_idiomas (id_idioma, des_idioma, des_corta) values('2','English','ENG')";
			$db->query($sql);
		}
	}
	
	//login
	$count=sizeof($tables);
	$existe=false;
	for($i=0;$i<$count;$i++){
		if ($tables[$i][0]=="t_login")
			$existe=true;
	}
	if (!$existe){
		$sql="create table `t_login`( ";
		$sql.="id_user int(11) NOT NULL auto_increment,";
		$sql.="user varchar(50) NOT NULL,";
		$sql.="pass varchar(64) NOT NULL,";
		$sql.="primary key (id_user)) CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$db->query($sql);
		$sql="insert into t_login (user, pass) values('admin','".sha1(str_replace("a","4",str_replace("e","3",str_replace("i","1",str_replace("o","0",PROJECT)))))."')";
		$db->query($sql);
	}
}
?>