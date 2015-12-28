<?php
class Payment_Model_DbTable_Items extends HTS_Db_Table
{
	const TABLE_NAME = 'ym_items';
	protected $_name = self::TABLE_NAME;
	protected $_rowClass = 'Payment_Model_Item';
	private static $_instance;

	/**
	 * @return Payment_Model_DbTable_Items
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function findByCode($code)
	{
		$query = $this->select()->where('code=?', $code);
		return $this->fetchAll($query)->current();
	}
}

