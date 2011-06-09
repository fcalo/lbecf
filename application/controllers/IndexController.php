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

            $dbSupport =new Application_Model_DbTable_Support();
            $supports=$dbSupport->fetchRow(
                    $dbSupport->select()
                    ->from("apoyo",
                            array('sum(apoyo) as sum_apoyo','count(apoyo) as count_apoyo', 'apoyo'))
                    ->where('id_proyecto = '.$project->id_proyecto)
                    ->group('apoyo')
                    );

            $dbNews=new Application_Model_DbTable_News();
            $news=$dbNews->fetchAll($dbNews->select());


            $this->view->recaudado=isset($supports->sum_apoyo)?$supports->sum_apoyo:0;
            $this->view->numApoyos=isset($supports->count_apoyo)?$supports->count_apoyo:0;
            $this->view->project=$project;
            $this->view->porcentaje=($supports->apoyo/$project->importe_solicitado)*100;
            $this->view->news=$news;
            $this->view->days=$project->days;
            $this->view->image="/admin/".str_replace("/".$project->id_proyecto."/", "/".$project->id_proyecto."/420x/thumb_", $project->imagen);
        }
        
    }


}

