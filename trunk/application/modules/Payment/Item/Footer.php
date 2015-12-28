<?php

class Payment_Item_Footer extends Payment_Item_Abstract
{
	const CODE = 11;
	protected $_code = self::CODE;
	private $_is_valid = array();

	public function getDefaultTime()
	{
		return self::ONE_MONTH;
	}

	public function isValid($id_page)
	{
		if (isset($this->_is_valid[$id_page]))
		{
			return $this->_is_valid[$id_page];
		}
		try
		{
			$pageItemsModel = Payment_Model_DbTable_PageItems::getInstance();
			$pageItem = $pageItemsModel->findByPageItem($id_page, self::CODE);
		}
		catch (Exception $ex)
		{
			//throw $ex;
		}

		$this->_is_valid[$id_page] = false;

		if (!empty($pageItem))
		{
			if (intval($pageItem->time_expired) - time() > 0)
			{
				$this->_is_valid[$id_page] = true;
			}
		}

		return $this->_is_valid[$id_page];
	}

}