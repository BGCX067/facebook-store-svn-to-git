<?php

class Stores_Form_Product extends HTS_Form
{
/*
     * Define protected variable
     */
    protected $_currency_code;
    protected $_classification;

    /**
     *
     * @param type $configs contain param action 
     */
    public function __construct($configs = null)
	{
        parent::__construct($configs);
	}
    
    protected function __initialization()
    {
        $this->setName('form_store');
		$this->setMethod('post');
        $this->setAttrib('class', "form_store");
        
        global $currency_code;
        global $classification;
        $this->_currency_code = $currency_code;
        $this->_classification = $classification;
    }

    public function init()
	{
		parent::init();
        $this->__initialization();
		
        $this->addElement('text', 'name', array(
            'label' => "LABEL_NAME",
            'required' => true,
            'validators' => array(
                $this->_getValidatorNotEmpty('NAME_'),
                $this->_getValidatorStringLength(10, 255, 'UTF-8', 'NAME_')
            ),
            'decorators' => $this->_elementDecorators
        ));
        
        $this->addElement('textarea', 'description', array(
            'label' => "LABEL_DESCRIPTION",
            'required' => true,
            'decorators' => $this->_elementDecorators,
            'validators' => array(
                $this->_getValidatorNotEmpty("DESCRIPTION_"),
                $this->_getValidatorStringLength(0, 1000, 'UTF-8', 'description_')
            )
        ));
        
        $this->addElement('file', 'logo', array(
            'label' => "LABEL_LOGO",
            'required' => true,
            'description' => "HINT_LOGO",
            'decorators' => $this->_file_decorators,
            'validators' => array(
                $this->_getValidatorFileUpload("LOGO_"),
                $this->_getValidatorFileCount(0, 1, "LOGO_"),
                $this->_getValidateIsImage(array(), "LOGO_"),
                $this->_getValidatorFileSize(0, "200kB", "LOGO_")
            )
        ));
        
        $this->addElement('file', 'banner', array(
            'label' => "LABEL_BANNER",
            'description' => "HINT_BANNER",
            'decorators' => $this->_file_decorators,
            'validators' => array(
                $this->_getValidatorFileCount(0, 1, "BANNER_"),
                $this->_getValidateIsImage(array(), "BANNER_"),
                $this->_getValidatorFileSize(0, "200kB", "BANNER_")
            )
        ));
        
        $this->addElement('text', 'link', array(
            'label' => "LABEL_LINK",
            'description' => "HINT_LINK",
            'decorators' => $this->_elementDecorators,
            'validators' => array(
                $this->_getValidatorRegex(HTS_Util::getPatternRegexUri(), "LINK_")
            )
        ));
        
        $this->addElement('select', 'currency', array(
            'label' => "LABEL_PAYMENT_UNIT",
            'value' => "USD",
            'decorators' => $this->_elementDecorators,
            'multiOptions' => $this->_currency_code
        ));
        
        $this->addElement('text', 'number_products', array(
            'label' => "LABEL_NUMBER_PRODUCTS",
            'value' => 20,
            'class' => "number_products",
            'decorators' => $this->_elementDecorators,
            'validators' => array(
                $this->_getValidatorBetween(1, 30, "NUMBER_PRODUCTS")
            )
        ));
        
        $this->addElement('textarea', "about_us", array(
            'label' => 'LABEL_ABOUT_US',
            'decorators' => $this->_elementDecorators,
            'validators' => array(
                $this->_getValidatorStringLength(0, 1000, 'UTF-8', 'description_')
            )
        ));
        
        $this->addElement('textarea', "terms_conditions", array(
            'label' => 'LABEL_TERMS_CONDITIONS',
            'decorators' => $this->_elementDecorators,
            'validators' => array(
                $this->_getValidatorStringLength(0, 1000, 'UTF-8', 'TERMS_CONDITIONS_')
            )
        ));
        
        $this->addElement('textarea', "contact_info", array(
            'label' => 'LABEL_CONTACT_INFO',
            'decorators' => $this->_elementDecorators,
            'validators' => array(
                $this->_getValidatorStringLength(10, 1000, 'UTF-8', 'contact_info_')
            )
        ));
        
        $this->addElement('select', 'classification', array(
            'label' => "LABEL_CLASSIFICATION",
            'decorators' => $this->_elementDecorators,
            'multiOptions' => $this->_classification
        ));
        
        $this->addElement('select', 'id_page', array(
            'label' => "LABEL_ID_PAGE",
            'decorators' => $this->_elementDecorators,
            'multiOptions' => $this->_social->getPageList()
        ));
        
        $this->addElement('file', 'theme', array(
            'label' => 'LABEL_THEME',
            'decorators' => $this->_file_decorators,
            'validators' => array(
                $this->_getValidatorFileCount(0, 1, "THEME_"),
                $this->_getValidatorFileExtensions(array('css'), "THEME_"),
                $this->_getValidatorFileUpload("THEME_")
            )
        ));
        
        $this->addElement('submit', 'submit', array(
            'decorators' => $this->_hidden_label_decorators
        ));
	}

	public function isValid($data) {
        return parent::isValid($data);
	}
}