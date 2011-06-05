<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
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
        $routeDefault = new Zend_Controller_Router_Route ( ':controller/:action/*', array ('controller' => 'index', 'action' => 'index', 'module' =>'default' ) );

        //set the download file page route
        //$routeDownload = new Zend_Controller_Router_Route( ':language/download/:id/*', array( 'language' => null, 'controller' => 'download', 'action' => 'file') );

        //set the vote page route
        //$routeVote = new Zend_Controller_Router_Route( ':language/vote/:action/:id/:type/*', array( 'language' => null, 'controller' => 'vote', 'action' => 'file') );

        //set the user profile route
        $routeProject = new Zend_Controller_Router_Route( '/proyecto/:project', array( 'controller' => 'project', 'action' => 'index') );

        //set the api route
        //$routeApi = new Zend_Controller_Router_Route('/api/:action/*', array(  'controller' => 'api', 'action' => 'index') );

        $router->addRoute ( 'default', $routeDefault );//important, put the default route first!
        $router->addRoute ( 'project', $routeProject);

        //set all routes
        $front->setRouter ( $router );

        return $front;
    }
}

