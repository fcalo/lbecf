<?php

class Model_Projects
{

    private $db=null;
    private $dbComments=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Projects();
        $this->dbComments=new Application_Model_DbTable_CommentsProjects();
    }



    public function fetchProjectByLinkRewrite($link)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('link_rewrite= "'.$link.'"')
        );
        return $row;
    }
    public function fetchById($idProject)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('id_proyecto= "'.$idProject.'"')
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
    public function comment($data){
        $data['fecha']=date(DATE_ATOM);
        $this->dbComments->insert($data);
    }

    public function getVotes($idProject){
        $sql=" SELECT positivos.total positivos, negativos.total negativos FROM";
        $sql.="(SELECT count(*) total FROM votos_proyectos where valor=1 and id_proyecto=?) positivos, ";
        $sql.="(SELECT count(*) total FROM votos_proyectos where valor=-1 and id_proyecto=?) negativos ";

        return $this->db->getAdapter()->fetchRow($sql,array($idProject,$idProject));
    }

     public function saveProject(array $data){
        if(!isset($data['activo']))
                $data['activo']='N';


        return $this->db->insert($data);
    }

    public function uploadImage($path, $idProject){


        $sizes[]=array("420","");
        $sizes[]=array("","20");
        $helper=new View_Helper_Image();

        foreach($sizes as $size){
            $width=$size[0];
            $height=$size[1];
            $pathRes=dirname($path)."/".$width."x".$height."/";


            if (!$helper->ensurePath($pathRes)){
                    die("Error creando estructura");
            }

            if($width=="")
                    $width=$height*10;

            if($height=="")
                    $height=$width*10;

            if(!$helper->resizeImage(dirname($path)."/", $pathRes, basename($path), $width,$height)){
                    die("Error redimensionando");
            }
            $a=explode("/admin",$path);

        }
        $this->db->update(array("imagen"=>".".$a[1]), "id_proyecto=".$idProject);
        return true;
    }

    public function fetchClosed()
    {

        $sql="SELECT p.id_proyecto, p.importe_solicitado, sum(a.apoyo) apoyo ";
        $sql.=" FROM proyectos p";
        $sql.=" INNER JOIN apoyo a ON a.id_proyecto=p.id_proyecto";
        $sql.=" WHERE coalesce(p.completo,'N')!='S'";
        $sql.=" AND p.fec_fin<now()";
        $sql.=" AND a.approved='S'";
        $sql.=" AND a.cancelado='N'";
        $sql.=" group by p.id_proyecto";

        return $this->db->getAdapter()->query($sql)->fetchAll();
    }
    

   public function setCompleted($idProject){
        return $this->db->update(array("completo"=>"S"), "id_proyecto=".$idProject);
    }
}

