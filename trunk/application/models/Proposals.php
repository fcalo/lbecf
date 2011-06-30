<?php

class Model_Proposals
{

    private $db=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Proposals();
    }


    public function saveProposal(array $data){
        return $this->db->insert($data);
    }
    public function updateProposal($idProposal, array $data){
        return $this->db->update($data, "id_propuesta=".$idProposal);
    }
    public function fetchByProject($idProject)
    {
        $sql="SELECT t.votos, u.username, u.imagen, u.id_usuario ";
        $sql.=", p.fecha, p.propuesta, p.id_propuesta, p.adjunto";
        $sql.=" FROM propuestas p";
        $sql.=" INNER JOIN usuario u ON u.id_usuario=p.id_usuario_propuesta";
        $sql.=" LEFT JOIN (SELECT sum(valor) votos, id_propuesta FROM votos_propuestas GROUP BY id_propuesta) t ON t.id_propuesta=p.id_propuesta";
        $sql.=" WHERE  p.id_proyecto= ?";
        $sql.=" ORDER BY t.votos DESC, p.fecha ASC";
        return $this->db->getAdapter()->fetchAll($sql,array($idProject));
    }
    public function fetchByUser($idUser)
    {
        $sql="SELECT count(*) c, y.titulo, y.link_rewrite ";
        $sql.=" FROM propuestas p";
        $sql.=" INNER JOIN proyectos y ON y.id_proyecto=p.id_proyecto";
        $sql.=" WHERE  p.id_usuario_propuesta= ?";
        $sql.=" GROUP BY y.titulo, y.link_rewrite";
        $sql.=" ORDER BY p.fecha ASC";
        return $this->db->getAdapter()->fetchAll($sql,array($idUser));
    }
    public function fetch($idProposal)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('id_propuesta= "'.$idPropuesta.'"')
        );
        return $row;
    }
    public function getComments($idProposal){

        $sql="SELECT u.username, u.imagen, u.id_usuario ";
        $sql.=", c.fecha, c.comentario";
        $sql.=" FROM comentarios_propuestas c";
        $sql.=" INNER JOIN usuario u ON u.id_usuario=c.id_usuario";
        $sql.=" WHERE  c.id_propuesta= ?";
        $sql.=" ORDER BY c.fecha ASC";
        return $this->db->getAdapter()->fetchAll($sql,array($idProposal));
    }
    public function getCommentsByUser($idUser){

        $sql="SELECT distinct uu.username, y.titulo, y.link_rewrite";
        $sql.=" FROM comentarios_propuestas c";
        $sql.=" INNER JOIN usuario u ON u.id_usuario=c.id_usuario";
        $sql.=" INNER JOIN propuestas p ON p.id_propuesta=c.id_propuesta";
        $sql.=" INNER JOIN proyectos y ON y.id_proyecto=p.id_proyecto";
        $sql.=" INNER JOIN usuario uu ON uu.id_usuario=p.id_usuario_propuesta";
        $sql.=" WHERE  u.id_usuario= ?";
        $sql.=" ORDER BY c.fecha ASC";
        return $this->db->getAdapter()->fetchAll($sql,array($idUser));
    }

    public function getVotes($idProposal){
        $sql=" SELECT positivos.total positivos, negativos.total negativos FROM";
        $sql.="(SELECT count(*) total FROM votos_propuestas where valor=1 and id_propuesta=?) positivos, ";
        $sql.="(SELECT count(*) total FROM votos_propuestas where valor=-1 and id_propuesta=?) negativos ";

        return $this->db->getAdapter()->fetchRow($sql,array($idProposal,$idProposal));
    }
   
}

