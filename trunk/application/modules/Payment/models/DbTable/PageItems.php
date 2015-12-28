<?php

class Payment_Model_DbTable_PageItems extends HTS_Db_Table
{
	const TABLE_NAME = 'ym_page_items';
	protected $_name = self::TABLE_NAME;
	protected $_rowClass = 'Payment_Model_PageItem';
	protected $_primary = array('id_page', 'id_item');
	private static $_instance;

	/**
	 * @return Payment_Model_DbTable_PageItems
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function updatePageItem($id_page, $id_item, $time_paid, $extra_time)
	{
		$row = $this->find($id_page, $id_item)->current();

		if (empty($row))
		{
			$row = $this->createRow();
			$row->id_page = $id_page;
			$row->id_item = $id_item;
		}

		$row->time_paid = time();

		if ($row->time_expired < time())
		{
			$row->time_expired = time() + $extra_time;
		}
		else if ($row->time_expired > time())
		{
			$row->time_expired = $row->time_expired + $extra_time;
		}
		$row->save();
	}

	public function findByPageItem($id_page, $code)
	{
		$query = $this->select()->setIntegrityCheck(false)
		->from(array('ip' => self::TABLE_NAME), array('ip.time_expired'))
		->joinLeft(array('i' => Payment_Model_DbTable_Items::TABLE_NAME), 'i.id = ip.id_item', array('i.code', 'i.id'))
		->where('ip.id_page=?', $id_page)
		->where('i.code=?', $code);
		return $this->fetchAll($query)->current();
	}
}

