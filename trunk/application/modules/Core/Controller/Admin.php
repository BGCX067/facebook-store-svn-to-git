<?php

class Core_Controller_Admin extends HTS_Controller_Action
{
    protected $_main_menu;
    function preDispatch() {
        parent::preDispatch();
    }
    
    protected function _initMenus($params)
    {
        $id_page = !empty($params['id_page'])?$params['id_page']:null;
        $pages = array(
            array(
                'params'     => array(
                    'id_page' => $id_page
                ),
                'action'     => 'index',
                'controller' => 'stores',
                'module'     => 'stores',
                'label'      => $this->translate('MENU_MY_STORES')
            ),
            array(
                'params'     => array(
                    'id_page' => $id_page
                ),
                'action'     => 'create',
                'controller' => 'stores',
                'module'     => 'stores',
                'label'      => $this->translate('MENU_CREATE_A_NEW_STORE')
            )
        );
        
        $this->_main_menu = new Zend_Navigation();
        $this->_main_menu->addPages($pages);
        $this->view->navigation($this->_main_menu);
    }        
}