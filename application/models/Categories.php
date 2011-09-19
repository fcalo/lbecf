<?php

class Model_Categories
{

    private $db=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Category();
    }


    public function fetch()
    {
        $rs=$this->db->fetchAll(
        $this->db->select()
        );
        return $rs;
    }


}

