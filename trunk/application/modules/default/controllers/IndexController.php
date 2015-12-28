<?php

class IndexController extends HTS_Controller_Action
{
    function preDispatch() {
        parent::preDispatch();
    }
    
    function indexAction()
	{
        if (!HTS_Util::isFacebookRequest())
        {
            $this->disableAutoRender();
            $data = $this->_social->getAppRequestData();
            if (isset($data))
            {
                $data = json_decode($data, true);
                Zend_Debug::dump($data);die();
            }
            //$this->_forward('index','stores','stores');
            //$this->_redirect('/stores/stores/index');
            HTS_Util::parentRedirect($this->view->url(array('module' => 'stores',
                'controller' => 'stores', 'action' => 'index')));
        }
	}
}
