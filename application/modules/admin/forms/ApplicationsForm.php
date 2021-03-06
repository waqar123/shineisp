<?php
class Admin_Form_ApplicationsForm extends Zend_Form
{
    
    public function init()
    {
        // Set the custom decorator
        $this->addElementPrefixPath('Shineisp_Decorator', 'Shineisp/Decorator/', 'decorator');

        $this->addElement('text', 'app_name', array(
            'filters'     => array('StringTrim'),
            'required'    => true,
            'decorators'  => array('Composite'),
            'label'       => 'Application Name',
            'class'       => 'text-input large-input'
        ));

        $this->addElement('text', 'client_id', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Client ID',
            'class'       => 'text-input large-input',
            'attribs'     => array('readonly' => 'disabled')
        ));

        $this->addElement('text', 'client_secret', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Client Secret',
            'class'       => 'text-input large-input',
            'attribs'     => array('readonly' => 'disabled')
        ));
        
        $this->addElement('text', 'redirect_uri', array(
            'filters'     => array('StringTrim'),
            'required'    => false,
            'decorators'  => array('Composite'),
            'label'       => 'Redirect URI',
            'class'       => 'text-input large-input'
        ));

		$this->addElement('select', 'active', array(
        'label' => 'Active',
        'decorators' => array('Composite'),
        'class'      => 'text-input large-input'
        ));
        $this->getElement('active')
                  ->setAllowEmpty(false)
                  ->setRegisterInArrayValidator(false) // Disable the Validator in order to manage a dynamic products list.
                  ->setMultiOptions(array(
				  	 '0' => 'Disabled'
				  	,'1' => 'Enabled'
				  ));
		
		$this->addElement('hidden', 'id');
    }
    
}