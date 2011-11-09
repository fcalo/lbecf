<?php

class Model_Rewards
{

    private $db=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Rewards();
    }

    public function save(array $data, $clean=false){
        if($clean)
            $this->db->delete("id_proyecto=".$data['id_proyecto']);
        return $this->db->insert($data);
    }

    public function fetch($id)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('id_recompensa= "'.$id.'"')
        );
        return $row;
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

    public function fetchByIdProject($idProject)
    {
        $row=$this->db->fetchAll(
        $this->db->select()
        ->where('id_proyecto= "'.$idProject.'"')
        );
        return $row->toArray();
    }
       
}

