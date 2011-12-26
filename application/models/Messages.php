<?php

class Model_Messages
{

    private $db=null;
    private $idUser=null;


    public function  __construct() {
        $this->db=new Application_Model_DbTable_Messages();
    }

    public function setIdUser($idUser){
        $this->idUser=$idUser;
    }

    public function save(array $data, $send=false, $remitente=null, $receptor=null){
        if($this->db->insert($data)){
            if($send){
                $mail = new Zend_Mail ( );
                $hostname = 'http://' . $_SERVER['HTTP_HOST'];

                $link=$hostname.'/user/mail/'.$receptor->username;
                $body="The user ".$receptor->username." has sent you an email! Log in to read it.";
                $mail->setBodyHtml ( $body);
                $mail->setFrom ( 'noresponder@rockingredticket.com', 'rockingredticket.com' );

                $mail->addTo($receptor->email);
                $mail->setSubject ( "Rocking Red Ticket, ".$data['asunto']);
                return $mail->send();
            }else{
                return true;
            }
        }

    }

    public function fetchIn(){
        $sql="SELECT m.id_mensaje, m.asunto, m.mensaje, m.id_usuario_remitente, date_format(fecha,'%e/%c/%Y') fecha , u.username ";
        $sql.=" FROM mensajes m";
        $sql.=" INNER JOIN usuario u ON u.id_usuario=m.id_usuario_remitente";
        $sql.=" WHERE  m.id_usuario_receptor= ?";
        $sql.=" ORDER BY m.fecha DESC, m.id_mensaje";
        return $this->db->getAdapter()->fetchAll($sql,array($this->idUser));
    }
    public function fetchOut(){
        $sql="SELECT m.id_mensaje, m.asunto, m.mensaje, m.id_usuario_remitente, date_format(fecha,'%e/%c/%Y') fecha , u.username ";
        $sql.=" FROM mensajes m";
        $sql.=" INNER JOIN usuario u ON u.id_usuario=m.id_usuario_receptor";
        $sql.=" WHERE  m.id_usuario_remitente= ?";
        $sql.=" ORDER BY m.fecha DESC, m.id_mensaje";
        return $this->db->getAdapter()->fetchAll($sql,array($this->idUser));
    }
}

