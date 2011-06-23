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
    public function fetchSupportByPreapprovedKey($preapprovedKey)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('preapproved_key= "'.$preapprovedKey.'"')
        );
        return $row;
    }
    public function fetchSupportByRewardSubasta($reward)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('id_recompensa= "'.$reward.'" AND approved="S" AND cancelado="N"')
        );
        return $row;
    }

    public function setCanceled($idSupport){
        return $this->db->update(array("cancelado"=>"S"), "id_apoyo=".$idSupport);
    }
    public function setApproved($idSupport){
        return $this->db->update(array("approved"=>"S"), "id_apoyo=".$idSupport);
    }

    public function fetchSupportsByProject($idProject){
            return $this->db->fetchRow(
                    $this->db->select()
                    ->from("apoyo",
                            array('sum(apoyo) as sum_apoyo','count(apoyo) as count_apoyo'))
                    ->where('id_proyecto = '.$idProject.' AND approved="S" AND cancelado="N"')
                    );
    }




   
}

