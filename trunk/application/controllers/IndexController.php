<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $users = new Application_Model_DbTable_Users();
        $this->view->users = $users->fetchAll();
        var_dump($this->view->users);
    }


}

