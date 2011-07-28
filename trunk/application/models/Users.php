<?php

class Model_Users
{

    private $db=null;
    public function  __construct() {
        $this->db=new Application_Model_DbTable_Users();
    }

    public function saveUser(array $data){
        if(!isset($data['activo']))
                $data['activo']='N';
        if (!isset($data["id_facebook"])){
            $data['pass']=md5($data['pass']);
            $data ['token'] = md5 ( uniqid ( rand (), 1 ) );
        }else{
            $data['activo']='S';
        }
        //Comprueba si existia pero dado de baja
        $user=$this->fetchUserByEmail($data['email'], true);
        if($user!=NULL){
            $data['fec_baja']=null;
            return $this->updateUser($user['id_usuario'], $data);
        }else
            return $this->db->insert($data);
    }
    public function generateCodSponsor($idUser){
        $cod=$this->randomString();
        return $this->db->update(array("cod_patrocinador"=>$cod), "id_usuario=".$idUser);
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



    public function updateUser($idUser, array $data){
        return $this->db->update($data, "id_usuario=".$idUser);
    }


    function deleteUser($idUser){
        $data['activo']='N';
        $data['fec_baja']=date("Y-m-d H:i:s");
        return $this->db->update($data, "id_usuario=".$idUser);
    }

    public function checkUserLogin($email, $password)
    {

        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('(email="'.$email.'" or username="'.$email.'") AND hex(pass)=hex(md5("'.$password.'")) and activo="S" and fec_baja is null')
        );
        return $row;
    }
    public function checkUserEmail($email, $forceAll=false)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('email= "'.$email.'"'.($forceAll?"":'and activo="S" and fec_baja is null'))
        );
        return count($row)==0;
    }
    public function checkUsername($username)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('username= "'.$username.'" and activo="S" and fec_baja is null')
        );
        return count($row)==0;
    }
    public function existsSponsor($codSponsor)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('cod_patrocinador= "'.$codSponsor.'"')
        );
        return count($row)>0;
    }

    public function getUserToken($email)
    {
       $row=$this->db->fetchRow(
        $this->db->select()
        ->where('email= "'.$email.'"')
        );
       return $row['token'];
    }

    public function fetchUserByToken($token)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('token= "'.$token.'"')
        );
        return $row;
    }

    public function fetchUser($id, $forceAll=false)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('id_usuario= "'.$id.'"'.($forceAll?"":' and activo="S" and fec_baja is null'))
        );
        return $row;
    }

    public function fetchUserByUsername($username)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('username= "'.$username.'" and activo="S" and fec_baja is null')
        );
        return $row;
    }

    public function fetchUserByEmail($email, $forceAll=false)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('email= "'.$email.'"'.($forceAll?"":' and activo="S" and fec_baja is null'))
        );
        return $row;
    }

    public function fetchUserByIdFacebook($idFacebook)
    {
        $row=$this->db->fetchRow(
            $this->db->select()
            ->where('id_facebook= "'.$idFacebook.'" and activo="S" and fec_baja is null')
        );
        return $row;
    }

    public function uploadImage($path, $idUser){


        $sizes[]=array("50","");
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
        $this->db->update(array("imagen"=>".".$a[1]), "id_usuario=".$idUser);
        return true;
    }

     public function getComments($idUser){

        $sql="SELECT count(*) comentarios, p.titulo, p.link_rewrite ";
        $sql.=" FROM comentarios_proyectos c";
        $sql.=" INNER JOIN proyectos p ON p.id_proyecto=c.id_proyecto";
        $sql.=" WHERE  c.id_usuario= ?";
        $sql.=" GROUP BY p.titulo, p.link_rewrite";
        $sql.=" ORDER BY p.id_proyecto ASC";
        return $this->db->getAdapter()->fetchAll($sql,array($idUser));
    }

}

