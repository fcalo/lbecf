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

    public function vote($data){
        $sql="INSERT INTO votos_proyectos ";
        $sql.=" (id_usuario, id_proyecto, valor)";
        $sql.=" VALUES (?,?,?)";
        $sql.=" ON DUPLICATE KEY UPDATE";
        $sql.=" valor=?,";
        $sql.=" fecha=now()";

        $this->db->getAdapter()->query($sql, array($data['id_usuario'], $data['id_proyecto'],$data['valor'],$data['valor']));
    }

    public function getVotes($idProject){
        $sql=" SELECT positivos.total positivos, negativos.total negativos FROM";
        $sql.="(SELECT count(*) total FROM votos_proyectos where valor=1 and id_proyecto=?) positivos, ";
        $sql.="(SELECT count(*) total FROM votos_proyectos where valor=-1 and id_proyecto=?) negativos ";

        return $this->db->getAdapter()->fetchRow($sql,array($idProject,$idProject));
    }


   
}

