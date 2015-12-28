<?php

class HTS_Model_DbTable_Users extends HTS_Db_Table
{
	protected $_name = 'users';
	protected $_rowClass = 'HTS_Model_User';

	public function findOneByIdSocial($id_social)
	{
		return $this->fetchRow('id_social = ' . $this->_db->quote($id_social));
	}
}