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

