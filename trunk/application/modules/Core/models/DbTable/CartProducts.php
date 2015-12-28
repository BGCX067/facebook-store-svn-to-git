<?php

class Core_Model_DbTable_CartProducts extends HTS_Db_Table
{
	protected $_name = 'cart_products';

	public function findAllByIdCart($id_cart)
	{
		return $this->fetchAll('id_cart = ' . $this->_db->quote($id_cart));
	}

}
