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


            $modelSupport=new Model_Supports();
            $supports=$modelSupport->fetchSupportsByProject($project->id_proyecto);
            
            $this->view->recaudado=isset($supports->sum_apoyo)?$supports->sum_apoyo:0;
            $this->view->numApoyos=isset($supports->count_apoyo)?$supports->count_apoyo:0;
            $this->view->rewards=$rewards;
            $this->view->rewardsSale=$rewardsSale;
            $this->view->project=$project;
            $this->view->porcentaje=($supports->sum_apoyo/$project->importe_solicitado)*100;
            //$now=new Datetime();
            //$interval = $this->dateDifference(date(), $project->fec_fin);
            $this->view->days=$project->days;
            $this->view->image="/admin/".str_replace("/".$project->id_proyecto."/", "/".$project->id_proyecto."/420x/thumb_", $project->imagen);
            $this->view->url="http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
            $this->view->votos=$model->getVotes($project->id_proyecto);

        }
        
    }

    public function createAction(){
        $auth=Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                $this->_redirect("/");
        $this->view->form=new Form_Project();

        
        $request = $this->getRequest ();
        

        if($request->isPost()){
            $form=$this->view->form;
            if (!$form->isValid($request->getPost())) {
                 // Invalid entries
                $this->view->error="<li>Revise todos los datos</li>";
                 return $this->render('create'); // re-render the login form
             }


             $model=new Model_Projects();
             $data=$form->getValues();

             if ($_FILES["imagen"]['name']!=""){
                 $a=explode("/",$data['fecha']);
                 $data['fec_fin']=$a[2]."-".$a[1]."-".$a[0];unset($data['fecha']);
                 $data['importe_solicitado']=$data['importe'];unset($data['importe']);

                 $data['link_rewrite']=Service_Urls::amigables($data['titulo']);
                 $data['id_usuario']=$auth->getIdentity()->id_usuario;

                 $idProject=$model->saveProject($data);

                 $path=$_SERVER['DOCUMENT_ROOT']."/admin/uploads/proyectos/".$idProject."/";
                 $helper=new View_Helper_Image();

                 $helper->ensurePath($path);
                 chmod($path,0777);
                 $path.=basename($_FILES["imagen"]['name']);

                 if(!move_uploaded_file($_FILES["imagen"]['tmp_name'], $path))
                        die("Ocurrio un error subiendo la imagen");
                 chmod($path,0777);

                 $user=new Model_Users();
                 if(!$model->uploadImage($path,$idProject))
                       die("error");

                 $this->view->msg="Gracias por crear tu proyecto.";

             }else{
                 $this->view->error="<li>Debe seleccionar una imagen para el proyecto</li>";
             }
        }

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
    public function commentAction(){
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            $request = $this->getRequest ();

            if($request->isPost()){
                $post=$request->getPost();
                $data['comentario']=$post['comentario'];

                $model=new Model_Projects();

                $project=$model->fetchProjectByLinkRewrite($request->link);

                $data['id_proyecto']=$project['id_proyecto'];
                $data['id_usuario']=$auth->getIdentity()->id_usuario;
                $model->comment($data);
                echo json_encode(array("username"=>$auth->getIdentity()->username,"txt"=>$data['comentario']));
                die;



            }
        }


    }



}

