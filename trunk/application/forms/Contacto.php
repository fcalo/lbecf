<?php

/**
 * This is the main Contact form.
 */

class Form_Contacto extends Zend_Form {

        public function init() {
                // set the method for the display form to POST
                $this->setMethod ( 'post' );

                // add an email element
                $this->addElement ( 'text', 'email', array ('label' => 'Email:', 'required' => true, 'filters' => array ('StringTrim' ), 'validators' => array ('EmailAddress' ) ) );

                $this->addElement ( 'textarea', 'message', 
                        array ('label' => 'Message:', 'validators' => array (array ('StringLength', false, array (0, 8000 ) ) ), 'required' => true,'rows' => 4,'cols' => 40 )

                 );

                $checkboxDecorator = array(
                                'ViewHelper',
                                'Errors',
                                array(array('data' => 'HtmlTag'), array('tag' => 'span', 'class' => 'element')),
                                array('Label', array('tag' => 'dt'),
                                array(array('row' => 'HtmlTag'), array('tag' => 'span')),
                            ));

                $this->addElement('checkbox', 'agree', array(
                    'decorators' => $checkboxDecorator,
                    'required' => true,
                    'checked' =>false
                    ));

                // add the submit button
                $this->addElement ( 'submit', 'submit', array (
                    'label' => 'Send',
                    'class' => 'btn-red') );
        }
}


