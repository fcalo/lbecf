<?php
/**
 * This is the UserForgot form.
 */

class Form_Forgot extends Zend_Form {
	public function init() {
		// set the method for the display form to POST
		$this->setMethod ( 'post' );

                $decorator = array(
                                'ViewHelper',
                                'Errors',
                                array(array('data' => 'HtmlTag'), array('tag' => 'span', 'class' => 'data')),
                                array('Label', array('tag' => 'div'),
                                array(array('row' => 'HtmlTag'), array('tag' => 'span')),
                            ));

		$this->addElement ( 'text', 'emailforgot', array ('decorators' => $decorator, 'label' => 'Enter your Email:', 'required' => true, 'filters' => array ('StringTrim' ), 'validators' => array ('EmailAddress' ) ) );

               
		// add the submit button
		$this->addElement ( 'submit', 'submit', 
                        array ('label' => 'Send'
                             ,'class'=>'btn-red')
                        );
	}
}

