<?php

class Payment_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected $_name = 'payment';

	function _initAcl()
	{
		$resource_name = $this->_name . ':index';

		$registry = Zend_Registry::getInstance();
		/**
		 * @var $acl Zend_Acl
		 */
		$acl = $registry->get('acl');
		$acl->addResource($resource_name);
		$arrGuest = array(
			'index'
			);

			$acl->allow(Core_Role::ROLE_GUEST, $resource_name, $arrGuest);
	}
}