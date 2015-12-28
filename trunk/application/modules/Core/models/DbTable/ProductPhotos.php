<?php

class Core_Model_DbTable_ProductPhotos extends HTS_Db_Table
{
	protected $_name = 'product_photos';

	public function findAllByIdProduct($id_product)
	{
		return $this->fetchRow('id_product = ' . $this->_db->quote($id_product));
	}

}
