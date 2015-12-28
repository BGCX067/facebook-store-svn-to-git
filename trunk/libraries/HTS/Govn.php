<?php
class HTS_Govn extends HTS_Social_Plugin
{
	protected $_name = 'Govn';

	private $_social;
	private $_access_token;
	private $_code;
	private $_user_pages;
	private $_page_id;
	private $_has_logged_in = false;
	private $_has_authorized = false;

	function __construct($config)
	{
		parent::__construct($config);
		$this->_social = new HTS_Govn_Api(array(
			'appId'  => $config->id,
			'secret' => $config->secret,
			'appUrl'  => $config->url,
			'proxy.host' => @$config->proxy->host,
			'proxy.port' => @$config->proxy->port,
		));

		$this->_init();
	}

	private function _init()
	{
		// code in tab page is transfered via URL /code/<SIGNED_REQUEST>
		// need to put this into $_REQUEST to be used by original Govn API
		$front = Zend_Controller_Front::getInstance();
		$request = $front->getRequest();
		$code =  $request->getParam('code');
		if (!empty($code) && empty($_REQUEST['code']))
		{
			$_REQUEST['code'] = $code;
		}

		$this->_access_token = $this->_social->getAccessToken();
		if (FB_DEBUG) Zend_Debug::dump($this->_access_token);

		$this->_user = HTS_Util::getDbRow('users');

		if (!empty($this->_access_token))
		{
			try
			{
				$this->_id_social = $this->_social->getUser();
				if (FB_DEBUG) Zend_Debug::dump($this->_id_social);
				 
				$this->_updateUserDb();
				$this->_has_authorized = true;
			}
			catch (Exception $e)
			{
				// Properly FB session expired
				$this->_has_authorized = false;
				return;
			}
		}
		else
		{
			$this->_has_authorized = false;
		}
	}

	public function handleFailedAuthorization($redirect = '/')
	{
		// user has authorized the app, but does not have enough access privileges
		if ($this->_has_authorized)
		{
			//TODO temporarily disable
			throw new Zend_Exception("Unauthorized access");
		}
		else
		{
			echo HTS_Util::parentRedirect("http://graph.go.vn/oauth/authorize?client_id=".$this->_config->id."&redirect_uri=".$this->url($redirect)."/&scope=".$this->_config->oauthScope);
		}
	}

	public function login()
	{

	}

	private function _updateUserDb()
	{
		$fb_users = HTS_Util::getDbTable('users');
		$fb_user = $fb_users->findOneByIdSocial($this->_id_social);

		if (empty($fb_user))
		{
			$user_profile = $this->_social->api('/me');
			if (FB_DEBUG) Zend_Debug::dump($user_profile);
			$fb_user = HTS_Util::getDbRow('users');
				
			$birthday = new Zend_Date(@$user_profile['Birthday'], 'dd/MM/yyyy');
			$updated_time = new Zend_Date(@$user_profile['Updated_time']);
				
			//TODO some values are null -> undefined index warning
			$fb_user->id_social = $user_profile['Id'];
			$fb_user->name = $user_profile['Name'];
			$fb_user->first_name = @$user_profile['First_name'];
			$fb_user->middle_name = @$user_profile['Middle_name'];
			$fb_user->last_name = @$user_profile['Last_name'];
			$fb_user->link = $user_profile['Link'];
			$fb_user->birthday = @$birthday->toString('yyyy/MM/dd');
			$fb_user->gender = $user_profile['Gender'];
			$fb_user->timezone = @$user_profile['Timezone'];
			$fb_user->locale = @$user_profile['Locale'];
			$fb_user->time_created = time();
			$fb_user->time_modified = time();
			$fb_user->time_social_modified = $updated_time->toValue();
				
			$this->_id_user = $fb_user->save();
		}

		$this->_user = $fb_user;
		$this->_id_user = $fb_user->id;
	}

	public function getUserPages()
	{
		if ($this->_user_pages == null)
		{
			if (!empty($this->_id_user))
			{
				$accounts = $this->_social->api('/me/accounts');
				$this->_user_pages = $accounts['data'];
			}
			else
			{
				$this->_user_pages = array();
			}
		}
		return $this->_user_pages;
	}

	public function getIdPage()
	{
		if (!is_null($this->_code['page']))
		{
			return $this->_code['page']['id'];
		}
		else
		{
			return $this->_page_id;
		}
	}
	//TODO we may need a generic array ('id' => 'name')
	public function getPageName($id_page)
	{
		$pages = $this->getUserPages();
		$page_name = '';
		foreach ($pages as $page)
		{
			if ($page['id'] == $id_page)
			{
				$page_name = $page['name'];
			}
		}
		return $page_name;
	}

	public function redirect($url)
	{
		HTS_Util::parentRedirect($this->_config->url . $url);
	}

	public function url($url)
	{
		return $this->getAppUrl() . $url;
	}

	public function postToWall($message, $link, $caption)
	{
		$url = 'https://graph.go.vn/'. $this->getId() .'/feed?';
		$data = array('from' => $this->_social->getAppId(),
			'access_token' => $this->_social->getAccessToken(),
			'message' => $message,
			'link' => $link,
			'caption' => $caption);

		return $this->_social->httpRequest($url, $data);
	}

	public function getPostedFields()
	{
		if (empty($this->_posted_fields))
		{
			$this->_posted_fields = array();
			if (isset($_REQUEST['code']))
			{
				$this->_posted_fields['code'] = $_REQUEST['code'];
			}
		}
		return $this->_posted_fields;
	}

	public function getProfileImage($id_social)
	{
		return 'https://graph.go.vn/'.$id_social.'/picture';
	}
}