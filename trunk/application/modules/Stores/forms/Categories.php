<?php

class Stores_Form_Categories extends HTS_Form
{
    protected $_configs;

    /**
     * @param type $configs contain param action
     */
    public function __construct($configs = null)
	{
        $this->_configs = $configs;
        parent::__construct($configs);
	}

    protected function __initialization()
    {
        $this->setName('form_add_category');
		$this->setMethod('post');
        $this->setAttrib('class', "form_add_category");

        if(!empty ($this->_configs['name']))
        {
            $this->setName($this->_configs['name']);
        }
    }
    
	public function init()
	{
		parent::init();
        $this->__initialization();

        $this->addElement('text', 'name', array(
            'label' => "LABEL_CATEGORY_NAME",
            'required' => true,
            'max_length' => 255,
            'validators' => array(
                $this->_getValidatorNotEmpty('LABEL_CATEGORY_NAME'),
                $this->_getValidatorStringLength(0, 255, 'UTF-8', 'LABEL_CATEGORY_NAME')
            ),
            'decorators' => $this->_customDecorators()
        ));
        
        if(empty($this->_configs['name']) || $this->_configs['name'] == 'form_add_categories')
        {
            $this->addElement('button', 'btn_add_more', array(
                'label' => 'LABEL_ADD_MORE',
                'decorators' => $this->_button_decorators
            ));
            $this->addElement('submit', 'btn_submit', array(
                'label' => 'LABEL_SUBMIT',
                'decorators' => $this->_button_decorators
            ));
        }
        else
        {
            $this->addElement('submit', 'btn_save', array(
                'label' => 'LABEL_SAVE',
                'decorators' => $this->_customDecorators(null, null, 'hidden', null)
            ));
        }
	}

	public function isValid($data) {
		return parent::isValid($data);
	}
}