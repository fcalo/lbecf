<?php
class Form_Login extends Zend_Form
{
    public function init()
    {
        $username = $this->addElement('text', 'user', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(3, 20)),
            ),
            'required'   => true,
            'label'      => 'Usuario:',
        ));

        $password = $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 20)),
            ),
            'required'   => true,
            'label'      => 'Contraseña:',
        ));

        $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Acceder',
            'class'=>'btn-red'
        ));
        $fb = $this->addElement('button', 'fb', array(
            'required' => false,
            'ignore'   => true,
            'label'    => '',
            'class'=>'fb'
        ));

        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
}
 

?>