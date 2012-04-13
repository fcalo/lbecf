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
    public function fetchOutstanding()
    {
        $sql="SELECT a.id_apoyo, a.id_usuario_apoyo";
        $sql.=" FROM apoyo a";
        $sql.=" INNER JOIN proyectos p ON p.id_proyecto=a.id_proyecto";
        $sql.=" WHERE coalesce(a.pagado,'N')!='S'";
        $sql.=" AND coalesce(a.cancelado,'N')!='S'";
        $sql.=" AND coalesce(a.approved,'N')='S'";
        $sql.=" AND coalesce(p.completo,'N')='S'";

        return $this->db->getAdapter()->query($sql)->fetchAll();
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

    public function fetchSupportersByProject($idProject){
        $sql="SELECT u.email, u.username, u.imagen, u.id_usuario";
        $sql.=" FROM apoyo a inner join usuario u on u.id_usuario=a.id_usuario_apoyo";
        $sql.=" WHERE a.id_proyecto=".$idProject;
        $sql.=" AND a.approved='S'";
        $sql.=" AND a.cancelado='N'";

        return $this->db->getAdapter()->query($sql)->fetchAll();
    }

    public function clearCodSponsor($codSponsor){
        $rt=true;
        $sql="UPDATE apoyo SET cod_patrocinio_apoyo=null";
        $sql.=" WHERE cod_patrocinio_apoyo='".$codSponsor."'";
        $rt=$rt && $this->db->getAdapter()->query($sql);

        $sql="UPDATE usuario SET cod_patrocinio=null";
        $sql.=" WHERE cod_patrocinio='".$codSponsor."'";
        $rt=$rt && $this->db->getAdapter()->query($sql);

        $sql="UPDATE usuario SET cod_patrocinador=null";
        $sql.=" WHERE cod_patrocinador='".$codSponsor."'";
        $rt=$rt && $this->db->getAdapter()->query($sql);

        return $rt;
        

    }
    public function generateCodTicket($idSupport){
        $cod=$this->randomString();
        return $this->db->update(array("cod_ticket"=>$cod), "id_apoyo=".$idSupport);
    }
    private function randomString($length=10,$uc=TRUE,$n=TRUE,$sc=FALSE){
	$source = 'abcdefghijklmnopqrstuvwxyz';
	if($uc==1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if($n==1) $source .= '1234567890';
	if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
	if($length>0){
		$rstr = "";
		$source = str_split($source,1);
		for($i=1; $i<=$length; $i++){
			mt_srand((double)microtime() * 1000000);
			$num = mt_rand(1,count($source));
			$rstr .= $source[$num-1];
		}

	}
	return $rstr;
    }
    public function generateTicket($idSupport,$digest, $code){


        $sql="SELECT p.ticket_centro_1, p.ticket_centro_2,p.ticket_centro_3,p.ticket_centro_4,p.ticket_derecha_1,p.ticket_derecha_2";
        $sql.=" FROM apoyo a";
        $sql.=" INNER JOIN proyectos p ON p.id_proyecto=a.id_proyecto";
        $sql.=" WHERE a.id_apoyo=".$idSupport;


        $rt=$this->db->getAdapter()->query($sql)->fetchAll();


        $codImagen=md5($digest.$this->randomString());

        $image = imagecreatefromjpeg(dirname(__file__)."/../ticket.jpg");
        $color = ImageColorAllocate($image, 0, 0, 0);
        $fuente=dirname(__file__)."/../arial.ttf";
        $fuenteBold=dirname(__file__)."/../Arial Bold.ttf";

        $widthCharacter=7;

        $texto=$rt[0]['ticket_centro_1'];
        imagettftext($image, 12, 0,290-((strlen($texto)/2)*$widthCharacter), 150, $color,$fuente, $texto);
        $texto=$rt[0]['ticket_centro_2'];
        imagettftext($image, 12, 0,290-((strlen($texto)/2)*$widthCharacter), 170, $color,$fuente, $texto);
        $texto=$rt[0]['ticket_centro_3'];
        imagettftext($image, 12, 0,290-((strlen($texto)/2)*$widthCharacter), 190, $color,$fuente, $texto);
        $texto=$rt[0]['ticket_centro_4'];
        imagettftext($image, 12, 0,290-((strlen($texto)/2)*$widthCharacter), 210, $color,$fuente, $texto);


        $widthCharacter=6;
        $texto=$rt[0]['ticket_derecha_1'];
        imagettftext($image, 10, 0,485-((strlen($texto)/2)*$widthCharacter), 58, $color,$fuente, $texto);
        $texto=$rt[0]['ticket_derecha_2'];
        imagettftext($image, 10, 0,485-((strlen($texto)/2)*$widthCharacter), 78, $color,$fuente, $texto);
        /*$texto="Doors: 8pm";
        imagettftext($image, 12, 0,490-((strlen($texto)/2)*$widthCharacter), 98, $color,$fuente, $texto);*/

        //codigo
        imagettftext($image, 14, 0,435, 220, $color,$fuenteBold, $code);



        $path=dirname(__file__)."/../../public_html/ticket/";
        @mkdir($path);
        imagejpeg($image, $path.$codImagen.".jpg");
        
        //liberamos la memoria ocupada por la imagen
        imagedestroy($image);
        return $codImagen;

    }




   
}

