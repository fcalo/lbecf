<?php

class Model_Projects
{

    private $db=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Projects();
    }



    public function fetchProjectByLinkRewrite($link)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('link_rewrite= "'.$link.'"')
        );
        return $row;
    }

   
}

