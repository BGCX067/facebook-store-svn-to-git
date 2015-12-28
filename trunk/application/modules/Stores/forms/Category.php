<?php

class Stores_Form_Category extends HTS_Form
{
    /**
     * @param type $configs contain param action 
     */
    public function __construct($configs = null)
	{
        parent::__construct($configs);
	}
    
    protected function __initialization()
    {
        $this->setName('form_category');
		$this->setMethod('post');
        $this->setAttrib('class', "form_category");
    }

    public function init()
	{
		parent::init();
        $this->__initialization();
		
        $this->addElement('text', 'name', array(
            'label' => "LABEL_CATEGORY_NAME",
            'required' => true,
            'validators' => array(
                $this->_getValidatorNotEmpty('LABEL_CATEGORY_NAME'),
                $this->_getValidatorStringLength(0, 255, 'UTF-8', 'LABEL_CATEGORY_NAME')
            ),
            'decorators' => $this->_element_decorators
        ));

        $this->addElement('text', 'owner', array(
            'label' => "LABEL_SHOP_OWNER",
            'required' => true,
            'decorators' => $this->_element_decorators,
            'validators' => array(
                $this->_getValidatorNotEmpty("LABEL_SHOP_OWNER"),
                $this->_getValidatorStringLength(0, 255, 'UTF-8', 'LABEL_SHOP_OWNER')
            )
        ));

        $this->addElement('text', 'phone_number', array(
            'label' => "LABEL_PHONE_NUMBER",
            'required' => true,
            'decorators' => $this->_element_decorators,
            'validators' => array(
                $this->_getValidatorNotEmpty("LABEL_PHONE_NUMBER"),
                $this->_getValidatorRegex('/^[0-9]{0,11}$/', 'LABEL_PHONE_NUMBER')
            )
        ));

        $this->addElement('text', 'email', array(
            'label' => "LABEL_EMAIL",
            'required' => true,
            'decorators' => $this->_element_decorators,
            'validators' => array(
                $this->_getValidatorNotEmpty("LABEL_EMAIL"),
                $this->_getValidatorRegex('/^[\w\d\-\_]+\@[\w\d\-\_]+(\.[\d\w]+){1,2}$/', 'LABEL_EMAIL')
            )
        ));

        $this->addElement('text', 'address', array(
            'label' => "LABEL_ADDRESS",
            'required' => true,
            'decorators' => $this->_element_decorators,
            'validators' => array(
                $this->_getValidatorNotEmpty("LABEL_ADDRESS"),
                $this->_getValidatorStringLength(0, 255, 'UTF-8', 'LABEL_ADDRESS')
            )
        ));

        $this->addElement('text', 'city', array(
            'label' => "LABEL_CITY",
            'required' => true,
            'decorators' => $this->_element_decorators,
            'validators' => array(
                $this->_getValidatorNotEmpty("LABEL_CITY"),
                $this->_getValidatorStringLength(0, 100, 'UTF-8', 'LABEL_CITY')
            )
        ));
        
        $this->addElement('select', 'base_currency', array(
            'label' => "LABEL_BASE_CURRENCY",
            'value' => '1000',
            'decorators' => $this->_element_decorators,
            'multiOptions' => Core_Model_DbTable_Stores::getBaseCurrencyList()
        ));
        
        $this->addElement('textarea', 'more_info', array(
            'label' => "LABEL_MORE_INFO",
            'required' => false,
            'decorators' => $this->_element_decorators,
            'validators' => array(
                $this->_getValidatorStringLength(0, 1000, 'UTF-8', 'LABEL_MORE_INFO')
            )
        ));
        
        $this->addElement('textarea', 'payment_methods', array(
            'label' => "LABEL_PAYMENT_METHODS",
            'required' => false,
            'decorators' => $this->_element_decorators,
            'validators' => array(
                $this->_getValidatorStringLength(0, 1000, 'UTF-8', 'LABEL_PAYMENT_METHODS')
            )
        ));
        
        $this->addElement('file', 'banner', array(
            'label' => "LABEL_BANNER",
            'description' => "HINT_BANNER",
            'decorators' => $this->_file_decorators,
            'destination' => UPLOAD_PATH,
            'validators' => array(
                $this->_getValidatorFileExtensions(array('jpg', 'jpeg', 'gif', 'png'), "LABEL_BANNER"),
                $this->_getValidatorFileSize(0, "512kB", "LABEL_BANNER")
            )
        ));
        
        $this->addElement('text', 'link', array(
            'label' => "LABEL_LINK",
            'description' => "HINT_LINK",
            'decorators' => $this->_element_decorators,
            'validators' => array(
                $this->_getValidatorRegex(HTS_Util::getPatternRegexUri(), "LABEL_LINK")
            )
        ));
        
        $this->addElement('text', 'products_frequency', array(
            'label' => "LABEL_PRODUCTS_FREQUENCY",
            'value' => 12,
            'class' => "products_frequency",
            'decorators' => $this->_element_decorators,
            'validators' => array(
                $this->_getValidatorBetween(1, 32, "LABEL_PRODUCTS_FREQUENCY")
            )
        ));
        
        $this->addElement('select', 'id_page', array(
            'label' => "LABEL_ID_PAGE",
            'decorators' => $this->_element_decorators,
            'multiOptions' => $this->_social->getPageList()
        ));
        
        $this->addElement('submit', 'submit', array(
            'label' => 'LABEL_SUBMIT',
            'decorators' => $this->_hidden_label_decorators
        ));
        
        $this->_generateGroup('store_owner_info', 'FORM_GROUP_STORE_OWNER_INFO', 0,
                array('name','owner','phone_number','email','address','city', 'base_currency', 'more_info', 'payment_methods')
        );
        $this->_generateGroup('store_popular_info', 'FORM_GROUP_STORE_POPULAR_INFO', 1,
                array('banner', 'link', 'products_frequency', 'id_page')
        );
	}

	public function isValid($data) {
        return parent::isValid($data);
	}
}