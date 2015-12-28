<?php
/**
 * @author HT-Stores-World 
 */
abstract class HTS_Controller_Action extends Zend_Controller_Action {

	/**
	 *
	 * @var HTS_Social_Plugin
	 */
	protected $_social;
	protected $_params;
	protected $_user;
	protected $_id_user;

	public function init()
	{
		parent::init();
		/*
         * Initial instance of social plugin
         */ 
		$this->_social = HTS_Api::getInstance()->getSocialPlugin();
		$this->view->social = $this->_social;
        /*
         * Initial layout
         */
		$this->_helper->layout->setLayout('hts_canvas');
		/*
         * Check user authorize apps 
         */
		$this->_authenticate();
		$this->_authorize();
        /*
         * Get user infomation
         */
		$this->_id_user = $this->_social->getIdUser();
		$this->_id_social = $this->_social->getIdSocial();
		$this->_user = $this->_social->getUser();
        /*
         * Get all params
         */
        $this->_params = $this->_getAllParams();
		$this->_params['id_user'] = $this->_id_user;
		$this->_params['id_social'] = $this->_id_social;
        Zend_Registry::set('params', $this->_params);
		/*
		 * Initial translate language
		 */
		$this->_initTranslator();
	}

	private function _authenticate()
	{
		 
	}

	private function _authorize()
	{
		$acl = Zend_Registry::get('acl');
		 
		$module = $this->_request->getModuleName();
		$controller = $this->_request->getControllerName();
		$action = $this->_request->getActionName();
		 
		$user_roles = $this->_social->getUser()->getRoles();
		$is_authorized = false;
		foreach ($user_roles as $role)
		{
			if ($acl->isAllowed($role, $module . ':' . $controller, $action))
			{
				$is_authorized = true;
				break;
			}
		}
		 
		if (!$is_authorized)
		{
			$this->_social->handleFailedAuthorization($this->_getRedirectUrlForAuthorization());
		}
	}

	protected function _initTranslator($language = DEFAULT_LANGUAGE_CODE)
	{
		$translate = new Zend_Translate(array(
			'adapter' => 'csv', 
    		'content' => APPLICATION_PATH.'/languages/'.$language.'/main.csv',
			'locale'  => $language,
			'delimiter' => ';'
        ));

        // add translation source of all modules
        $front = Zend_Controller_Front::getInstance();
        $modules = $front->getControllerDirectory();
        foreach (array_keys($modules) as $module) {
            $file = APPLICATION_PATH.'/modules/'.ucwords($module).'/languages/'.$language.'/main.csv';
            if (file_exists($file))
            {
                $translate->addTranslation(
                array(
                    'adapter' => 'csv', 
                    'content' => $file,
                    'locale'  => $language,
                    'delimiter' => ';'
                ));
            }
        }

        Zend_Registry::set('Zend_Translate', $translate);

        return $translate;
	}

	protected function translate($word)
	{
		return $this->view->translate($word);
	}

	protected function _getRedirectUrlForAuthorization()
	{
		return '/';
	}

	/**
	 * Use to disable auto-render
	 * @return void
	 */
	public function disableAutoRender()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
	}
}