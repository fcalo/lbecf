<?php

class ProjectController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->headScript()->appendFile( '/js/project.js');
    }

    public function indexAction()
    {

        $request = $this->getRequest ();

        $linkRewrite=$request->project;

        //TODO:old
        $dbProject=new Application_Model_DbTable_Projects();

        $model=new Model_Projects();


        //->from(array("proyectos"), array("*","days"=>"abs(datediff(now(),p.fec_fin)"))
        $project=$dbProject->fetchRow(
                $dbProject->select()
                ->from(array("proyectos"), array("*","days"=>new Zend_Db_Expr("abs(datediff(now(),fec_fin))")))
                ->where('link_rewrite = "'.$linkRewrite.'"')
                );

        if(count($project)==0)
            $this->_redirect ( '/');
        else{
            $dbReward=new Application_Model_DbTable_Rewards();
            $rewards=$dbReward->fetchAll(
                $dbReward->select()
                ->where('id_proyecto = '.$project->id_proyecto.' AND subasta!="S"')
                );

            $rewardsSale=$dbReward->fetchAll(
                $dbReward->select()
                ->where('id_proyecto = '.$project->id_proyecto.' AND subasta="S"')
                );


            $dbSupport =new Application_Model_DbTable_Support();
            $supports=$dbSupport->fetchRow(
                    $dbSupport->select()
                    ->from("apoyo",
                            array('sum(apoyo) as sum_apoyo','count(apoyo) as count_apoyo', 'apoyo'))
                    ->where('id_proyecto = '.$project->id_proyecto.' AND approved="S"')
                    ->group('apoyo')
                    );
            
            $this->view->recaudado=isset($supports->sum_apoyo)?$supports->sum_apoyo:0;
            $this->view->numApoyos=isset($supports->count_apoyo)?$supports->count_apoyo:0;
            $this->view->rewards=$rewards;
            $this->view->rewardsSale=$rewardsSale;
            $this->view->project=$project;
            $this->view->porcentaje=($supports->apoyo/$project->importe_solicitado)*100;
            //$now=new Datetime();
            //$interval = $this->dateDifference(date(), $project->fec_fin);
            $this->view->days=$project->days;
            $this->view->image="/admin/".str_replace("/".$project->id_proyecto."/", "/".$project->id_proyecto."/420x/thumb_", $project->imagen);
            $this->view->url="http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
            $this->view->votos=$model->getVotes($project->id_proyecto);

        }
        
    }

    public function createAction(){
        $this->view->form=new Form_Project();
    }

    public function voteAction(){
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            $request = $this->getRequest ();

            if($request->isPost()){
                $post=$request->getPost();
                $data['valor']=$post['valor'];

                $model=new Model_Projects();

                $project=$model->fetchProjectByLinkRewrite($request->link);

                $data['id_proyecto']=$project['id_proyecto'];
                $data['id_usuario']=$auth->getIdentity()->id_usuario;
                $model->vote($data);
                echo json_encode($model->getVotes($data['id_proyecto']));
                die;


                
            }
        }

        
    }



}

