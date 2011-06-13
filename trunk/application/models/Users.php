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

        return $this->db->insert($data);
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
    public function checkUserEmail($email)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('email= "'.$email.'" and activo="S" and fec_baja is null')
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

    public function fetchUser($id)
    {
        //return $this->db->users->findOne( array('IdUser' =>$id) );
    }

    public function fetchUserByUsername($username)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('username= "'.$username.'" and activo="S" and fec_baja is null')
        );
        return $row;
    }

    public function fetchUserByEmail($email)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('email= "'.$email.'" and activo="S" and fec_baja is null')
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

}

