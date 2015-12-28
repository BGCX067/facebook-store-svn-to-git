<?php

class Core_Model_DbTable_PaymentMethods extends HTS_Db_Table
{
	protected $_name = 'payment_methods';

	public function findAllByIdStore($id_store)
	{
		return $this->fetchAll('id_store = ' . $this->_db->quote($id_store));
	}

}
