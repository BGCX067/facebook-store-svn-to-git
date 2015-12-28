<?php

class HTS_Facebook_Element_LoadFanpage extends Zend_Form_Element_MultiCheckbox
{

	function init()
	{
		parent::init();
		$social = HTS_Api::getInstance()->getSocialPlugin();
		$name = $social->getName();

		$viewPath = HTS_PATH . '/' . $name . '/views/scripts/' . strtolower($name);
		$view = $this->getView();
		$view->addScriptPath($viewPath);

		$this->setMultiOptions($social->getPageList());
		$this->setDecorators(array(array('ViewScript', array(
			'viewScript' => 'loadFanpages.phtml',
		))));
	}
}

