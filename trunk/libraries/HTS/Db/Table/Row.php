<?php
abstract class HTS_Db_Table_Row extends Zend_Db_Table_Row_Abstract {

	public function quote($value, $type = null)
	{
		return $this->_table->quote($value, $type);
	}
}