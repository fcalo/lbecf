<?php

class Model_Rewards
{

    private $db=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Rewards();
    }

    public function save(array $data){
        return $this->db->insert($data);
    }



    public function isSubasta($idReward)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('id_recompensa= "'.$idReward.'"')
        );
        return $row['subasta']=="S";
    }
    public function set($data, $idReward){
        $this->db->update($data, "id_recompensa=".$idReward);
        return true;
    }

       
}

