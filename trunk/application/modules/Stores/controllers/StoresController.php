<?php

class Stores_StoresController extends Core_Controller_Admin
{
    /*
     * Define some variable was used again in this controller
     */
    private $_page_list;
    private $_extra_values;
    private $_id_page;
    private $_stores_table;
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
        $this->_stores_table = HTS_Util::getDbTable('stores');
        
        $this->_initMenus($this->_params);
        $this->_extraValues();
	}

	public function indexAction()
	{
        $this->_main_menu->findOneBy("action", "index")->setActive();
        $stores_list = $this->_stores_table->getAllStores(array(
            'id_page'   => $this->_id_page
        ));
        $this->view->stores_list = $stores_list;
	}
    
    protected function _saveStore($data, $id = null)
    {
        if(empty ($id))
        {
            $this->_store_row = HTS_Util::getDbRow('stores');
            $this->_store_row->setFromArray($data);
        }
        else
        {
            $this->_store_row = $this->_stores_table->findOne($id);
            $this->_store_row->setFromArray($data);
        }
        return $this->_store_row->save();
    }
    
    public function createAction()
    {
        $action_url = $this->view->url(array('module' => 'stores', 'controller' => 'stores', 'action' => 'create'));
        $form_store = new Stores_Form_Store();
        $form_store->setAction($action_url);
        
        if (isset($this->_params['form_submitted']))
        {
            $form_data = $this->getRequest()->getPost();
            $banner_name = $form_store->receiveFile('banner', time() . '_', true);
            if ($form_store->isValid($form_data))
            {
                if(!empty ($banner_name))
                {
                    $form_data['banner'] = $banner_name;
                }
                $id = $this->_saveStore($form_data);
                $this->_redirect('/stores/products/index/id_store/' . $id);
            }
            else{
                $this->view->form_store = $form_store;
            }
        }
        else
        {
            $form_store->populate(array('id_page' => $this->_id_page));
            $this->view->form_store = $form_store;
        }
        $this->_main_menu->findOneBy("action", "create")->setActive();
    }

    public function editAction()
    {
        $id = $this->_getParam('id', null);
        $action_url = $this->view->url(array('module' => 'stores', 'controller' => 'stores', 'action' => 'edit', 'id' => $id));
        $form_store = new Stores_Form_Store();
        $form_store->setAction($action_url);

        if (isset($this->_params['form_submitted']))
        {
            $form_data = $this->getRequest()->getPost();
            $banner_name = $form_store->receiveFile('banner', time() . '_', true);
            if ($form_store->isValid($form_data))
            {
                if(!empty ($banner_name))
                {
                    $form_data['banner'] = $banner_name;
                }
                $result = $this->_saveStore($form_data, $id);
                $this->_redirect('/stores/stores/index');
            }
            else{
                $this->view->form_store = $form_store;
            }
        }
        else
        {
            $this->_store_row = $this->_stores_table->findOne($id);
            $form_store->populate($this->_store_row->toArray());
            $this->view->form_store = $form_store;
        }
        $this->_main_menu->addPage(array(
            'params'     => array(
                'id' => $id,
                'id_page' => $this->_id_page
            ),
            'action'     => 'edit',
            'controller' => 'stores',
            'module'     => 'stores',
            'label'      => substr($this->_store_row->name, 0, 100),
            'active'     => true
        ));
    }
    
    public function deleteAction()
    {
        
    }        
    
    public function publishAction()
    {
        
    }
    
    public function unpublishAction()
    {
        
    }        
}