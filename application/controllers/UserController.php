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
                $this->_redirect ( '/usuario/perfil/'.$auth->getIdentity()->username );
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
                $this->_redirect("/usuario/perfil/".$auth->getIdentity()->username);
            }else{
                // Invalid credentials
                $form->setDescription('Usuario o contraseña incorrectos');
                $this->view->form = $form;
                return $this->render('login'); // re-render the login form
            }

        }
    }

    public function registerAction()
    {
        $this->view->headTitle()->append('Nuevo Usuario');
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
                    $view->error .= '<li>Las contraseñas no coinciden.</li>';
                }
                $view = $this->initView();
                //check agree tos and privacy
                if ($formulario['agree'] != '1' ){
                    $view->error .= '<li>Debe aceptar las condiciones de uso y la política de privacidad</li>';

                }
                $model = new Model_Users();


                //not allow to use the email as username
                if ( $formulario['email'] == $formulario['username']){
                    $view->error .= '<li>No puede usar de Nombre de Usuario el Email</li>';
                }

                //check user email and nick if exists
                if (!$model->checkUserEmail($formulario ['email'])){
                    $view->error .= '<li>Ya está dado de alta el Email en el sistema.</li>';

                }

                if (!$model->checkUsername ( $formulario ['username'] )){
                    $view->error .= '<li>El nombre de usuario no está libre.</li>';
                }

                if (!isset($view->error))
                {

                    // success: insert the new user on ddbb
                    //update the ddbb with new password
                    $data ['email'] = $formulario ['email'];
                    $data ['pass'] =  $formulario ['password1'] ;
                    $data ['username'] = $formulario ['username'];
                    $model->saveUser ( $data );

                    //once token generated by model save, now we need it to send to the user by email
                    $token = $model->getUserToken($formulario['email']);

                    //now lets send the validation token by email to confirm the user email
                    $hostname = 'http://' . $this->getRequest ()->getHttpHost ();

                    $mail = new Zend_Mail ( );
                    $link=$hostname  . '/usuario/validar/'  . $token;
                    $mail->setBodyHtml ( 'Por favor, pulsa el siguiente enlace para terminar el registro<br />'
                            .'<a href="'.$link.'">'.$link.'</a><br /><br />_______________________________<br />La Butaca Escarlata');
                    $mail->setFrom ( 'noresponder@labutacaescarlata.com', 'labutacaescarlata.com' );

                    $mail->addTo($formulario['email']);
                    $mail->setSubject ( $formulario ['username'].', confirma tu email');
                    $this->view->procesado=$mail->send();

                }
            }
            $this->view->form = $form;
        }
    }

     public function forgotAction()
    {
        $this->view->headTitle()->append($this->view->translate('¿Has olvidado tu contraseña?'));
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
                    $view->error = '<li>Email no registrado.</li>';

                } else{ // success: the email exists , so lets change the password and send to user by mail
                    //regenerate the token
                    $data['token'] = md5 ( uniqid ( rand (), 1 ) );
                    $model->updateUser($mailcheck['id_usuario'], $data);

                    //now lets send the validation token by email to confirm the user email
                    $hostname = 'http://' . $this->getRequest ()->getHttpHost ();

                    $mail = new Zend_Mail ('utf-8');
                    $link= $hostname .'/usuario/validar/'.$data['token'];
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
                    $this->_redirect ("{$config->oauth->facebook->siteUrl}?scope=user_location&client_id={$config->oauth->facebook->consumerKey}&redirect_uri={$config->oauth->facebook->callbackUrl}");
                    break;
                break;
            case "callback":
                    try {
                        $code = $this->getRequest()->getParam('code');
                        if ($code==null)
                        {
                            $error = $this->getRequest()->getParam('error');
                            $this->_redirect("/usuario/login/".$error);
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
                    }
                    catch (Exception $e)
                    {
                        //$this->_helper->_flashMessenger->addMessage ( sprintf($this->view->translate ( 'Sorry, we are experiencing technical problems with the %s service.' ), "Facebook") );
                        $this->_redirect("/usuario/login/technical-problems-facebook");
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
                    $data['email'] = $oauthuser->link;
                    $data['username'] = $username;
                    $model->saveUser ($data);

                    $user = $model->fetchUserByIdFacebook($oauthid);
                }

                // do the authentication
                $auth = Zend_Auth::getInstance ();
                $auth->getStorage ()->write ( (object)$user );

                $this->_redirect("/usuario/perfil/".$auth->getIdentity()->username);

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

                $this->_redirect ( '/usuario/perfil/'.$data ['username'] );

            } else{
                $this->view->error='Lo lamentamos, el token no existe o ya ha sido utilizado';
            }

        } else{
            $this->view->error='Lo lamentamos, la url no es valida o ha caducado';
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


    public function profileAction(){
        $auth=Zend_Auth::getInstance();
        $request = $this->getRequest ();
        if($auth->getIdentity()->username!=$request->username)
                $this->_redirect("/usuario/perfil/".$auth->getIdentity()->username);

        $this->view->user=$auth->getIdentity();
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

