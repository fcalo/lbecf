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
        $data['pass']=md5($data['pass']);
        return $this->db->insert($data);
    }
    public function updateUser($idUser, array $data){
        return $this->db->update($data, "id_usuario=".$idUser);
    }


    function deleteUser($username){
        //TODO
    }

    public function checkUserLogin($email, $password)
    {

        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('(email="'.$email.'" or username="'.$email.'") AND hex(pass)=hex(md5("'.$password.'")) and activo="S"')
        );
        return $row;
    }
    public function checkUserEmail($email)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('email= "'.$email.'"')
        );
        return count($row)==0;
    }
    public function checkUsername($username)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('username= "'.$username.'"')
        );
        return count($row)==0;
    }

    public function getUserToken($email)
    {
       //$user = $this->db->users->findOne( array('email' =>$email), array('token') );
       return $user['token'];
    }

    public function fetchUserByToken($token)
    {
        //return $this->db->users->findOne( array('token' =>$token) );
    }

    public function fetchUser($id)
    {
        //return $this->db->users->findOne( array('IdUser' =>$id) );
    }

    public function fetchUserByUsername($username)
    {
        //return $this->db->users->findOne( array('username' =>$username) );
    }

    public function fetchUserByEmail($email)
    {
        $row=$this->db->fetchRow(
        $this->db->select()
        ->where('email= "'.$email.'"')
        );
        return $row;
    }

    public function fetchUserByIdFacebook($idFacebook)
    {
        $row=$this->db->fetchRow(
            $this->db->select()
            ->where('id_facebook= "'.$idFacebook.'"')
        );
        return $row;
    }
}

