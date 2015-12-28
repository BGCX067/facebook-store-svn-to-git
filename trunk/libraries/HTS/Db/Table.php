<?php
abstract class HTS_Db_Table extends Zend_Db_Table_Abstract
{
	private $_translator;

	/**
	 *
	 * @return Zend_Translate
	 */
	private function _getTranslator()
	{
		if (is_null($this->_translator))
		{
			$this->_translator = Zend_Registry::get('Zend_Translate');
		}
		return $this->_translator;
	}

	/**
	 * @return HTS_Db_Table_Row
	 */
	public function findOne()
	{
		$args = func_get_args();
		$ret = call_user_func_array(array($this, 'find'), $args);
		return $ret->current();
	}

	protected function translate($text)
	{
		return $this->_getTranslator()->translate($text);
	}

	public function quote($value, $type = null)
	{
		return $this->_db->quote($value, $type);
	}
}