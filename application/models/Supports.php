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
    public function fetch($idSupport)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('id_apoyo= "'.$idSupport.'"')
        );
        return $row;
    }
    public function fetchSupportsByUser($idUser)
    {


        $sql="SELECT p.titulo, p.link_rewrite, a.apoyo, a.preapproved_key, r.recompensa, r.subasta";
        $sql.=" FROM proyectos p";
        $sql.=" INNER JOIN apoyo a ON a.id_proyecto=p.id_proyecto";
        $sql.=" INNER JOIN recompensa r ON r.id_recompensa=a.id_recompensa";
        $sql.=" WHERE a.id_usuario_apoyo=?";
        $sql.=" AND a.approved='S'";
        $sql.=" AND a.cancelado='N'";
        $sql.=" AND p.fec_fin>now()";
        $sql.=" ORDER BY p.id_proyecto, a.id_apoyo";

        return $this->db->getAdapter()->query($sql, array($idUser))->fetchAll();
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
    public function setPayed($idSupport){
        return $this->db->update(array("pagado"=>"S"), "id_apoyo=".$idSupport);
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

