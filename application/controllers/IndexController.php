<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->headScript()->appendFile( '/js/home.js');
    }

    public function indexAction()
    {

        $dbProject=new Application_Model_DbTable_Projects();

        $project=$dbProject->fetchRow(
                $dbProject->select()
                ->from(array("proyectos"), array("*","days"=>new Zend_Db_Expr("abs(datediff(now(),fec_fin))")))
                ->where('destacado = "S"')
                ->order("id_proyecto DESC")
                );


        if(count($project)!=0){

            $model=new Model_Supports();
            $supports=$model->fetchSupportsByProject($project->id_proyecto);

            
            $dbNews=new Application_Model_DbTable_News();
            $news=$dbNews->fetchAll($dbNews->select());


            $this->view->recaudado=isset($supports->sum_apoyo)?$supports->sum_apoyo:0;
            $this->view->numApoyos=isset($supports->count_apoyo)?$supports->count_apoyo:0;
            $this->view->project=$project;
            $this->view->porcentaje=($supports->sum_apoyo/$project->importe_solicitado)*100;
            $this->view->news=$news;
            $this->view->days=$project->days;
            $this->view->image="/admin/".str_replace("/".$project->id_proyecto."/", "/".$project->id_proyecto."/420x/thumb_", $project->imagen);
        }
        
    }


}

