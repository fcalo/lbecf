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

        /*$dbProject=new Application_Model_DbTable_Projects();

        $project=$dbProject->fetchRow(
                $dbProject->select()
                ->from(array("proyectos"), array("*","days"=>new Zend_Db_Expr("datediff(fec_fin,now())")))
                ->where('destacado = "S" AND fec_fin>now()')
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
            $this->view->closed=$project->days<1;
            $this->view->image="/admin/".str_replace("/".$project->id_proyecto."/", "/".$project->id_proyecto."/420x/thumb_", $project->imagen);
        }*/

        $modelProject=new Model_Projects();
        $this->view->projects=$modelProject->fetchActives(isset($this->view->project)?$project->id_proyecto:null);
        $modelCategorias=new Model_Categories();
        $this->view->categories=$modelCategorias->fetch();
    }


}

