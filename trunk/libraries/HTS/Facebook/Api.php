<?php
include_once 'Facebook/facebook.php';

class HTS_Facebook_Api extends Facebook
{
	protected $_user;
    protected $_cache;

    public function __construct($config)
	{
		parent::__construct($config);
		if (isset($config['proxy.host']) && isset($config['proxy.port']))
		{
			self::$CURL_OPTS[CURLOPT_HTTPPROXYTUNNEL] = 'true';
			self::$CURL_OPTS[CURLOPT_PROXY] = $config['proxy.host'];
			self::$CURL_OPTS[CURLOPT_PROXYPORT] = $config['proxy.port'];
		}
	}

	public function getSignedRequest() {
		if (! $this->signedRequest) {
			if (isset ( $_REQUEST ['signed_request'] )) {
				$this->signedRequest = $this->parseSignedRequest ( $_REQUEST ['signed_request'] );
				$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
				$module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
				setcookie($this->getSignedRequestCookieName (), $_REQUEST ['signed_request'], 0, $base_url . '/' . $module);
			} else if (isset ( $_COOKIE [$this->getSignedRequestCookieName ()] )) {
				$this->signedRequest = $this->parseSignedRequest ( $_COOKIE [$this->getSignedRequestCookieName ()] );
			}
		}
        
		return $this->signedRequest;
	}

	public function getOauthUrl($redirect_url, $oauth_scopes)
	{
		$this->establishCSRFTokenState();
		return "https://www.facebook.com/dialog/oauth?client_id=".$this->getAppId()
		."&redirect_uri=".$redirect_url
		."/&state=".$this->state."&scope=".$oauth_scopes;
	}

	protected function getUserAccessToken()
	{
        $access_token = $this->getPersistentData('access_token');
        if(empty($access_token))
        {
            $this->_cache = HTS_Util::getObjectFileCache();
            $access_token = $this->_getCacheAccessToken();
            if ($access_token === false || !$this->_checkValidAccessToken($access_token))
            {    
                if (isset($this->signedRequest['access_token']))
                {
                    $access_token = $this->signedRequest['access_token'];
                    $access_token = $this->_getExtendsTimeOut($access_token);
                    $this->_setCacheAccessToken($access_token);
                    $this->setPersistentData('access_token', $access_token);
                }
                else
                {
                    $access_token = parent::getUserAccessToken();
                    $access_token = $this->_getExtendsTimeOut($access_token);
                    $this->_setCacheAccessToken($access_token);
                    $this->setPersistentData('access_token', $access_token);
                }
            }
            else
            {
                $this->setPersistentData('access_token', $access_token);
            }    
        }
        return $access_token;
	}
    
    protected function _getCacheAccessToken()
    {
        $id_user = $this->signedRequest['user_id'];
        $cache_key = 'access_token_' . $id_user;
        $access_token = $this->_cache->load($cache_key);
        return $access_token;
    }
    
    protected function _setCacheAccessToken($access_token)
    {
        $id_user = $this->signedRequest['user_id'];
        $cache_key = 'access_token_' . $id_user;
        $this->_cache->save($access_token, $cache_key, array(), 30*24*60*60);
    }        

    protected function _getExtendsTimeOut($access_token)
	{
		try
		{
			$access_token_response = $this->_oauthRequest(
			$this->getUrl('graph', '/oauth/access_token'), array(
                    'client_id' => $this->getAppId(),
                    'client_secret' => $this->getApiSecret(),
                    'grant_type' => 'fb_exchange_token',
                    'fb_exchange_token' => $access_token,
			));
		}
		catch (Exception $e)
		{
			return $access_token;
		}

		if (empty($access_token_response)) {
			return $access_token;
		}

		$response_params = array();
		parse_str($access_token_response, $response_params);

		if (!isset($response_params['access_token'])) {
			return $access_token;
		}
        
		return $response_params['access_token'];
	}
    
    protected function _checkValidAccessToken($access_token)
    {
        $is_valid = true;
        $url = "https://graph.facebook.com/me?access_token=" . $access_token;
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        
        $result = json_decode(curl_exec($ch), true);
        if(!empty($result['error']))
        {
            $is_valid = false;
        }    
        curl_close($ch);
        return $is_valid;
    }
}