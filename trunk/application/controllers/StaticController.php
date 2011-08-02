<?php

class StaticController extends Zend_Controller_Action
{


    public function avisoAction(){$this->view->text=$this->_getText(new Application_Model_DbTable_StaticAviso());}
    public function condicionesAction(){$this->view->text=$this->_getText(new Application_Model_DbTable_StaticCondiciones());}
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
        $this->view->headScript()->appendFile( '/js/funciona.js');
        $rt[]=nl2br($data['text_1']);
        $rt[]=nl2br($data['text_2']);
        $rt[]=nl2br($data['text_3']);
        $rt[]=nl2br($data['text_4']);
        $rt[]=nl2br($data['text_5']);
        return $rt;
    }

    public function contactoAction(){

        $request = $this->getRequest ();
        $form = new Form_Contacto();


        // check to see if this action has been POST'ed to
        if ($this->getRequest()->isPost ()) {

                // now check to see if the form submitted exists, and
                // if the values passed in are valid for this form
                if ($form->isValid ( $request->getPost () )) {
                        //check agree tos and privacy
                        $view = $this->initView();

                        $view->error="";

                        if ( $this->_request->getPost ( 'agree' ) != '1'  ){
                            $view->error.= '<li>Por favor, Acepte la política de privacidad y condiciones de uso</li>';
                        } else {


                        // collect the data from the user
                        $f = new Zend_Filter_StripTags ( );
                        $email = $f->filter ( $this->_request->getPost ( 'email' ) );
                        $message = $f->filter ( $this->_request->getPost ( 'message' ) );
                        $message = str_replace("\n", "<br/>", $message);

                        $user_info .= $_SERVER ['REMOTE_ADDR'];
                        $user_info .= ' ' . $_SERVER ['HTTP_USER_AGENT'];

                        $mail = new Zend_Mail ('utf-8');
                        $body = $user_info.'<br/>'.$message;
                        $mail->setBodyHtml ( $body );
                        $mail->setFrom ( $email );
                        $mail->addTo ( 'contacto@labutacaescarlata.com' );

                        $mail->setSubject ( 'labutacaescarlata.com - contacto de ' . $email );
                        $view->procesado=($mail->send());
                }

                }
        }

        // assign the form to the view
        $this->view->form = $form;

    }


}

