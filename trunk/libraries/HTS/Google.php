<?php
require_once 'Google/apiClient.php';
require_once 'Google/contrib/apiPlusService.php';

class HTS_Google extends HTS_Social_Plugin
{
	protected $_name = 'Google';

	private $_social = null;
	private $_client = null;
	private $_me;
	private $_friends = null;
	private $_friend_ids = null;

	private $_user_pages;


	function __construct($config)
	{
		parent::__construct($config);

		$this->_client = new apiClient();
		//TODO where is the best place for APPLICATION_NAME ?
		$this->_client->setApplicationName(APPLICATION_NAME);
		$this->_client->setClientId($this->_config->id);
		$this->_client->setClientSecret($this->_config->secret);
		$this->_client->setRedirectUri($this->_config->url);
		//$this->_client->setDeveloperKey('insert_your_developer_key');
		$this->_client->setScopes(array('https://www.googleapis.com/auth/plus.me'));
		$this->_social = new apiPlusService($this->_client);

		try {
			$this->_init();
		} catch (Exception $e) {
			setcookie('access_token', '', time()-1000);
			unset($_SESSION['access_token']);
			session_destroy();
			die("Please ensure you have a Google Plus account and have authorized this app!");
		}
	}

	private function _init()
	{
		// this represents a GUEST user
		$this->_user = HTS_Util::getDbRow('users');

		$front = Zend_Controller_Front::getInstance();
		$request = $front->getRequest();
		$action = $request->getActionName();
		if ($action == 'logout' || isset($_REQUEST['logout'])) {
			unset($_SESSION['access_token']);
			return;
		}

		if (isset($_REQUEST['error'])) {
			throw new Exception('Got error from Google Plus service');
		}

		if (isset($_GET['code'])) {
			$this->_client->authenticate();
			$access_token = $this->_client->getAccessToken();
			$_SESSION['access_token'] = $this->_client->getAccessToken();
			setcookie('access_token', $this->_client->getAccessToken(), time()+60*60*24*30);
		}

		if (isset($_COOKIE['access_token'])) {
			$this->_client->setAccessToken($_COOKIE['access_token']);
		}

		if (isset($_SESSION['access_token'])) {
			$this->_client->setAccessToken($_SESSION['access_token']);
		}

		if ($this->_client->getAccessToken()) {
			$this->_me = $this->_social->people->get('me');
			$this->_id_social = $this->_me['id'];

			//		  $optParams = array('maxResults' => 100);
			//		  $activities = $this->_social->activities->listActivities('me', 'public', $optParams);

			// The access token may have been updated lazily.
			$_SESSION['access_token'] = $this->_client->getAccessToken();

			try
			{
				$this->_updateUserDb();
				$this->_has_authorized = true;
			}
			catch (Exception $e)
			{
				// Properly session expired
				$this->_has_authorized = false;
				return;
			}
		} else {
			$this->_has_authorized = false;
			return;

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
			$this->login();
		}
	}

	public function login()
	{
		$authUrl = $this->_client->createAuthUrl();
		echo HTS_Util::parentRedirect($authUrl);
	}

	public function logout()
	{
		setcookie('access_token', FALSE, time()-3600);
		unset($_SESSION['access_token']);
		session_destroy();
	}

	public function url($url)
	{
		return $this->_config->url . $url;
	}

	private function _updateUserDb()
	{
		$google_users = HTS_Util::getDbTable('users');
		$google_user = $google_users->findOneByIdSocial($this->_id_social);

		if (empty($google_user))
		{
			$google_user = HTS_Util::getDbRow('users');
			$birthday = new Zend_Date(@$this->_me['birthday'], 'dd/MM/yyyy');
				
			//TODO some values are null -> undefined index warning
			$google_user->id_social = $this->_id_social;
			$google_user->name = $this->_me['displayName'];
			//			$google_user->first_name = $this->_me['name']['givenName'];
			//			$google_user->middle_name = @$this->_me['name']['middleName'];
			//			$google_user->last_name = @$this->_me['name']['familyName'];
			$google_user->link = $this->_me['url'];
			$google_user->image = $this->_me['image']['url'];
			$google_user->birthday = @$birthday->toString('yyyy/MM/dd');
			$google_user->gender = $this->_me['gender'];
			//$google_user->timezone = $this->_me['timezone'];
			$google_user->locale = @$this->_me['currentLocation'];
			$google_user->time_created = time();
			$google_user->time_modified = time();
				
			$this->_id_user = $google_user->save();
		}

		$this->_user = $google_user;
		$this->_id_user = $google_user->id;
	}

	public function getIdUser()
	{
		return $this->_id_user;
	}

	public function getMe()
	{
		return $this->_me;
	}

	public function getProfileImage()
	{
		return $this->_me['image']['url'];
	}

	public function getUserPages()
	{
		return $this->_user_pages;
	}

	public function redirect($url)
	{
		HTS_Util::parentRedirect($this->_config->url . $url);
	}

	public function getFriendIds()
	{
		if ($this->_friend_ids == null)
		{
			$this->getFriends();
		}
		return $this->_friend_ids;
	}

	public function getFriends() {
		if ($this->_friends == null)
		{
			$this->_friends = array();
			$this->_friend_ids = array();
			if ( $this->_id_social != "" ) {
				$visible_url = 'https://plus.google.com/_/socialgraph/lookup/visible/?o=%5Bnull%2Cnull%2C%22' . $this->_id_social . '%22%5D';
				$jsondata = GoogleUtil::FetchGoogleJSON( $visible_url );
				$visiblepeople = $jsondata[2];
				//Zend_Debug::dump($visiblepeople);die();
				foreach( $visiblepeople as $pdata ) {
					$googleplus_id = $pdata[0][2];
					 
					$name = $pdata[2][0];
					//	                if ( preg_match( '/^([\w\.\s]+) (\w+)$/', $name, $matches ) ) {
					//	                    $first_name = $matches[1];
					//	                    $last_name = $matches[2];
					//	                } else {
					//	                    $first_name = $name;
					//	                }

					$image = $pdata[2][8];

					$this->_friends[$googleplus_id]['name'] = $name;
					$this->_friends[$googleplus_id]['image'] = $image;
					 
					$this->_friend_ids[] = '\'' . $googleplus_id . '\'';
				}
			}
		}

		return $this->_friends;
	}
}

class GoogleUtil {


	public static function FetchGoogleJSON( $url ) {
		$response = file_get_contents( $url );
		$response = GoogleUtil::CleanGoogleJSON( $response );
		return json_decode( $response, true );
	}

	public static function CleanGoogleJSON( $googlejson ) {

		//delete anti-xss junk ")]}'\n" (5 chars);
		$googlejson = substr( $googlejson, 5 );

		//pass through result and turn empty elements into nulls
		//echo strlen( $googlejson ) . '<br>';
		$instring = false;
		$inescape = false;
		$lastchar = '';
		$output = "";
		for ( $x=0; $x<strlen( $googlejson ); $x++ ) {

			$char = substr( $googlejson, $x, 1 );

			//toss unnecessary whitespace
			if ( !$instring && ( preg_match( '/\s/', $char ) ) ) {
				continue;
			}

			//handle strings
			if ( $instring ) {
				if ( $inescape ) {
					$output .= $char;
					$inescape = false;
				} else if ( $char == '\\' ) {
					$output .= $char;
					$inescape = true;
				} else if ( $char == '"' ) {
					$output .= $char;
					$instring = false;
				} else {
					$output .= $char;
				}
				$lastchar = $char;
				continue;
			}


			switch ( $char ) {
				 
				case '"':
					$output .= $char;
					$instring = true;
					break;

				case ',':
					if ( $lastchar == ',' || $lastchar == '[' || $lastchar == '{' ) {
						$output .= 'null';
					}
					$output .= $char;
					break;

				case ']':
				case '}':
					if ( $lastchar == ',' ) {
						$output .= 'null';
					}
					$output .= $char;
					break;

				default:
					$output .= $char;
					break;
			}
			$lastchar = $char;
		}
		return $output;
	}
}
