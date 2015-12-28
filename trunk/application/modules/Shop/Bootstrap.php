<?php

class Shop_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected $_name = 'shop';

	function _initAcl() {
		$registry = Zend_Registry::getInstance();
		$acl = $registry->get('acl');

        $acl->addResource($this->_name . ':products');
		$acl->addResource($this->_name . ':carts');
        $acl->addResource($this->_name . ':accounts');
		
        /*
         * Divide role for users and guests on contoller products
         */
		$acl->allow(Core_Role::ROLE_GUEST, $this->_name . ':products', array(
    		'all'
    	));
    	$acl->allow(Core_Role::ROLE_USER, $this->_name . ':products', array(
    		'list', 'create', 'edit'
    	));
        
        /*
         * Divide role for users and guests on contoller carts
         */
		$acl->allow(Core_Role::ROLE_GUEST, $this->_name . ':carts', array());
    	$acl->allow(Core_Role::ROLE_USER, $this->_name . ':carts', array(
    		'list', 'add', 'edit'
    	));
        
        /*
         * Divide role for users and guests on contoller stores
         */
		$acl->allow(Core_Role::ROLE_GUEST, $this->_name . ':accounts', array());
    	$acl->allow(Core_Role::ROLE_USER, $this->_name . ':accounts', array(
    		'list', 'create', 'edit'
    	));

    	$registry->set('acl', $acl);
	}
}

