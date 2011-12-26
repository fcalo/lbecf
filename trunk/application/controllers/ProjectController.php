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

        $msg = $this->getRequest()->getParam('msg');

        $linkRewrite=$request->project;

        //TODO:old
        $dbProject=new Application_Model_DbTable_Projects();

        $model=new Model_Projects();

        //->from(array("proyectos"), array("*","days"=>"abs(datediff(now(),p.fec_fin)"))
        $project=$dbProject->fetchRow(
                $dbProject->select()
                ->from(array("proyectos"), array("*","days"=>new Zend_Db_Expr("datediff(fec_fin,now())")))
                ->where('link_rewrite = "'.$linkRewrite.'" AND activo="S" AND fec_fin>now()')
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
            $this->view->closed=$project->days<1;
            $this->view->image="/admin/".str_replace("/".$project->id_proyecto."/", "/".$project->id_proyecto."/420x/thumb_", $project->imagen);
            $this->view->url="http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
            $this->view->votos=$model->getVotes($project->id_proyecto);
            $this->view->comments=$model->getComments($project->id_proyecto);
            $modelCollaborators=New Model_Collaborators();
            $this->view->collaborators=$modelCollaborators->fetchByProject($project->id_proyecto);
            $this->view->setConcurso=(isset($request->concurso) && $request->concurso==1);
            switch($msg){
                case 1:$this->view->msg="Código de patrocinador no válido";
            }

            $modelProposal=New Model_Proposals();
            

            $request = $this->getRequest ();
            if($request->isPost()){
                if($request->proposal!=""){
                    $data['id_usuario_propuesta']=$project->id_usuario;
                    $data['id_proyecto']=$project->id_proyecto;
                    $data['propuesta']=$request->proposal;

                    $idProposal=$modelProposal->saveProposal($data);
                    $this->view->proposalUpload=$idProposal;

                }
                if ($_FILES["attached"]['name']!=""){
                     $path=$_SERVER['DOCUMENT_ROOT']."/admin/uploads/propuesta/".$idProposal."/";
                     $helper=new View_Helper_Image();

                     $helper->ensurePath($path);
                     chmod($path,0777);
                     $path.=basename($_FILES["attached"]['name']);

                     if(!move_uploaded_file($_FILES["attached"]['tmp_name'], $path))
                            die("Ocurrio un error subiendo la imagen");
                     chmod($path,0777);

                     $a=explode("/admin",$path);
                     $data["adjunto"]=$a[1];
                     $modelProposal->updateProposal($idProposal,$data);

                }
            }

            $this->view->proposals=$modelProposal->fetchByProject($project->id_proyecto);
            //Carga los comentarios de las propuestas
            foreach($this->view->proposals as $key=>$proposal){
                $proposal['comments']=$modelProposal->getComments($proposal['id_propuesta']);
                $proposal['votes']=$modelProposal->getVotes($proposal['id_propuesta']);
                $this->view->proposals[$key]=$proposal;
            }

        }
        
    }
    public function listAction(){

        
        $in=(strtolower($_SERVER['REQUEST_URI'])=="/events")||(strtolower($_SERVER['REQUEST_URI'])=="/events/");

        $page = $this->_getParam('page', 1);
        $category = $this->_getParam('category', 0);
        

        $registrosXpagina = 8;

        $rangoPaginas = 10;

        $modelProyecto=new Model_Projects();
        $proyectos =$modelProyecto->fetchActives(null, $category);
        /*if(count($proyectos)<=1 && $in)
            $this->_redirect ( '/proyecto/el-desafio-escarlata');*/

        $paginador = Zend_Paginator::factory($proyectos);
        $paginador->setItemCountPerPage($registrosXpagina)
              ->setCurrentPageNumber($page)
              ->setPageRange($rangoPaginas);

        $modelCategorias=new Model_Categories();
        $this->view->categories=$modelCategorias->fetch();
        $this->view->idCategory=$category;
        $this->view->page=$page;
        $this->view->projects = $paginador;
    }

    

    public function createAction(){
        $auth=Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                $this->_redirect("/");
        $this->view->form=new Form_Project();
        $cat=New Model_Categories();
        $this->view->category=$cat->fetch();


        
        $request = $this->getRequest ();
        

        if($request->isPost()){
            $form=$this->view->form;
            if (!$form->isValid($request->getPost())) {
                 // Invalid entries
                $this->view->error="<li>review all data</li>";
                 return $this->render('create'); // re-render the login form
             }


             $model=new Model_Projects();
             $data=$form->getValues();

             if ($_FILES["imagen"]['name']!=""){
                 $a=explode("/",$data['fecha']);
                 $data['fec_fin']=$a[2]."-".$a[1]."-".$a[0];unset($data['fecha']);
                 $a=explode("/",$data['fecha_evento']);
                 $data['fecha']=$a[2]."-".$a[1]."-".$a[0];unset($data['fecha_evento']);
                 $data['importe_solicitado']=$data['importe'];unset($data['importe']);

                 $data['link_rewrite']=Service_Urls::amigables($data['titulo']);
                 $data['id_usuario']=$auth->getIdentity()->id_usuario;
                 $data['id_categoria']=$_POST['id_categoria'];

                 $recompensas=$_POST['recompensa'];unset($data['recompensa']);
                 $minimos=$_POST['minimo'];unset($data['minimo']);
                 
                 
                 $idProject=$model->saveProject($data);

                 $modelReward=new Model_Rewards();

                 foreach($recompensas as $key=>$recompensa){
                     if($recompensa!="" && $minimos[$key]!=""){
                         $dataRecompensa=array();
                         $dataRecompensa['id_proyecto']=$idProject;
                         $dataRecompensa['apoyo_minimo']=$minimos[$key];
                         
                         $dataRecompensa['subasta']=isset($_POST['subasta_'.($key+1)])?"S":"N";
                         $dataRecompensa['recompensa']=$recompensa;
                         $modelReward->save($dataRecompensa);
                     }
                 }

                 $path=$_SERVER['DOCUMENT_ROOT']."/admin/uploads/proyectos/".$idProject."/";
                 $helper=new View_Helper_Image();

                 $helper->ensurePath($path);
                 chmod($path,0777);
                 $path.=basename($_FILES["imagen"]['name']);

                 if(!move_uploaded_file($_FILES["imagen"]['tmp_name'], $path))
                        die("an error occurred in upload photo");
                 chmod($path,0777);

                 $user=new Model_Users();
                 if(!$model->uploadImage($path,$idProject))
                       die("error");

                 $this->view->msg="thank you for create your event.";

             }else{
                 $this->view->error="<li>You should select a photo for the event</li>";
             }
        }

    }

    public function editAction(){
        $auth=Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                $this->_redirect("/");
        $this->view->form=new Form_Project();
        $cat=New Model_Categories();
        $this->view->category=$cat->fetch();
        
        $linkRewrite=$this->getRequest()->project;

        $request = $this->getRequest ();

        $modelProject=new Model_Projects();
        $data=$modelProject->fetchProjectByLinkRewrite($linkRewrite);
        if($data['activo']=="S"){
            //No se puede editar
            $this->view->editable=false;
        }else{
            $this->view->editable=true;
            if($request->isPost()){
                $idProject=$data['id_proyecto'];
                $form=$this->view->form;
                if (!$form->isValid($request->getPost())) {
                     // Invalid entries
                    $this->view->error="<li>Reqiev all data</li>";
                    return $this->render('create'); // re-render
                }

                $model=new Model_Projects();
                 $data=$form->getValues();


                 $a=explode("/",$data['fecha']);
                 $data['fec_fin']=$a[2]."-".$a[1]."-".$a[0];unset($data['fecha']);
                 $a=explode("/",$data['fecha_evento']);
                 $data['fecha']=$a[2]."-".$a[1]."-".$a[0];unset($data['fecha_evento']);
                 $data['importe_solicitado']=$data['importe'];unset($data['importe']);

                 $data['link_rewrite']=Service_Urls::amigables($data['titulo']);
                 $data['id_usuario']=$auth->getIdentity()->id_usuario;
                 $data['id_categoria']=$_POST['id_categoria'];

                 $recompensas=$_POST['recompensa'];unset($data['recompensa']);
                 $minimos=$_POST['minimo'];unset($data['minimo']);

                $model->updateProject($idProject, $data);

                 $modelReward=new Model_Rewards();

                 $clean=true;
                 foreach($recompensas as $key=>$recompensa){
                     if($recompensa!="" && $minimos[$key]!=""){
                         $dataRecompensa=array();
                         $dataRecompensa['id_proyecto']=$idProject;
                         $dataRecompensa['apoyo_minimo']=$minimos[$key];

                         $dataRecompensa['subasta']=isset($_POST['subasta_'.($key+1)])?"S":"N";
                         $dataRecompensa['recompensa']=$recompensa;
                         $modelReward->save($dataRecompensa, $clean);
                         $clean=false;
                     }
                 }
                 if ($_FILES["imagen"]['name']!=""){
                     $path=$_SERVER['DOCUMENT_ROOT']."/admin/uploads/proyectos/".$idProject."/";
                     $helper=new View_Helper_Image();

                     $helper->ensurePath($path);
                     chmod($path,0777);
                     $path.=basename($_FILES["imagen"]['name']);

                     if(!move_uploaded_file($_FILES["imagen"]['tmp_name'], $path))
                            die("an error occurred in upload photo");
                     chmod($path,0777);

                     $user=new Model_Users();
                     if(!$model->uploadImage($path,$idProject))
                           die("error");
                 }

                 $this->view->msg="Event modified.";

            }else{
                foreach($data as $key=>$value){
                    $d[$key]=$value;
                }
                $a=explode("-",$d['fec_fin']);
                $d['fecha']=$a[2]."/".$a[1]."/".$a[0];
                $d['importe']=round($d['importe_solicitado']);
                $this->view->data=$d;
                $this->view->form->populate($d);

                $modelRewards=new Model_Rewards();
                $this->view->rewards=$modelRewards->fetchByIdProject($d['id_proyecto']);
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
    public function voteproposalAction(){
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            $request = $this->getRequest ();

            if($request->isPost()){
                $post=$request->getPost();
                $data['valor']=$post['valor'];
                $data['id_propuesta']=$post['propuesta'];

                $model=new Model_Projects();

                $data['id_proyecto']=$project['id_proyecto'];
                $data['id_usuario']=$auth->getIdentity()->id_usuario;
                $model->voteProposal($data);
                $modelProposal=new Model_Proposals();
                echo json_encode($modelProposal->getVotes($data['id_propuesta']));
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
                $data['comentario']=htmlentities(utf8_decode($post['comentario']));

                $model=new Model_Projects();

                $project=$model->fetchProjectByLinkRewrite($request->link);

                $data['id_proyecto']=$project['id_proyecto'];
                $data['id_usuario']=$auth->getIdentity()->id_usuario;
                $helper=new View_Helper_Image();
                $modelUser=new Model_Users();
                $user=$modelUser->fetchUser($auth->getIdentity()->id_usuario);
                $imagen=$helper->image($auth->getIdentity()->id_usuario, $user['imagen']) ;
                $model->comment($data);
                echo json_encode(array("username"=>$auth->getIdentity()->username,"txt"=>$data['comentario'],"imagen"=>$imagen));
                die;



            }
        }


    }

    public function commentproposalAction(){
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            $request = $this->getRequest ();

            if($request->isPost()){
                $post=$request->getPost();
                $data['comentario']=htmlentities(utf8_decode($post['comentario']));
                $data['id_propuesta']=$post['propuesta'];

                $model=new Model_Projects();

                $data['id_usuario']=$auth->getIdentity()->id_usuario;
                $helper=new View_Helper_Image();
                $modelUser=new Model_Users();
                $user=$modelUser->fetchUser($auth->getIdentity()->id_usuario);
                $imagen=$helper->image($auth->getIdentity()->id_usuario, $user['imagen']) ;
                $model->commentProposal($data);
                echo json_encode(array("propuesta"=>$post['propuesta'], "username"=>$auth->getIdentity()->username,"txt"=>$data['comentario'],"imagen"=>$imagen));
                die;



            }
        }


    }



}

