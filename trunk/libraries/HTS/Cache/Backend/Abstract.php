<?php
abstract class HTS_Cache_Backend_Abstract extends Zend_Cache_Backend_Memcached
{
	const SPECIAL_SEPERATOR	= ':';

	protected $_prefix;

	/**
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->_prefix;
	}

	/**
	 *
	 * @param string $value
	 * @return HTS_Cache_Backend_Abstract
	 */
	public function setPrefix($value)
	{
		$this->_prefix = $value;
		return $this;
	}

	protected function _makeId($id)
	{
		if( empty($id) || !is_string($id))
		{
			throw new Exception('HTS_Cache_Backend_Abstract::_makeId: id argument must be an instance of string and not empty');
		}
		$value = $this->_prefix.self::SPECIAL_SEPERATOR.$id;
		return $value;
	}

	public function load($id, $doNotTestCacheValidity = false)
	{
		return parent::load($this->_makeId($id), $doNotTestCacheValidity);
	}

	public function save($data, $id, $tags = array(), $specificLifetime = false)
	{
		return parent::save($data, $this->_makeId($id), $tags, $specificLifetime);
	}

	public function remove($id)
	{
		return parent::remove($this->_makeId($id));
	}
}
