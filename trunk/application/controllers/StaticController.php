<?php

class StaticController extends Zend_Controller_Action
{


    public function avisoAction(){$this->view->text=$this->_getText(new Application_Model_DbTable_StaticAviso());}
    public function condicionesAction(){$this->view->text=$this->_getText(new Application_Model_DbTable_StaticCondiciones());}
    public function contactoAction(){}
    public function faqAction(){$this->view->text=$this->_getText(new Application_Model_DbTable_StaticFaq());}
    public function funcionaAction(){$this->view->text=$this->_getTexts(new Application_Model_DbTable_StaticFunciona());}
    public function politicaAction(){$this->view->text=$this->_getText(new Application_Model_DbTable_StaticPolitica());}
    public function sobreAction(){$this->view->text=$this->_getText(new Application_Model_DbTable_StaticSobre());}
    
    private function _getText($model){
        $data=$model->fetchRow($model->select());
        return $data['text'];
    }
    private function _getTexts($model){
        $data=$model->fetchRow($model->select());
        $rt=array();
        $rt[]=$data['text_1'];
        $rt[]=$data['text_2'];
        $rt[]=$data['text_3'];
        $rt[]=$data['text_4'];
        $rt[]=$data['text_5'];
        return $rt;
    }


}

