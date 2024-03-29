<?php

class SupportController extends Zend_Controller_Action
{

    private $idRecompensaPatrocinador=null;

    public function init()
    {
        /* Initialize action controller here */
        $this->idRecompensaPatrocinador=2;
    }
    
    public function pagoAction(){

        $config = Zend_Registry::get('config');
        $support = $this->getRequest()->getParam('support');
        //Comprobar si tiene descuento
        $auth = Zend_Auth::getInstance ();
        $data['id_usuario_apoyo']=$auth->getIdentity()->id_usuario;
        $modelUser=New Model_Users();
        $user=$modelUser->fetchUser($data['id_usuario_apoyo']);
        $supportPaypal=$support*(1-($user->descuento/100));

        $reward = $this->getRequest()->getParam('reward');
        $sponsor = $this->getRequest()->getParam('sponsor');
        $linkRewrite = $this->getRequest()->getParam('proyect');


        $userModel=new Model_Users();
        if($sponsor!="" && !$userModel->existsSponsor($sponsor)){
            //redirecciona con cod_mensaje= 1
            $this->_redirect("/event/".$linkRewrite."/alert/1");
        }

        $Project=new Model_Projects();
        $project=$Project->fetchProjectByLinkRewrite($linkRewrite);

        $hostname='http://' . $this->getRequest ()->getHttpHost ();

        $paypalConfig['cancelUrl']=$hostname.$config->payment->paypal->return_cancel;
        $paypalConfig['returnUrl']=$hostname.$config->payment->paypal->return;
        $paypalConfig['paypalUrl']=$config->payment->paypal->paypal_url;
        $paypalConfig['notifyUrl']=$hostname.$config->payment->paypal->notify_url;
        $paypalConfig['bussines']=$config->payment->paypal->bussines;
        

        $params=array();
        $paypal=new Service_Paypal();

        $cd = strtotime($project->fec_fin);
        $fecPago= date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+15,date('Y',$cd)));


        $preapprovalKey=$paypal->CallPreapproval($paypalConfig['notifyUrl'],$paypalConfig['returnUrl'], $paypalConfig['cancelUrl'], "EUR", $project->fec_fin, $fecPago, $supportPaypal, "", 1, "", "", "", "", "", "");
        //Se guarda la clave

        $Support=new Model_Supports();

        $data['id_proyecto']=$project->id_proyecto;
        
        $data['id_usuario_apoyo']=$auth->getIdentity()->id_usuario;
        $data['apoyo']=$support;
        $data['descuento']=$user->descuento;
        $data['id_recompensa']=$reward;
        $data['fecha']=date("Y-m-d H:i:s");
        $data['preapproved_key']=$preapprovalKey;
        if($sponsor!="")
            $data['cod_patrocinio_apoyo']=$sponsor;
        $Support->saveSupport($data);

        $this->_redirect($paypal->getUrlPayPal($paypal->getCmdPreapproval($preapprovalKey)));
    }
    
    
    public function confirmAction(){

        $config = Zend_Registry::get('config');
        $idSupport = $this->getRequest()->getParam('support');

        $modelSupport=new Model_Supports();
        $support=$modelSupport->fetch($idSupport);

        $hostname=Service_Urls::getHost();

        $paypalConfig['cancelUrl']=$hostname.$config->payment->paypal->return_cancel;
        $paypalConfig['returnUrl']=$hostname.$config->payment->paypal->return;
        $paypalConfig['paypalUrl']=$config->payment->paypal->paypal_url;
        $paypalConfig['bussines']=$config->payment->paypal->bussines;
        //var_dump($paypalConfig['bussines']);


        $params=array();
        $paypal=new Service_Paypal();

        $amount=$support['apoyo']*(1-($support['descuento']/100));
        
        $pay=$paypal->CallPay("PAY", $paypalConfig['cancelUrl'], $paypalConfig['returnUrl'] , "EUR", array($paypalConfig['bussines']), array($amount), null, null, null, null, null, $pin, $support['preapproved_key'], null, null, null);

        if($pay['paymentExecStatus']=="COMPLETED"){
            $modelSupport->setPayed($idSupport);
            echo "Pagado ".$support['apoyo']." del apoyo".$idSupport;
             //Manda mail al pagador
            $mail = new Zend_Mail ( );
            $modelProject=new Model_Projects();
            $p=$modelProject->fetchById($support['id_proyecto']);

            $body="Thanks for making things happen! A has successfully achieved its goal! The charge in your account will be effectively made in the next few hours. To attend the event you just have to print your Rocking Red Ticket attached in this mail.<br/><br/>";
            if($support['cod_ticket']!="")
                $body.="Your ticket code is:<b>".$support['cod_ticket']."<br/><br/>";
            $body.="Enjoy the event and see you soon!";
            $body.="<br/><br/>";
            $modelUser=new Model_Users();
            $u=$modelUser->fetchUser($support['id_usuario_apoyo']);
            $imagen=$modelSupport->generateTicket($idSupport,$u['email'].$support['cod_ticket'],$support['cod_ticket']);
            $body.="<img src='http://www.rockingredticket.com/ticket/".$imagen.".jpg' />";
            
            $mail->setBodyHtml ( $body);
            $mail->setFrom ( 'noresponder@rockingredticket.com', 'rockingredticket.com' );

            
            $mail->addTo($u['email']);
            $mail->setSubject('Support confirm');
            $mail->send();

        }else{
            echo "Fallo!<br>";
            var_dump($pay);
        }
        die;
        
    }

    public function cancelAction(){


        $preapprovalKey = $this->getRequest()->getParam('preapproval_key');

        $modelSuport=new Model_Supports();
        $support=$modelSuport->fetchSupportByPreapprovedKey($preapprovalKey);
        $auth = Zend_Auth::getInstance();

        if($auth->getIdentity()->id_usuario!=$support['id_usuario_apoyo'])
                $this->_redirect("/");

        $config = Zend_Registry::get('config');

        $params=array();

        $paypal=New Service_Paypal();

        $rt=$paypal->CallCancelPreapproval($preapprovalKey);
        if($rt['responseEnvelope.ack']!="Success")
            die("ko");

        $modelSuport->setCanceled($support['id_apoyo']);
        
        if($support['id_recompensa']==$this->idRecompensaPatrocinador){
            //Borra el código de patrocinador
            $modelUser=new Model_Users();
            $user=$modelUser->fetchUser($support['id_usuario_apoyo']);
            $modelSuport->clearCodSponsor($user['cod_patrocinador']);
        }


        die("ok");


    }

    public function okAction(){}

    public function koAction(){}

    public function ipnAction(){


         if($_REQUEST['approved']=="true"){
             $modelSupport=new Model_Supports();
             $key=$_REQUEST['preapproval_key'];
             $support=$modelSupport->fetchSupportByPreapprovedKey($key);
             $rewardSupport=new Model_Rewards();
             //var_dump($rewardSupport->isSubasta($support['id_recompensa']));
             if($rewardSupport->isSubasta($support['id_recompensa'])){
                 //cancelar el apoyo anterior
                 $supportKo=$modelSupport->fetchSupportByRewardSubasta($support['id_recompensa']);
                 if(isset($supportKo['id_apoyo'])){

                     $modelSupport->setCanceled($supportKo['id_apoyo']);
                     //mandar mail al superado
                     $modelProject=new Model_Projects();
                     $project=$modelProject->fetchById($supportKo['id_proyecto']);
                     $link=Service_Urls::getHost()."/event/".$project['link_rewrite'];
                     //$body="El apoyo que has realizado de ".$supportKo['apoyo']."&euro; al proyecto <a href='".$link."'>".$project['titulo']."</a> ha sido superado por otro patrocinador.";
                     //$body.="<br/>Esto anula autom&aacute;ticamente tu apoyo, ya que solo el m&aacute;s alto es que se tiene en cuenta.";
                     $modelUser=new Model_Users();
                     $user=$modelUser->fetchUser($supportKo['id_usuario_apoyo']);
                     Service_Mail::sendMail($user['email'],"Your bid has been exceeded", "Somebody has exceeded your bid in the auction! Log in an make a new one!");

                 }
                 //Se actualiza la recompensa para que el apoyo minimo sea superior
                 $rewardSupport->set(array("apoyo_minimo"=>($_REQUEST['max_total_amount_of_all_payments']+1)),$support['id_recompensa']);


                 
             }


             $modelSupport->setApproved($support['id_apoyo']);
             $support=$modelSupport->fetch($support['id_apoyo']);


            /***
          * Código de ticket
          */
             $modelReward=new Model_Rewards();
             $reward=$modelReward->fetch($support['id_recompensa']);
             
             if($reward['no_entrada']=="N"){
             //incluye entrada
                $modelSupport->generateCodTicket($support['id_apoyo']);
             }
             

             if($support['cancelado']!="S"){
                 $modelProject=new Model_Projects();
                 $p=$modelProject->fetchById($support['id_proyecto']);

                 $modelUser=new Model_Users();
                 $u=$modelUser->fetchUser($p['id_usuario']);
                 $uSupport=$modelUser->fetchUser($support['id_usuario_apoyo']);

                //Mail al patrocinado
                 $mail = new Zend_Mail ( );
                 $body="The user ".$uSupport['username']." has become a supporter of your event! Congratulations!";
                 $mail->setBodyHtml ( $body);
                 $mail->setFrom ( 'noresponder@rockingredticket.com', 'rockingredticket.com' );

                 $mail->addTo($u['email']);
                 $mail->setSubject('New support');
                 $mail->send();
             }



             /********************
              * Código de recompensa al que se le asocia el código de patrocinador
              */
             $idRecompensaPatrocinador=$this->idRecompensaPatrocinador;

             if($support['id_recompensa']==$idRecompensaPatrocinador && $support['cancelado']=="N"){
                 $modelUser=new Model_Users();
                 $modelUser->generateCodSponsor($support['id_usuario_apoyo']);
                 $user=$modelUser->fetchUser($support['id_usuario_apoyo']);
                 //Lo manda por correo
                 $hostname = 'http://' . $this->getRequest ()->getHttpHost ();


                 //Mail al que  patrocina
                 $mail = new Zend_Mail ( );
                 $mail->setBodyHtml ( 'Thank you for becoming a Sponsor<br/>Sponsor code:<b>'.$user['cod_patrocinador']."</b><br /><br />_______________________________<br />Rocking Red Ticket");
                 $mail->setFrom ( 'noresponder@rockingredticket.com', 'rockingredticket.com' );

                 $mail->addTo($user['email']);
                 $mail->setSubject ( $user['username'].' you have become a sponsor');
                 $mail->send();


             }


             
         }
         die;

    }


    public function indexAction(){}


}

