<?php

class Payment_Model_DbTable_OrderItems extends HTS_Db_Table
{
	const TABLE_NAME = 'ym_order_items';
	protected $_name = self::TABLE_NAME;
	protected $_rowClass = 'Payment_Model_OrderItem';
	protected $_primary = array('id_order', 'id_item');

	private static $_instance;

	/**
	 * @return Payment_Model_DbTable_OrderItems
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function findByIdOrder($id_order)
	{
		$query = $this->select()->where('id_order=?', $id_order);
		return $this->fetchAll($query);
	}

	public function createFromParams($id_order, $id_item)
	{
		$orderItem = $this->createRow();
		$orderItem->id_order = $id_order;
		$orderItem->id_item = $id_item;
		return $orderItem->save();
	}

}

