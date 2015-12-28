<?php

class HTS_Social_Controller_Action
{

	protected $view;
	protected $_action;
	protected $_is_rendered = true;
	protected $_params;
	protected $_social;
	protected $_social_name;
	function __construct()
	{
		$this->_social = HTS_Api::getInstance()->getSocialPlugin();
		$this->view = new Zend_View();
		$this->view->social = $this->_social;
		$this->_social_name = $this->view->social->getName();
	}

	public function __call($method_name, $args)
	{
		if ('Action' == substr($method_name, -6))
		{
			$action = substr($method_name, 0, strlen($method_name) - 6);
			//TODO throw which Exception?
			throw new Zend_Controller_Action_Exception(sprintf('Social action "%s" does not exist and was not trapped in __call()', $action), 404);
		}

		$this->_action = $method_name;
		$action_name = $method_name . 'Action';
		$this->_params = $args[0];

		foreach ($this->_params as $key => $value)
		{
			$this->view->$key = $value;
		}

		$result = '';
		//allow rendering view without action!?
		if (method_exists($this, $action_name))
		{
			$result = call_user_func(array($this, $action_name));
		}
		if ($this->_is_rendered)
		{
			$result = $this->render();
		}
		return $result;
	}

	protected function setNoRender()
	{
		$this->_is_rendered = false;
	}

	protected function render($filename = null)
	{
		if ($filename == null)
		{
			$filename = $this->_action . '.phtml';
		}

		if (file_exists(APPLICATION_PATH . '/modules/' . $this->_social_name . '/views/scripts/' . strtolower($this->_social_name) .'/' . $filename))
		{
			$this->view->addScriptPath(APPLICATION_PATH . '/modules/' . $this->_social_name . '/views/scripts/' . strtolower($this->_social_name));
		}
		else
		{
			$this->view->addScriptPath(HTS_PATH . '/' . $this->_social_name . '/views/scripts/' . strtolower($this->_social_name));
		}

		// after rendering, no more render required
		$this->setNoRender();
		return $this->view->render($filename);
	}
}