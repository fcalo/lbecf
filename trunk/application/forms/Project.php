<?php
/**
 * This is the UserRegister form.
 */

class Form_Project extends Zend_Form {
	
	public function init() {
		
		$this->setMethod ( 'post' );

                $decorator = array(
                                'ViewHelper',
                                'Errors',
                                array(array('data' => 'HtmlTag'), array('tag' => 'span', 'class' => 'data')),
                                array('Label', array('tag' => 'div'),
                                array(array('row' => 'HtmlTag'), array('tag' => 'span')),
                            ));

                $this->addElement ( 'text', 'titulo', array ('decorators' => $decorator, 'label' => 'Proyecto:', 'filters' => array ('StringTrim' ),'required' => true ));
                $this->addElement ( 'text', 'breve_descripcion', array ('decorators' => $decorator, 'label' => 'Breve descripción:', 'filters' => array ('StringTrim' ),'required' => true ));

                $this->addElement ( 'textarea', 'descripcion',
                        array ('label' => 'Describe tu proyecto:', 'required' => true,'rows' => 4,'cols' => 60 )
                 );
                $this->addElement ( 'text', 'video_embed', array ('decorators' => $decorator, 'label' => 'Inserta vídeo(embed code):', 'filters' => array ('StringTrim' ) ));
                $this->addElement ( 'text', 'fecha', array ('decorators' => $decorator, 'label' => '¿Cuando vence?(dd/mm/yyyy):', 'filters' => array ('StringTrim' ),'required' => true ));
                $this->addElement ( 'text', 'importe', array ('decorators' => $decorator, 'label' => '¿Cuanto necesitas?:', 'filters' => array ('StringTrim' ),'required' => true ));

                $this->addElement ( 'text', 'recompensa', array ('decorators' => $decorator, 'label' => '¿Qué Ofreces?:', 'filters' => array ('StringTrim' ),'required' => true ));
                $this->addElement ( 'text', 'minimo', array ('decorators' => $decorator, 'label' => '¿A cambio de cuánto?:', 'filters' => array ('StringTrim' ),'required' => true ));
                /*$this->addElement ( 'checkbox', 'subasta', array ('decorators' => $decorator, 'label' => '¿Se subasta?:'));*/


                

  
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

