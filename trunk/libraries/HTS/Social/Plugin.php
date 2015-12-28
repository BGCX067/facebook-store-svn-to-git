<?php
abstract class HTS_Social_Plugin
{
	protected $_name;
	protected $_config;
	protected $_id_social;
	protected $_user;
	protected $_id_user;
	protected $_posted_fields = array();

	public function __construct($config)
	{
		$this->_config = $config;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function getAppUrl()
	{
		return $this->_config->url;
	}

	public function getAppId()
	{
		return $this->_config->id;
	}

	public function getAppProfileUrl()
	{
		return $this->_config->profileUrl;
	}

	public function getIdSocial()
	{
		return $this->_id_social;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getIdUser()
	{
		return $this->_id_user;
	}

	public function getOAuthScope()
	{
		return $this->_config->oauthScope;
	}

	public function render($action, $params = array())
	{
		$front = Zend_Controller_Front::getInstance();
		$directory = $front->getControllerDirectory($this->_name);
		include_once $directory . '/' . $this->_name . 'Controller.php';

		$controller_class = $this->_name . '_' . $this->_name . 'Controller';
		$controller = new $controller_class();

		return $controller->$action($params);
	}

	public function getPostedFields()
	{
		return $this->_posted_fields;
	}

	public function getPostedFieldsForUrl()
	{
		$this->getPostedFields();
		$ret = '';
		foreach ($this->_posted_fields as $field => $value)
		{
			$ret .= '/' . $field . '/' . $value;
		}
		return $ret;
	}

	/**
	 * Get a list of fanpages administered by the current user
	 * @return array in the form of
	 * 		[] => array ('id' => <PAGE_ID>, 'name' => <PAGE_NAME>)
	 */
	public function getUserPages()
	{
		return array();
	}

	/**
	 * Get list of fanpages in the form array[id] = name
	 * return array
	 */
	public function getPageList()
	{
		return array();
	}

	/**
	 * Get a list of fanpage Ids administered by the current user
	 * @return array
	 */
	public function getUserPageIds()
	{
		return array();
	}

	/**
	 * Get fanpage url based on id_page
	 * @param string $id_page
	 * @param string $page_name name of the fanpage
	 * @return string
	 */
	public function getFanPageUrl($id_page, $page_name = null)
	{
		return '';
	}

	/**
	 * Wrap the $original_url by Social app url
	 * @param string $original_url
	 * @return string
	 */
	public function url($original_url)
	{
		return $original_url;
	}

	/**
	 * Build a fanpage URL using $params
	 * @param array $params
	 * @return string
	 */
	public abstract function pageUrl($params, $id_page = null);

	/**
	 * Handle the situation where current user does not have enough privileges to perform a specific action
	 * Common case: redirect user to Social OAuth page
	 * @param string $redirect_url the url to be redirected to after Social Plugin tries to authenticate and authorize the user
	 */
	public abstract function handleFailedAuthorization($redirect_url = '/');

	/**
	 * Add user to database if it is the first time user authorizes this app
	 */
	protected abstract function _updateUserDb();
	public abstract function redirect($url);
	public abstract function getIdPage();
	public abstract function getPageName($id_page);
	public abstract function isPageAdmin();
	public abstract function hasLikedPage();
    /**
     * Get facebook user profile
     * @return array infomation of user 
     */
    public abstract function getUserProfile();

	/**
	 * Post a link to a fanpage wall
	 * @param string $url link to be posted
	 * @param string $message message to be posted
	 * @param string $id_page ID of page to be posted
	 * 		if null given, then post to the current fanpage
	 * @return object result
	 */
	public abstract function postUrlToPageWall($url, $message = '', $id_page = null);

	public abstract function getShareUrl($url);

	public abstract function redirectLink($url);

	public abstract function isUseLocalScript();

	/**
	 * Get a list of albums photo by id_page
	 * @return array albums photos
	 */
	public abstract function getAlbumsByIdPage($id_page);
	/**
	 * Get information of album
	 * @param $id_album
	 * @return array information of album
	 */
	public abstract function getAlbumInfo($id_album);
	/**
	 * Get album cover photo
	 * @param $id_photo
	 * @return array information of photo
	 */
	public abstract function getPhotoInfo($id_photo);
	/**
	 * Get list photo by id album
	 * @param $id_album
	 * @return array photos
	 */
	public abstract function getAlbumPhotos($id_album);
    /**
     * Get Access token
     * return parent access_token 
     */
    public abstract function getAccessToken();
    /**
     * Get Url infomation
     * @param $url
     * @return Information of url
     */
    public abstract function getUrlInfo($url = '');
    /**
     * Get Application request data
     * @return array data request 
     */
    public abstract function getAppRequestData();
}
