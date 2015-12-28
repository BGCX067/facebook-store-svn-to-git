<?php
class HTS_Social_Dummy extends HTS_Social_Plugin
{
	protected $_name = 'Dummy';

	function __construct($config)
	{
		parent::__construct($config);

		$this->_user = HTS_Util::getDbRow('users');
	}

	public function handleFailedAuthorization($redirect_url)
	{
		return true;
	}
	protected function _updateUserDb()
	{
		return;
	}
	public function redirect($url)
	{
		HTS_Util::redirect($this->getAppUrl() . $url);
	}
	public function getIdPage()
	{
		return '0';
	}
	public function getPageName($id_page)
	{
		return '';
	}
	public function isPageAdmin()
	{
		return false;
	}
	public function hasLikedPage()
	{
		return false;
	}
}