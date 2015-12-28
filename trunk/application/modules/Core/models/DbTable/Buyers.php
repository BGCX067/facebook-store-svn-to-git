<?php

class Core_Model_DbTable_Buyers extends HTS_Db_Table
{
	protected $_name = 'buyers';

	public function findOneByIdBuyer($id_buyer)
	{
		return $this->fetchRow('id_user = ' . $this->_db->quote($id_buyer));
	}
}
