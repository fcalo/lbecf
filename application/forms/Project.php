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

                $this->addElement ( 'text', 'titulo', array ('decorators' => $decorator, 'label' => 'Title:', 'filters' => array ('StringTrim' ),'required' => true ));

                $this->addElement ( 'text', 'ciudad', array ('decorators' => $decorator, 'label' => 'City:', 'filters' => array ('StringTrim' ),'required' => true ));
                $this->addElement ( 'text', 'fecha_evento', array ('decorators' => $decorator, 'label' => 'When?(dd/mm/yyyy):', 'filters' => array ('StringTrim' ),'required' => true ));

                $this->addElement ( 'text', 'breve_descripcion', array ('decorators' => $decorator, 'label' => 'Brief introduction:', 'filters' => array ('StringTrim' ),'required' => true ));

                $this->addElement ( 'textarea', 'descripcion',
                        array ('label' => 'Describe your project:', 'required' => true,'rows' => 4,'cols' => 60 )
                 );
                $this->addElement ( 'text', 'video_embed', array ('decorators' => $decorator, 'label' => 'Insert video(embed code):', 'filters' => array ('StringTrim' ) ));
                $this->addElement ( 'text', 'fecha', array ('decorators' => $decorator, 'label' => 'Deadline?(dd/mm/yyyy):', 'filters' => array ('StringTrim' ),'required' => true ));
                $this->addElement ( 'text', 'importe', array ('decorators' => $decorator, 'label' => 'Sum of money?:', 'filters' => array ('StringTrim' ),'required' => true ));

                $this->addElement ( 'text', 'recompensa', array ('decorators' => $decorator, 'label' => 'What do you offer?:', 'filters' => array ('StringTrim' ),'required' => true ));
                $this->addElement ( 'text', 'minimo', array ('decorators' => $decorator, 'label' => 'Price?:', 'filters' => array ('StringTrim' ),'required' => true ));
                /*$this->addElement ( 'checkbox', 'subasta', array ('decorators' => $decorator, 'label' => '¿Se subasta?:'));*/


                $this->addElement ( 'text', 'titulo_concurso', array ('decorators' => $decorator, 'label' => 'Title contest(Optional):', 'filters' => array ('StringTrim' ),'required' => false ));
                $this->addElement ( 'textarea', 'descripcion_concurso',
                        array ('label' => 'Describe your contest(Optional):', 'required' => false,'rows' => 4,'cols' => 60 )
                 );

                $this->addElement ( 'text', 'colaborador', array ('decorators' => $decorator, 'label' => 'Collaborator:', 'filters' => array ('StringTrim' ),'required' => true ));
                $this->addElement ( 'text', 'contacto', array ('decorators' => $decorator, 'label' => 'Contact:', 'filters' => array ('StringTrim' ),'required' => true ));
                $this->addElement ( 'textarea', 'descripcion_colaborador',
                        array ('label' => 'Describes the collaboration were looking:', 'required' => false,'rows' => 4,'cols' => 60 )
                 );


                

  
                $this->addElement ( 'submit', 'submit',
                        array ('label' => 'Submit',
                            'class'=>'btn-red'));

                $fb = $this->addElement('button', 'fb', array(
                    'required' => false,
                    'ignore'   => true,
                    'label'    => '',
                    'class'=>'fb'
                ));



	}
}

