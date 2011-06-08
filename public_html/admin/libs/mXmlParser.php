<?php
include ("util/xmlParser.php");
class mXmlParser {
	private $xml_parser;
	private $entities;
	private $hasLanguages;
	
	
	function mXmlParser() {
	    $this->xml_parser = new xmlParser();
	    $this->entities = array();
	}
	
	function getHasLanguages() {
        return $this->hasLanguages;
    }
	
	function &getEntities() {
        return $this->entities;
    }

    function addEntity( $entity) {
        $this->entities[] = $entity;
    }
	
	function parse($data){
		if (!$nodes = $this->xml_parser->parse( $data ))
	      die($this->xml_parser->lastError());
      	for ( $i = 0; $i < $nodes->nChildNodes(); $i++ ) {
			$node = $nodes->getChildNode($i);
            $name = strtolower($node->getName());

            switch ( $name ) {
            	case "entity":
            		$this->parseEntity($node);
            		break;
            }
		}
	}
	
	private function parseEntity($nodes, $deep=false){
		$entity=new entity();
		for ( $i = 0; $i < $nodes->nChildNodes(); $i++ ) {
			$node = $nodes->getChildNode($i);
            $name = strtolower($node->getName());
			
            switch ( $name ) {
            	case "id_entity":
            		$entity->setIdEntity($node->getText());
            		break;
            	case "title":
            		$entity->setTitle($node->getText());
            		break;
            	case "table":
            		$entity->setTable($node->getText());
            		break;
            	case "table_languages":
            		$entity->setTableLanguages($node->getText());
            		if ($node->getText()!=""){
            			$this->hasLanguages=true;
            		}
            		break;
            	case "fields":
            		$fields=$this->parseFields($node);
            		for($j=0;$j<sizeof($fields);$j++){
            			$entity->addField($fields[$j]);
            		}
            		break;
            	case "dialog_detail":
					$entity->setIsDialogDetail(strtolower($node->getText())=="s");
					break;
				case "by_user":
					$entity->setByUser(strtolower($node->getText())=="s");
					break;
				case "searchable":
					$entity->setSearchable(strtolower($node->getText())=="s");
					break;
				case "color":
					$entity->setColor($node->getText());
					break;
				case "layout":
					$entity->setLayout($node->getText());
					break;
				case "help_file_list":
					$entity->setHelpFileList($node->getText());
					break;
				case "hooked":
					$entity->setHooked($node->getText());
					break;
				case "hook_child":
					$entity->setHookChild($node->getText());
					break;
				case "cb_hook":
					$entity->setCbHook($node->getText());
					break;
                                case "icon":
					$entity->setIcon($node->getText());
					break;
				case "help_file_detail":
					$entity->setHelpFileDetail($node->getText());
					break;
				case "max_count":
					$entity->setMaxCount($node->getText());
					break;
				case "entity":
					$entity->addEntityChild($this->parseEntity($node,true));
					break;
				case "maintance_type":
					$entity->setMaintanceType($node->getText());
            		break;
            }
            
		}	
		if(!$deep)
			$this->addEntity($entity);
		return $entity;
	}
	
	private function parseFields($nodes){
		//fields
		for ( $i = 0; $i < $nodes->nChildNodes(); $i++ ) {
			$node = $nodes->getChildNode($i);
            $name = strtolower($node->getName());
            switch ( $name ) {
            	case "field":
            		$field=$this->parseField($node);
            		break;
            }
            $fields[$i]=$field;
		}	
		return $fields;
	}
	private function parseField($nodes){
		$field=new field();
		for ($i=0;$i<$nodes->nChildNodes();$i++){
			$currentNode = $nodes->getChildNode($i);
			$name = strtolower($currentNode->getName());
			switch ($name) {
				case "name":
					$field->setName($currentNode->getText());
					break;
				case "description":
					$field->setDescription($currentNode->getText());
					break;
				case "type":
					$field->setType($currentNode->getText());
					break;
				case "null":
					$field->setIsNull(strtolower($currentNode->getText())=="s");
					break;
				case "auto_increment":
					$field->setIsAutoIncrement(strtolower($currentNode->getText())=="s");
					break;
				case "primary_key":
					$field->setIsKey(strtolower($currentNode->getText())=="s");
					break;
				case "in_list":
					$field->setInList(strtolower($currentNode->getText())=="s");
					break;	
				case "entity_ref":
					$field->setEntityRef($currentNode->getText());
					break;
				case "combo_description":
					$field->setIsComboDescription(strtolower($currentNode->getText())=="s");
					break;
				case "multilanguage":
					$field->setIsMultiLanguage(strtolower($currentNode->getText())=="s");
					break;
				case "file":
					$field->setIsFile(strtolower($currentNode->getText())=="s");
					break;
				case "pass":
					$field->setIsPass(strtolower($currentNode->getText())=="s");
					break;
				case "locked":
					$field->setIsLocked(strtolower($currentNode->getText())=="s");
					break;
				case "sizes":
					$sizes=$this->parseSizes($currentNode);
            		for($j=0;$j<sizeof($sizes);$j++){
            			$field->addSize($sizes[$j]);
            		}
            		break;
            	case "ref":
            		$ref=$this->parseRef($currentNode);
					$field->setRef($ref);
            		break;
				case "fckeditor":
					$field->setFckEditor(strtolower($currentNode->getText())=="s");
					break;
			}
		}
		return $field;
	}
	
	private function parseSizes($nodes){
		//fields
		for ( $i = 0; $i < $nodes->nChildNodes(); $i++ ) {
			$node = $nodes->getChildNode($i);
            $name = strtolower($node->getName());
            switch ( $name ) {
            	case "size":
            		$size=$this->parseSize($node);
            		break;
            }
            $sizes[$i]=$size;
		}	
		return $sizes;
	}
	private function parseRef($nodes){
		//fields
		$ref=new ref();
		for ( $i = 0; $i < $nodes->nChildNodes(); $i++ ) {
			$node = $nodes->getChildNode($i);
            $name = strtolower($node->getName());
            switch ($name) {
            	case "table":
            		$ref->setTable($node->getText());
            		break;
            	case "row":
            		$ref->setRow($node->getText());
            		break;
            	case "row_depends":
            		$ref->setRowDepends($node->getText());
            		break;
            	case "values":
            		$values=$this->parseValues($node);
            		for($j=0;$j<sizeof($values);$j++){
            			$ref->addValue($values[$j]);
            		}
            		break;
            	case "depends":
            		$ref->setDepends($node->getText());
            		break;
            }
		}	
		return $ref;
	}
	
	private function parseValues($nodes){
		//fields
		for ( $i = 0; $i < $nodes->nChildNodes(); $i++ ) {
			$node = $nodes->getChildNode($i);
            $name = strtolower($node->getName());
            switch ( $name ) {
            	case "value":
            		$value=$node->getText();
            		break;
            }
            $values[$i]=$value;
		}	
		return $values;
	}
	
	private function parseSize($nodes){
		$size=new size();
		for ($i=0;$i<$nodes->nChildNodes();$i++){
			$currentNode = $nodes->getChildNode($i);
			$name = strtolower($currentNode->getName());
			switch ($name) {
				case "width":
					$size->setWidth($currentNode->getText());
					break;
				case "height":
					$size->setHeight($currentNode->getText());
					break;
			}
		}
		return $size;
	}
	
	
}
	
?>