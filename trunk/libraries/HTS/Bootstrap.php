<?php

class HTS_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	private $_app_unique_key;
	protected function _initHTSPath()
	{
		defined('HTS_PATH')
		|| define('HTS_PATH', realpath(dirname(__FILE__)));
	}

	protected function _initBaseUrl()
	{
		$this->bootstrap("frontController");
		$front = $this->getResource("frontController");
		$request = new Zend_Controller_Request_Http();
		$front->setRequest($request);

		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		$this->_app_unique_key = 'hts_'.hash('MD5', $base_url);
	}

	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('XHTML1_STRICT');
	}

	protected function _initSession()
	{
		Zend_Session::start();
		$session = new Zend_Session_Namespace($this->_app_unique_key);
		 
		Zend_Registry::getInstance()->set('session', $session);
	}

	protected function _initModules()
	{
		$front = $this->bootstrap("frontController")->frontController;
		$modules = $front->getControllerDirectory();
		$default = $front->getDefaultModule();

		set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_PATH . '/modules/');

		$autoloader = Zend_Loader_Autoloader::getInstance();
		$controllers = array();
		foreach (array_keys($modules) as $module) {
			$directory = $front->getModuleDirectory($module);
			$controllers[strtolower($module)] = $directory . '/controllers';

			// init auto loader
			$autoloader->pushAutoloader(new Zend_Application_Module_Autoloader(array(
                'namespace' => ucwords($module),
                'basePath' => $directory,
		    	'resourceTypes' => array(
			        'model' => array(
			            'path'      => 'models/',
			            'namespace' => 'Model',
			)
			)
			)));

			// init auto loader namspace
			$autoloader->registerNamespace($module . '_');
		}

		$front->setControllerDirectory($controllers);
		$this->__initPaymentManager();
	}

	protected function _initAcl()
	{
		$acl = new Zend_Acl();
		 
		$acl->addRole(new Zend_Acl_Role(Core_Role::ROLE_GUEST));
		$acl->addRole(new Zend_Acl_Role(Core_Role::ROLE_USER), Core_Role::ROLE_GUEST);
		 
		$acl->addResource('default:index');
		$acl->allow(Core_Role::ROLE_GUEST, 'default:index', 'index');

		$registry = Zend_Registry::getInstance();
		$registry->set('acl', $acl);
	}

	protected function _initYouNetApi()
	{
		// init Social plugin
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/social.ini', APPLICATION_ENV);
		HTS_Api::getInstance()->initSocialPlugin($config->social);
	}

	protected function _initModuleBootstrap()
	{
		$front = Zend_Controller_Front::getInstance();
		$modules = $front->getControllerDirectory();

		foreach (array_keys($modules) as $module) {
			$directory = $front->getModuleDirectory($module);
			if (file_exists($directory . '/Bootstrap.php')) {
				$bootstrap_class = ucfirst($module) . '_Bootstrap';
				if (class_exists($bootstrap_class))
				{
					$bootstrap = new $bootstrap_class($this->getApplication());
					$bootstrap->bootstrap();
				}
			}
		}
	}

	protected function _initTimezone()
	{
		date_default_timezone_set('UTC');
	}

	protected function _initP3P()
	{
		header('P3P: CP="NON DSP COR CURa ADMa DEVa CUSa TAIa OUR SAMa IND"');
	}

	protected function _initCache()
	{
		$options		= $this->getOptions();
		$backendOptions	= array();
		if( isset($options['cache']) && isset($options['cache']['backend']) ){
			$backendOptions = $options['cache']['backend'];
		}
		$cacheManager	= new HTS_Cache_Manager($this->_app_unique_key, $backendOptions);
		Zend_Registry::set('cache_manager', $cacheManager);

		$this->__initDbCache();
	}

	public function __initDbCache()
	{
		Zend_Db_Table_Abstract::setDefaultMetadataCache(HTS_Util::getObjectFileCache());
	}

	public function _initStorage()
	{
		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		define('STATIC_PATH', INDEX_PATH);
        define('STATIC_URL', 'http://' . $_SERVER['SERVER_ADDR'] . $base_url);
		$this->__initPath();
	}

	public function __initPath()
	{
		// to be defined by each application
	}

	public function __initPaymentManager()
	{
		// to be defined by each application
	}

	public function _initGoogleAnalytics()
	{
		$options = $this->getOptions();
		if (isset($options['googleAnalytics']))
		{
			define('GOOGLE_ANALYTICS_ID', $options['googleAnalytics']['id']);
		}
		else
		{
			define('GOOGLE_ANALYTICS_ID', '');
		}
	}
    
    public function _initDefaultLanguage()
    {
        $options = $this->getOptions();
        if (isset($options['language']['code']))
        {
            define('DEFAULT_LANGUAGE_CODE', $options['language']['code']);
        }
        else
        {
            define('DEFAULT_LANGUAGE_CODE', 'en');
        }
    }
}
