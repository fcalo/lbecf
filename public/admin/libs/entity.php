<?php
class entity{
	private $title;
	private $idEntity;
	private $table;
	private $tableLanguages;
	private $isDialogDetail;
	private $hasChilds=false;
	private $isChild=false;
	private $byUser=false;
	private $entities;
	private $fields;
	private $color="CDCDCD";
	private $maxCount=0;
	/************************
	/*0->Sin Mantenimiento
	/*1->Sin Listado
	/*2->Completo
	/*3->Sin boton Nuevo
	/************************/
	private $maintanceType=2;
	private $helpFileList="";
	private $helpFileDetail="";
	private $hooked="";
	private $hookChild="";
	private $cbHook="";
	private $searchable=false;
	/************************
	/*0->Tabs
	/*1->pagina unica separada en secciones
	/************************/
	private $layout=0;
        private $icon="";
	
	function entity( $idEntity= '' ) {
        $this->idEntity = $idEntity;
        $this->fields = array();
    }
    
    function setIdEntity($idEntity){ 
    	$this->idEntity = $idEntity;
    }
    function getIdEntity(){
		return $this->idEntity;
	}
    
    function setTitle($title){ 
    	$this->title = $title;
    }
    function getTitle(){
		return $this->title;
	}
	
	function setTable($table){ 
    	$this->table = $table;
    }
    function getTable(){
		return $this->table;
	}
	
	function setTableLanguages($tableLanguages){ 
    	$this->tableLanguages = $tableLanguages;
    }
    function getTableLanguages(){
		return $this->tableLanguages;
	}
	
	function setIsDialogDetail($isDialogDetail){ 
    	$this->isDialogDetail = $idDialogDetail;
    }
    function getIsDialgDetail(){
		return $this->isDialogDetail;
	}
	
	function &getFields() {
        return $this->fields;
    }

    function addField($field) {
        $this->fields[] = $field;
    }
    
    function &getEntitiesChilds() {
        return $this->entities;
    }

    function addEntityChild($entity) {
    	$entity->isChild(true);
        $this->entities[] = $entity;
        $this->hasChilds(true);
        
    }
    function isChild($isChild){ 
    	$this->isChild = $isChild;
    }
    function getIsChild(){
		return $this->isChild;
	}
	
	function hasChilds($hasChilds){ 
    	$this->hasChilds = $hasChilds;
    }
    function getHasChilds(){
		return $this->hasChilds;
	}
	function setMaintanceType($maintanceType){ 
    	$this->maintanceType = $maintanceType;
    }
    function getMaintanceType(){
		return $this->maintanceType;
	}
  	function setByUser($byUser){ 
    	$this->byUser = $byUser;
    }
    function getByUser(){
		return $this->byUser;
	}  
	function setColor($color){ 
    	$this->color = $color;
    }
    function getColor(){
		return $this->color;
	}
        function setIcon($icon){
    	$this->icon = $icon;
    }
    function getIcon(){
		return $this->icon;
	}
	function setHelpFileList($helpFileList){ 
    	$this->helpFileList = $helpFileList;
    }
    function getHelpFileList(){
		return $this->helpFileList;
	}
	function setHelpFileDetail($helpFileDetail){ 
    	$this->helpFileDetail = $helpFileDetail;
    }
    function getHelpFileDetail(){
		return $this->helpFileDetail;
	}	
	function setHooked($hooked){ 
    	$this->hooked = $hooked;
    }
    function getHooked(){
		return $this->hooked;
	}
	function setHookChild($hookChild){ 
    	$this->hookChild = $hookChild;
    }
    function getHookChild(){
		return $this->hookChild;
	}
	function setCbHook($cbHook){ 
    	$this->cbHook = $cbHook;
    }
    function getCbHook(){
		return $this->cbHook;
	}
	function setSearchable($searchable){ 
    	$this->searchable = $searchable;
    }
    function getSearchable(){
		return $this->searchable;
	}
	function setMaxCount($maxCount){ 
    	$this->maxCount = $maxCount;
    }
    function getMaxCount(){
		return $this->maxCount;
	}  
	function setLayout($layout){ 
    	$this->layout = $layout;
    }
    function getLayout(){
		return $this->layout;
	}  
}
?>