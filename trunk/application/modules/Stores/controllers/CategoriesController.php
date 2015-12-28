<?php

class Stores_CategoriesController extends Core_Controller_Admin
{
	 /*
     * Define some variable was used again in this controller
     */
    private $_extra_values;
    private $_categories_list;
    private $_id_page;
    private $_id_store;
    private $_id_category;
    private $_store_row;
    private $_category_row;

    protected function _extraValues()
    {
        $this->_extra_values['user'] = $this->_user;
        $this->_extra_values['categories_list'] = $this->_categories_list;
        $this->_extra_values['params'] = $this->_params;
        $this->_extra_values['id_page'] = $this->_id_page;
        $this->_extra_values['id_store'] = $this->_id_store;
        $this->_extra_values['id_category'] = $this->_id_category;

        $this->view->extra_values = $this->_extra_values;
    }

    public function preDispatch()
	{
		parent::preDispatch();
		$this->_helper->layout()->setLayout('hts_canvas');
        $this->_id_page = !empty($this->_params['id_page'])?$this->_params['id_page']:null;
        $this->_id_store = !empty($this->_params['id_store'])?$this->_params['id_store']:null;
        $this->_id_category = !empty($this->_params['id_category'])?$this->_params['id_category']:null;
        $this->_store_row = Core_Model_DbTable_Stores::getInstance()->findOne($this->_id_store);
        $this->_categories_list = Core_Model_DbTable_Categories::getInstance()->getAllCategories($this->_params);

        $this->_initMenus($this->_params);
        $this->_extraValues();

        $this->_main_menu->addPage(array(
            'params'     => array(
                'id_store' => $this->_id_store,
                'id_page' => $this->_id_page
            ),
            'action'     => 'index',
            'controller' => 'products',
            'module'     => 'stores',
            'label'      => sprintf(HTS_Util::translate('MENU_MANAGE_PRODUCTS'), substr($this->_store_row->name, 0, 100)),
        ));
	}

    public function indexAction()
    {
        //Zend_Debug::dump($this->_categories_list);
        if(empty ($this->_id_category))
        {
            $this->view->categories_list = $this->_categories_list;
        }
        else
        {
            if(!empty ($this->_categories_list[$this->_id_category]['children']))
            {
                $this->view->categories_list = $this->_categories_list[$this->_id_category]['children'];
            }
            else
            {
                $this->view->categories_list = array();
            }
        }
        $form_add_categories = new Stores_Form_Categories(array('name' => 'form_add_categories'));
        $form_edit_category = new Stores_Form_Categories(array('name' => 'form_edit_category'));
        $this->view->form_add_categories = $form_add_categories;
        $this->view->form_edit_category = $form_edit_category;
        $this->_main_menu->addPage(array(
            'params'     => array(
                'id_store' => $this->_id_store,
                'id_page' => $this->_id_page
            ),
            'action'     => 'index',
            'controller' => 'categories',
            'module'     => 'stores',
            'label'      => sprintf(HTS_Util::translate('MENU_MANAGE_CATEGORIES'), substr($this->_store_row->name, 0, 100)),
            'active'     => true
        ));
    }
    public function createAction()
    {
        $this->disableAutoRender();
        Zend_Debug::dump($this->_params);die();
    }
    public function editAction()
    {
        $this->disableAutoRender();
        Zend_Debug::dump($this->_params);die();
    }
    public function deleteAction()
    {
        $this->disableAutoRender();
    }
}