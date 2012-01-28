<?php

class Model_Projects
{

    private $db=null;
    private $dbComments=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Projects();
        $this->dbComments=new Application_Model_DbTable_CommentsProjects();
        $this->dbCommentsProposal=new Application_Model_DbTable_CommentsProposal();
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
    public function fetchByUser($idUser)
    {
        $rs=$this->db->fetchAll(
        $this->db->select()
        ->where('id_usuario= "'.$idUser.'"')
        );
        return $rs;
    }

    public function fetchActives($idProjectExcept=null, $idCategory=0)
    {

        $params=array();
        $params[]="S";

        $sql="SELECT coalesce(t.numApoyos,0) numApoyos, datediff(p.fec_fin,now()) days, p.*, t.apoyo recaudado,";
        $sql.=" (CASE WHEN p.importe_solicitado=0 THEN 100 ELSE ((t.apoyo/p.importe_solicitado)*100) END) porcentaje, p.ciudad, date_format(p.fecha,'%e/%c/%Y') fecha ";
        $sql.=" FROM proyectos p";
        $sql.=" LEFT JOIN (";
        $sql.="   select count(*) numApoyos, sum(apoyo) apoyo, id_proyecto";
        $sql.="   from apoyo";
        $sql.="   where approved='S' AND cancelado!='S'";
        $sql.="   GROUP BY id_proyecto";
        $sql.=" ) t ON t.id_proyecto=p.id_proyecto";
        $sql.=" WHERE p.activo= ?";
        $sql.=" AND fec_fin>now()";

        if($idProjectExcept!=null){
            $sql.=" AND p.id_proyecto!=?";
            $params[]=$idProjectExcept;
        }
        if($idCategory!=0){
            $sql.=" AND p.id_categoria=?";
            $params[]=$idCategory;
        }
        $sql.=" ORDER BY p.id_proyecto ASC";



        return $this->db->getAdapter()->fetchAll($sql,$params);

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
    public function voteProposal($data){
        $sql="INSERT INTO votos_propuestas ";
        $sql.=" (id_usuario, id_propuesta, valor)";
        $sql.=" VALUES (?,?,?)";
        $sql.=" ON DUPLICATE KEY UPDATE";
        $sql.=" valor=?,";
        $sql.=" fecha=now()";

        $this->db->getAdapter()->query($sql, array($data['id_usuario'], $data['id_propuesta'],$data['valor'],$data['valor']));
    }
    public function comment($data){
        $data['fecha']=new Zend_Db_Expr("now()");
        $this->dbComments->insert($data);
    }
    public function commentProposal($data){
        $data['fecha']=new Zend_Db_Expr("now()");
        $this->dbCommentsProposal->insert($data);
    }

    public function getComments($idProject){
        
        $sql="SELECT u.username, u.imagen, u.id_usuario ";
        $sql.=", c.fecha, c.comentario";
        $sql.=" FROM comentarios_proyectos c";
        $sql.=" INNER JOIN usuario u ON u.id_usuario=c.id_usuario";
        $sql.=" WHERE  c.id_proyecto= ?";
        $sql.=" ORDER BY c.fecha ASC";
        return $this->db->getAdapter()->fetchAll($sql,array($idProject));
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
        if(!isset($data['destacado']))
                $data['destacado']='N';


        return $this->db->insert($data);
    }

    public function updateProject($idProject, array $data){
        if(!isset($data['activo']))
                $data['activo']='N';
        if(!isset($data['destacado']))
                $data['destacado']='N';

        return $this->db->update($data, "id_proyecto=".$idProject);
    }

    public function uploadImage($path, $idProject){


        $sizes[]=array("420","");
        $sizes[]=array("160","");
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

