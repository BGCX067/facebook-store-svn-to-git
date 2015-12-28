<?php

abstract class Payment_Item_Abstract implements Payment_Item_Interface
{
	const ONE_MONTH = 2592000; //30 days

	protected $_code;
	protected $_item;
	protected $_page_item;

	public function getItem()
	{
		if (is_null($this->_item))
		{
			$itemModel = Payment_Model_DbTable_Items::getInstance();
			$this->_item = $itemModel->findByCode($this->_code);
		}
		return $this->_item;
	}

	public function getItemPrice()
	{
		return $this->getItem()->price;
	}

	public function getItemId()
	{
		return $this->getItem()->id;
	}

	public function getItemTitle()
	{
		return $this->getItem()->title;
	}

	public function getPageItem($id_page)
	{
		$pageItemsModel = Payment_Model_DbTable_PageItems::getInstance();
		$this->_page_item = $pageItemsModel->findByPageItem($id_page, $this->_code);
		return $this->_page_item;
	}

	public function getTimeExpired($id_page)
	{
		return $this->getPageItem($id_page)->time_expired;
	}

	public function updatePageItem($id_page)
	{
		try
		{
			$pageItemsModel = Payment_Model_DbTable_PageItems::getInstance();
			$pageItemsModel->updatePageItem(
			$id_page, $this->getItemId(), time(), $this->getDefaultTime()
			);
		}
		catch (Exception $ex)
		{
			throw $ex;
		}
	}

}