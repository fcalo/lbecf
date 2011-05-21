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
        // action body
        //$users = new Application_Model_DbTable_Users();
        //$this->view->users = $users->fetchAll();
    }


}

