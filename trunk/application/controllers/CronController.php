<?php

class CronController extends Zend_Controller_Action
{


    public function indexAction(){

        //chequea si hay que completar algun proyecto
        $modelProjectos=new Model_Projects();
        $projects=$modelProjectos->fetchClosed();
        foreach($projects as $project){
            if($project['apoyo']>=$project['importe_solicitado']){
                echo "completando el proyecto ".$project['id_proyecto'];
                $modelProjectos->setCompleted($project['id_proyecto']);
                //Se manda mail al propietario del proyecto
                $hostname = 'http://' . $this->getRequest ()->getHttpHost ();
                $link= $hostname .'/condiciones/';
                $mail = new Zend_Mail ( );
                $body="Congratulations! You event has been successfully backed! You will receive the money within the next few days.<br/>";
                $body.="Here is the list of people who has supported your event and their reward. You have to contact them within the next week (terms of use <a href='".$link."'>".$link."</a>.<br/><br/>";
                $body.="We wish you the best for your event, we are sure it’s going to be an exit!<br/><br/>";
                $body.="See you soon!<br/><br/>";
                $body.="List:<br/>";

                $modelUser=new Model_User();
                $modelReward=new Model_Rewards();

                $modelSupport=new Model_Supports();
                $supports=$modelSupport->fetchSupportsByProject($project['id_proyecto']);

                foreach($supports as $support){
                    $u=$modelUser->fetchUser($support['id_usuario_apoyo']);
                    $r=$modelReward->fetch($support['id_recompensa']);
                    $body=$u['username']."(".$u['email'].") - ".$r['recompensa'];
                }

                $mail->setBodyHtml ($body);
                $mail->setFrom ('noresponder@rockingredticket.com', 'rockingredticket.com' );

                $modelUser=new Model_Users();
                $u=$modelUser->fetchUser($project['id_usuario']);

                $mail->addTo($u['email']);
                $mail->setSubject('Event finish');
                $mail->send();
            }else{
                echo "completando el proyecto no conseguido ".$project['id_proyecto'];
                $modelProjectos->setCompleted($project['id_proyecto']);

                $mail = new Zend_Mail ( );
                $hostname = 'http://' . $this->getRequest ()->getHttpHost ();
                $link= $hostname .'/proyectos/';
                $body="We are sorry to inform you that X has not achieved its collecting goal. The charge in your account has already been cancelled.<br/>";
                $body.="Check the new events alive! <a href='".$link."'>".$link."</a></br></br>";
                $body.="See you soon!";
                 $mail->setBodyHtml ($body);
                $mail->setFrom ('noresponder@rockingredticket.com', 'rockingredticket.com' );

                $modelUser=new Model_Users();
                $u=$modelUser->fetchUser($project['id_usuario']);

                $mail->addTo($u['email']);
                $mail->setSubject('Event finish');
                $mail->send();
            }
        }


        //Obtiene todos los apoyos de los proyectos que han caducado y no han sido pagados
        $modelSupports=new Model_Supports();
        $supports=$modelSupports->fetchOutstanding();

        foreach($supports as $support){
            $ch = curl_init();
            
            echo "pagando el apoyo ".$support['id_apoyo'];

            $url=Service_Urls::getHost()."/apoyo/confirm/".$support['id_apoyo'];
            echo "(".$url.")";

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
           
            
        }
        echo "<br/>Terminado</br>";
        die;

    }

}

