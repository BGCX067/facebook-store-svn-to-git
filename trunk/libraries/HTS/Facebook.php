<?php
/**
 *@author HT-Stores-World 
 */
class HTS_Facebook extends HTS_Social_Plugin {

    protected $_name = 'Facebook';
    /**
     *
     * @var HTS_Facebook_Api
     */
    private $_facebook;
    private $_signed_request;
    private $_user_profile;
    private $_user_pages;
    private $_user_page_ids;
    private $_page_id;
    private $_has_logged_in = false;
    private $_has_authorized = false;

    function __construct($config) {
        parent::__construct($config);
        $this->_facebook = new HTS_Facebook_Api(array(
            'appId' => $config->id,
            'secret' => $config->secret,
            'proxy.host' => @$config->proxy->host,
            'proxy.port' => @$config->proxy->port,
        ));

        $this->_init();
    }
    /**
     * Initialization
     */
    private function _init() {
        // signed_request in tab page is transfered via URL /signed_request/<SIGNED_REQUEST>
        // need to put this into $_REQUEST to be used by original Facebook API
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $signed_request = $request->getParam('signed_request');
        if (!empty($signed_request) && empty($_REQUEST['signed_request'])) {
            $_REQUEST['signed_request'] = $signed_request;
        }

        // FIXME temporary only, need to do this only once for an app
        if (!empty($this->_config->product->enabled)) {
            $app_access_token = $this->_config->id . '|' . $this->_config->secret;
            //set post_authorize_redirect_url
            $post_redirect_url = $this->_config->url;
            $str = "https://api.facebook.com/method/admin.setAppProperties?access_token=" . $app_access_token . "&properties={'post_authorize_redirect_url':'" . $post_redirect_url . "'}";
            $obj = @file_get_contents($str);
        }
        // FIXME - end

        $this->_signed_request = $this->_facebook->getSignedRequest();
        if (!empty($this->_config->debug)) {
            Zend_Debug::dump($this->_signed_request);
        }

        // add app_data from fanpage to the universal params
        // app_data must be in json format
        // FIXME fix the case app_data not well-formatted
        if (isset($_POST['signed_request']) && isset($this->_signed_request['app_data'])) {
            $data = json_decode($this->_signed_request['app_data']);
            foreach ($data as $name => $value) {
                if ($request->getParam($name) == null) {
                    $request->setParam($name, $value);
                }
            }
        }

        try {
            $this->_id_social = $this->_facebook->getUser();
            if (!empty($this->_config->debug)) {
                Zend_Debug::dump($this->_id_social);
            }
        } catch (FacebookApiException $e) {
            $this->_id_social = null;
        }

        // user has logged in and authorized this app
        if ($this->_id_social) {
            $this->_updateUserDb();
            $this->_has_authorized = true;
        } else {
            $this->_user = HTS_Util::getDbRow('users');
            $this->_has_authorized = false;
        }

        $installed = $request->getParam('installed');
        $fb_page_id = $request->getParam('fb_page_id');

        if (isset($installed) && !empty($fb_page_id) && $installed == '1') {
            // if user has not granted permission to get email or manage fanpages, then ask for permission
            $user_profile = $this->getUserProfile();
            $user_pages = $this->getUserPages();

            if (empty($user_profile['email']) || empty($user_pages)) {
                $scope = '';
                if (!empty($this->_config->oauthScope)) {
                    $scope = $this->_config->oauthScope . ',';
                }
                HTS_Util::parentRedirect($this->_facebook->getOauthUrl(
                                        $this->url('/' . $request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName() . '/installed/1/fb_page_id/' . $fb_page_id),
                                        $scope . 'email,manage_pages'));
                exit();
            }
            // end if

            if (!empty($this->_config->product->enabled)) {
                try {
                    $client = new SoapClient(null, array(
                                'location' => $this->_config->product->url,
                                'uri' => $this->_config->product->namespace,
                                'trace' => 1));

                    $return = $client->__soapCall("createAProductForUser", array(// input parameters
                                $this->_config->product->username,
                                md5($this->_config->product->password),
                                $user_profile['email'],
                                $user_profile['first_name'],
                                $user_profile['last_name'],
                                $fb_page_id,
                                $this->_config->product->id
                            ));

                    error_log("INFO | " . date('Y-m-d H:i:s') . " | createAProductForUser(" . $this->_config->product->username
                            . "," . md5($this->_config->product->password)
                            . "," . $user_profile['email']
                            . "," . $user_profile['first_name']
                            . "," . $user_profile['last_name']
                            . "," . $fb_page_id
                            . "," . $this->_config->product->id . ") | $return\n", 3, 'soap.log');
                } catch (Exception $e) {
                    error_log("ERR | " . date('Y-m-d H:i:s') . " | createAProductForUser(" . $this->_config->product->username
                            . "," . md5($this->_config->product->password)
                            . "," . $user_profile['email']
                            . "," . $user_profile['first_name']
                            . "," . $user_profile['last_name']
                            . "," . $fb_page_id
                            . "," . $this->_config->product->id . ") | $e\n", 3, 'soap.log');
                    if (!empty($this->_config->product->debug)) {
                        throw $e;
                    } else {
                        // FIXME temporarily ignore
                    }
                }
            }

            HTS_Util::parentRedirect('http://facebook.com/pages/' . APPLICATION_NAME . '/' . $fb_page_id . '?sk=app_' . $this->getAppId());
        }

        $uninstalled = $request->getParam('fb_uninstalled');
        $fb_page_id = @$this->_signed_request['profile_id'];

        if (isset($uninstalled) && $uninstalled == '1' && !empty($fb_page_id)) {
            if (!empty($this->_config->product->enabled)) {
                try {
                    $client = new SoapClient(null, array(
                                'location' => $this->_config->product->url,
                                'uri' => $this->_config->product->namespace,
                                'trace' => 1));

                    $return = $client->__soapCall("removeAProductFromUser", array(// input parameters
                                $this->_config->product->username,
                                md5($this->_config->product->password),
                                $fb_page_id,
                                $this->_config->product->id
                            ));

                    error_log("INFO | " . date('Y-m-d H:i:s') . " | removeAProductFromUser(" . $this->_config->product->username
                            . "," . md5($this->_config->product->password)
                            . "," . $fb_page_id
                            . "," . $this->_config->product->id . ") | $return\n", 3, 'soap.log');
                } catch (Exception $e) {
                    error_log("ERR | " . date('Y-m-d H:i:s') . " | removeAProductFromUser(" . $this->_config->product->username
                            . "," . md5($this->_config->product->password)
                            . "," . $fb_page_id
                            . "," . $this->_config->product->id . ") | $e\n", 3, 'soap.log');
                    if (!empty($this->_config->product->debug)) {
                        throw $e;
                    } else {
                        // FIXME temporarily ignore
                    }
                }
            }
        }
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::handleFailedAuthorization()
     */
    public function handleFailedAuthorization($redirect_url = '/') {
        // user has authorized the app, but does not have enough access privileges
        if ($this->_has_authorized) {
            throw new HTS_Exception("User has authorized the app, but does not have enough access privileges");
        } else {
            HTS_Util::parentRedirect($this->_facebook->getOauthUrl($this->url($redirect_url), $this->_config->oauthScope));
            exit(0);
        }
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::_updateUserDb()
     */
    protected function _updateUserDb() {
        $userData = HTS_Util::getObjectFileCache()->load('user_' . $this->_id_social);
        if ($userData === false) {
            $user_db = HTS_Util::getDbTable('users');
            $user = $user_db->findOneByIdSocial($this->_id_social);

            if (empty($user)) {
                $user_profile = $this->getUserProfile();
                $user = HTS_Util::getDbRow('users');

                //TODO some values are null -> undefined index warning
                if (!empty($user_profile['birthday'])) {
                    $birthday = new Zend_Date(@$user_profile['birthday'], 'dd/MM/yyyy');
                    $user->birthday = @$birthday->toString('yyyy/MM/dd');
                }
                $updated_time = new Zend_Date($user_profile['updated_time']);

                $user->id_social = $user_profile['id'];
                $user->name = $user_profile['name'];
                $user->email = @$user_profile['email'];
                $user->first_name = $user_profile['first_name'];
                $user->middle_name = @$user_profile['middle_name'];
                $user->last_name = @$user_profile['last_name'];
                $user->link = $user_profile['link'];
                $user->image = 'http://graph.facebook.com/' . $user_profile['id'] . '/picture';
                $user->gender = $user_profile['gender'];
                $user->timezone = $user_profile['timezone'];
                $user->locale = $user_profile['locale'];
                $user->time_created = time();
                $user->time_modified = time();
                $user->time_social_modified = $updated_time->toValue();
            }
            $user->save();
            HTS_Util::getObjectFileCache()->save($user->toArray(), 'user_' . $this->_id_social);
        } else {
            $user_db = HTS_Util::getDbTable('users');
            $user = $user_db->createRow($userData);
        }
        $this->_user = $user;
        $this->_id_user = $user->id;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getUserProfile()
     */
    public function getUserProfile() {
        if ($this->_user_profile == null) {
            try {
                $this->_user_profile = $this->_facebook->api('/me');
            } catch (Exception $e) {
                $this->_user_profile = array();
            }
        }
        return $this->_user_profile;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getUserPages()
     */
    public function getUserPages() {
        if ($this->_user_pages == null) {
            if (!empty($this->_id_user)) {
                $accounts = $this->_facebook->api('/me/accounts');
                $this->_user_pages = $accounts['data'];
                if (!empty($this->_user_pages)) {
                    foreach ($this->_user_pages as $key => $page) {
                        if ($page['category'] == 'Application') {
                            unset($this->_user_pages[$key]);
                        }
                    }
                }
            } else {
                $this->_user_pages = array();
            }
        }
        return $this->_user_pages;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getPageList()
     */
    public function getPageList() {
        $ret = array();
        foreach ($this->getUserPages() as $page) {
            $ret[$page['id']] = $page['name'];
        }
        return $ret;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getUserPageIds()
     */
    public function getUserPageIds() {
        if ($this->_user_page_ids == null) {
            $user_pages = $this->getUserPages();
            if (!empty($user_pages)) {
                foreach ($user_pages as $page) {
                    $this->_user_page_ids[] = $page['id'];
                }
            } else {
                $this->_user_page_ids = array();
            }
        }
        return $this->_user_page_ids;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getIdPage()
     */
    public function getIdPage() {
        if (isset($this->_signed_request['page'])) {
            return $this->_signed_request['page']['id'];
        }
        return null;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getPageName()
     */
    public function getPageName($id_page) {
        $pages = $this->getUserPages();
        $page_name = '';
        foreach ($pages as $page) {
            if ($page['id'] == $id_page) {
                $page_name = $page['name'];
            }
        }
        return $page_name;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::redirect()
     */
    public function redirect($url) {
        HTS_Util::parentRedirect($this->_config->url . $url);
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::redirectLink()
     */
    public function redirectLink($url) {
        if ($this->_isUseAppsLink()) {
            HTS_Util::parentRedirect($url);
        } else {
            HTS_Util::redirect($url);
        }
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::url()
     */
    public function url($original_url) {
        if (substr($original_url, 0, 4) == 'http') {
            return $original_url;
        } else {
            return $this->getAppUrl() . $original_url;
        }
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::pageUrl()
     */
    public function pageUrl($params, $id_page = null) {
        if (empty($id_page)) {
            $id_page = $this->getIdPage();
        }
        if (!empty($params)) {
            $app_data = urlencode(json_encode($params));
            return $this->getFanPageUrl($id_page, 'page') . '&app_data=' . $app_data;
        } else {
            return $this->getFanPageUrl($id_page, 'page');
        }
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getFanPageUrl()
     */
    public function getFanPageUrl($id_page, $page_name = null) {
        if (empty($page_name)) {
            $page_name = $this->getPageName($id_page);
        }
        return 'http://www.facebook.com/pages/' . $page_name . '/' . $id_page . '?sk=app_' . $this->getAppId();
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::isPageAdmin()
     */
    public function isPageAdmin($id_page = null) {
        if (isset($this->_signed_request['page'])) {
            return $this->_signed_request['page']['admin'];
        }
        return false;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::hasLikedPage()
     */
    public function hasLikedPage() {
        if (isset($this->_signed_request['page'])) {
            return $this->_signed_request['page']['liked'];
        }
        return false;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::postUrlToPageWall()
     */
    public function postUrlToPageWall($url, $message = '', $id_page = null) {
        if (is_null($id_page)) {
            $id_page = $this->getIdPage();
        }
        $page_access_token = $this->_getPageAccessToken($id_page);
        if (!empty($page_access_token)) {
            $data['access_token'] = $page_access_token;
            $data['message'] = $message;
            $data['link'] = $url;

            return $this->_facebook->api('/' . $this->getIdPage() . '/feed', 'POST', $data);
        }
        return false;
    }
    
    private function _getPageAccessToken($id_page) {
        //TODO need to cache this
        $pages = $this->getUserPages();
        foreach ($pages as $page) {
            if ($page['id'] == $id_page) {
                return $page['access_token'];
            }
        }
        return '';
    }
    
    public function isUseLocalScript() {
        return $this->_config->localScript;
    }

    private function _isUseAppsLink() {
        if (isset($this->_config->appsLink)) {
            return $this->_config->appsLink;
        } else {
            return true;
        }
    }

    public function getShareUrl($url) {
        if ($this->_isUseAppsLink()) {
            return $this->getAppUrl() . $url;
        }

        return 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . $url;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getAlbumsByIdPage()
     */
    public function getAlbumsByIdPage($id_page) {
        $albums = $this->_facebook->api('/' . $id_page . '/albums/');
        $albums_data = $albums['data'];
        return $albums_data;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getAlbumInfo()
     */
    public function getAlbumInfo($id_album) {
        $album = $this->_facebook->api('/' . $id_album . '/');
        return $album;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getPhotoInfo()
     */
    public function getPhotoInfo($id_photo) {
        $photo = $this->_facebook->api('/' . $id_photo . '/');
        return $photo;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getAlbumPhotos()
     */
    public function getAlbumPhotos($id_album) {
        $photos = $this->_facebook->api('/' . $id_album . '/photos/');
        $photos_data = $photos['data'];
        return $photos_data;
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getAccessToken()
     */
    public function getAccessToken() {
        return $this->_facebook->getAccessToken();
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getUrlInfo()
     */
    public function getUrlInfo($url = '') {
        return $this->_facebook->api(array(
            'method' => 'fql.query',
            'query' => 'SELECT url, normalized_url, share_count, like_count, comment_count, total_count,commentsbox_count, comments_fbid, click_count FROM link_stat WHERE url=\'' . $url . '\''
        ));
    }
    /* (non-PHPdoc)
     * @see HTS_Social_Plugin::getAppRequestData()
     */
    public function getAppRequestData()
	{
        if(isset($_REQUEST['request_ids']))
        {    
            $requests = explode(',', $_REQUEST['request_ids']);

            $request = $this->_facebook->api('/' . $requests[count($requests)-1] . '/', 'GET');
            if (isset($request['data']))
            {
                return $request['data'];
            }
        }
        return null;    
	}
}
