<?php

class SupportController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function pagoAction(){

        $config = Zend_Registry::get('config');
        $support = $this->getRequest()->getParam('support');
        $reward = $this->getRequest()->getParam('reward');
        $linkRewrite = $this->getRequest()->getParam('proyect');

        $Project=new Model_Projects();
        $project=$Project->fetchProjectByLinkRewrite($linkRewrite);

        $hostname='http://' . $this->getRequest ()->getHttpHost ();

        $paypalConfig['cancelUrl']=$hostname.$config->payment->paypal->return_cancel;
        $paypalConfig['returnUrl']=$hostname.$config->payment->paypal->return;
        $paypalConfig['paypalUrl']=$config->payment->paypal->paypal_url;
        $paypalConfig['notifyUrl']=$hostname.$config->payment->paypal->notify_url;
        $paypalConfig['bussines']=$hostname.$config->payment->paypal->bussines;

        $params['amount']=$support;
        $params['date']=$project->fec_fin;

        
        
        $paypal=new Zend_Paypal($paypalConfig);

        

        $preapprovalKey=$paypal->getPreapprovalKey($params);
        //Se guarda la clave

        var_dump($preapprovalKey);

        die("c");

        $Support=new Model_Supports();

        $data['id_proyecto']=$project->id_proyecto;
        $auth = Zend_Auth::getInstance ();
        $data['id_usuario_apoyo']=$auth->getIdentity()->id_usuario;
        $data['apoyo']=$support;
        $data['id_recompensa']=$reward;
        $data['fecha']=date("Y-m-d H:i:s");
        $data['preapproved_key']=$preapprovalKey;
        $Support->saveSupport($data);

        $this->_redirect($paypal->getUrlPreapproval($preapprovalKey));
    }

    public function okAction(){}

    public function koAction(){}

    public function ipnAction(){

         //TODO:
        //$this->getRequest()['status']


         foreach ($this->getRequest() as $key => $value) { $body .= "\n$key: $value"; }
         $f=fopen("/home/jkyljsco/temp/log.txt","w");
         fwrite($f, $body);
         fclose($f);
         var_dump($body);
         die;

    }

    public function indexAction(){}


}

