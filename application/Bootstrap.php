<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initRegistry()
    {
        
        $config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/application.ini' , 'production', array("allowModifications"=>true) );
        if (file_exists(APPLICATION_PATH . '/configs/local.ini')) {
            $lconfig = new Zend_Config_Ini( APPLICATION_PATH . '/configs/local.ini' , 'production' );
            $config->merge($lconfig);
            $config->setReadOnly();
        }

        Zend_Registry::set('config', $config);

    }

    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');

        ZendX_JQuery::enableView($view);

    }

    protected function _initAutoload()
    {

        $moduleLoader = new Zend_Application_Module_Autoloader(array(
                        'namespace' => '',
                        'basePath' => APPLICATION_PATH));

        return $moduleLoader;
    }


    protected function _initPlugins()
    {

        //Zend_Controller_Action_HelperBroker::addPath( APPLICATION_PATH .'/controllers/helpers');

        $front = Zend_Controller_Front::getInstance();

        //init the routes
        $router = $front->getRouter ();

        //set the language route url (the default also)
        $routeDefault = new Zend_Controller_Router_Route ( ':controller/:action/*', array ('controller' => 'index', 'action' => 'index') );
        //set the user profile route
        $routeAddProject = new Zend_Controller_Router_Route( '/event/crear/', array( 'controller' => 'project', 'action' => 'create') );
        $routeEditProject = new Zend_Controller_Router_Route( '/event/editar/:project', array( 'controller' => 'project', 'action' => 'edit') );
        $routeVoteProject = new Zend_Controller_Router_Route( '/event/voto/:link', array( 'controller' => 'project', 'action' => 'vote') );
        $routeVoteProposal = new Zend_Controller_Router_Route( '/event/voto-propuesta/:link', array( 'controller' => 'project', 'action' => 'voteProposal') );
        $routeCommentProject = new Zend_Controller_Router_Route( '/event/comentario/:link', array( 'controller' => 'project', 'action' => 'comment') );
        $routeCommentProposal = new Zend_Controller_Router_Route( '/event/comentario-propuesta/:link', array( 'controller' => 'project', 'action' => 'commentProposal') );
        $routeProject = new Zend_Controller_Router_Route( '/event/:project', array( 'controller' => 'project', 'action' => 'index') );
        $routeProjectAlert = new Zend_Controller_Router_Route( '/event/:project/alert/:msg', array( 'controller' => 'project', 'action' => 'index',) );
        $routeProjectConcurso = new Zend_Controller_Router_Route( '/event/:project/concurso', array( 'controller' => 'project', 'action' => 'index', 'concurso'=>1) );
        $routeProjectList = new Zend_Controller_Router_Route( '/events/:page/:category', array( 'controller' => 'project', 'action' => 'list', 'page'=>1, 'category'=>0) );
        
        //set the user profile route
        $routeProfile = new Zend_Controller_Router_Route( '/user/perfil/:username', array( 'controller' => 'user', 'action' => 'profile') );
        $routeMail = new Zend_Controller_Router_Route( '/user/mail/:username', array( 'controller' => 'user', 'action' => 'mail') );
        $routeOauth = new Zend_Controller_Router_Route( '/user/oauth/:step', array( 'controller' => 'user', 'action' => 'oauth') );
        $routeValidate = new Zend_Controller_Router_Route( '/user/validar/:token', array( 'controller' => 'user', 'action' => 'validate') );
        $routeDelete = new Zend_Controller_Router_Route( '/user/baja/', array( 'controller' => 'user', 'action' => 'delete') );
        $routeUser = new Zend_Controller_Router_Route( '/user/:action/*', array( 'controller' => 'user', 'action' => 'index') );
        $routeAviso= new Zend_Controller_Router_Route( '/legal/', array( 'controller' => 'static', 'action' => 'aviso') );
        $routeCondiciones = new Zend_Controller_Router_Route( '/terms/', array( 'controller' => 'static', 'action' => 'condiciones') );
        $routeContacto = new Zend_Controller_Router_Route( '/contact/', array( 'controller' => 'static', 'action' => 'contacto') );
        $routeFaq = new Zend_Controller_Router_Route( '/faq/', array( 'controller' => 'static', 'action' => 'faq') );
        $routeFunciona = new Zend_Controller_Router_Route( '/how/', array( 'controller' => 'static', 'action' => 'funciona') );
        $routePolitica = new Zend_Controller_Router_Route( '/policy/', array( 'controller' => 'static', 'action' => 'politica') );
        $routeSobre = new Zend_Controller_Router_Route( '/about/', array( 'controller' => 'static', 'action' => 'sobre') );
        $routePayment = new Zend_Controller_Router_Route( '/apoyo/pago/:proyect/:support/:reward/', array( 'controller' => 'support', 'action' => 'pago') );
        $routePayment2 = new Zend_Controller_Router_Route( '/apoyo/pago/:proyect/:support/:reward/:sponsor', array( 'controller' => 'support', 'action' => 'pago') );
        $routeCancelPayment = new Zend_Controller_Router_Route( '/apoyo/cancel/:preapproval_key', array( 'controller' => 'support', 'action' => 'cancel') );
        $routeSupport = new Zend_Controller_Router_Route( '/apoyo/:action/*', array( 'controller' => 'support', 'action' => 'index') );
        $routeConfirm = new Zend_Controller_Router_Route( '/apoyo/confirm/:support/', array( 'controller' => 'support', 'action' => 'confirm') );

        //set the api route
        //$routeApi = new Zend_Controller_Router_Route('/api/:action/*', array(  'controller' => 'api', 'action' => 'index') );

        $router->addRoute ( 'default', $routeDefault );//important, put the default route first!
        $router->addRoute ( 'project', $routeProject);
        $router->addRoute ( 'project_concurso', $routeProjectConcurso);
        $router->addRoute ( 'projects', $routeProjectList);
        $router->addRoute ( 'project_alert', $routeProjectAlert);
        $router->addRoute ( 'add_project', $routeAddProject);
        $router->addRoute ( 'edit_project', $routeEditProject);
        $router->addRoute ( 'vote_project', $routeVoteProject);
        $router->addRoute ( 'vote_proposal', $routeVoteProposal);
        $router->addRoute ( 'comment_project', $routeCommentProject);
        $router->addRoute ( 'comment_proposal', $routeCommentProposal);
        
        $router->addRoute ( 'user', $routeUser);
        $router->addRoute ( 'profile', $routeProfile);
        $router->addRoute ( 'mail', $routeMail);
        $router->addRoute ( 'oauth', $routeOauth);
        $router->addRoute ( 'validate', $routeValidate);
        $router->addRoute ( 'delete', $routeDelete);
        $router->addRoute ( 'aviso', $routeAviso);
        $router->addRoute ( 'condiciones', $routeCondiciones);
        $router->addRoute ( 'contacto', $routeContacto);
        $router->addRoute ( 'faq', $routeFaq);
        $router->addRoute ( 'funciona', $routeFunciona);
        $router->addRoute ( 'politica', $routePolitica);
        $router->addRoute ( 'sobre', $routeSobre);
        $router->addRoute ( 'support', $routeSupport );
        $router->addRoute ( 'confirm', $routeConfirm);
        $router->addRoute ( 'payment', $routePayment );
        $router->addRoute ( 'payment2', $routePayment2 );
        $router->addRoute ( 'cancelPayment', $routeCancelPayment );

        //set all routes
        $front->setRouter ( $router );

        return $front;
    }
}



