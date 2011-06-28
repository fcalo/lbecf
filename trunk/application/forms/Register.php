<?php
/**
 * This is the UserRegister form.
 */

class Form_Register extends Zend_Form {
	
	public function init() {
		
		$this->setMethod ( 'post' );

                $decorator = array(
                                'ViewHelper',
                                'Errors',
                                array(array('data' => 'HtmlTag'), array('tag' => 'span', 'class' => 'data')),
                                array('Label', array('tag' => 'div'),
                                array(array('row' => 'HtmlTag'), array('tag' => 'span')),
                            ));

		$this->addElement ( 'text', 'email', array ('decorators' => $decorator, 'label' => 'email:', 'required' => true, 'filters' => array ('StringTrim' ), 'validators' => array ('EmailAddress' ) ) );

                $this->addElement ( 'text', 'username', array ('decorators' => $decorator, 'label' => 'nombre de usuario:', 'filters' => array ('StringTrim', 'StringToLower' ),
                    'validators' => array ('alnum', array ('regex', false, array ('/^[a-z]/i' ) ), array ('StringLength', false, array (3, 20 ) ) ), 'required' => true )
                );

                $this->addElement ( 'password', 'password1', array ('decorators' => $decorator, 'filters' => array ('StringTrim' ), 'validators' => array (array ('StringLength', false, array (5, 20 ) ) ), 'required' => true,
                    'label' => 'Contraseña:' ) );

                $this->addElement ( 'password', 'password2', array ('decorators' => $decorator, 'filters' => array ('StringTrim' ), 'validators' => array (array ('StringLength', false, array (5, 20 ) ) ), 'required' => true,
                    'label' => 'Repetir contraseña:' ) );

		/*$this->addElement ( 'captcha', 'captcha', array (
                    'label' => 'Please, insert the 5 characters shown:', 'required' => true,
                    'captcha' => array ('captcha' => 'Image', 'wordLen' => 5, 'height' => 50, 'width' => 160, 'gcfreq' => 50, 'timeout' => 300,
                     'font' => APPLICATION_PATH . '/configs/antigonimed.ttf',
                     'imgdir' => FOOFIND_PATH . '/public/images/captcha' ) ) );
                 * *
                 */

                $this->addElement('checkbox', 'agree', array(
                    'decorators' => $decorator,
                    'required' => true,
                    'checked' =>false
                    ));

  
                $this->addElement ( 'submit', 'submit',
                        array ('label' => 'Aceptar',
                            'class'=>'btn-red'));

                $fb = $this->addElement('button', 'fb', array(
                    'required' => false,
                    'ignore'   => true,
                    'label'    => '',
                    'class'=>'fb'
                ));



	}
}
