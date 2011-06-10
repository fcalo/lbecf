<?php

class Model_Supports
{

    private $db=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Support();
    }


    public function saveSupport(array $data){
        if(!isset($data['approved']))
                $data['approved']='N';

        return $this->db->insert($data);
    }

   
}

