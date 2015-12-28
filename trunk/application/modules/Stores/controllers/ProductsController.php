<?php

class Stores_ProductsController extends Core_Controller_Admin
{
    /*
     * Define some variable was used again in this controller
     */
    private $_page_list;
    private $_extra_values;
    private $_id_page;
    private $_id_store;
    private $_products_row;
    private $_store_row;

    protected function _extraValues()
    {
        $this->_extra_values['user'] = $this->_user;
        $this->_extra_values['page_list'] = $this->_page_list;
        $this->_extra_values['params'] = $this->_params;
        $this->_extra_values['id_page'] = $this->_id_page;

        $this->view->extra_values = $this->_extra_values;
    }
    
	public function preDispatch()
	{
		parent::preDispatch();
		$this->_helper->layout()->setLayout('hts_canvas');
        $this->_page_list = $this->_social->getPageList();
        $this->_id_page = !empty($this->_params['id_page'])?$this->_params['id_page']:null;
        $this->_id_store = !empty($this->_params['id_store'])?$this->_params['id_store']:null;
        $this->_store_row = Core_Model_DbTable_Stores::getInstance()->findOne($this->_id_store);

        $this->_initMenus($this->_params);
        $this->_extraValues();
	}

	public function indexAction()
	{
        $products_list = Core_Model_DbTable_Products::getInstance()->getAllProducts($this->_params);
        die();
        $this->_main_menu->addPage(array(
            'params'     => array(
                'id_store' => $this->_id_store,
                'id_page' => $this->_id_page
            ),
            'action'     => 'index',
            'controller' => 'produts',
            'module'     => 'stores',
            'label'      => substr($this->_store_row->name, 0, 100),
            'active'     => true
        ));
	}
}
