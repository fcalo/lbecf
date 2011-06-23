<?php

class Model_Collaborators
{

    private $db=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Collaborators();
    }


    public function fetchByProject($idProject)
    {
        $row=$this->db->fetchAll(
        $this->db->select()
        ->where('id_proyecto= "'.$idProject.'"')
        );
        return $row;
    }


}

