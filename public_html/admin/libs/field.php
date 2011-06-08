<?php

class size{
	private $width;
	private $height;
	function setWidth($width){$this->width=$width;}
    function getWidth(){return $this->width;}
    function setHeight($height){$this->height=$height;}
    function getHeight(){return $this->height;}
	
}

class ref{
	private $table="";
	private $row="";
	private $rowDepends="";
	private $depends="";
	private $values;
	
	function addValue($value) {
        $this->values[] = $value;
    }
    function &getValues() {
        return $this->values;
    }
	function setTable($table){$this->table=$table;}
    function getTable(){return $this->table;}
    function setRow($row){$this->row=$row;}
    function getRow(){return $this->row;}
    function setRowDepends($rowDepends){$this->rowDepends=$rowDepends;}
    function getRowDepends(){return $this->rowDepends;}
    function setDepends($depends){$this->depends=$depends;}
    function getDepends(){return $this->depends;}
}

class field{
	private $name;
	private $description;
	private $type;
	private $isNull=true;
	private $isAutoIncrement=false;
	private $isKey=false;
	private $inList=false;
	private $entityRef=0;
	private $isComboDescription=false;
	private $isMultilanguage=false;
	private $isFile=false;
	private $isPass=false;
	private $fckEditor=false;
	private $sizes;
	private $ref;
	private $locked=false;
	
	function field( $name = '' ) {
        $this->name = $name;
    }
    
    function setName($name){ 
    	$this->name=$name;
    }
    function getName(){
		return $this->name;
	}
	
	function setDescription($description){ 
    	$this->description=$description;
    }
    function getDescription(){
		return $this->description;
	}
	
	function setType($type){ 
    	$this->type = $type;
    }
    function getType(){
		return $this->type;
	}
	
	function setIsNull($isNull){ 
    	$this->isNull = $isNull;
    }
    function getIsNull(){
		return $this->isNull;
	}
	
	function setIsAutoIncrement($isAutoIncrement){ 
    	$this->isAutoIncrement = $isAutoIncrement;
    }
    function getIsAutoIncrement(){
		return $this->isAutoIncrement;
	}
	
	function setIsKey($isKey){ 
    	$this->isKey = $isKey;
    }
    function getIsKey(){
		return $this->isKey;
	}
	
	function setInList($inList){ 
    	$this->inList = $inList;
    	if ($this->isFile && $this->inList){
    		$s=new size();
    		$s->setHeight(20);
    		$this->addSize($s);
    	}
    }
    function getInList(){
		return $this->inList;
	}
	
    function setEntityRef($entityRef){
        $this->entityRef = $entityRef;
    }
    function getEntityRef(){
        return $this->entityRef;
    }
	
	function setIsComboDescription($isComboDescription){ 
    	$this->isComboDescription = $isComboDescription;
    }
    function getIsComboDescription(){
		return $this->isComboDescription;
	}
	
	function setIsMultilanguage($isMultilanguage){ 
    	$this->isMultilanguage= $isMultilanguage;
    }
    function getIsMultilanguage(){
		return $this->isMultilanguage;
	}
	
	function setIsFile($isFile){ 
    	$this->isFile= $isFile;
    	if ($this->isFile && $this->inList){
    		$s=new size();
    		$s->setHeight(20);
    		$this->addSize($s);
    	}
    }
    function getIsFile(){
		return $this->isFile;
	}
    function getIsPass(){
		return $this->isPass;
	}
	function setIsPass($isPass){ 
    	$this->isPass= $isPass;
    }
	function setFckEditor($fckEditor){ 
    	$this->fckEditor= $fckEditor;
    }
    function getFckEditor(){
		return $this->fckEditor;
	}
	function addSize($size) {
        $this->sizes[] = $size;
    }
    function &getSizes() {
        return $this->sizes;
    }
    function getRef(){
		return $this->ref;
	}
	function setRef($ref){ 
    	$this->ref= $ref;
    }
    function getIsLocked(){
		return $this->locked;
	}
	function setIsLocked($locked){ 
    	$this->locked=$locked;
    }
    
}
?>