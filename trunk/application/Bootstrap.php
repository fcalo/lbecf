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
        $routeProject = new Zend_Controller_Router_Route( '/proyecto/:project', array( 'controller' => 'project', 'action' => 'index') );
        //set the user profile route
        $routeProfile = new Zend_Controller_Router_Route( '/usuario/perfil/:username', array( 'controller' => 'user', 'action' => 'profile') );
        $routeOauth = new Zend_Controller_Router_Route( '/usuario/oauth/:step', array( 'controller' => 'user', 'action' => 'oauth') );
        $routeUser = new Zend_Controller_Router_Route( '/usuario/:action/*', array( 'controller' => 'user', 'action' => 'index') );
        $routeAviso= new Zend_Controller_Router_Route( '/aviso/', array( 'controller' => 'static', 'action' => 'aviso') );
        $routeCondiciones = new Zend_Controller_Router_Route( '/condiciones/', array( 'controller' => 'static', 'action' => 'condiciones') );
        $routeContacto = new Zend_Controller_Router_Route( '/contacto/', array( 'controller' => 'static', 'action' => 'contacto') );
        $routeFaq = new Zend_Controller_Router_Route( '/faq/', array( 'controller' => 'static', 'action' => 'faq') );
        $routeFunciona = new Zend_Controller_Router_Route( '/funciona/', array( 'controller' => 'static', 'action' => 'funciona') );
        $routePolitica = new Zend_Controller_Router_Route( '/politica/', array( 'controller' => 'static', 'action' => 'politica') );
        $routeSobre = new Zend_Controller_Router_Route( '/sobre/', array( 'controller' => 'static', 'action' => 'sobre') );

        //set the api route
        //$routeApi = new Zend_Controller_Router_Route('/api/:action/*', array(  'controller' => 'api', 'action' => 'index') );

        $router->addRoute ( 'default', $routeDefault );//important, put the default route first!
        $router->addRoute ( 'project', $routeProject);
        
        $router->addRoute ( 'user', $routeUser);
        $router->addRoute ( 'profile', $routeProfile);
        $router->addRoute ( 'oauth', $routeOauth);
        $router->addRoute ( 'aviso', $routeAviso);
        $router->addRoute ( 'condiciones', $routeCondiciones);
        $router->addRoute ( 'contacto', $routeContacto);
        $router->addRoute ( 'faq', $routeFaq);
        $router->addRoute ( 'funciona', $routeFunciona);
        $router->addRoute ( 'politica', $routePolitica);
        $router->addRoute ( 'sobre', $routeSobre);

        //set all routes
        $front->setRouter ( $router );

        return $front;
    }
}



