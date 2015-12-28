<?php
class HTS_Cache_Backend_Dummy extends HTS_Cache_Backend_Abstract
{
	public function __construct()
	{
	}
	public function load($id, $doNotTestCacheValidity = false)
	{
		return false;
	}

	public function save($data, $id, $tags = array(), $specificLifetime = false)
	{
		return false;
	}

	public function remove($id)
	{
		return false;
	}

	public function test($id)
	{
		return false;
	}
}
