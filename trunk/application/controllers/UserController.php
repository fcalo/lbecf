<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {

        /* Initialize action controller here */
        $this->view->headScript()->appendFile( '/js/user.js');
    }
    
    public function indexAction()
    {
        if(!Zend_Auth::getInstance()->hasIdentity())
                $this->_helper->redirector('login');
    }
    public function loginAction(){

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
                $this->_redirect ( '/user/perfil/'.$auth->getIdentity()->username );
        }

        $request = $this->getRequest();

        
        // Get our form and validate it
        $form = $this->getForm();

        $this->view->form = $form;
        if ($request->isPost ()){
            if (!$form->isValid($request->getPost())) {
                // Invalid entries
                $this->view->form = $form;
                return $this->render('login'); // re-render the login form
            }
            $data=$form->getValues();

            $model = new Model_Users();
            $checkuser  = $model->checkUserLogin($data['user'],$data['password']);

            if ( $checkuser  ){
                // success: store database row to auth's storage
                // system. (Not the password though!)
                unset ( $checkuser['pass'] );
                $auth->getStorage ()->write ( (object)$checkuser );
                $this->_redirect("/user/perfil/".$auth->getIdentity()->username);
            }else{
                // Invalid credentials
                $form->setDescription('incorrect user or password');
                $this->view->form = $form;
                return $this->render('login'); // re-render the login form
            }

        }
    }

    public function registerAction()
    {
        $this->view->headTitle()->append('New user');
        $request = $this->getRequest ();
        $form = $this->getFormRegister();
        $this->view->form=$form;


        if ($this->getRequest ()->isPost ()){

            if ($form->isValid ( $request->getPost () ))
            {
                $formulario = $form->getValues ();

                 //check 2 passwords matches
                if ($formulario['password1']!= $formulario['password2'] )
                {
                    $view = $this->initView();
                    $view->error .= '<li>the passwords doesn\'t match.</li>';
                }
                $view = $this->initView();
                //check agree tos and privacy
                if ($formulario['agree'] != '1' ){
                    $view->error .= '<li>You must agree to the terms of use</li>';

                }
                $model = new Model_Users();


                //not allow to use the email as username
                if ( $formulario['email'] == $formulario['username']){
                    $view->error .= '<li>The username and the email must be different</li>';
                }

                //check user email and nick if exists
                if (!$model->checkUserEmail($formulario ['email'])){
                    $view->error .= '<li>Email is already in the system.</li>';

                }

                if (!$model->checkUsername ( $formulario ['username'] )){
                    $view->error .= '<li>The username is taken.</li>';
                }

                if($formulario ['patrocinador']!="" && !$model->existsSponsor($formulario ['patrocinador'])){
                        $view->error .= '<li>Wrong sponsor code.</li>';
                }

                if (!isset($view->error))
                {

                    // success: insert the new user on ddbb
                    //update the ddbb with new password
                    $data ['email'] = $formulario ['email'];
                    $data ['pass'] =  $formulario ['password1'] ;
                    $data ['username'] = $formulario ['username'];
                    if($formulario ['patrocinador']!="")
                        $data ['cod_patrocinio'] = $formulario ['patrocinador'];
                    $model->saveUser ( $data );

                    //once token generated by model save, now we need it to send to the user by email
                    $token = $model->getUserToken($formulario['email']);

                    //now lets send the validation token by email to confirm the user email
                    $hostname = 'http://' . $this->getRequest ()->getHttpHost ();

                    $mail = new Zend_Mail ( );
                    $link=$hostname  . '/user/validar/'  . $token;
                    $mail->setBodyHtml ( 'Please click the link to complete registration<br />'
                            .'<a href="'.$link.'">'.$link.'</a><br /><br />_______________________________<br />Rocking Red Ticket');
                    $mail->setFrom ( 'noresponder@rockingredticket.com', 'rockingredticket.com' );

                    $mail->addTo($formulario['email']);
                    $mail->setSubject ( $formulario ['username'].', confirm your email');
                    $this->view->procesado=$mail->send();

                }
            }
            $this->view->form = $form;
        }
    }

     public function forgotAction()
    {
        $this->view->headTitle()->append($this->view->translate('Forgot password?'));
        $request = $this->getRequest ();
        $form = $this->getFormForgot();

        if ($this->getRequest ()->isPost ())
        {
            
            if ($form->isValid ( $request->getPost () ))
            {
                // collect the data from the form
                $f = new Zend_Filter_StripTags ( );
                $email = $f->filter ( $this->_request->getPost ( 'emailforgot' ) );
                $model = new Model_Users();
                $mailcheck = $model->fetchUserByEmail( $email );

                $view = $this->initView ();
                if ($mailcheck == NULL){
                    // failure: email does not exists on ddbb
                    $view->error = '<li>Email not found.</li>';

                } else{ // success: the email exists , so lets change the password and send to user by mail
                    //regenerate the token
                    $data['token'] = md5 ( uniqid ( rand (), 1 ) );
                    $model->updateUser($mailcheck['id_usuario'], $data);

                    //now lets send the validation token by email to confirm the user email
                    $hostname = 'http://' . $this->getRequest ()->getHttpHost ();

                    $mail = new Zend_Mail ('utf-8');
                    $link= $hostname .'/user/validar/'.$data['token'];
                    $mail->setBodyHtml ( 'Has solicitado tus datos de conexión de La Butaca Escarlata<br />'
                            .'<a href="'.$link.'">'.$link.'</a>'.
                            '<br /><br />'.
                            'En otro caso, ignora el mensaje'.
                            '<br />____<br />La Butaca Escarlata' );
                    $mail->setFrom ( 'noreply@labutacaescarlata.com', 'labutacaescarlata.com' );

                    $mail->addTo($mailcheck ['email']);
                    $mail->setSubject ( $mailcheck ['username'] . ', Reestablece tu acceso a La Butaca Escarlata');
                    if($mail->send()){
                        $this->view->procesado=true;
                    }

                }

            }
        }
        $this->view->form = $form;

    }

    public function oauthAction()
    {
        $config = Zend_Registry::get('config');
        $step = $this->getRequest()->getParam('step');

        switch($step) {
            case "login":
                    $this->_redirect ("{$config->oauth->facebook->siteUrl}?scope=email&client_id={$config->oauth->facebook->consumerKey}&redirect_uri={$config->oauth->facebook->callbackUrl}");
                    break;
                break;
            case "callback":
                    try {
                        $code = $this->getRequest()->getParam('code');
                        if ($code==null)
                        {
                            $error = $this->getRequest()->getParam('error');
                            $this->_redirect("/user/login/".$error);
                        }

                        $httpconf = array('adapter' => 'Zend_Http_Client_Adapter_Socket', 'ssltransport' => 'tls');
                        $access_url = "{$config->oauth->facebook->accessUrl}?client_id={$config->oauth->facebook->consumerKey}&redirect_uri=".urlencode($config->oauth->facebook->callbackUrl)."&client_secret={$config->oauth->facebook->consumerSecret}&code=".urlencode($code);
                        $httpclient = new Zend_Http_Client($access_url, $httpconf);

                        $response = $httpclient->request();
                        parse_str($response->getBody(),$access_code);
                        $httpclient = new Zend_Http_Client($config->oauth->facebook->queryUrl."?".http_build_query($access_code), $httpconf);
                        $response = $httpclient->request();

                        $oauthuser = json_decode($response->getBody());

                        if (strstr($oauthuser->link, "profile.php?id=")===false && ($username = strrchr($oauthuser->link, "/"))!==false )
                        {
                            $username = substr($username, 1);
                        } else {
                            $username = $oauthuser->name;
                        }

                        $oauthid = $oauthuser->id;
                        //Imagen
                        $httpconf = array('adapter' => 'Zend_Http_Client_Adapter_Socket', 'ssltransport' => 'tls');
                        $access_url = $oauthuser->link;
                        $httpclient = new Zend_Http_Client($access_url, $httpconf);

                        $response = $httpclient->request();
                        parse_str($response->getBody(),$access_code);

                        $a=explode("profile_pic", $response->getBody());
                        $b=explode('src=', $a[3]);
                        $c=explode('alt',$b[1]);

                        $image=trim(str_replace("_n","_q",str_replace("\\","",str_replace("\"","",$c[0]))));

                        //TODO: Crear copia

                    }
                    catch (Exception $e)
                    {
                        //$this->_helper->_flashMessenger->addMessage ( sprintf($this->view->translate ( 'Sorry, we are experiencing technical problems with the %s service.' ), "Facebook") );
                        $this->_redirect("/user/login/technical-problems-facebook");
                    }

                $model = new Model_Users();

                $user = $model->fetchUserByIdFacebook($oauthid);

                if ($user == null){
                    $usernamepre = $username = $this->adapt_username($username);
                    $usernamecount = 1;
                    while (isset($model->checkusers[$username]))
                    {
                        $username = $usernamepre."_".$usernamecount;
                        $usernamecount++;
                    }
                    $data['id_facebook'] = $oauthid;
                    $data['activo'] = "S";
                    $data['email'] = $oauthuser->email;
                    $data['username'] = $username;
                    $model->saveUser ($data);
                    $user = $model->fetchUserByIdFacebook($oauthid);

                    //imagen
                    $httpconf = array('adapter' => 'Zend_Http_Client_Adapter_Socket', 'ssltransport' => 'tls');
                    $access_url = $image;
                    $httpclient = new Zend_Http_Client($access_url, $httpconf);

                    $response = $httpclient->request();
                    parse_str($response->getBody(),$access_code);

                    $path=$_SERVER['DOCUMENT_ROOT']."/admin/uploads/user/".$user->id_usuario."/";

                    
                    $helper=new View_Helper_Image();

                    $helper->ensurePath($path);
                    chmod($path,0777);
                 
                    $path.=basename($image);

                    $f=fopen($path,"w");
                    fwrite($f, $response->getBody());
                    fclose($f);

                    chmod($path,0777);

                    if(!$model->uploadImage($path,$user['id_usuario']))
                        die("error");

                    $user = $model->fetchUserByIdFacebook($oauthid);
                }

                // do the authentication
                $auth = Zend_Auth::getInstance ();
                $auth->getStorage ()->write ( (object)$user );

                $this->_redirect("/user/perfil/".$auth->getIdentity()->username);

        }

    }
    public function validateAction()
    {
        // Do not attempt to render a view
        $token = $this->_request->getParam ( 'token' ); //the token

        if (! is_null ( $token ))
        {
            //lets check this token against ddbb
            $model = new Model_Users();
            $validatetoken = $model->fetchUserByToken( $token );

            if ($validatetoken !== NULL)
            {

                //first kill previous session or data from client
                //kill the user logged in (if exists)
                Zend_Auth::getInstance ()->clearIdentity ();

                //update the active status to 1 of the user
                $data ['activo'] = "S";
                $data ['username'] = $validatetoken ['username'];
                //reset the token
                $data['token'] = NULL;

                $model->updateUser( $validatetoken ['id_usuario'] , $data);

                //LETS OPEN THE GATE!
                //update the auth data stored
                $data = $model->fetchUserByUsername( $validatetoken ['username'] );
                $auth = Zend_Auth::getInstance ();

                $auth->getStorage()->write( (object)$data);

                $this->_redirect ( '/user/perfil/'.$data ['username'] );

            } else{
                $this->view->error='Sorry, the token does not exist or has expired';
            }

        } else{
            $this->view->error='Sorry, the url is not valid or has expired';
        }
    }

    public function deleteAction()
    {

        $auth=Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
                $this->_redirect("/");

        $user=$auth->getIdentity();
        $model = new Model_Users();
        $model->deleteUser($user['id_usuario']);
        
        $auth->clearIdentity ();
    }

    public function mailAction(){
        $auth=Zend_Auth::getInstance();
        $request = $this->getRequest ();
        if($auth->getIdentity()->username!=$request->username){
                $this->_redirect("/");
        }else{
            $messages=new Model_Messages();
            if($request->isPost()){
                $data['id_usuario_remitente']=$auth->getIdentity()->id_usuario;
                $data['id_usuario_receptor']=$_POST['id_usuario'];
                $data['asunto']=$_POST['asunto'];
                $data['mensaje']=$_POST['mensaje'];
                $message=new Model_Messages();
                $modelUser=new Model_Users();
                $remitente=$modelUser->fetchUser($data['id_usuario_remitente']);
                $receptor=$modelUser->fetchUser($data['id_usuario_receptor']);
                $message->save($data, true, $remitente, $receptor);

            }
            $messages->setIdUser($auth->getIdentity()->id_usuario);
            $this->view->messagesOut=$messages->fetchOut();
            $this->view->messagesIn=$messages->fetchIn();
            $this->view->user=$auth->getIdentity();


        }

    }

    public function profileAction(){
        $auth=Zend_Auth::getInstance();
        $request = $this->getRequest ();
        if($auth->getIdentity()->username!=$request->username){
                $self=false;
                //$this->_redirect("/user/perfil/".$auth->getIdentity()->username);
                $modelUser=new Model_Users();
                $this->view->user=$modelUser->fetchUserByUsername($request->username);
                $this->view->logged=($auth->getIdentity()->username!="");
        }else{
            $self=true;
            $this->view->user=$auth->getIdentity();
        }
        
        
        if($request->isPost()){
            if($self){
                //Cambio de imagen de perfil
                 if ($_FILES["imagen"]['name']!=""){
                     $path=$_SERVER['DOCUMENT_ROOT']."/admin/uploads/user/".$this->view->user->id_usuario."/";
                     $helper=new View_Helper_Image();

                     $helper->ensurePath($path);
                     chmod($path,0777);
                     $path.=basename($_FILES["imagen"]['name']);

                     if(!move_uploaded_file($_FILES["imagen"]['tmp_name'], $path))
                            die("an error occurred in upload photo");
                     chmod($path,0777);

                     $user=new Model_Users();
                     if(!$user->uploadImage($path,$this->view->user->id_usuario))
                           die("error");

                     $u=$user->fetchUser($this->view->user->id_usuario);

                     unset($u['pass']);

                    $auth->clearIdentity ();
                    $auth->getStorage ()->write ( (object)$u );
                 }
            }else{
                //Mensajeria interna
                $data['id_usuario_remitente']=$auth->getIdentity()->id_usuario;
                $data['id_usuario_receptor']=$this->view->user->id_usuario;
                $data['asunto']=$_POST['asunto'];
                $data['mensaje']=$_POST['mensaje'];
                $message=new Model_Messages();
                $modelUser=new Model_Users();
                $remitente=$modelUser->fetchUser($data['id_usuario_remitente']);
                $receptor=$modelUser->fetchUser($data['id_usuario_receptor']);
                $message->save($data, true, $remitente, $receptor);
                

            }
        }

        


        $modelSupport=new Model_Supports();
        $this->view->supports=$modelSupport->fetchSupportsByUser($this->view->user->id_usuario);

        $modelUser=new Model_Users();
        $this->view->comments=$modelUser->getComments($this->view->user->id_usuario);

        if($self){
            $auth->getStorage ()->write ( (object)$modelUser->fetchUser($this->view->user->id_usuario) );
            $this->view->user=$auth->getIdentity();
        }

        $modelProposal=new Model_Proposals();
        $this->view->proposals=$modelProposal->fetchByUser($this->view->user->id_usuario);

        $modelProjects=new Model_Projects();
        $this->view->projects=$modelProjects->fetchByUser($this->view->user->id_usuario);

        $this->view->proposalsComments=$modelProposal->getCommentsByUser($this->view->user->id_usuario);

        $this->view->self=$self;
        
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect("/");
    }

    private function getAuthAdapter(array $params)
    {
        // Leaving this to the developer...
        // Makes the assumption that the constructor takes an array of
        // parameters which it then uses as credentials to verify identity.
        // Our form, of course, will just pass the parameters 'username'
        // and 'password'.
    }
    private function getForm()
    {
        return new Form_Login(array(
            'method' => 'post',
        ));
    }
    private function getFormRegister(){
        return new Form_Register(array(
            'method' => 'post',
        ));
    }
    private function getFormForgot(){
        return new Form_Forgot(array(
            'method' => 'post',
        ));
    }
    private function adapt_username($username) {
        $username = preg_replace('/[^a-z0-9]/', '', strtolower($username));
        if (is_numeric(substr($username,0,1))) $username = "u$username";
        if (strlen($username)<3) $username .= "Foofy";
        return substr($username, 0, 20);
    }


}

