<?php
/**
 * @author HT-Stores-World 
 */
class HTS_Cache_Manager extends Zend_Cache_Manager{
	const OBJECTCACHE_FILE			= 'ObjFile';
	const OBJECTCACHE_MEMCACHED		= 'ObjMemcached';

	protected $_prefix			= '';
	protected $_options;

	public function __construct($prefix, $options = array())
	{
		$this->_options			= $options;
		$this->_prefix			= $prefix;
		$this->_init();
	}

	public function _init()
	{
		$this->_initObjectFile();
		$this->_initObjectMemcached();
	}

	protected function _initObjectFile()
	{
		if( isset($this->_options['file']) )
		{
			$this->_optionTemplates[self::OBJECTCACHE_FILE] = array(
				'frontend'	=> $this->_optionTemplates['default']['frontend'],
				'backend'	=> $this->_options['file']
			);
		}
	}

	protected function _initObjectMemcached()
	{
		if( isset($this->_options['memcached']) )
		{
			$this->_optionTemplates[self::OBJECTCACHE_MEMCACHED] = array(
				'frontend'	=> $this->_optionTemplates['default']['frontend'],
				'backend'	=> $this->_options['memcached']
			);
		}
		else
		{
			$this->_optionTemplates[self::OBJECTCACHE_MEMCACHED] = array(
				'frontend'	=> $this->_optionTemplates['default']['frontend'],
				'backend'	=> array(
					'name'					=> 'HTS_Cache_Backend_Dummy',
					'customBackendNaming'	=> true
                )
            );
		}
		$memcached = $this->getCache(self::OBJECTCACHE_MEMCACHED);
		$memcached->getBackend()->setPrefix($this->_prefix);
	}


	public function getObjectFileCache()
	{
		return $this->getCache(self::OBJECTCACHE_FILE);
	}
    
	public function getObjectMemcached()
	{
		return $this->getCache(self::OBJECTCACHE_MEMCACHED);
	}
}
