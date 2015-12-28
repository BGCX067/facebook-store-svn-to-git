<?php

class Stores_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected $_name = 'stores';

	function _initAcl() {
		$registry = Zend_Registry::getInstance();
		$acl = $registry->get('acl');

		$acl->addResource($this->_name . ':stores');
		$acl->addResource($this->_name . ':categories');
        $acl->addResource($this->_name . ':products');
		$acl->addResource($this->_name . ':carts');
        $acl->addResource($this->_name . ':accounts');
		
        /*
         * Divide role for users and guests on contoller stores
         */
    	$acl->allow(Core_Role::ROLE_USER, $this->_name . ':stores', array(
    		'index', 'create', 'edit', 'delete', 'publish', 'unpublish'
    	));
        
        /*
         * Divide role for users and guests on contoller categories
         */
		$acl->allow(Core_Role::ROLE_GUEST, $this->_name . ':categories', array());
    	$acl->allow(Core_Role::ROLE_USER, $this->_name . ':categories', array(
    		'index', 'create', 'edit', 'delete'
    	));
        
        /*
         * Divide role for users and guests on contoller products
         */
		$acl->allow(Core_Role::ROLE_GUEST, $this->_name . ':products', array());
    	$acl->allow(Core_Role::ROLE_USER, $this->_name . ':products', array(
    		'index', 'create', 'edit', 'delete'
    	));
        
        /*
         * Divide role for users and guests on contoller carts
         */
		$acl->allow(Core_Role::ROLE_GUEST, $this->_name . ':carts', array());
    	$acl->allow(Core_Role::ROLE_USER, $this->_name . ':carts', array(
    		'index'
    	));
        
        /*
         * Divide role for users and guests on contoller stores
         */
		$acl->allow(Core_Role::ROLE_GUEST, $this->_name . ':accounts', array());
    	$acl->allow(Core_Role::ROLE_USER, $this->_name . ':accounts', array(
    		'index', 'edit'
    	));

    	$registry->set('acl', $acl);
	}
}

