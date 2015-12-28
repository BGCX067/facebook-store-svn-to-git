<?php
class HTS_Cache_Dummy extends Zend_Cache_Core{
	public function save($data, $id = null, $tags = array(), $specificLifetime = false, $priority = 8)
	{
		return false;
	}
}
