<?php
class HTS_Api
{
	protected static $_instance;
	private $_socialPlugin;
	private $_socialPluginConfig;

	/**
	 * Singleton instantiation
	 * @return HTS_Api
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	 
	/**
	 * Init social plugin configuration
	 * @param Zend_Config $config
	 */
	public function initSocialPlugin($config)
	{
		$this->_socialPluginConfig = $config;
	}
	 
	/**
	 * Factory method to create a HTS_Social_Plugin instance
	 * HTS_Social_Plugin instance is cached after creation
	 * @return HTS_Social_Plugin
	 */
	public function getSocialPlugin()
	{
		if (is_null($this->_socialPlugin))
		{
			try {
				if ($this->_socialPluginConfig->name == 'Facebook'):
				$this->_socialPlugin = new HTS_Facebook($this->_socialPluginConfig->facebook);
				elseif ($this->_socialPluginConfig->name == 'Google'):
				$this->_socialPlugin = new HTS_Google($this->_socialPluginConfig->google);
				elseif ($this->_socialPluginConfig->name == 'Govn'):
				$this->_socialPlugin = new HTS_Govn($this->_socialPluginConfig->govn);
				else:
				$this->_socialPlugin = new HTS_Social_Dummy($this->_socialPluginConfig->dummy);
				endif;
			} catch (Exception $e) {
				if (!empty($this->_socialPluginConfig->debug))
				{
					Zend_Debug::dump($e);
				}
			}
		}
		return $this->_socialPlugin;
	}
}