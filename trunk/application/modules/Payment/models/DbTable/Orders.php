<?php

class Payment_Model_DbTable_Orders extends HTS_Db_Table
{
	const TABLE_NAME = 'ym_orders';
	const PAYMENT_STATUS_COMPLETE = 'Complete';

	protected $_name = self::TABLE_NAME;
	protected $_rowClass	= 'Payment_Model_Order';
	private static $_instance;

	/**
	 * @return Payment_Model_DbTable_Orders
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function createOrderFromParams($data, $items)
	{
		$this->getAdapter()->beginTransaction();

		$order = $this->createRow();
		$order->id_user = $data['id_user'];
		$order->id_page = $data['id_page'];
		$order->id_txn = $data['txn_id'];
		$order->total = $data['mc_gross'];
		$order->time_created = time();
		$order->status = self::PAYMENT_STATUS_COMPLETE;
		$id_order = $order->save();

		$this->_createOrderItems($items, $order->id_page, $id_order);
		$this->getAdapter()->commit();
	}

	private function _createOrderItems($items, $id_page, $id_order)
	{
		$orderItemModel = Payment_Model_DbTable_OrderItems::getInstance();
		foreach ($items as $item)
		{
			//create order_items
			$orderItemModel->createFromParams($id_order, $item->getItemId());
			$item->updatePageItem($id_page);
		}
	}
}

